<?php
include_once (__DIR__ . '/../DB.php');
class User {
	private static $getUserData = "Select * From User AS usr Where usr.Login = '@PARAM'";
	public function User($login) {
		$conn = DB::getNewConnection ();
		$success = $conn->query ( str_replace ( "@PARAM", $login, self::$getUserData ) );
		if (! $success)
			die ( "Failed" . "\nerrorno: " . $conn->errno . "\nerror: " . $conn->error );
		else
			echo "queried succesfully!\n";
		$results = $conn->use_result();
/**
			echo $conn->errno."\n";
			echo '\''.$conn->error."\n";
			echo $conn->field_count."\n";
		if ($conn->errno !== 0) {
			echo "User does not exist";
		}
	    else {
	    }
/**/
			if ($results === false)
				die ( "faailed to get user, errno: " .$conn->errno ." error: " . "'" . $conn->error . "'" . mysqli_errno());
			$arr = $results->fetch_array ();
			$userData = $arr [0];
	/*		*/
	}
}

?>