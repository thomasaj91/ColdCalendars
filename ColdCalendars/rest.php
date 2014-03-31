<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__.'/lib/User.php');
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

  updateSessionCommunication($user,$_COOKIE['login'],$_COOKIE['authToken']);
    
  switch($requestData->requestType) {
  	/* Admin Only*/
  	case 'CreateUser':         return createUser($requestData);
  	case 'DeleteUser':         return deleteUser($requestData);
  	case 'PasswordReset':      return passwordReset($requestData);
  	case 'ChangeTitle':        return changeTitle($requestData);
  	case 'ChangeWorkStatus':   return changeWorkStatus($requestData);
  	case 'ChangeVacationDays': return changeVacationDays($requestData);
  	/* Manager only (goes here) */
  	case 'AddToSchedule':      return addShift($requestData);
  	case 'RemoveFromSchedule': return removeShift($requestData);
  	case 'ViewSchedule':       return viewSchedule($requestData);
  	case 'ViewTemplate':       return viewTemplate($requestData);		//TODO sprint 3
  	case 'CreateTemplate':     return createTemplate($requestData);		//TODO sprint 3
  	case 'LoadTemplate':       return loadTemplate($requestData);		//TODO sprint 3
  	case 'RemoveTemplate':     return removeTemplate($requestData);		//TODO sprint 3
  	case 'ViewQueue':          return viewQueue($requestData);
  	case 'DecideSwap':         return approveSwap($requestData);
  	case 'DecideVacation':     return approveVacation($requestData);	//TODO sprint 3
  	case 'DecideTimeOff':      return approveTimeOff($requestData);		//TODO sprint 3
  	case 'ReportExport';       return export($requestData);				//TODO sprint 3
  	case 'GetMainActivityLog': return getMainActivityLog($requestData); //TODO sprint 3
  	/* All Users */
  	case 'UserInfo':           return getUserInfo($requestData);
  	case 'UserPhone':          return getPhoneNumbers($requestData);
  	case 'AddPhone':           return addPhoneNumber($requestData);
  	case 'PhonePriority':      return phonePriority($requestData); 		//TODO
  	case 'RemovePhone':        return removePhoneNumber($requestData);
  	case 'UserEmail':          return getEmails($requestData);
  	case 'AddEmail':           return addEmail($requestData);
  	case 'EmailPriority':      return emailPriority($requestData); 		//TODO
  	case 'RemoveEmail':        return removeEmail($requestData);
  	case 'UserList':           return userList($requestData);
  	case 'GetUserAvailability':return userAvailability($requestData);
  	case 'AddAvailability':    return addAvailability($requestData);
  	case 'RemoveAvailability': return removeAvailability($requestData);
  	case 'GetUserActivityLog': return getUserActivityLog($requestData); //TODO sprint 3
  	case 'RequestVacation':    return requestVacation($requestData);	//TODO sprint 3
  	case 'RequestTimeOff':     return requestTimeOff($requestData);		//TODO sprint 3
  	case 'ReleaseShift':       return releaseShift($requestData);
  	case 'PickUpShift':        return pickUpShift($requestData);
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
  	$validation['password'] = isValidPassword( $dataBlob->password);

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
  	$validation['phone'] = (int)isValidPhone($dataBlob->phone);

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
  	$validation['phone'] = (int)isValidPhone($dataBlob->phone);

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
  	$validation['phone']    = (int)isValidPhone($dataBlob->phone);
  	$validation['priority'] = (int)isValidPriority($dataBlob->priority);

  	if(in_array(false,$validation))
  		return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;

    $user->setPhoneNumberPriority($dataBlob->phone,$dataBlob->priority);
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
  	$validation['email'] = (int)isValidEmail($dataBlob->email);

  	if(in_array(false,$validation))
  	  return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;

    $user->addEmailAddress($dataBlob->email);
  	$user->commitEmailData();
  	return $validation;
  }

  function removeEmail($dataBlob) {
  	$validation = array();
  	$validation['email'] = (int)isValidEmail($dataBlob->email);

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
  	$validation['email']    = (int)isValidEmail($dataBlob->email);
  	$validation['priority'] = (int)isValidPriority($dataBlob->priority);

  	if(in_array(false,$validation))
  	  return $validation;

  	$user = getUserObj($_COOKIE['login']);
    if($user === null)
      return null;

    $user->setEmailAddressPriority($dataBlob->email,$dataBlob->priority);
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
  
  function addShift($dataBlob) { //TODO
  	$validation = array();
  	$validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
  	$validation['date'] = (int)isValidDate($dataBlob->date);
  	$validation['start'] = (int)isValidTime($dataBlob->start);
  	$validation['end'] = (int)isValidTime($dataBlob->end);
  	
  	if(in_array(false,$validation))
  		return $validation;
  	
  	$user = getUserObj($_COOKIE['login']);
  	if($user === null)
  		return null;
  	
  	$shift->addShift($dataBlob->userID,$dataBlob->shift,$dataBlob->start,$dataBlob->end);
  	$shift->commitShiftData();
  	return $validation;
  }
  function removeShift($datablob){ //TODO
  	$validation = array();
  	$validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
  	$validation['date'] = (int)isValidDate($dataBlob->date);
  	$validation['start'] = (int)isValidTime($dataBlob->start);
  	$validation['end'] = (int)isValidTime($dataBlob->end);
  	
  	if(in_array(false,$validation))
  		return $validation;
  	 
  	$user = getUserObj($_COOKIE['login']);
  	if($user === null)
  		return null;
  	 
  	$shift->removeShift($dataBlob->userID,$dataBlob->date,$dataBlob->start,$dataBlob->end);
  	$shift->commitShiftData();
  	return $validation;
  }
  function viewSchedule($dataBlob){ //TODO
  	$list;
  	
  	try {
  		$list = Shift::getAllShifts($start,$end); //pass a start time and end time to define the range of shifts that should be passed back
  	}
  	catch (Exception $e) {
  		return null;
  	}
  	return $list;
  }
  
  function viewQueue($dataBlob){ //TODO
	$list;
	
	try {
		$list = Queue::getQueue(); // not sure what to do with this
	}
	catch (Exception $e) {
		return null;
	}
	return $list;
  }
  function approveSwap($dataBlob){ //TODO
	$validation = array();
	$validation['prev'] = (int)isValidUserLogin($dataBlob->prev);
	$validation['next'] = (int)isValidUserLogin($dataBlob->next);
	$validation['startDate'] = (int)isValidDate($dataBlob->startDate);
	$validation['startTime'] = (int)isValidTime($dataBlob->startTime);
	$validation['endDate'] = (int)isValidDate($dataBlob->endDate);
	$validation['endTime'] = (int)isValidTime($dataBlob->endTime);
	$validation['approved'] = (int)isValidBool($dataBlob->approved);
	
	if(in_array(false,$validation))
		return $validation;
	 
	$user = getUserObj($_COOKIE['login']);
	if($user === null)
		return null;
	 
	$swap->approveSwap($dataBlob->prev,$dataBlob->next,$dataBlob->startDate,$dataBlob->startTime,$dataBlob->endDate,$dataBlob->endTime,$dataBlob->approved);
	$swap->commitSwapData();
	return $validation;
  }
  

  function userAvailability($dataBlob) { //TODO
  	//$validation = array();
  	//$validation['login'] = isValidUserLogin($dataBlob->login);
  	$list;
  	
  	try {
  		$list = User::getAvailability($dataBlob->login);
  	}
  	catch (Exception $e) {
  		return null;
  	}
  	return $list;
  }
  function addAvailability($dataBlob){ //TODO
  	$validation = array();
  	$validation['day'] = (int)isValidDay($dataBlob->day);
  	$validation['start'] = (int)isValidTime($dataBlob->start);
  	$validation['end'] = (int)isValidTime($dataBlob->end);
  	
  	if(in_array(false,$validation))
  		return $validation;
  	
  	$user = getUserObj($_COOKIE['login']);
  	if($user === null)
  		return null;
  	
  	$user->addAvailability($dataBlob->day,$dataBlob->start,$dataBlob->end);
  	$user->commitAvailabilityData();
  	return $validation;
  }
  function removeAvailability($dataBlob) { //TODO
  	$validation = array();
  	$validation['day'] = (int)isValidDay($dataBlob->day);
  	$validation['start'] = (int)isValidTime($dataBlob->start);
  	$validation['end'] = (int)isValidTime($dataBlob->end);
  	
  	if(in_array(false,$validation))
  		return $validation;
  	
  	$user = getUserObj($_COOKIE['login']);
  	if($user === null)
  		return null;
  	
  	$user->removeAvailability($dataBlob->day,$dataBlob->start,$dataBlob->end);
  	$user->commitAvailabilityData();
  	return $validation;
  }
  
  function releaseShift($dataBlob) { //TODO
  	$validation = array();
  	$validation['startDate'] = (int)isValidDate($dataBlob->startDate);
  	$validation['startTime'] = (int)isValidTime($dataBlob->startTime);
  	$validation['endDate'] = (int)isValidDate($dataBlob->endDate);
  	$validation['endTime'] = (int)isValidTime($dataBlob->endTime);
  	
  	if(in_array(false,$validation))
  		return $validation;
  	
  	$user = getUserObj($_COOKIE['login']);
  	if($user === null)
  		return null;
  	
  	$shift->releaseShift($dataBlob->startDate,$dataBlob->startTime,$dataBlob->endDate,$dataBlob->endTime);
  	$shift->commitShiftData();
  }
  function pickUpShift($requestData) { //TODO
  	$validation = array();
  	$validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
  	$validation['startDate'] = (int)isValidDate($dataBlob->startDate);
  	$validation['startTime'] = (int)isValidTime($dataBlob->startTime);
  	$validation['endDate'] = (int)isValidDate($dataBlob->endDate);
  	$validation['endTime'] = (int)isValidTime($dataBlob->endTime);
  	
  	if(in_array(false,$validation))
  		return $validation;
  	 
  	$user = getUserObj($_COOKIE['login']);
  	if($user === null)
  		return null;
  	
  	$shift->pickUpShift($dataBlob->userID,$dataBlob->startDate,$dataBlob->startTime,$dataBlob->endDate,$dataBlob->endTime);
  	$shift->commitShiftData();
  }
  
  function getUserObj($login) {
  	$user;
  	try { $user = User::load($login); }
  	catch(Exception $e) { return null; }
    return $user;  	 
  }

?>
