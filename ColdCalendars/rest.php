<?php

error_reporting(E_ALL);
ini_set('display_errors', 3);
include_once(__DIR__.'/user/User.php');
include_once(__DIR__.'/auth/validation.php');

  $MAX_STR_LEN = 255;
  $MIN_PASSWORD_LENGTH = 8;
  $MIN_PHONE_LEN = 7;
  $MAX_PHONE_LEN = 16;
  $AUTH_STR_LEN = 1024;
  
  $adminOnlyRequests   = array('CreateUser','DeleteUser','PasswordReset','ChangeTitle','ChangeWorkStatus','ChangeVacationDays');
  $managerOnlyRequests = array('AddToSchedule','RemoveFromSchedule');
  
  if(!isset($_GET) 
  || !isset($_GET['json'])
  || !isset($_COOKIE) 
  || !isset($_COOKIE['login'])
  || !isset($_COOKIE['authToken']))
    die('Improperly formated request 1');

  $requestData = json_decode($_GET['json']);
  if(!isset($requestData->requestType))
    die('Improperly Formated Request 2');

  if(!isValidUserlogin($_COOKIE['login']))
  	die('Improperly Formatted User Login 3');

  if(!isValidAuthenticationToken($_COOKIE['authToken']))
  	die('Improperly Formatted authentication Token 4');
  
  try {
  	$user = User::load($_COOKIE['login']);
  }
  catch(Exception $e) {
  	die('User::load error\n'.$e->getMessage());
  }
  if(!$user->isAuthenticated($_COOKIE['authToken']))
  	die('Invlaid user authentication 5');

  if(  (!$user->isAdmin()   && in_array($requestData->requestType,$adminOnlyRequests))
    || (!$user->isManager() && in_array($requestData->requestType,$managerOnlyRequests)))
  	die(json_encode('Unauthorized Request'));
  
  
  switch($requestData->requestType) {
  	case 'CreateUser':         createUser($requestData); break;
  	case 'DeleteUser':         deleteUser($requestData); break;
  	case 'PasswordReset':      passwordReset($requestData); break;
  	case 'ChangeTitle':        changeTitle($requestData); break;
  	case 'ChangeWorkStatus':   changeWorkStatus($requestData); break;
  	case 'ChangeVacationDays': changeVacationDays($requestData); break;
  	case 'UserInfo':  getUserInfo($requestData); break;
  	case 'UserPhone': getPhoneNumbers($requestData); break;
  	case 'AddPhone': addPhoneNumber($requestData); break;
  	case 'PhonePriority': phonePriority($requestData); break;
  	case 'RemovePhone': removePhoneNumber($requestData); break;
  	case 'UserEmail': getEmails($requestData); break;
  	case 'AddEmail': addEmail($requestData); break;
  	case 'EmailPriority': emailPriority($requestData); break;
  	case 'RemoveEmail': removeEmail($requestData); break;
  	case 'UserList': userList($requestData); break;
  	default: die('Invalid Request Specification');
  }
  
  
  function createUser($dataBlob) {
  	$validation = array();
  	$validation['userID']       = (int)isValidUserLogin($dataBlob->userID);
  	$validation['password']     = (int)isValidPassword($dataBlob->password);
  	$validation['firstName']    = (int)isValidName($dataBlob->firstName);
  	$validation['lastName']     = (int)isValidName($dataBlob->lastName);
  	$validation['workStatus']   = (int)isValidBool($dataBlob->workStatus);
  	$validation['title']        = (int)isValidTitle($dataBlob->title);
  	$validation['vacationDays'] = (int)isValidRange($dataBlob->vacationDays);
  	$validation['phone']        = (int)isValidPhone($dataBlob->phone);
  	$validation['email']        = (int)isValidEmail($dataBlob->email);

  	
  	//$goodData=true;
  	//foreach($validation as $valid)
  	// 	$goodData &= $valid;
  	
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	//do User::load work
	try {
		User::create($dataBlob->userID,
					$dataBlob->password,
					$dataBlob->firstName,
					$dataBlob->lastName,
					$dataBlob->title,
					$dataBlob->workStatus,
					$dataBlob->vacationDays,
					$dataBlob->phone,
					$dataBlob->email);
	}
	catch (Exception $e) {
		die(json_encode(null));
	}
	echo json_encode($validation);
  }  
  
  function deleteUser($dataBlob) {
  	$validation = array();
  	$validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
    if(in_array(false,$validation))
  		die(json_encode($validation));
    $user;
    
    if($dataBlob->userID===$_COOKIE['login'])
    	die(json_encode('You can\'t delete yourself. Bad Admin.'));
    
    try {
    	$user = User::load($dataBlob->userID);
    }
    catch (Exception $e) {
    	die(json_encode(null));
    }
    $user->terminateUser();
    $user->commitUserData();
    echo json_encode($validation);
    //
  }

  function passwordReset($dataBlob) {
  	$validation = array();
  	$validation['userID']   = isValidUserLogin($dataBlob->userID);
  	$validation['password'] = isValidPassword($dataBlob->password);
  	if(!$validation[0] || !$validation[1]) {
  		die(json_encode($validation));
  	}
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->setPassword($dataBlob->password);
  	$user->commitUserData();
  	echo json_encode($validation);
  }
  
  function changeTitle($dataBlob) {
  	$validation = array();
  	$validation['userID'] = isValidUserLogin($dataBlob->userID);
  	$validation['title']  = isValidTitle($dataBlob->title);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeTitle($dataBlob->title);
  	echo json_encode($validation);
  }
  
  function changeWorkStatus($dataBlob) {
  	$validation = array();
  	$validation['userID']     = isValidUserLogin($dataBlob->userID);
  	$validation['workStatus'] = isValidBool($dataBlob->workStatus);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeWorkStatus($dataBlob->workStatus);
  	echo json_encode($validation);
  }
  
  function changeVacationDays($dataBlob) {
  	$validation = array();
  	$validation['userID']       = isValidUserLogin($dataBlob->userID);
  	$validation['vacationDays'] = isValidBool($dataBlob->vacationDays);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeVacationDays($dataBlob->vacationDays);
  	echo json_encode($validation);
  }
  
  function getUserInfo($dataBlob) {
  	if(!isValidUserLogin($dataBlob->userID))
  		die(json_encode(null));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	echo json_encode($user->getInfo());
  }
  
  function getPhoneNumbers($dataBlob) {
  	if(!isValidUserLogin($dataBlob->userID))
  		die(json_encode(null));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	echo json_encode($user->getPhoneNumbers());
  }
  
  function addPhoneNumber($dataBlob) {
  	$validation = array();
  	$validation['phone'] = isValidPhone($dataBlob->phone);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($_COOKIE['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->addPhoneNumber($dataBlob->phone);
  	$user->commitPhoneData();
  	echo json_encode($validation);
  }
  
  function removePhoneNumber($dataBlob) {
  	$validation = array();
  	$validation['phone'] = isValidPhone($dataBlob->phone);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($_COOKIE['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->removePhoneNumber($dataBlob->phone);
  	$user->commitPhoneData();
  	echo json_encode($validation);
  }
  
  function phonePriority($dataBlob) {
  	$validation = array();
  	$validation['phone']    = isValidPhone($dataBlob->phone);
  	$validation['priority'] = isValidPriority($dataBlob->priority);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($_COOKIE['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changePhoneNumberPriority($dataBlob->phone,$dataBlob->priority);
  	echo json_encode($validation);
  }
  
  function getEmails($dataBlob) {
  	if(!isValidUserLogin($dataBlob->userID))
  		die(json_encode(null));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	echo json_encode($user->getEmailAddresses());
  }
  
  function addEmail($dataBlob) {
  	$validation = array();
  	$validation['email'] = isValidEmail($dataBlob->email);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($_COOKIE['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->addEmail($dataBlob->email);
  	$user->commitEmailData();
  	echo json_encode($validation);
  }

  function removeEmail($dataBlob) {
  	$validation = array();
  	$validation['email'] = isValidEmail($dataBlob->email);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($_COOKIE['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->removeEmail($dataBlob->email);
  	$user->commitEmailData();
  	echo json_encode($validation);
  }
  
  function emailPriority($dataBlob) {
  	$validation = array();
  	$validation['email']    = isValidEmail($dataBlob->email);
  	$validation['priority'] = isValidPriority($dataBlob->priority);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($_COOKIE['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeEmailPriority($dataBlob->email,$dataBlob->priority);
  	echo json_encode($validation);
  }
  
  function userList($dataBlob) {
  	$list;
  	try {
		$list = User::getAllLogins();
  	}
  	catch (Exception $e) {
  		die(json_encode(null));
  	}
	echo json_encode($list);
  }
  
?>