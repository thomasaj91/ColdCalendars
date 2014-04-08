<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);

require_once (__DIR__ . '/../DB.php');
class TimeOffRequest {
  private $login;
  private $approved;
  private $startTime;
  private $endTime;
  
  public function TimeOffRequest($login, $start, $end, $create) {
    if($create) {
    }
    else {
    }
  }
  
  public static function create($login, $start, $end) {
    return new TimeOffRequest($login, $start, $end, true);
  }
  
  public static function load($login, $start, $end) {
    return new TimeOffRequest($login, $start, $end, false);
  }
}

?>