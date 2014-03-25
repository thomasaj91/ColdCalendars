<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 3);
  require_once(__DIR__.'/../user/User.php');
  require_once(__DIR__.'/../DB.php');
  
  testGeneralUserFunctionality();
  testadminUserFunctionality();
  
  function testGeneralUserFunctionality() {
    echo "UserExists:" . (testUserExists() ? 'Passed' : 'Failed') . "\n";
  }
  function testAdminUserFunctionality() {
  	echo "UserCreate:" . (testUserCreate() ? 'Passed' : 'Failed') . "\n";
  	echo "UserRemove:" . (testUserRemove() ? 'Passed' : 'Failed') . "\n";
  }
  
  function testUserExists() {
    DB::buildDatabase();
  	$success  = true;
  	$success &= checkUserExists('AustinT', false);
  	$success &= checkUserExists('CalebW' , false);
  	$success &= checkUserExists('AlexW'  , false);
  	$success &= checkUserExists('JonZ'   , false);
  	$success &= checkUserExists('root'   , true );
  	return $success;
  }

  function checkUserExists($login,$expected) {
    $success;
  	if(!($success = (User::userExists($login) === $expected))) {
  		echo 'Failed: ';
  		var_dump($login);
  		echo 'Expected: ';
  		var_dump($expected);
  		echo 'Actual: ';
  		var_dump(User::userExists($login));
  	}
  	return $success;
  }

  function testUserCreate() {
  	DB::buildDatabase();
  	$success  = true;
  	$success &= checkUserCreate('AustinT', User::userExists('AustinT'));
  	$success &= checkUserCreate('CalebW' , User::userExists('CalebW' ));
  	$success &= checkUserCreate('AlexW'  , User::userExists('AlexW'  ));
  	$success &= checkUserCreate('JonZ'   , User::userExists('JonZ'   ));
  	$success &= checkUserCreate('root'   , User::userExists('root'   ));
  	return $success;
  }
  
  function checkUserCreate($login,$expected) {
  	$failed=false;
  	try {
  	  User::create($login,'supersecret', $login.'_Fname', $login.'_Lname', 'Employee', true, 10, '8675309', $login.'@uwm.edu');
  	}
  	catch(Exception $e) {
      $failed = true;
  	}
  	$success = $expected === $failed;
  	if(!$success) {
  		echo 'Failed: ';
  		var_dump($login);
  		echo 'Expected: ';
  		var_dump($expected);
  		echo 'Actual: ';
  		var_dump($failed);
  	}
  	return $success;
  }

  function testUserRemove() {
  	DB::buildDatabase();
  	$success  = true;
  	$success &= checkUserRemove('root');
  	return $success;
  }
  
  function checkUserRemove($login) {
  	$success = true;
  	try {
  		/* make sure termination sticks in the DB */
  		$user = User::load($login);
  		$user->terminateUser();
  		$user->commitUserData();
  		$user = User::load($login);
        $success &= $user->isTerminated();

        /* no toggling functionality */
        $user->terminateUser();
        $user->commitUserData();
        $user = User::load($login);
        $success &= $user->isTerminated();
        
  		/* make sure untermination sticks in the DB */
  		$user = User::load($login);
  		$user->unTerminateUser();
  		$user->commitUserData();
  		$user = User::load($login);
        $success &= !$user->isTerminated();

        /* no toggling functionality */
        $user->unTerminateUser();
        $user->commitUserData();
        $user = User::load($login);
        $success &= !$user->isTerminated();
  	}
  	catch(Exception $e) {
  		$success = false;
  	}

  	if(!$success) {
  		echo 'Failed: ';
  		var_dump($login);
  		echo 'Expected: ';
  		var_dump($expected);
  		echo 'Actual: ';
  		var_dump($failed);
  	}
  	return $success;
  }
  
  
  //var_dump(User::userExists('AustinT'));
  //var_dump(User::userExists('AustinT2'));
  
  /* Can I load the user data? */
  //$alex = User::load('AlexW');
  //var_dump($alex);
  //$alex->generateAuthenticationToken();
  //var_dump($alex);
  $root = User::load('root');
  var_dump($root);
  User::create('JonZ', 'supersecret', 'Jon', 'Zanura', 'Employee', true, 10, '8675309', ' jczamora@uwm.edu');
  $jon = User::load('JonZ');
  var_dump($jon);
  //  echo $testUser->correctPassword('');
  /**
  $jon->addPhoneNumber('5555555555');
  $jon->addPhoneNumber('1234567890');
  var_dump($jon);
  $jon->removePhoneNumber('5555555555');
  var_dump($jon);
  $jon->commitPhoneData();
  $jon = User::load('JonZ');
  var_dump($jon);
  **/
  $jon->addEmailAddress('JonZ@gmail.com');
  $jon->addEmailAddress('JonZ@yahoo.com');
  var_dump($jon);
  $jon->removeEmailAddress('JonZ@yahoo.com');
  var_dump($jon);
  $jon->commitEmailData();
  $jon = User::load('JonZ');
  var_dump($jon);
  
?>