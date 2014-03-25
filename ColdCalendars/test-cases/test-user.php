<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 3);
//  include_once(__DIR__.'/../initiate/buildDatabase.php');
  require_once(__DIR__.'/../user/User.php');
  require_once(__DIR__.'/../DB.php');
  
  testGeneralUserFunctionality();
  
  function testGeneralUserFunctionality() {
    DB::buildDatabase();
    if(!testUserExists()){
    	echo "UserExists Failed!\n";
    }
  }

  function testUserExists() {
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