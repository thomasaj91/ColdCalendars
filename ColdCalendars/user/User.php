<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);

include_once (__DIR__ . '/../DB.php');
class User {
	private static $qryUserData   = "SELECT * FROM User WHERE Login = '@PARAM' LIMIT 1";
	private static $qryUserPhone  = "SELECT phn.Number,  phn.Priority FROM Phone AS phn JOIN User AS usr ON phn.User_FK = usr.PK WHERE usr.Login = '@PARAM' ORDER BY phn.Priority";
	private static $qryUserEmail  = "SELECT eml.Address, eml.Priority FROM Email AS eml JOIN User AS usr ON eml.User_FK = usr.PK WHERE usr.Login = '@PARAM' ORDER BY eml.Priority";
	private static $qryUserExists = "SELECT EXISTS(SELECT 1 FROM User WHERE Login = '@PARAM' LIMIT 1)";
	
	private $login;
	private $firstName;
	private $lastName;
	private $title;
	private $workStatus;
	private $vacationDays;

	private $phone;
	private $email;
	
	private $fired;
	private $hash;
	private $salt;
	private $authToken;
	private $lastCommunication;

/**/
	public function User($login,$password,$firstName,$lastName,$title,$workStatus,$vacationDays, $phone, $email) {
		if(User::userExists($login))
			throw new Exception("User '$login' already exists");

		$this->login             = $login;
		$this->firstName         = $firstName;
		$this->lastName          = $lastName;
		$this->title             = $title;
		$this->workStatus        = $workStatus;
		$this->vacationDays      = $vacationDays;
		$this->phone             = array($phone);
		$this->email             = array($email);
		$this->fired             = false;
		$this->salt              = mcrypt_create_iv(255, MCRYPT_DEV_URANDOM);
        $this->hash              = self::hashPassword($password,$salt);
        $this->authToken         = null;
        $this->lastCommunication = date();
        $this->commitUserData();
	}
/**/
	public function User($login) {
        if(!self::userExists($login))
        	throw new Exception("User '$login' Does Not Exist");
        
        $this->login = $login;
        $this->refreshUserData();
        /*
			$arr = $results->fetch_array ();
					$userData = $arr [0];
			$this->title             = (int)   $userData[ 1];
			$this->workStatus              = (bool)  $userData[ 2];
			$this->vacationDays      = (int)   $userData[ 3];
			$this->fired             = (bool)  $userData[ 4];
			$this->firstName         = (string)$userData[ 5];
			$this->lastName          = (string)$userData[ 6];
			$this->login             = (string)$userData[ 7];
			$this->hash              = (string)$userData[ 8];
			$this->salt              = (string)$userData[ 9];
			$this->auth              = (string)$userData[10];
			$this->lastCommunication = (string)$userData[11];
			*/
	}


	public function refreshUserData() {
		$conn     = DB::getNewConnection();
		$result   = DB::query($conn, str_replace ( "@PARAM", $this->login, self::$qryUserData));
		$userData = $result[0];

		$this->title             = (int)   $userData[ 1];
		$this->workStatus              = (bool)  $userData[ 2];
		$this->vacationDays      = (int)   $userData[ 3];
		$this->fired             = (bool)  $userData[ 4];
		$this->firstName         = (string)$userData[ 5];
		$this->lastName          = (string)$userData[ 6];
      //$this->login             = (string)$userData[ 7];
		$this->hash              = (string)$userData[ 8];
		$this->salt              = (string)$userData[ 9];
		$this->auth              = (string)$userData[10];
		$this->lastCommunication = (string)$userData[11];
		
		$phoneData = DB::query($conn, str_replace ( "@PARAM", $this->login, self::$qryUserPhone));
        $this->phone = array();
        for($i=0; $i<count($phoneData); $i++)
          $this->phone[$i] = $phoneData[$i][0];	

        $emailData = DB::query($conn, str_replace ( "@PARAM", $this->login, self::$qryUserEmail));
        $this->email = array();
        for($i=0; $i<count($emailData); $i++)
          $this->email[$i] = $emailData[$i][0];	
        $conn->close();
	}	
	
	public function correctPassword($password) {
	  return $this->hash === self::hashPassword($password,$this->salt);
	}
	
	private static function hashPassword($password,$salt) {
		return crypt($password,$salt);
	}
	
	public  static function userExists($login) {
		$conn = DB::getNewConnection ();
		$results = DB::query($conn, str_replace ( "@PARAM", $login, self::$qryUserExists) );
		$conn->close();
		return ($results[0][0]==='1') ? true : false;
	}
}
?>