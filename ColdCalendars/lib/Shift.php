<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);

require_once(__DIR__ . '/../DB.php');
class Shift {

	private static $qryCreateShift = "set @pk := (SELECT AUTO_INCREMENT
FROM information_schema.tables
WHERE table_name = 'Shift'
AND   table_schema = DATABASE());

INSERT INTO Shift 
VALUES (@pk,'@PARAM','@PARAM');

INSERT INTO Swap
VALUES (@pk,
        (SELECT PK FROM User WHERE Login = '@PARAM')
        ,NULL,False,NULL,NOW());
";

		private static $qryLoadShift = "SELECT 
    Start_time,
    End_time,
    Released, 
    Approved,
    User.Login
	FROM Shift
	JOIN ( SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	  FROM Swap
	  GROUP BY Shift_FK
	) AS swp
	ON   swp.Shift_FK  = Shift.PK
	JOIN Swap
	ON   swp.Shift_FK  = Swap.Shift_FK
	AND  swp.Timestamp = Swap.Timestamp
	LEFT JOIN User
	ON   Swap.Next = User.PK
	WHERE Shift.Start_time = '@PARAM'
	AND   Shift.End_time   = '@PARAM'
	AND   Swap.Prev =
	(SELECT PK FROM User WHERE Login = '@PARAM') LIMIT 1";

	private static $qryUpdateShift = 
	"UPDATE Swap
SET Next=(SELECT PK FROM User WHERE Login = '@PARAM'), Released = @PARAM, Approved = @PARAM, Timestamp = NOW()
WHERE Shift_FK IN
(SELECT PK
 FROM Shift
 WHERE Start_time = '@PARAM'
 AND   End_time   = '@PARAM')
AND Prev = 
(SELECT PK FROM User WHERE Login = '@PARAM')";

	private static $qryInsertNewOwner = "
set @pk :=
 (SELECT PK FROM Shift
  JOIN Swap
  ON   Swap.Shift_FK = Shift.PK
  WHERE Shift.Start_time = '@PARAM'
  AND   Shift.End_time   = '@PARAM'
  AND   Swap.Prev = (SELECT PK FROM User WHERE Login = '@PARAM')
  LIMIT 1);
INSERT INTO Swap VALUES
(@pk,
 (SELECT PK FROM User WHERE Login = '@PARAM')
 ,NULL,False,NULL,NOW());";

	private static $qryDeleteShift = "set @pk := (SELECT PK FROM Shift
         JOIN Swap
         ON   Swap.Shift_FK = Shift.PK
         WHERE Shift.Start_time = '@PARAM'
         AND   Shift.End_time   = '@PARAM'
         AND   Swap.Prev = (SELECT PK FROM User WHERE Login = '@PARAM')
         LIMIT 1);
    DELETE FROM Swap WHERE Shift_FK = @pk;
    DELETE FROM Shift WHERE PK = @pk;";

	private static $qryGetAllShifts = "SELECT 
    User.Login,
    Start_time,
    End_time
    FROM Shift
	JOIN ( SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	  FROM Swap
	  GROUP BY Shift_FK
	) AS swp
	ON   swp.Shift_FK  = Shift.PK
	JOIN Swap
	ON   swp.Shift_FK  = Swap.Shift_FK
	AND  swp.Timestamp = Swap.Timestamp
    JOIN User
    ON   Swap.Prev = User.PK
    WHERE Shift.Start_time >= '@PARAM'
    AND   Shift.End_time   <= '@PARAM'";

	private static $qryUndecidedSwaps = "
SELECT 
    User.Login,
	Start_time,
    End_time,
    Released, 
    Approved
	FROM Shift
	JOIN ( SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	  FROM Swap
	  GROUP BY Shift_FK
	) AS  swp
	ON    swp.Shift_FK  = Shift.PK
	JOIN  Swap
	ON    swp.Shift_FK  = Swap.Shift_FK
	AND   swp.Timestamp = Swap.Timestamp
	LEFT JOIN User
	ON    Swap.Prev = User.PK
	WHERE Shift.Start_time >= '@PARAM'
	AND   Shift.End_time   <= '@PARAM'
    AND   Next IS NOT NULL
    AND   Approved IS NULL
    ";

	private $owner;
	private $pickuper;
	private $released;
	private $approved;
	private $startTime;
	private $endTime;

	public function Shift($login,$start,$end,$create) {
		if($create) {
			$conn = DB::getNewConnection();
			DB::execute($conn,DB::injectParamaters(array($start,$end,$login), self::$qryCreateShift));
		    $conn->close();
			$this->owner     = $login;
			$this->pickuper  = null;
			$this->released  = false;
			$this->approved  = null;
			$this->startTime = $start;
			$this->endTime   = $end;
			return;
		}
		else { //load shift
			if(!self::exists($login, $start, $end))
				throw new Exception("Shift Does Not Exist");
			$conn    = DB::getNewConnection();
			$results = DB::query($conn,DB::injectParamaters(array($start,$end,$login), self::$qryLoadShift));
			$conn->close();
			$shiftData       = $results[0];
			$this->owner     = $login;		
			$this->pickuper  = $shiftData[4];
			$this->released  = (bool)$shiftData[2];
			$this->approved  = ($shiftData[3]===NULL) ? NULL : (bool) $shiftData[3];
			$this->startTime = $start;
			$this->endTime   = $end;
		}
	}

	public static function create($login,$start,$end) {
	  return new Shift($login, $start, $end, true);
	}

	public static function load($login,$start,$end) {
      return new Shift($login, $start, $end, false);
	}

	public static function delete($login,$start,$end) {
		$sql  = DB::injectParamaters(array($start,$end,$login), self::$qryDeleteShift);
		$conn = DB::getNewConnection();
		$res  = DB::execute($conn, $sql);
		$conn->close();
	}

	public static function exists($login,$start,$end) {
		$conn    = DB::getNewConnection();
		$results = DB::query($conn,DB::injectParamaters(array($start,$end,$login), self::$qryLoadShift));
		return count($results) !== 0;
	}

	public static function getAllUndecidedSwaps($start, $end) {
		$conn    = DB::getNewConnection();
		$results = DB::query($conn,DB::injectParamaters(array($start,$end), self::$qryUndecidedSwaps));
		$out = array();
		foreach($results as $row)
		  array_push($out, self::load($row[0], $row[1], $row[2])->getInfo());
        return $out;
	}
	public function getInfo() {
		$out = array();
		$out['owner']     =  $this->owner;
		$out['pickuper']  = ($this->pickuper !== null) ? $this->pickuper : 'Null';
		$out['startTime'] =  $this->startTime;
		$out['endTime']   =  $this->endTime;
		$out['released']  =  $this->released;
		$out['approved']  = ($this->approved !== null) ? $this->approved : 'Null';
		return $out;
	}

	public function getStartTime() {
		return $this->startTime;
	}

	public function getEndTime() {
		return $this->startEnd;
	}

	/* Return DB.Prev */
	public function getOwner() {
		return $this->owner;
	}

	/* True iff DB.Released is True && starttime > NOW() */
	public function isReleased() {
		return $this->released;
	}

	/* True iff DB.Next !== NULL && starttime > NOW() */
	public function isPickedUp() {
		return $this->pickuper !== NULL;
	}

	/* True iff DB.Approved !== NULL */
	public function isDecided() {
	    return $this->approved !== NULL;
	}

	/* True iff DB.Approved === True */
	public function isApproved() {
		return ($this->approved === NULL) ? false : $this->approved; 
	}

	/* Set DB.Released = True  */
	public function release() {
		if($this->isReleased())
			return;
		$this->released = true;
		$this->update();
	}

	/* Set DB.Next = PK of $login */	
	public function pickup($login) {
	  if(!$this->isReleased())
	  	return;
	  $this->pickuper = $login;
	  $this->update();
	}

	/* Set DB.approved = False */	
	public function reject() {
		if(!$this->isPickedUp())
			return;
		$this->approved = false;
		$this->update();
	}

	/* Set DB.approved = True,
	 * Create new Swap Record for new owner
	 */	
	public function approve() {
		if(!$this->isPickedUp())
			return;
	  $this->approved = true;
	  $this->update();
	  $this->transferResponsiblity();
	}

	private function update() {
		$params = array($this->pickuper
				,$this->released
				,($this->approved===NULL) ? 'NULL' : (int) $this->approved
				,$this->startTime
				,$this->endTime
				,$this->owner
		);
		$sql  = DB::injectParamaters($params, self::$qryUpdateShift);
		$conn = DB::getNewConnection();
		$res  = DB::execute($conn, $sql);
		$conn->close();
	}

	private function transferResponsiblity() {
		$params = array($this->startTime
				       ,$this->endTime
			           ,$this->owner
				       ,$this->pickuper);
		$sql  = DB::injectParamaters($params, self::$qryInsertNewOwner);
		$conn = DB::getNewConnection();
		$res  = DB::execute($conn, $sql);
		$conn->close();
		/* Maybe don't do this and mark the object as dirty? */
		$this->owner    = $this->pickuper;
		$this->pickuper = null;
		$this->released = false;
		$this->approved = null;
	}

	public static function toDateString($date, $time) {
		return "$date $time";
	}

	public static function getAllShifts($start,$end) {
		$conn = DB::getNewConnection();
		$sql  = DB::injectParamaters(array($start,$end), self::$qryGetAllShifts);
		$res  = DB::query($conn, $sql);
		$out  = array();
//		var_dump($res);
// 		$limit = count($res);
// 		for($i = 0; $i < $limit; $i++)
// 			$out[$i] = self::load($res[$i][0],$res[$i][1],$res[$i][2]);

		foreach($res as $row)
			array_push($out, (self::load($row[0], $row[1], $row[2])->getInfo() ));
		$conn->close();
		return $out;
	}
}
?>
