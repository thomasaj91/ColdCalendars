<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__.'/user/User.php');
require_once(__DIR__.'/auth/authentication.php');
require_once(__DIR__.'/auth/validation.php');

echo json_encode(processREST());

function processREST() {

  $IMPROPER            = 'Improperly Formatted Request';
  $INVALID             = 'Invalid Formatted Request';
  $UNAUTHORIZED        = 'Invalid Request Specification';
	
  $adminOnlyRequests   = array('CreateUser','DeleteUser','PasswordReset','ChangeTitle','ChangeWorkStatus','ChangeVacationDays');
  $managerOnlyRequests = array('AddToSchedule','RemoveFromSchedule');
  
  if(!validAjaxGet() && !validCookieDataSent())
    return $IMPROPER;

  $requestData = json_decode($_GET['json']);
  if(!isset($requestData->requestType))
    return $IMPROPER;
  
  $user = getUserObj($_COOKIE['login']);
  if($user === null)
    return null;
    
  if(   !$user->isAuthenticated($_COOKIE['authToken'])
    || (!$user->isAdmin()   && in_array($requestData->requestType,$adminOnlyRequests))
    || (!$user->isManager() && in_array($requestData->requestType,$managerOnlyRequests)))
  	return $UNAUTHORIZED;

  $user->aknowledgeCommunication();
  $user->commitUserData();
  
  switch($requestData->requestType) {
  	/* Admin Only*/
  	case 'CreateUser':         return createUser($requestData);
  	case 'DeleteUser':         return deleteUser($requestData);
  	case 'PasswordReset':      return passwordReset($requestData);
  	case 'ChangeTitle':        return changeTitle($requestData);
  	case 'ChangeWorkStatus':   return changeWorkStatus($requestData);
  	case 'ChangeVacationDays': return changeVacationDays($requestData);
  	/* Manager only (goes here) */
  	/* All Users */
  	case 'UserInfo':           return getUserInfo($requestData);
  	case 'UserPhone':          return getPhoneNumbers($requestData);
  	case 'AddPhone':           return addPhoneNumber($requestData);
  	case 'PhonePriority':      return phonePriority($requestData); //TODO
  	case 'RemovePhone':        return removePhoneNumber($requestData);
  	case 'UserEmail':          return getEmails($requestData);
  	case 'AddEmail':           return addEmail($requestData);
  	case 'EmailPriority':      return emailPriority($requestData); //TODO
  	case 'RemoveEmail':        return removeEmail($requestData);
  	case 'UserList':           return userList($requestData);
  	default:                   return $INVALID;
  }
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
  		return $validation;
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
		return null;
	}
	return $validation;
  }  
  
  function deleteUser($dataBlob) {
  	$validation = array();
  	$validation['userID'] = (int)isValidUserLogin($dataBlob->userID);

    if(in_array(false,$validation))
  		return $validation;

    if($dataBlob->userID===$_COOKIE['login'])
    	return 'You can\'t delete yourself. Bad Admin.';

    $user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;

  	$user->terminateUser();
    $user->commitUserData();
    return $validation;
  }

  function passwordReset($dataBlob) {
  	$validation = array();
  	$validation['userID']   = isValidUserLogin($dataBlob->userID);
  	$validation['password'] = isValidPassword($dataBlob->password);

    if(in_array(false,$validation))
  		return $validation;
  	
    $user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;
    
  	$user->setPassword($dataBlob->password);
  	$user->commitUserData();
  	return $validation;
  }
  
  function changeTitle($dataBlob) {
  	$validation = array();
  	$validation['userID'] = isValidUserLogin($dataBlob->userID);
  	$validation['title']  = isValidTitle($dataBlob->title);

  	if(in_array(false,$validation))
  		return $validation;

    $user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;
  	
  	
   	$user->setTitle($dataBlob->title);
   	$user->commitUserData();
  	return $validation;
  }
  
  function changeWorkStatus($dataBlob) {
  	$validation = array();
  	$validation['userID']     = isValidUserLogin($dataBlob->userID);
  	$validation['workStatus'] = isValidBool($dataBlob->workStatus);

  	if(in_array(false,$validation))
  		return $validation;

    $user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;
  	
    if($dataBlob->workStatus)
      $user->setFullTime();
    else
      $user->setPartTime();
  	$user->commitUserData();
  	return $validation;
  }
  
  function changeVacationDays($dataBlob) {
  	$validation = array();
  	$validation['userID']       = isValidUserLogin($dataBlob->userID);
  	$validation['vacationDays'] = isValidBool($dataBlob->vacationDays);

   	if(in_array(false,$validation))
  		return $validation;

    $user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;
   	
  	$user->setVacationDays($dataBlob->vacationDays);
  	$user->commitUserData();
  	return $validation;
  }
  
  function getUserInfo($dataBlob) {
  	if(!isValidUserLogin($dataBlob->userID))
  		return null;

    $user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;
  	  	
   	return $user->getInfo();
  }
  
  function getPhoneNumbers($dataBlob) {
  	if(!isValidUserLogin($dataBlob->userID))
  	  return null;
  	
    $user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;

    return $user->getPhoneNumbers();
  }
  
  function addPhoneNumber($dataBlob) {
  	$validation = array();
  	$validation['phone'] = isValidPhone($dataBlob->phone);

  	if(in_array(false,$validation))
  		return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;
  	
  	$user->addPhoneNumber($dataBlob->phone);
  	$user->commitPhoneData();
  	return $validation;
  }
  
  function removePhoneNumber($dataBlob) {
  	$validation = array();
  	$validation['phone'] = isValidPhone($dataBlob->phone);

  	if(in_array(false,$validation))
  		return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;
      	
  	$user->removePhoneNumber($dataBlob->phone);
  	$user->commitPhoneData();
  	return $validation;
  }
  
  function phonePriority($dataBlob) {
  	$validation = array();
  	$validation['phone']    = isValidPhone($dataBlob->phone);
  	$validation['priority'] = isValidPriority($dataBlob->priority);

  	if(in_array(false,$validation))
  		return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;

    $user->changePhoneNumberPriority($dataBlob->phone,$dataBlob->priority);
    $user->commitPhoneData();
  	return $validation;
  }
  
  function getEmails($dataBlob) {
  	if(!isValidUserLogin($dataBlob->userID))
  		return null;

  	$user = getUserObj($dataBlob->userID);
    if($user === null)
      return null;

    return $user->getEmailAddresses();
  }
  
  function addEmail($dataBlob) {
  	$validation = array();
  	$validation['email'] = isValidEmail($dataBlob->email);

  	if(in_array(false,$validation))
  	  return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;

    $user->addEmail($dataBlob->email);
  	$user->commitEmailData();
  	return $validation;
  }

  function removeEmail($dataBlob) {
  	$validation = array();
  	$validation['email'] = isValidEmail($dataBlob->email);

  	if(in_array(false,$validation))
  	  return $validation;
  	
  	$user = getUserObj($_COOKIE['login']);
  	if($user === null)
  	  return null;
  	
  	$user->removeEmail($dataBlob->email);
  	$user->commitEmailData();
  	return $validation;
  }
  
  function emailPriority($dataBlob) {
  	$validation = array();
  	$validation['email']    = isValidEmail($dataBlob->email);
  	$validation['priority'] = isValidPriority($dataBlob->priority);

  	if(in_array(false,$validation))
  	  return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;

    $user->changeEmailPriority($dataBlob->email,$dataBlob->priority);
    $user->commitEmailData();
  	return $validation;
  }
  
  function userList($dataBlob) {
  	$list;

  	try {
      $list = User::getAllLogins();
  	}
  	catch (Exception $e) {
  	  return null;
  	}
	return $list;
  }

  
  
  function getUserObj($login) {
  	$user;
  	try { $user = User::load($login); }
  	catch(Exception $e) { return null; }
    return $user;  	 
  }
  
?>