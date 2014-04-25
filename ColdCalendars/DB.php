<?php
require_once (__DIR__ . '/lib/User.php');

class DB {
	private static $host = 'preumbranet.domaincommysql.com';
	private static $name = 'cold_calendars_test';
	private static $user = 'backend';
	private static $pass = 'wearewinners';
	private static $qryReport = "SELECT CONCAT(u.First,' ',u.Last) AS Name,
       								 CASE u.PTFT WHEN 1 THEN 'Full Time'
                                     ELSE 'Part Time' END AS 'Work Status', SUM(swappy.Hours) as 'Total Hours'
								FROM User u
								JOIN(SELECT
									Swap.Owner,
									(time_to_sec(timediff(End_time, Start_time )) / 3600) as Hours
									FROM Shift
									JOIN ( SELECT Shift_FK, MAX(Timestamp) AS Timestamp
									  FROM  Swap
								      WHERE Approved = True
									  GROUP BY Shift_FK, Approved
									) AS swp
									ON   swp.Shift_FK  = Shift.PK
									JOIN Swap
									ON   swp.Shift_FK  = Swap.Shift_FK
									AND  swp.Timestamp = Swap.Timestamp
									WHERE Shift.Start_time >= '@PARAM'
									AND   Shift.End_time   <= '@PARAM'
								) as swappy
								ON swappy.Owner = u.PK
								GROUP BY u.Login";

	public static function getCSVExport($start,$end) {
	  $conn    = DB::getNewConnection();
	  $result  = DB::query($conn, DB::injectParamaters(array($start,$end), self::$qryReport));
	  array_unshift($result, array('Employee','Work Status','Hours'));
	  $rows    = array();
	  foreach($result as $row)
	    array_push($rows,implode(',',$row));
	  return implode("\n",$rows);
	}

	public static function getNewConnection() {
		return new Mysqli ( self::$host, self::$user, self::$pass, self::$name );
	}
	public static function query($conn, $str) {
		$resultSet = $conn->query ( $str );
		if ($resultSet === false)
			throw new Exception ( "Error querying DB with SQL: $str\n" . "\nerrorno: " . $conn->errno . "\nerror: " . $conn->error );
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

	public static function timeToDateTime($time) {
		if(!preg_match('/^1970-01-01 .*$/',$time))
	    	return '1970-01-01 '.$time;
		return $time;
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

	public static function trinaryVariableToSQL($var) {
	  return $var===null ? 'Null' : (int) $var;
	}

	public static function sqlToTrinaryVariable($var) {
	  return $var===null ? null : (bool) $var;
	}
}

?>
