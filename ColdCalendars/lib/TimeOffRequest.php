<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);

require_once (__DIR__ . '/../DB.php');

class TimeOffRequest {
  
  private static $qryTimeOffRequestExists = "
SELECT EXISTS(SELECT 1
FROM  TimeOff
JOIN  User
ON    User_FK    = PK
WHERE Login      = '@PARAM'
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM')";
  private static $qryCreateTimeOffRequest = "
      INSERT INTO TimeOff
VALUES ((SELECT PK FROM User WHERE Login = '@PARAM' LIMIT 1),
      Null,
      '@PARAM',
      '@PARAM')";
  private static $qryLoadTimeOffRequest = "
SELECT Approved, Start_time, End_time, First, Last
FROM  TimeOff
JOIN  User
ON    User_FK    = PK
WHERE Login      = '@PARAM'
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM'";

  private static $qryUpdateTimeOffRequest = "
UPDATE TimeOff
Set   Approved   = @PARAM
WHERE User_FK    = (SELECT PK FROM User WHERE Login = '@PARAM')
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM'";
  
  private static $qryUndecidedTimeOffRequests = "
  		SELECT u.login, t.Start_Time, t.End_Time
  		FROM TimeOff t
  		JOIN User u
  		ON (t.User_FK = u.PK)
  		WHERE t.Start_Time >= NOW()
  		AND t.Approved IS NULL";

  private static $qryDecidedTimeOffRequests = "
  		SELECT u.login, t.Start_Time, t.End_Time
  		FROM TimeOff t
  		JOIN User u
  		ON (t.User_FK = u.PK)
  		WHERE t.Start_Time >= NOW()
  		AND t.Approved IS NOT NULL";
  
  private static $qryUserDecidedTimeOffRequests = "
  		SELECT u.login, v.Start_Time, v.End_Time
  		FROM TimeOff v
  		JOIN User u
  		ON (v.User_FK = u.PK)
  		WHERE v.Start_Time >= (NOW() - INTERVAL 7 day)
        AND User_FK = (SELECT PK FROM User WHERE Login = '@PARAM')
  		AND v.Approved IS NOT NULL";
  
  
  private $login;
  private $approved;
  private $startTime;
  private $endTime;
  private $first;
  private $last;
  
  public function TimeOffRequest($login, $start, $end, $create) {
    if($create) {
      $params = array($login, $start,$end);
      $conn   = DB::getNewConnection();
      $sql    = DB::injectParamaters($params, self::$qryCreateTimeOffRequest);
      $result = DB::execute($conn, $sql);
      $conn->close();
    }
    else {
      $params = array($login, $start, $end);
      $conn   = DB::getNewConnection();
      $sql    = DB::injectParamaters($params, self::$qryLoadTimeOffRequest);
      $result = DB::query($conn, $sql);
      $conn->close();
      $requestData     = $result[0];
      $this->login     = $login;
      $this->approved  = ($requestData[0]===null) ? null : (bool) $requestData[0];
      $this->startTime =  $requestData[1];
      $this->endTime   =  $requestData[2];
      $this->first     =  $requestData[3];
      $this->last      =  $requestData[4];
    }
  }
  
  public static function create($login, $start, $end) {
    if(self::exists($login, $start, $end))
      throw new Exception("TimeOffRequest($login,$start,$end) already exists!");
    return new TimeOffRequest($login, $start, $end, true);
  }
  
  public static function load($login, $start, $end) {
    if(!self::exists($login, $start, $end))
      throw new Exception("TimeOffRequest($login,$start,$end) does NOT exist!");
    return new TimeOffRequest($login, $start, $end, false);
  }
  
  public static function exists($login, $start, $end) {
    $conn   = DB::getNewConnection();
    $sql    = DB::injectParamaters(array($login, $start, $end), self::$qryTimeOffRequestExists);
    $result = DB::query($conn, $sql);
    $conn->close();
    return $result[0][0] != 0;
  }
  
  public static function getUndecidedTimeOffRequests() {
  	$conn   = DB::getNewConnection();
  	$sql    = DB::injectParamaters(array(), self::$qryUndecidedTimeOffRequests);
  	$result = DB::query($conn, $sql);
  	$out = array();
  	foreach($result as $row)
  		array_push($out, self::load($row[0], $row[1], $row[2])->getInfo());
  	$conn->close();
  	return $out;
  }

  public static function getDecidedTimeOffRequests() {
    $conn   = DB::getNewConnection();
    $sql    = DB::injectParamaters(array(), self::$qryDecidedTimeOffRequests);
    $result = DB::query($conn, $sql);
    $out = array();
    foreach($result as $row)
    		array_push($out, self::load($row[0], $row[1], $row[2])->getInfo());
    $conn->close();
    return $out;
  }
  
  
  public static function getUserDecidedTimeOffRequests($login) {
    $conn   = DB::getNewConnection();
    $sql    = DB::injectParamaters(array($login), self::$qryUserDecidedTimeOffRequests);
    $result = DB::query($conn, $sql);
    $out = array();
    foreach($result as $row)
    		array_push($out, self::load($row[0], $row[1], $row[2])->getInfo());
    $conn->close();
    return $out;
  }
  public function getInfo() {
    return array(
    	'login'     => $this->login,
    	'approved'  => $this->approved,
      	'startTime' => $this->startTime,
      	'endTime'   => $this->endTime,
    	'first'     => $this->first,
    	'last'      => $this->last
    );
  }
  
  public function isDecided() {
    return $this->approved !== null;
  }
  
  public function isApproved() {
    return $this->approved === true;
  }
  
  public function isRejected() {
    return $this->approved === false;
  }

  public function approve() {
    if($this->isDecided())
      return;
    $this->approved = true;
    $this->commitData();
  }

  public function reject() {
    if($this->isDecided())
      return;
    $this->approved = false;
    $this->commitData();
  }

  public function unApprove() {
    if($this->isApproved())
      return;
    $this->approved = false;
    $this->commitData();
  }

  public function unReject() {
    if(!$this->isrejected())
      return;
    $this->approved = true;
    $this->commitData();
  }
  
  
  private function commitData() {
    $params = array(DB::trinaryVariableToSQL($this->approved),  $this->login, $this->startTime, $this->endTime);
    $conn   = DB::getNewConnection();
    $sql    = DB::injectParamaters($params, self::$qryUpdateTimeOffRequest);
    $result = DB::execute($conn, $sql);
    $conn->close();    
  }
  
}

?>
