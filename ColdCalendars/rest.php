<?php
  $MAX_STR_LEN = 255;
  $MIN_PASSWORD_LENGTH = 8;
  $MIN_PHONE_LENGTH = 7;
  
  if(!isset($_GET) || !isset($_GET['json']))
    die('Improperly formated request');

  $requestData = json_decode($_GET['json']);
  if(!isset($requestData['login'])
  || !isset($requestData['authToken'])
  || !isset($requestData['requestType']))
    die('Improperly Formated Request');

  if(!isValidUserlogin($requestData['login']))
  	die('Improperly Formatted User Login');

  if(!isValidAuthenticationToken($requestData['authToken']))
  	die('Improperly Formatted authentication Token');
  
  $user = User::load($requestData['login']);
  if(!$user->isAuthenticated($requestData['authToken']))
  	die('Invlaid user authentication');

  switch($requestData['requestType']) {
  	case 'CreateUser': createUser($requestData); break;
  	case 'DeleteUser': deleteUser($requestData); break;
  	case 'PasswordReset': passwordReset($requestData); break;
  	case 'ChangeTitle': changeTitle($requestData); break;
  	case 'ChangeWorkStatus': changeWorkStatus($requestData); break;
  	case 'ChangeVacationDays': changeVacationDays($requestData); break;
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
  	$validation['userID'] = isValidUserLogin($dataBlob['userID']);
  	$validation['password'] = isValidPassword($dataBlob['password']);
  	$validation['firstName'] = isValidName($dataBlob['firstName']);
  	$validation['lastName'] = isValidName($dataBlob['lastName']);
  	$validation['workStatus'] = isValidBool($dataBlob['workStatus']);
  	$validation['title'] = isValidTitle($dataBlob['title']);
  	$validation['vacationDays'] = isValidRange($dataBlob['vacationDays']);
  	$validation['phone'] = isValidPhone($dataBlob['phone']);
  	$validation['email'] = isValidEmail($dataBlob['email']);

  	
  	//$goodData=true;
  	//foreach($validation as $valid)
  	// 	$goodData &= $valid;
  	
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	//do User::load work
	try {
		User::create($dataBlob['userID'],
					$dataBlob['password'],
					$dataBlob['firstName'],
					$dataBlob['lastName'],
					$dataBlob['title'],
					$dataBlob['workStatus'],
					$dataBlob['vacationDays'],
					$dataBlob['phone'],
					$dataBlob['email']);
	}
	catch (Exception $e) {
		die(json_encode(null));
	}
  }  
  
  function isValidUserLogin($str) {
  	global $MAX_STR_LEN;
    return strlen($str) <= $MAX_STR_LEN
        && preg_match('^[a-zA-Z0-9]+$',$str);
  }

  function isValidPassword($str) {
  	global $MIN_PASSWORD_LENGTH;
  	return strlen($str) >= $MIN_PASSWORD_LENGTH
  	    && preg_match('^[^\x00-\x1f\x7F]$',$str);
  }
  
  function isValidName($str) {
  	global $MAX_STR_LEN;
  	return strlen($str) <= $MAX_STR_LEN
  		&& preg_match('^[a-zA-z]+$',$str);
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
  		&& preg_match('^[0-9]+$',$str);
  }
  
  function isValidPhone($str) {
  	global $MAX_STR_LEN, $MIN_PHONE_LEN;
  	return strlen($str) <= $MAX_STR_LEN
  		&& strlen($str) >= $MIN_PHONE_LEN
  		&& preg_match('^[0-9]+([xX][0-9]{1-5})?$',$str);
  }
  
  function isValidEmail($str) {
  	global $MAX_STR_LEN;
  	return strlen($str) <= $MAX_STR_LEN
  		&& preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD',$str);
  }
  
  function isValidPriority($str) {
  	global $MAX_STR_LEN;
  	return strlen($stgr) <= $MAX_STR_LEN
  		&& preg_match('^[0-9]+$')
  		&& $str !== '0';
  }
  
  function deleteUser($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['login']);
    if(in_array(false,$validation))
  		die(json_encode($validation));
    $user;
    try {
    	$user = User::load($dataBlob['login']);
    }
    catch (Exception $e) {
    	die(json_encode(null));
    }
    $user->terminateUser();
    echo json_encode($validation);
    //
  }

  function passwordReset($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidPassword($dataBlob['password']);
  	if(!$validation[0] || !$validation[1]) {
  		die(json_encode($validation));
  	}
  	try {
  		$user = User::load($datablob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changePassword($dataBlob['password']);
  	echo json_encode($validation);
  }
  
  function changeTitle($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidTitle($dataBlob['title']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeTitle($dataBlob['title']);
  	echo json_encode($validation);
  }
  
  function changeWorkStatus($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidBool($dataBlob['workStatus']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeWorkStatus($dataBlob['workStatus']);
  	echo json_encode($validation);
  }
  
  function changeVacationDays($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidBool($dataBlob['vacationDays']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeVacationDays($dataBlob['vacationDays']);
  	echo json_encode($validation);
  }
  
  function getPhoneNumbers($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->getPhoneNumbers();
  	echo json_encode($validation);
  }
  
  function addPhoneNumber($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidPhone($dataBlob['phone']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->addPhoneNumber($dataBlob['phone']);
  	echo json_encode($validation);
  }
  
  function removePhoneNumber($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidPhone($dataBlob['phone']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->removePhoneNumber($dataBlob['phone']);
  	echo json_encode($validation);
  }
  
  function phonePriority($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidPhone($dataBlob['phone']);
  	$validation[1] = isValidPriority($dataBlob['priority']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changePhoneNumberPriority($dataBlob['phone'],$dataBlob['priority']);
  	echo json_encode($validation);
  }
  
  function getEmails($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->getEmails();
  	echo json_encode($validation);
  }
  
  function addEmail($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidEmail($dataBlob['email']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->addEmail($dataBlob['email']);
  	echo json_encode($validation);
  }

  function removeEmail($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userID']);
  	$validation[1] = isValidEmail($dataBlob['email']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['userID']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->removeEmail($dataBlob['email']);
  	echo json_encode($validation);
  }
  
  function emailPriority($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidEmail($dataBlob['email']);
  	$validation[1] = isValidPriority($dataBlob['priority']);
  	if(in_array(false,$validation))
  		die(json_encode($validation));
  	try {
  		$user = User::load($dataBlob['login']);
  	}
  	catch(Exception $e) {
  		die(json_encode(null));
  	}
  	$user->changeEmailPriority($dataBlob['email'],$dataBlob['priority']);
  	echo json_encode($validation);
  }
  
  function userList($dataBlob) {
  	$list;
  	try {
		$list = User::getAllLogins();
  	}
  	catch (Exception $e) {
  		die(json_encode($list));
  	}
	echo json_encode($list);
  }
  
?>