<?php
include_once (__DIR__ . '/../DB.php');
class User {
	private static $getUserData = "Select * From User AS usr Where usr.Login = '@PARAM'";

	private $title;
	private $ptft;
	private $vacationDays;
	private $released;
	private first      Varchar(255) NOT NULL,
	private Last       Varchar(255) NOT NULL,
	private Login      Varchar(255) NOT NULL UNIQUE,
	private $hash       Varchar(255) NOT NULL,
	private $salt
	private $auth       Varchar(1024) NOT NULL,
	private $time       DATE,
	
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

			$this->type = $userData[1];
			$this->ptft = (bool)$userData[2];
			$this->vacation = (bool)$userData[3];
				
	/*		*/
	}
	
	public function correctPassword($password) {
	  return $this->hash === self::hashPassword($this->salt,$password);
	}
	
	private static function hashPassword($salt,$password) {
	  return ourhashingfunction($salt . $password);
	}
}

?>