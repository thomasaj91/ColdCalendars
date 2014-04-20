<?php
error_reporting(E_ALL);
ini_set('display errors',3);
require_once(__DIR__.'/../DB.php');

class Template {
	private static $qryCreateTemplate = "sql";
	private static $qryTemplateExists = "sql";
	private static $qryDeleteTemplate = "sql";
	private static $qryLoadTemplate = "sql";
	private static $qryGetAllTemplates = "sql";
	
	private $title;
	private $startTime;
	private $endTime;
	
	public function Template($title, $start, $end, $create) {
		if($create) {
			$params = array($title, DB::timeToDateTime($start), DB::timeToDateTime($end));
			$conn = DB::getNewConnection();
			$sql = DB::injectParameters($params, self::$qryCreateTemplate);
			$result = DB::execute($conn, $sql);
			$conn->close(); 
		}
		else {
			$this->title = $title;
			$this->startTime = $start;
			$this->endTime = $end;
		}
	}
	
	public static function create($title, $start, $end) {
		self::assertNonExistance($title, $start, $end);
		return new Template($title, $start, $end, true);
	}
	
	public static function load($title, $start, $end) {
		self::assertExistance($title, $start, $end);
		return new Template($title, $start, $end, false);
	}
	
	public static function delete($title, $start, $end) {
		self::assertExistance($title, $start, $end);
		$params = array($title, DB::timeToDateTime($start),DB::timeToDateTime($end));
	    $conn   = DB::getNewConnection();
	    $sql    = DB::injectParamaters($params, self::$qryDeleteTemplate);
	    $result = DB::execute($conn, $sql);
	    $conn->close();
	}
	
	public static function exists($title, $start, $end) {
		$conn    = DB::getNewConnection();
		$sql     = DB::injectParamaters(array($title,DB::timeToDateTime($start),DB::timeToDateTime($end)), self::$qryTemplateExists);
		$results = DB::query($conn, $sql);
		$conn->close();
		return ($results [0] [0] === '1') ? true : false;
	}
	
	public static function getAllTemplates($start, $end) {
		$conn	 = DB::getNewConnection();
		$sql     = DB::injectParameters(array(DB::timeToDateTime($start), DB::timeToDateTime($end)), self::$qryDeleteTemplate);
		$result  = DB::query($conn, $sql);
		$conn->close();
		$out = array();
		foreach($result as $row)
			array_push($out, self::load($title, DB::timeToDateTime($row[0]), DB::timeToDateTime($row[2]))->getInfo() );
		return $out;
	}
	
	public function getInfo() {
		return array(
			'title' => $this->title,
			'start' => $this->startTime,
			'end'   => $this->endTime
		);
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getStartTime() {
		return $this->startTime;
	}
	
	public function getEndTime() {
		return $this->endTime;
	}
	
	private static function assertExistance($title, $start, $end) {
		if(!self::exists($title, $start, $end))
			throw new Exception("Template ($title, $start, $end) does not exist!");
	}
	
	private static function assertNonExistance($title, $start, $end) {
		if(self::exists($title, $start, $end))
			throw new Exception("Template ($title, $start, $end) already exists!");
	}
}
?>
