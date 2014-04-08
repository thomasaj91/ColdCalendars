<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__ . '/../DB.php');
class VacationRequest {
  private static $qryCreateAvailability = "INSERT INTO Availability VALUES ((SELECT PK FROM User WHERE Login = '@PARAM'),@PARAM,'@PARAM','@PARAM');";  
  private static $qryLoadAvailability   = "
SELECT Login, Day, Start_time, End_time
FROM  Availability
JOIN  User
ON    User_FK    = PK
WHERE Login      = '@PARAM'
AND   Day        =  @PARAM
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM'
      ";
  private static $qryDeleteAvailability = "
DELETE FROM Availability
WHERE User_FK    = (SELECT PK FROM User WHERE Login = '@PARAM')
AND   Day        =  @PARAM
AND   Start_time = '@PARAM'
AND   End_time   = '@PARAM'";
  
  
  private static $DAYS = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
  private $login;
  private $day;
  private $startTime;
  private $endTime;
  
  public function Availability($login, $day, $start, $end, $create) {
  	if($create) {
  	  $params = array($login, self::dayToNum($day), DB::timeToDateTime($start),DB::timeToDateTime($end));
  	  $conn   = DB::getNewConnection();
  	  $sql    = DB::injectParamaters($params, self::$qryCreateAvailability);
  	  $result = DB::query($conn, $sql);
  	  $conn->close();
  	}
  	else {
  	  $this->login     = $login;
  	  $this->day       = $day;
  	  $this->startTime = $start;
  	  $this->endTime   = $end;
  	}
  }
  
  public static function create($login, $day, $start, $end) {
    assertNonExistance($login, $day, $start, $end);
    return new Availability($login, $start, $end, true);
  }
  
  public static function load($login, $day, $start, $end) {
    assertExistance($login, $day, $start, $end);
    return new Availability($login, $start, $end, false);
  }

  public static function delete($login, $day, $start, $end) {
    assertExistance($login, $day, $start, $end);
    $params = array($login, self::dayToNum($day), DB::timeToDateTime($start),DB::timeToDateTime($end));
    $conn   = DB::getNewConnection();
    $sql    = DB::injectParamaters($params, self::$qryDeleteAvailability);
    $result = DB::query($conn, $sql);
    $conn->close();
  }

  public static function exists($login, $day, $start, $end) {
    $params = array($login, self::dayToNum($day), DB::timeToDateTime($start),DB::timeToDateTime($end));
    $conn   = DB::getNewConnection();
    $sql    = DB::injectParamaters($params, self::$qryLoadAvailability);
    $result = DB::query($conn, $sql);
    $conn->close();
    return count($result) !== 0;
  }
  
  public function getInfo() {
    return array(
    	'login'     => $this->login,
    	'day'       => $this->day,
        'startTime' => $this->startTime,
        'endTiem'   => $this->endTime
        );
  }
  
  private static function numToDay($number) {
    return self::$DAYS[$num];
  }

  private static function dayToNum($day) {
    return array_search($day, self::$DAYS);
  }
  
  private static function assertExistance($login, $day, $start, $end) {
    if(!self::exists($login, $day, $start, $end))
      throw new Exception("Availability ($login, $day, $start, $end) does not exist!");
  }

  private static function assertNonExistance($login, $day, $start, $end) {
    if(self::exists($login, $day, $start, $end))
      throw new Exception("Availability ($login, $day, $start, $end) already exists!");
  }
}
?>