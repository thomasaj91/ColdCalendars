<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);

include_once (__DIR__ . '/../DB.php');
class User {
	private static $AUTHENTICATION_TIMEOUT = 3600;
	
	private static $qryUserList    = "SELECT Login FROM User WHERE LegacyUser = false ORDER BY Last, First, Login";
	private static $qryUserExists  = "SELECT EXISTS(SELECT 1 FROM User WHERE Login = '@PARAM' LIMIT 1)";
	private static $qryUserData    = "SELECT usr.Login, usr.First, usr.Last, typ.Title, usr.PTFT, usr.Vacation, usr.LegacyUser, usr.Salt, usr.Hash, usr.Auth, usr.Time FROM User AS usr JOIN UserType AS typ	ON usr.Title = typ.PK WHERE usr.Login = '@PARAM' LIMIT 1";
	private static $qryUserPhone   = "SELECT phn.Number,  phn.Priority FROM Phone AS phn JOIN User AS usr ON phn.User_FK = usr.PK WHERE usr.Login = '@PARAM' ORDER BY phn.Priority";
	private static $qryUserEmail   = "SELECT eml.Address, eml.Priority FROM Email AS eml JOIN User AS usr ON eml.User_FK = usr.PK WHERE usr.Login = '@PARAM' ORDER BY eml.Priority";
	private static $qryInsertUser  = "INSERT INTO User VALUES (Null,'@PARAM','@PARAM','@PARAM',(SELECT PK FROM UserType WHERE Title LIKE '@PARAM'),@PARAM,@PARAM,@PARAM,'@PARAM','@PARAM','@PARAM',NOW())";
	private static $qryUpdateUser  = "UPDATE User SET First='@PARAM', Last='@PARAM', Title = (SELECT PK FROM UserType WHERE Title LIKE '@PARAM'), PTFT=@PARAM, Vacation=@PARAM, LegacyUser=@PARAM, Salt='@PARAM', Hash='@PARAM', Auth='@PARAM', Time='@PARAM' WHERE Login = '@PARAM'";
	private static $qryInsertPhonePrefix = "INSERT INTO Phone VALUES ";
	private static $qryInsertPhoneSuffix = "((SELECT PK FROM User WHERE Login LIKE '@PARAM'),'@PARAM',@PARAM)";
	private static $qryInsertEmailPrefix = "INSERT INTO Email VALUES ";
	private static $qryInsertEmailSuffix = "((SELECT PK FROM User WHERE Login LIKE '@PARAM'),'@PARAM',@PARAM)";
	private static $qryDeletePhone = "DELETE FROM Phone WHERE (SELECT PK FROM User WHERE Login = '@PARAM')";
	private static $qryDeleteEmail = "DELETE FROM Email WHERE (SELECT PK FROM User WHERE Login = '@PARAM')";
	
	private $login;
	private $firstName;
	private $lastName;
	private $title;
	private $workStatus;
	private $vacationDays;
	private $fired;
	private $hash;
	private $salt;
	private $authToken;
	private $lastCommunication;
	private $phone;
	private $email;
	
	
/**/
	public function User($login,$password=null,$firstName=null,$lastName=null,$title=null,$workStatus=null,$vacationDays=null, $phone=null, $email=null) {
		if(User::userExists($login)) {
            $this->login = $login;
			$this->refreshUserData();
		    return;
		}
		
		$this->login             = $login;
		$this->firstName         = $firstName;
		$this->lastName          = $lastName;
		$this->title             = $title;
		$this->workStatus        = $workStatus;
		$this->vacationDays      = $vacationDays;
		$this->fired             = false;
        $this->authToken         = null;
        $this->lastCommunication = self::getSystemTime();
		$this->phone             = array($phone);
		$this->email             = array($email);
        $this->setPassword($password);
        $this->insertUserData();
	}

	public static function create($login,$password,$firstName,$lastName,$title,$workStatus,$vacationDays, $phone, $email) {
		if(User::userExists($login))
			throw new Exception("User '$login' already exists");
  		return new User($login,$password,$firstName,$lastName,$title,$workStatus,$vacationDays, $phone, $email);		
	}
	
	public static function load($login) {
        if(!self::userExists($login))
        	throw new Exception("User '$login' Does Not Exist");        
        return new User($login);
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
	
	public function refreshUserData() {
		$conn     = DB::getNewConnection();
		$result   = DB::query($conn, str_replace ( "@PARAM", $this->login, self::$qryUserData));
		$userData = $result[0];

		$this->login             = (string)$userData[ 0];
		$this->firstName         = (string)$userData[ 1];
		$this->lastName          = (string)$userData[ 2];
		$this->title             = (string)$userData[ 3];
		$this->workStatus        = (bool)  $userData[ 4];
		$this->vacationDays      = (int)   $userData[ 5];
		$this->fired             = (bool)  $userData[ 6];
		$this->salt              = (string)$userData[ 7];
		$this->hash              = (string)$userData[ 8];
		$this->authToken         = (string)$userData[ 9];
		$this->lastCommunication = (string)$userData[10];
		
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

	public function commitUserData() {
		$conn    = DB::getNewConnection();
	    $payload = $this->getUpdateUserSql();
		$result  = DB::execute($conn, $payload);
		$conn->close();
	}

	public function commitPhoneData() {
		$conn    = DB::getNewConnection();
		$payload = str_replace("@PARAM", $this->login, self::$qryDeletePhone).' ; '
		          .$this->getInsertPhoneSql().' ; ';
		$result  = DB::execute($conn, $payload);
		$conn->close();
	}
	
	public function commitEmailData() {
		$conn    = DB::getNewConnection();
		$payload = str_replace("@PARAM", $this->login, self::$qryDeleteEmail).' ; '
				.$this->getInsertEmailSql().' ; ';
		$result  = DB::execute($conn, $payload);
		$conn->close();
	}
	
	public function setPassword($password) {
      $this->salt = mcrypt_create_iv(255, MCRYPT_DEV_URANDOM);
	  $this->hash = self::hashPassword($password,$this->salt);
	}
	
	public function correctPassword($password) {
		return $this->hash === self::hashPassword($password,$this->salt);
	}
	
	public function isAuthenticated($challengeToken) {
		$then    = new DateTime($this->lastCommunication);
		$now     = new DateTime(date("Y-m-d H:i:s"));
		$seconds = self::getSeconds($now->diff($then,true));
		return $this->authToken === $challengeToken
		    && $seconds <= self::$AUTHENTICATION_TIMEOUT;
	}
	
	public function generateAuthenticationToken() {
		$this->authToken = mcrypt_create_iv(1024, MCRYPT_DEV_URANDOM);
		//$this->commitUserData();
	}
	
	public function getInfo() {
		$out = array();
    	$out['firstName']  = $this->firstName;
    	$out['lastName']   = $this->lastName;
    	$out['title']      = $this->title;
    	$out['workStatus'] = $this->workStatus;
    	return $out;
	}
	
	public function isAdmin() {
		return strcasecmp($this->title,'Admin')===0;
	}
	
	public function isManager() {
		return strcasecmp($this->title,'Manager')===0;
	}
	
	public function isTerminated() {
		return $this->fired;
	}
	
	public function terminateUser() {
		$this->fired = true;
	}
	
	public function unTerminateUser() {
		$this->fired = false;
	}
	
	public function getPhoneNumbers() {
		return $this->phone;
	}
	
	public function addPhoneNumber($number) {
		array_push($this->phone,$number);
	}
	
	public function removePhoneNumber($number) {
		if(count($this->phone) < 2 || !in_array($number,$this->phone))
			return;
		unset($this->phone[array_search($number,$this->phone)]);
		$this->phone = array_values($this->phone);
	}
	
	public function getEmailAddresses() {
		return $this->email;
	}
	
	public function addEmailAddress($address) {
		array_push($this->email,$address);
	}
	
	public function removeEmailAddress($address) {
		if(count($this->email) < 2 || !in_array($address,$this->email))
			return;
		unset($this->email[array_search($address,$this->email)]);
		$this->email = array_values($this->email);
	}
	
	public function getAuthToken() {
		return $this->authToken;	
	}
	
	public function updateCommunication() {
		$this->lastCommunication = self::getSystemTime();
	}
	
	private static function getSystemTime() {
		return date('Y-m-d H:i:s',time());
	}
	
	private function insertUserData() {
		$conn    = DB::getNewConnection();
	    $payload = $this->getInsertUserSql()  .' ; '
	    		 . $this->getInsertPhoneSql() .' ; '
	    		 . $this->getInsertEmailSql() .' ; ';
		$result  = DB::execute($conn, $payload);
		$conn->close();
	}
	
	private function getInsertUserSql() {
		$params   = array($this->login
				         ,$this->firstName
				         ,$this->lastName
				         ,$this->title
				         ,$this->workStatus ? 'True' : 'False'
				         ,$this->vacationDays
				         ,$this->fired      ? 'True' : 'False'
				         ,DB::escapeString($this->salt)
				         ,$this->hash
			 	         ,DB::escapeString($this->authToken)
				         ,$this->lastCommunication
		                 );
		$sql = self::$qryInsertUser;
		foreach($params as $param)
			$sql = self::str_replace_once('@PARAM', $param, $sql);
		return $sql;
	}	
	
	private function getInsertPhoneSql() {
		$sql   = self::$qryInsertPhonePrefix;
		$count = count($this->phone);
        for($i=0; $i<$count; $i++) {
        	$params = array($this->login,$this->phone[$i],$i+1);
            $next   = self::$qryInsertPhoneSuffix;
            foreach($params as $param)
        	    $next = self::str_replace_once('@PARAM', $param, $next);
        	$sql .= ($i===0?'':', ') . $next;
        }
		return $sql;
	}

	private function getInsertEmailSql() {
		$sql   = self::$qryInsertEmailPrefix;
		$count = count($this->email);
        for($i=0; $i<$count; $i++) {
        	$params = array($this->login,$this->email[$i],$i+1);
            $next   = self::$qryInsertEmailSuffix;
            foreach($params as $param)
        	    $next = self::str_replace_once('@PARAM', $param, $next);
        	$sql .= ($i===0?'':', ') . $next;
        }
		return $sql;
	}
	
	private function getUpdateUserSql() {
		$params   = array($this->firstName
				,$this->lastName
				,$this->title
				,$this->workStatus ? 'True' : 'False'
				,$this->vacationDays
				,$this->fired      ? 'True' : 'False'
				,DB::escapeString($this->salt)
				,$this->hash
				,DB::escapeString($this->authToken)
				,$this->lastCommunication
				,$this->login
		);
		$sql = self::$qryUpdateUser;
		foreach($params as $param)
			$sql = self::str_replace_once('@PARAM', $param, $sql);
		//echo $sql;
		return $sql;
	}
	
	
	
		
	private static function str_replace_once($needle,$replace,$haystack) {
	  $pos = strpos($haystack,$needle);
	  if ($pos === false)
	  	return $haystack;
	  return  substr_replace($haystack,$replace,$pos,strlen($needle));
    }
    
    private static function getSeconds($interval) {
    	return  $interval->y * 365 * 24 * 60 * 60 
             +  $interval->m *  28 * 24 * 60
             +  $interval->d *  24 * 60 * 60
             +  $interval->h *  60 * 60
             +  $interval->i *  60
             +  $interval->s;
    }
    
    public static function getAuthenticationTimeOut() {
    	return self::$AUTHENTICATION_TIMEOUT;
    }
    
    public static function getAllLogins() {
    	$conn    = DB::getNewConnection();
    	$results = DB::query($conn,self::$qryUserList);
    	$rows    = count($results);
    	$out     = array();
    	for($i=0; $i<$rows; $i++)
    		$out[$i] = $results[$i][0];
    	return $out;
    }
}
?>