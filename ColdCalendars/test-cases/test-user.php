<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', 3 );
require_once (__DIR__ . '/../user/User.php');
require_once (__DIR__ . '/../DB.php');

testGeneralUserFunctionality ();
testAdminUserFunctionality ();
function testGeneralUserFunctionality() {
	echo "UserExists:" . printResult ( testUserExists () );
}
function testAdminUserFunctionality() {
	echo 'UserCreate:      ' . printResult ( testUserCreate ()       );
	echo 'UserRemove:      ' . printResult ( testUserRemove ()       );
	echo 'PasswordReset:   ' . printResult ( testPasswordReset ()    );
	echo 'ChangeTitle:     ' . printResult ( testChangeTitle ()      );
	echo 'ChangeWorkStatus:' . printResult ( testChangeWorkStatus () );
}
function printResult($boolean) {
	return " \t" . ($boolean ? 'Passed' : 'Failed') . "\n";
}
function testUserExists() {
	DB::buildDatabase ();
	$success = true;
	$success &= checkUserExists ( 'AustinT', false );
	$success &= checkUserExists ( 'CalebW',  false );
	$success &= checkUserExists ( 'AlexW',   false );
	$success &= checkUserExists ( 'JonZ',    false );
	$success &= checkUserExists ( 'root',    true  );
	return $success;
}
function checkUserExists($login, $expected) {
	$success;
	if (! ($success = (User::userExists ( $login ) === $expected))) {
		echo 'Failed: ';
		var_dump ( $login );
		echo 'Expected: ';
		var_dump ( $expected );
		echo 'Actual: ';
		var_dump ( User::userExists ( $login ) );
	}
	return $success;
}
function testUserCreate() {
	DB::buildDatabase ();
	$success  = true;
	$success &= checkUserCreate ( 'AustinT', User::userExists ( 'AustinT' ) );
	$success &= checkUserCreate ( 'CalebW',  User::userExists ( 'CalebW' ) );
	$success &= checkUserCreate ( 'AlexW',   User::userExists ( 'AlexW' ) );
	$success &= checkUserCreate ( 'JonZ',    User::userExists ( 'JonZ' ) );
	$success &= checkUserCreate ( 'root',    User::userExists ( 'root' ) );
	return $success;
}
function checkUserCreate($login, $expected) {
	$failed = false;
	try {
		User::create ( $login, 'supersecret', $login . '_Fname', $login . '_Lname', 'Employee', true, 10, '8675309', $login . '@uwm.edu' );
	} catch ( Exception $e ) {
		$failed = true;
	}
	$success = $expected === $failed;
	if (! $success) {
		echo 'Failed: ';
		var_dump ( $login );
		echo 'Expected: ';
		var_dump ( $expected );
		echo 'Actual: ';
		var_dump ( $failed );
	}
	return $success;
}
function testUserRemove() {
	DB::buildDatabase ();
	$success  = true;
	$success &= checkUserRemove ( 'root' );
	return $success;
}
function checkUserRemove($login) {
	$success = true;
	try {
		/* make sure termination sticks in the DB */
		$user     = User::load ( $login );
		$user->terminateUser ();
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= $user->isTerminated ();
		
		/* no toggling functionality */
		$user->terminateUser ();
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= $user->isTerminated ();
		
		/* make sure untermination sticks in the DB */
		$user     = User::load ( $login );
		$user->unTerminateUser ();
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isTerminated ();
		
		/* no toggling functionality */
		$user->unTerminateUser ();
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isTerminated ();
	} catch ( Exception $e ) {
		$success = false;
	}
	
	if (! $success) {
		echo 'Failed: ';
		var_dump ( $login );
		echo 'Expected: ';
		var_dump ( $expected );
		echo 'Actual: ';
		var_dump ( $failed );
	}
	return $success;
}
function testPasswordReset() {
	DB::buildDatabase ();
	$success = true;
	$success &= checkPasswordReset ( 'root' );
	return $success;
}
function checkPasswordReset($login) {
	$success = true;
	try {
		$password = 'We_are_winners!';
		$user     = User::load ( $login );
		$success &= ! $user->correctPassword ( $password );
		$user->setPassword ( $password );
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= $user->correctPassword ( $password );
	} catch ( Exception $e ) {
		$success = false;
	}
	if (! $success) {
		echo 'Failed: ';
		var_dump ( $login );
	}
	return $success;
}
function testChangeTitle() {
	DB::buildDatabase ();
	$success = true;
	try {
		/* Assert that you can NOT change an Admin to an Employee or Manager */
		$login    = 'root';
		$user     = User::load ( $login );
		$success &= $user->isAdmin ();
		$user->setTitle ( 'Manager' );
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isManager () && $user->isAdmin ();
		
		$user     = User::load ( $login );
		$success &= $user->isAdmin ();
		$user->setTitle ( 'Employee' );
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isEmployee () && $user->isAdmin ();
		
		/* Assert that you can change to an Employee to a Manager */
		$login    = 'JonZ';
		User::create ( $login, 'supersecret', $login . '_Fname', $login . '_Lname', 'Employee', true, 10, '8675309', $login . '@uwm.edu' );
		$user     = User::load ( $login );
		$success &= $user->isEmployee ();
		$user->setTitle ( 'Manager' );
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isEmployee () && $user->isManager ();
		
		/* Assert that you can change to a Manager to an Employee */
		$user     = User::load ( $login );
		$success &= $user->isManager ();
		$user->setTitle ( 'Employee' );
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isManager () && $user->isEmployee ();
		
		/* Assert that you can NOT change an Employee or Manager to an Admin */
		$user     = User::load ( $login );
		$success &= $user->isEmployee ();
		$user->setTitle ( 'Admin' );
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isAdmin () && $user->isEmployee ();
		
		$user->setTitle ( 'Manager' );
		$user->commitUserData ();
		
		$user     = User::load ( $login );
		$success &= $user->isManager ();
		$user->setTitle ( 'Admin' );
		$user->commitUserData ();
		$user     = User::load ( $login );
		$success &= ! $user->isAdmin () && $user->isManager ();
	} catch ( Exception $e ) {
		$success = false;
	}
	if (! $success) {
		echo 'Failed: ';
		var_dump ( $login );
	}
	return $success;
}

function testChangeWorkStatus() {
	DB::buildDatabase ();
	$success = true;
	$login   = 'JonZ';
	try {
		User::create ( $login, 'supersecret', $login . '_Fname', $login . '_Lname', 'Employee', true, 10, '8675309', $login . '@uwm.edu' );

		$user     = User::load($login);
		$success &= $user->isFullTime();
		$user->setPartTime();
		$user->commitUserData();
		$user     = User::load($login);
		$success &= $user->isPartTime();
		
		$user     = User::load($login);
		$success &= $user->isPartTime();
		$user->setFullTime();
		$user->commitUserData();
		$user     = User::load($login);
		$success &= $user->isFullTime();
		
	} catch ( Exception $e ) {
		$success = false;
	}
	if (! $success) {
		echo 'Failed: ';
		var_dump ( $login );
	}
	return $success;
}

// var_dump(User::userExists('AustinT'));
// var_dump(User::userExists('AustinT2'));

/* Can I load the user data? */
// $alex = User::load('AlexW');
// var_dump($alex);
// $alex->generateAuthenticationToken();
// var_dump($alex);
$root = User::load ( 'root' );
var_dump ( $root );
User::create ( 'JonZ', 'supersecret', 'Jon', 'Zanura', 'Employee', true, 10, '8675309', ' jczamora@uwm.edu' );
$jon = User::load ( 'JonZ' );
var_dump ( $jon );
// echo $testUser->correctPassword('');
/**
 * $jon->addPhoneNumber('5555555555');
 * $jon->addPhoneNumber('1234567890');
 * var_dump($jon);
 * $jon->removePhoneNumber('5555555555');
 * var_dump($jon);
 * $jon->commitPhoneData();
 * $jon = User::load('JonZ');
 * var_dump($jon);
 */
$jon->addEmailAddress ( 'JonZ@gmail.com' );
$jon->addEmailAddress ( 'JonZ@yahoo.com' );
var_dump ( $jon );
$jon->removeEmailAddress ( 'JonZ@yahoo.com' );
var_dump ( $jon );
$jon->commitEmailData ();
$jon = User::load ( 'JonZ' );
var_dump ( $jon );

?>