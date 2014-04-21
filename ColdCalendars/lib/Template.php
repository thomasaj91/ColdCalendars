<?php
error_reporting(E_ALL);
ini_set('display errors',3);
require_once(__DIR__.'/../DB.php');

class Template {
	private static $qryCreateTemplate = "INSERT INTO Templates
			                             VALUES(NULL, '@PARAM', '@PARAM', '@PARAM')";
	
	private static $qryTemplateExists = "SELECT EXISTS(SELECT 1
			                             FROM Templates
			                             WHERE title = '@PARAM'";
	
	private static $qryDeleteTemplate = "DELETE FROM Templates
			                             WHERE title = '@PARAM'";
	
	private static $qryLoadTemplate = "SELECT title, start_time, end_time
			                           FROM Templates
			                           WHERE title = '@PARAM'";
	
	private static $qryGetAllTemplates = "SELECT title, start_time, end_time
			                              FROM Templates";
	
	private $title;
	private $startTime;
	private $endTime;
	
	public function Template($title) {
		$this->title = $title;
	}
	
	public function Template($title, $start, $end) {
		$params = array($title, DB::timeToDateTime($start), DB::timeToDateTime($end));
		$conn = DB::getNewConnection();
		$sql = DB::injectParameters($params, self::$qryCreateTemplate);
		$result = DB::execute($conn, $sql);
		$conn->close(); 
	}
	
	public static function create($title, $start, $end) {
		self::assertNonExistance($title, $start, $end);
		return new Template($title, $start, $end);
	}
	
	public static function load($title) {
		self::assertExistance($title);
		return new Template($title);
	}
	
	public static function delete($title) {
		self::assertExistance($title);
		$params = array($title);
	    $conn   = DB::getNewConnection();
	    $sql    = DB::injectParamaters($params, self::$qryDeleteTemplate);
	    $result = DB::execute($conn, $sql);
	    $conn->close();
	}
	
	public static function exists($title) {
		$conn    = DB::getNewConnection();
		$sql     = DB::injectParamaters(array($title), self::$qryTemplateExists);
		$results = DB::query($conn, $sql);
		$conn->close();
		return ($results [0] [0] === '1') ? true : false;
	}
	
	public static function getAllTemplates() {
		$conn	 = DB::getNewConnection();
		$sql     = DB::injectParameters(array(), self::$qryGetAllTemplates);
		$result  = DB::query($conn, $sql);
		$conn->close();
		$out = array();
		foreach($result as $row)
			array_push($out, self::load($row[0], DB::timeToDateTime($row[1]), DB::timeToDateTime($row[2]))->getInfo() );
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
	
	private static function assertExistance($title) {
		if(!self::exists($title))
			throw new Exception("Template ($title) does not exist!");
	}
	
	private static function assertNonExistance($title) {
		if(self::exists($title))
			throw new Exception("Template ($title) already exists!");
	}
}
?>
