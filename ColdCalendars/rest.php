<?php

error_reporting(E_ALL);
ini_set('display_errors', 3);
include_once(__DIR__.'/user/User.php');

  $MAX_STR_LEN = 255;
  $MIN_PASSWORD_LENGTH = 8;
  $MIN_PHONE_LEN = 7;
  $MAX_PHONE_LEN = 16;
  $AUTH_STR_LEN = 1024;
  
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

  switch($requestData->requestType) {
  	case 'CreateUser':         if(!$user->isAdmin()) die('Unauthorized Request'); else createUser($requestData); break;
  	case 'DeleteUser':         if(!$user->isAdmin()) die('Unauthorized Request'); else deleteUser($requestData); break;
  	case 'PasswordReset':      if(!$user->isAdmin()) die('Unauthorized Request'); else passwordReset($requestData); break;
  	case 'ChangeTitle':        if(!$user->isAdmin()) die('Unauthorized Request'); else changeTitle($requestData); break;
  	case 'ChangeWorkStatus':   if(!$user->isAdmin()) die('Unauthorized Request'); else changeWorkStatus($requestData); break;
  	case 'ChangeVacationDays': if(!$user->isAdmin()) die('Unauthorized Request'); else changeVacationDays($requestData); break;
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
  
  function isValidUserLogin($str) {
  	global $MAX_STR_LEN;
    return strlen($str) <= $MAX_STR_LEN
        && preg_match('/^[a-zA-Z0-9]+$/',$str);
  }

  function isValidPassword($str) {
  	global $MIN_PASSWORD_LENGTH;
  	return strlen($str) >= $MIN_PASSWORD_LENGTH
  	    && preg_match('/^\S+$/',$str);
  }
  
  function isValidAuthenticationToken($str) {
  	global $AUTH_STR_LEN;
  	return strlen($str) === $AUTH_STR_LEN;
  }
  
  function isValidName($str) {
  	global $MAX_STR_LEN;
  	return strlen($str) <= $MAX_STR_LEN
  		&& preg_match('/^[a-zA-z]+$/',$str);
  }
  
  function isValidBool($str) {
  	return $str == 1 || $str == 0;
  }
  
  function isValidTitle($str) {
  	return strcasecmp($str,'Admin')
  	    || strcasecmp($str,'Manager')
  	    || strcasecmp($str,'Employee');
  }
  
  function isValidRange($str) {
  	global $MAX_STR_LEN;
  	return strlen($str) <= $MAX_STR_LEN
  		&& preg_match('/^[0-9]+$/',$str);
  }
  
  function isValidPhone($str) {
  	global $MAX_PHONE_LEN, $MIN_PHONE_LEN;
  	return strlen($str) <= $MAX_PHONE_LEN
  		&& strlen($str) >= $MIN_PHONE_LEN
  		&& preg_match('/^[0-9]+([xX][0-9]{1-5})?$/',$str);
  }
  
  function isValidEmail($str) {
  	global $MAX_STR_LEN;
  	return strlen($str) <= $MAX_STR_LEN
  		&& preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD',$str);
  }
  
  function isValidPriority($str) {
  	global $MAX_STR_LEN;
  	return strlen($stgr) <= $MAX_STR_LEN
  		&& preg_match('/^[0-9]+$/')
  		&& $str !== '0';
  }
  
  function deleteUser($dataBlob) {
  	$validation = array();
  	$validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
    if(in_array(false,$validation))
  		die(json_encode($validation));
    $user;
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
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidPassword($dataBlob->password);
  	if(!$validation[0] || !$validation[1]) {
  		die(json_encode($validation));
  	}
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changePassword($dataBlob->password);
  	echo json_encode($validation);
  }
  
  function changeTitle($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidTitle($dataBlob->title);
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
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidBool($dataBlob->workStatus);
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
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidBool($dataBlob->vacationDays);
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
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidPhone($dataBlob->phone);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->addPhoneNumber($dataBlob->phone);
  	echo json_encode($validation);
  }
  
  function removePhoneNumber($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidPhone($dataBlob->phone);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->removePhoneNumber($dataBlob->phone);
  	echo json_encode($validation);
  }
  
  function phonePriority($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidPhone($dataBlob->phone);
  	$validation[1] = isValidPriority($dataBlob->priority);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->login);
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
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidEmail($dataBlob->email);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->addEmail($dataBlob->email);
  	echo json_encode($validation);
  }

  function removeEmail($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob->userID);
  	$validation[1] = isValidEmail($dataBlob->email);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->userID);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->removeEmail($dataBlob->email);
  	echo json_encode($validation);
  }
  
  function emailPriority($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidEmail($dataBlob->email);
  	$validation[1] = isValidPriority($dataBlob->priority);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob->login);
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