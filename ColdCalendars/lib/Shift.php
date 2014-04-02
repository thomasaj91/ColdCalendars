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
	public function relsease() {
		$this->released = true;
		$this->update();
	}
	
	/* Set DB.Next = PK of $login */	
	public function pickUp($login) {
	  if(!$this->isReleased())
	  	return;
	  $this->pickuper = $login;
	  $this->update();
	}

	/* Set DB.approved = False */	
	public function reject() {
		$this->approved = false;
		$this->update();
	}

	/* Set DB.approved = True,
	 * Create new Swap Record for new owner
	 */	
	public function approve() {
	  $this->approved = true;
	  $this->update();
	  $this->transferResponsiblity();
	}

	private function update() {
		$params = array($this->pickuper
				,$this->released
				,($this->approved===NULL) ? 'NULL' : $this->approved
				,$this->startTime
				,$this->endTime
				,$this->owner
		);
		$sql  = DB::injectParamaters($params, self::$qryUpdateShift);
		$conn = DB::getNewConnection();
		$res  = DB::execute($conn, $sql);
		$conn->close();
	}

}
?>