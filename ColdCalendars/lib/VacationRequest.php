<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);

require_once (__DIR__ . '/../DB.php');

class VacationRequest {
  
  private static $qryVacationRequestExists = "
SELECT EXISTS(SELECT 1
FROM  Vacation
JOIN  User
ON    User_FK    = PK
WHERE Login      = '@PARAM'
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM')";
  private static $qryCreateVacationRequest = "
      INSERT INTO Vacation
VALUES ((SELECT PK FROM User WHERE Login LIKE '@PARAM' LIMIT 1),
      Null,
      '@PARAM',
      '@PARAM')";
  private static $qryLoadVacationRequest = "
SELECT Approved, Start_time, End_time
FROM  Vacation
JOIN  User
ON    User_FK    = PK
WHERE Login      = '@PARAM'
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM'";

  private static $qryUpdateVacationRequest = "
UPDATE Vacation
Set   Approved   = @PARAM
WHERE User_FK    = (SELECT PK FROM User WHERE Login = '@PARAM')
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM'";
  
  private static $qryUndecidedVacationRequests = "
  		SELECT u.login, v.Approved, v.Start_Time, v.End_Time
  		FROM Vacation v
  		JOIN User u
  		ON (v.User_FK = u.PK)
  		WHERE v.Start_Time >= NOW()
  		AND v.Approved IS NULL";
  
  private $login;
  private $approved;
  private $startTime;
  private $endTime;
  
  public function VacationRequest($login, $start, $end, $create) {
    if($create) {
      $params = array($login, $start, $end);
      $conn   = DB::getNewConnection();
      $sql    = DB::injectParamaters($params, self::$qryCreateVacationRequest);
      $result = DB::execute($conn, $sql);
      $conn->close();
    }
    else {
      $params = array($login, $start, $end);
      $conn   = DB::getNewConnection();
      $sql    = DB::injectParamaters($params, self::$qryLoadVacationRequest);
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
      throw new Exception("VacationRequest($login,$start,$end) already exists!");
    return new VacationRequest($login, $start, $end, true);
  }
  
  public static function load($login, $start, $end) {
    if(!self::exists($login, $start, $end))
      throw new Exception("VacationRequest($login,$start,$end) does NOT exist!");
    return new VacationRequest($login, $start, $end, false);
  }
  
  public static function exists($login, $start, $end) {
    $conn   = DB::getNewConnection();
    $sql    = DB::injectParamaters(array($login, $start, $end), self::$qryVacationRequestExists);
    $result = DB::query($conn, $sql);
    $conn->close();
    return count($result) !== 0;
  }
  
  public static function getUndecidedVacationRequests() {
  	$conn   = DB::getNewConnection();
  	$sql    = DB::injectParamaters(array(), self::$qryUndecidedVacationRequests);
  	$result = DB::query($conn, $sql);
  	//var_dump($result);
  	$out = array();
  	foreach($result as $row)
  		array_push($out, self::load($row[0], $row[2], $row[3])->getInfo());
  	$conn->close();
  	return $out;
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
    $sql    = DB::injectParamaters($params, self::$qryUpdateVacationRequest);
    $result = DB::query($conn, $sql);
    $conn->close();    
  }
  
}

?>
