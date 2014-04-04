<?php
require_once (__DIR__ . '/lib/User.php');

class DB {
	private static $host = 'preumbranet.domaincommysql.com';
	private static $name = 'cold_calendars_test';
	private static $user = 'backend';
	private static $pass = 'wearewinners';
	public static function getNewConnection() {
		return new Mysqli ( self::$host, self::$user, self::$pass, self::$name );
	}
	public static function query($conn, $str) {
		$resultSet = $conn->query ( $str );
		if ($resultSet === false)
			throw new Exception ( "Error querying DB with SQL: $str\n" . "\nerrorno: " . $conn->errno . "\nerror: " . $conn->error );
			// $resultSet->data_seek(0);
		$resultArr = array ();
		for($i = 0; $row = $resultSet->fetch_row (); $i ++) {
			$resultArr [$i] = $row;
		}
		$resultSet->free ();
		return $resultArr;
	}
	public static function execute($conn, $str) {
		$success = $conn->multi_query ( $str );
		if ($success === false)
			throw new Exception ( "Error querying DB with SQL: $str\n" . "\nerrorno: " . $conn->errno . "\nerror: " . $conn->error );
    }
    
	public static function escapeString($str) {
		$tmp = self::getNewConnection ();
		$out = $tmp->real_escape_string ( $str );
		$tmp->close ();
		return $out;
	}
	
	public static function getSystemTime() {
		return date('Y-m-d H:i:s',time());
	}
	
	public static function str_replace_once($needle,$replace,$haystack) {
		$pos = strpos($haystack,$needle);
		if ($pos === false)
			return $haystack;
		return  substr_replace($haystack,$replace,$pos,strlen($needle));
	}

	public static function injectParamaters($params,$sql) {
		foreach($params as $param)
			$sql = DB::str_replace_once('@PARAM', $param, $sql);
		return $sql;
	}
	
	public static function buildDatabase() {
		$dbConn = self::getNewConnection ();
		if ($dbConn->connect_error)
			return false;
		    //die ( "Could not connect to database $name" . "\nat host: $host" . "\nas user: $user" . "\nerrorno: " . $dbConn->connect_errorno . "\nerror: " . $dbConn->connect_error );
		//else
		//	echo "Connected Successfully\n";
		
		$sqlPayload = file_get_contents ( __DIR__ . '/initiate/schema.txt' );
		
		// $payloads = explode(';', $sqlPayload);
		
		$success = $dbConn->multi_query ( $sqlPayload );
		if (! $success)
			return false;
			//die ( "Failed to build database" . "\nerrorno: " . $dbConn->errno . "\nerror: " . $dbConn->error );
		//else
		//	echo "Succefully built\n";
		$dbConn->close ();
		
		sleep ( 2 ); /* it needs to be two seconds, do NOT change */
		
		try {
			User::create ( 'root', 'lolsecurity', 'Fname', 'LName', 'Admin', true, 0, '5558675309', 'admin@coldcalendars.preumbra.net' );
		} catch ( Exception $e ) {
          return false;
//			die ( 'failed to create user root\n' . $e->getMessage () );
		}
		return true;
	}
}

?>