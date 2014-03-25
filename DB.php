<?php
class DB {
	
	private static $host   = 'preumbranet.domaincommysql.com';
	private static $name   = 'cold_calendars_test';
	private static $user   = 'backend';
	private static $pass   = 'wearewinners';
	
	public static function getNewConnection() {
	  return new Mysqli(self::$host
  	  	 	           ,self::$user
 	  	 	           ,self::$pass
	  			       ,self::$name);
	}
	
	public static function query($conn, $str) {
		$resultSet = $conn->query ($str);
		if ($resultSet === false)
			throw new Exception("Error querying DB with SQL: $str\n" . "\nerrorno: " . $conn->errno . "\nerror: " . $conn->error );
		//$resultSet->data_seek(0);
		$resultArr = array();
		for($i=0; $row = $resultSet->fetch_row(); $i++){
			$resultArr[$i]  = $row;
		}
		$resultSet->free();
		return $resultArr;
	}
	
	public static function execute($conn, $str) {
  	  $success = $conn->multi_query($str);
  	  if ($success === false)
  	  	throw new Exception("Error querying DB with SQL: $str\n" . "\nerrorno: " . $conn->errno . "\nerror: " . $conn->error );
	}  	  	
	
	public static function escapeString($str) {
		$tmp = self::getNewConnection();
		$out = $tmp->real_escape_string($str);
		$tmp->close();
		return $out;
	}
}

?>