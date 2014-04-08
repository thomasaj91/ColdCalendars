<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__ . '/../DB.php');
class VacationRequest {

  private $login;
  private $approved;
  private $startTime;
  private $endTime;
  
  
  public function VactionRequest($login, $start, $end, $create) {
  	if($create) {
  		
  	}
  	else {
  		
  	}
  }
  
  public static function create($login, $start, $end) {
  	return new VacationRequest($login, $start, $end, true);
  }
  
  public static function load($login, $start, $end) {
  	return new VacationRequest($login, $start, $end, false);
  }
  
  
}

?>