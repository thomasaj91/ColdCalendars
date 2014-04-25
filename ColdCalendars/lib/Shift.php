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
        ,False,True,NOW());
";

		private static $qryLoadShift = "SELECT 
    Start_time,
    End_time,
    Released, 
    Approved
	FROM Shift
	JOIN ( SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	  FROM  Swap
      WHERE Approved = True
	  GROUP BY Shift_FK, Approved
	) AS swp
	ON   swp.Shift_FK  = Shift.PK
	JOIN Swap
	ON   swp.Shift_FK  = Swap.Shift_FK
	AND  swp.Timestamp = Swap.Timestamp
	WHERE Shift.Start_time = '@PARAM'
	AND   Shift.End_time   = '@PARAM'
	AND   Swap.Owner =
	(SELECT PK FROM User WHERE Login = '@PARAM') LIMIT 1";

		private static $qryLoadSwaps = "SELECT User.Login
	FROM Shift
	JOIN (
      SELECT Swap.Shift_FK, iswp.Timestamp
  	  FROM Shift 
  	  JOIN (
  	    SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	    FROM  Swap
        WHERE Approved = True
        GROUP BY Shift_FK, Approved
	  ) AS iswp
	  ON iswp.Shift_FK  = Shift.PK
	  JOIN Swap
	  ON   iswp.Shift_FK  = Swap.Shift_FK
	  AND  iswp.Timestamp = Swap.Timestamp
	  AND  Swap.Owner = (SELECT PK FROM User WHERE Login = '@PARAM')
	) AS oswp
	ON   oswp.Shift_FK  = Shift.PK
	JOIN Swap
	ON   oswp.Shift_FK   = Swap.Shift_FK
	AND  oswp.Timestamp <= Swap.Timestamp
    JOIN User
    ON   User.PK = Swap.Owner
	WHERE Shift.Start_time = '@PARAM'
	AND   Shift.End_time   = '@PARAM'
    AND   Approved IS NULL";

	private static $qryDecideSwapper = 
	"UPDATE Swap
SET Owner=(SELECT PK FROM User WHERE Login = '@PARAM'), Released = False, Approved = @PARAM, Timestamp = NOW()
WHERE Shift_FK IN
(SELECT PK
   FROM Shift
   JOIN (
        SELECT Shift_FK, Owner, MAX(Timestamp) AS Timestamp
	    FROM  Swap
        WHERE Approved = True
        GROUP BY Shift_FK, Approved
   ) AS swp
   ON Shift.PK = swp.Shift_FK
   WHERE Start_time = '@PARAM'
   AND   End_time   = '@PARAM'
   AND Owner =
       (SELECT PK FROM User WHERE Login = '@PARAM'))
AND Owner =
    (SELECT PK FROM User WHERE Login = '@PARAM')
AND Timestamp = (
  SELECT Timestamp
   FROM Shift
   JOIN (
        SELECT Shift_FK, Owner, MAX(Timestamp) AS Timestamp
	    FROM  Swap
        WHERE Approved IS NULL
        GROUP BY Shift_FK, Approved,Owner
   ) AS swp
   ON Shift.PK = swp.Shift_FK
   WHERE Start_time = '@PARAM'
   AND   End_time   = '@PARAM'
   AND Owner =
       (SELECT PK FROM User WHERE Login = '@PARAM')
   LIMIT 1
  )";
   
	private static $qryUpdateShift =
	"UPDATE Swap
SET Owner=(SELECT PK FROM User WHERE Login = '@PARAM'), Released = @PARAM, Approved = True, Timestamp = NOW()
WHERE Shift_FK IN
(SELECT PK
 FROM Shift
 WHERE Start_time = '@PARAM'
 AND   End_time   = '@PARAM')
AND Owner =
(SELECT PK FROM User WHERE Login = '@PARAM')";


	private static $qryInsertNewOwner = "
set @pk :=
 (SELECT PK FROM Shift
  JOIN Swap
  ON   Swap.Shift_FK = Shift.PK
  WHERE Shift.Start_time = '@PARAM'
  AND   Shift.End_time   = '@PARAM'
  AND   Swap.Owner = (SELECT PK FROM User WHERE Login = '@PARAM')
  LIMIT 1);
INSERT INTO Swap VALUES
(@pk,
 (SELECT PK FROM User WHERE Login = '@PARAM')
 ,NULL,False,NULL,NOW());";

	private static $qryInsertSwapper = "
	set @pk :=
	(SELECT PK
	FROM Shift
	JOIN ( SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	  FROM  Swap
      WHERE Approved = True
	  GROUP BY Shift_FK, Approved
	) AS swp
	ON   swp.Shift_FK  = Shift.PK
	JOIN Swap
	ON   swp.Shift_FK  = Swap.Shift_FK
	AND  swp.Timestamp = Swap.Timestamp
	WHERE Swap.Owner = (SELECT PK FROM User WHERE Login = '@PARAM')
	AND   Shift.Start_time = '@PARAM'
	AND   Shift.End_time   = '@PARAM'
	LIMIT 1);
	INSERT INTO Swap VALUES
	(@pk,
	(SELECT PK FROM User WHERE Login = '@PARAM')
	,False,NULL,NOW());";


	private static $qryDeleteShift = "set @pk := (SELECT PK FROM Shift
         JOIN Swap
         ON   Swap.Shift_FK = Shift.PK
         WHERE Shift.Start_time = '@PARAM'
         AND   Shift.End_time   = '@PARAM'
         AND   Swap.Owner = (SELECT PK FROM User WHERE Login = '@PARAM')
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
      WHERE Approved = True
	  GROUP BY Shift_FK
	) AS swp
	ON   swp.Shift_FK  = Shift.PK
	JOIN Swap
	ON   swp.Shift_FK  = Swap.Shift_FK
	AND  swp.Timestamp = Swap.Timestamp
    JOIN User
    ON   Swap.Owner = User.PK
    WHERE Shift.Start_time >= '@PARAM'
    AND   Shift.End_time   <= '@PARAM'";

	private static $qryUndecidedSwaps = "
SELECT
    oswp.Login AS 'Owner Login',
    oswp.First AS 'Owner FName',
    oswp.Last  AS 'Owner LName',
	User.Login AS 'Swapper Login',
    User.First AS 'Swapper FName',
    User.Last  AS 'Swapper FLast',
	Start_time,
    End_time,
	Approved    
	FROM Shift
	JOIN (
      SELECT Swap.Shift_FK, iswp.Timestamp, User.Login, User.First, User.Last
 	  FROM Shift 
  	  JOIN (
  	    SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	    FROM  Swap
        WHERE Approved = True
        GROUP BY Shift_FK, Approved
	  ) AS iswp
	  ON iswp.Shift_FK  = Shift.PK
	  JOIN Swap
	  ON   iswp.Shift_FK  = Swap.Shift_FK
	  AND  iswp.Timestamp = Swap.Timestamp
      JOIN User
      ON   User.PK = Swap.Owner
    ) AS oswp
	ON   oswp.Shift_FK  = Shift.PK
    AND  Shift.Start_time >= '@PARAM'
    AND  Shift.End_time <= '@PARAM'
	JOIN Swap
	ON   Swap.Shift_FK   = Shift.PK
    AND  Swap.Timestamp >= oswp.Timestamp
    JOIN User
    ON   Swap.Owner = User.PK
    WHERE Swap.Approved IS NULL
    ";

	private static $qryDecidedSwaps = "
SELECT
    oswp.Login AS 'Owner Login',
    oswp.First AS 'Owner FName',
    oswp.Last  AS 'Owner LName',
	User.Login AS 'Swapper Login',
    User.First AS 'Swapper FName',
    User.Last  AS 'Swapper FLast',
	Start_time,
    End_time,
	Approved
	FROM Shift
	JOIN (
      SELECT Swap.Shift_FK, iswp.Timestamp, User.Login, User.First, User.Last
 	  FROM Shift
  	  JOIN (
  	    SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	    FROM  Swap
        WHERE Approved = True
        GROUP BY Shift_FK, Approved
	  ) AS iswp
	  ON iswp.Shift_FK  = Shift.PK
	  JOIN Swap
	  ON   iswp.Shift_FK  = Swap.Shift_FK
	  AND  iswp.Timestamp = Swap.Timestamp
      JOIN User
      ON   User.PK = Swap.Owner
    ) AS oswp
	ON   oswp.Shift_FK  = Shift.PK
    AND  Shift.Start_time >= '@PARAM'
    AND  Shift.End_time <= '@PARAM'
	JOIN Swap
	ON   Swap.Shift_FK   = Shift.PK
    AND  Swap.Timestamp >= oswp.Timestamp
    JOIN User
    ON   Swap.Owner = User.PK
    WHERE Swap.Approved IS NOT NULL
    ";
	
	
	private static $qryUserDecidedSwaps = "
    SELECT
    oswp.Login AS 'Owner Login',
    oswp.First AS 'Owner FName',
    oswp.Last  AS 'Owner LName',
	User.Login AS 'Swapper Login',
    User.First AS 'Swapper FName',
    User.Last  AS 'Swapper FLast',
	Start_time,
    End_time,
	Approved
	FROM Shift
	JOIN (
      SELECT Swap.Shift_FK, iswp.Timestamp, User.Login, User.First, User.Last
 	  FROM Shift
  	  JOIN (
  	    SELECT Shift_FK, MAX(Timestamp) AS Timestamp
	    FROM  Swap
        WHERE Approved = True
        GROUP BY Shift_FK, Approved
	  ) AS iswp
	  ON iswp.Shift_FK  = Shift.PK
	  JOIN Swap
	  ON   iswp.Shift_FK  = Swap.Shift_FK
	  AND  iswp.Timestamp = Swap.Timestamp
      JOIN User
      ON   User.PK = Swap.Owner
    ) AS oswp
	ON   oswp.Shift_FK  = Shift.PK
    AND  Shift.Start_time >= '@PARAM'
    AND  Shift.End_time <= '@PARAM'
	AND  Shift.Owner = (SELECT PK FROM user Where Login = '@PARAM')
	JOIN Swap
	ON   Swap.Shift_FK   = Shift.PK
    AND  Swap.Timestamp >= oswp.Timestamp
    JOIN User
    ON   Swap.Owner = User.PK
    WHERE Swap.Approved IS NOT NULL
    ";
	
	private $owner;
	private $swappers;
	private $released;
	private $startTime;
	private $endTime;
	private $approved;

	public function Shift($login,$start,$end,$create) {
		if($create) {
			$conn = DB::getNewConnection();
			DB::execute($conn,DB::injectParamaters(array($start,$end,$login), self::$qryCreateShift));
		    $conn->close();
			$this->owner     = $login;
			$this->swappers  = array();
			$this->released  = false;
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
			$this->released  = (bool)$shiftData[2];
			$this->startTime = $start;
			$this->endTime   = $end;
			$this->swappers  = $this->getAllSwaps();
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
		  array_push($out, self::extendedSwapInfo($row));
        return $out;
	}

	public static function getAllDecidedSwaps($start, $end) {
	  $conn    = DB::getNewConnection();
	  $results = DB::query($conn,DB::injectParamaters(array($start,$end), self::$qryDecidedSwaps));
	  $out = array();
	  foreach($results as $row)
	    array_push($out, self::extendedSwapInfo($row));
	  return $out;
	}
	
	public static function getAllUserDecidedSwaps($login,$start,$end) {
	    $conn    = DB::getNewConnection();
	    $results = DB::query($conn,DB::injectParamaters(array($start,$end,$login), self::$qryUserDecidedSwaps));
	    $out = array();
	    foreach($results as $row)
	      array_push($out, self::extendedSwapInfo($row));
	    return $out;
    }

	public function getInfo() {
		$out = array();
		$out['owner']     =  $this->owner;
		$out['swappers']  =  $this->swappers;
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

	/* Return DB.owner */
	public function getOwner() {
		return $this->owner;
	}

	/* True iff DB.Released is True && starttime > NOW() */
	public function isReleased() {
		return $this->released;
	}

	/* True iff DB.Next !== NULL && starttime > NOW() */
	public function isPickedUp() {
		return !empty($this->swappers);
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
	  if(!$this->isReleased()
	  || $this->owner === $login
	  || in_array($login,$this->swappers))
	  	return;

	  array_push($this->swappers, $login);
	  $this->insertSwapper($login);
	}

	/* Set DB.approved = False */	
	public function reject($login) {
		if(!$this->isPickedUp() || !in_array($login, $this->swappers))
			return;
		$this->decideSwapper($login,false);
	}

	/* Set DB.approved = True,
	 * Create new Swap Record for new owner
	 */	
	public function approve($login) {
		if(!$this->isPickedUp() || !in_array($login, $this->swappers))
			return;
		foreach($this->swappers as $swapper)
		  if($swapper !== $login)
		    $this->decideSwapper($swapper, false);
		
	    $this->decideSwapper($login,true);
	    $this->owner    = $login;
	    $this->swappers = array();
	    $this->released = false;
	}

	private function update() {
		$params = array($this->owner
		        ,$this->released
//				,'NULL'
				,$this->startTime
				,$this->endTime
				,$this->owner
		);
		$sql  = DB::injectParamaters($params, self::$qryUpdateShift);
		$conn = DB::getNewConnection();
		$res  = DB::execute($conn, $sql);
		$conn->close();
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

	public function insertSwapper($login) {
	  $conn = DB::getNewConnection();
	  $sql  = DB::injectParamaters(array($this->owner,$this->startTime,$this->endTime,$login), self::$qryInsertSwapper);
	  $res  = DB::execute($conn, $sql);
	  $conn->close();
	}

	private function getAllSwaps() {
	  $conn = DB::getNewConnection();
	  $sql  = DB::injectParamaters(array($this->owner,$this->startTime,$this->endTime), self::$qryLoadSwaps);
	  $res  = DB::query($conn, $sql);
	  $out  = array();
	  foreach($res as $row)
	    array_push($out, $row[0]);
	  $conn->close();
	  return $out;
	}

	public function decideSwapper($login, $approval) {
	  $params = array( $login
            	     , $approval ? 'True' : 'False'
              	     , $this->startTime
          	         , $this->endTime
	                 , $this->owner
	                 , $login
              	     , $this->startTime
          	         , $this->endTime
	                 , $login
	                 );
	  $conn = DB::getNewConnection();
	  $sql  = DB::injectParamaters($params, self::$qryDecideSwapper);
	  $res  = DB::execute($conn, $sql);
	  $conn->close();
//	  $this = new self($login, $this->start, $this->end, false);	   
	}

	private static function extendedSwapInfo($arr) {
	  return array(
	     'owner'     => array('login' => $arr[0] 
	                         ,'first' => $arr[1]
	                         ,'last'  => $arr[2]
	                         )
	    ,'swapper'   => array('login' => $arr[3]
	                         ,'first' => $arr[4]
	                         ,'last'  => $arr[5]
	                         )
	    ,'startTime' => $arr[6]
	    ,'endTime'   => $arr[7]
	    ,'approved'  => $arr[8]
	    );
	}
}
?>
