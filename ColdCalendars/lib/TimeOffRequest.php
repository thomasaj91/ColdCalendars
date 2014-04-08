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
SELECT Approved, Start_time, End_time
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
  
  private $login;
  private $approved;
  private $startTime;
  private $endTime;
  
  public function TimeOffRequest($login, $start, $end, $create) {
    if($create) {
      $params = array($login, $start,$end);
      $conn   = DB::getNewConnection();
      $sql    = DB::injectParamaters($params, self::$qryCreateTimeOffRequest);
      $result = DB::query($conn, $sql);
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
  }
  
  public function getInfo() {
    return array(
    	'login'     => $this->login,
    	'approved'  => $this->approved,
      	'startTime' => $this->startTime,
      	'endTime'   => $this->endTime,
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
    $result = DB::query($conn, $sql);
    $conn->close();    
  }
  
}

?>