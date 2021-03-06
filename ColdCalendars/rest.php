<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__.'/lib/User.php');
require_once(__DIR__.'/lib/Shift.php');
require_once(__DIR__.'/auth/authentication.php');
require_once(__DIR__.'/auth/validation.php');
require_once(__DIR__.'/lib/Availability.php');
require_once(__DIR__.'/lib/TimeOffRequest.php');
require_once(__DIR__.'/lib/VacationRequest.php');

echo json_encode(processREST());

function processREST() {

  $IMPROPER = 'Improperly Formatted Request';
  $INVALID = 'Invalid Formatted Request';
  $UNAUTHORIZED = 'Unauthorized Request Specification';

  $adminOnlyRequests = array('CreateUser','DeleteUser','PasswordReset','ChangeTitle','ChangeWorkStatus','ChangeVacationDays');
  $managerOnlyRequests = array('AddToSchedule','RemoveFromSchedule');
  
  if(!validAjaxGet() && !validCookieDataSent())
    return $IMPROPER;

  $requestData = json_decode($_GET['json']);
  if(!isset($requestData->requestType))
    return $IMPROPER;
  
  $user = getUserObj($_COOKIE['login']);
  if($user === null)
    return null;
    
  if( !$user->isAuthenticated($_COOKIE['authToken'])
    || (!$user->isAdmin() && in_array($requestData->requestType,$adminOnlyRequests))
    || (!$user->isManager() && in_array($requestData->requestType,$managerOnlyRequests)))
   return $UNAUTHORIZED;

  updateSessionCommunication($user,$_COOKIE['login'],$_COOKIE['authToken']);
    
  switch($requestData->requestType) {
   /* Admin Only*/
   case 'CreateUser': return createUser($requestData);
   case 'DeleteUser': return deleteUser($requestData);
   case 'PasswordReset': return passwordReset($requestData);
   case 'ChangeTitle': return changeTitle($requestData);
   case 'ChangeWorkStatus': return changeWorkStatus($requestData);
   case 'ChangeVacationDays': return changeVacationDays($requestData);
   /* Manager only (goes here) */
   case 'AddToSchedule': return addShift($requestData);
   case 'RemoveFromSchedule': return removeShift($requestData);
   case 'ViewQueue': return viewQueue($requestData);
   case 'DecideSwap': return decideSwap($requestData);
   case 'DecideVacation': return decideVacation($requestData);	//TODO sprint 3
   case 'DecideTimeOff': return decideTimeOff($requestData);	//TODO sprint 3
   case 'GetUnapprovedRequests': return getUnapprovedRequests($requestData);
   case 'ReportExport'; return export($requestData);
   case 'GetFullActivityLog': return getFullActivityLog($requestData); //TODO sprint 3
   case 'ViewSchedule': return viewSchedule($requestData);
   case 'ViewTemplate': return viewTemplate($requestData);	//TODO sprint 3
   case 'CreateTemplate': return createTemplate($requestData);	//TODO sprint 3
   case 'LoadTemplate': return loadTemplate($requestData);	//TODO sprint 3
   case 'RemoveTemplate': return removeTemplate($requestData);	//TODO sprint 3
   case 'OverTimeCheck': return overTimeCheck($requestData);
   /* All Users */
   case 'UserInfo': return getUserInfo($requestData);
   case 'UserPhone': return getPhoneNumbers($requestData);
   case 'AddPhone': return addPhoneNumber($requestData);
   case 'PhonePriority': return phonePriority($requestData);
   case 'RemovePhone': return removePhoneNumber($requestData);
   case 'UserEmail': return getEmails($requestData);
   case 'AddEmail': return addEmail($requestData);
   case 'EmailPriority': return emailPriority($requestData);
   case 'RemoveEmail': return removeEmail($requestData);
   case 'UserList': return userList($requestData);
   case 'UserListInfo': return userListInfo($requestData);
   case 'UserFullListInfo': return userFullListInfo($requestData);
   case 'GetUserAvailability':return userAvailability($requestData);
   case 'AddAvailability': return addAvailability($requestData);
   case 'RemoveAvailability': return removeAvailability($requestData);
   case 'GetUserActivityLog': return getUserActivityLog($requestData); //TODO sprint 3
   case 'RequestVacation': return requestVacation($requestData);	//TODO sprint 3
   case 'RequestTimeOff': return requestTimeOff($requestData);	//TODO sprint 3
   case 'ReleaseShift': return releaseShift($requestData);
   case 'PickUpShift': return pickUpShift($requestData);
   case 'LogoutUser': return logoutUser();
   default: return $INVALID;
  }
}
  
  function createUser($dataBlob) {
   $validation = array();
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['password'] = (int)isValidPassword($dataBlob->password);
   $validation['firstName'] = (int)isValidName($dataBlob->firstName);
   $validation['lastName'] = (int)isValidName($dataBlob->lastName);
   $validation['workStatus'] = (int)isValidBool($dataBlob->workStatus);
   $validation['title'] = (int)isValidTitle($dataBlob->title);
   $validation['vacationDays'] = (int)isValidRange($dataBlob->vacationDays);
   $validation['phone'] = (int)isValidPhone($dataBlob->phone);
   $validation['email'] = (int)isValidEmail($dataBlob->email);

  
   //$goodData=true;
   //foreach($validation as $valid)
   // $goodData &= $valid;
  
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
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['password'] = (int)isValidPassword( $dataBlob->password);

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
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['title'] = (int)isValidTitle($dataBlob->title);

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
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['workStatus'] = (int)isValidBool($dataBlob->workStatus);

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
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['vacationDays'] = (int)isValidBool($dataBlob->vacationDays);

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
   $validation['phone'] = (int)isValidPhone($dataBlob->phone);
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
  
   $user->removeEmailAddress($dataBlob->email);
   $user->commitEmailData();
   return $validation;
  }
  
  function emailPriority($dataBlob) {
   $validation = array();
   $validation['email'] = (int)isValidEmail($dataBlob->email);
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
  
  function userListInfo($dataBlob) {
   $list;
  
   try {
   $list = User::getAllNames();
   }
   catch (Exception $e) {
   return null;
   }
   return $list;
  }

  function userFullListInfo($dataBlob) {
    $list;
  
    try {
      $list = User::getAllLogins();
    }
    catch (Exception $e) {
      return null;
    }
    $out = array();
    foreach($list as $login) {
      $out[$login] = array('info'         => getUserInfo(     (object)array('userID' => $login))
                          ,'phones'       => getPhoneNumbers( (object)array('userID' => $login))
                          ,'emails'       => getEmails(       (object)array('userID' => $login))
                          ,'availability' => userAvailability((object)array('login' => $login))
                          );
    }
    
    return $out;
  }
  
  
  function addShift($dataBlob) {
   $validation = array();
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
   $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
  
   if(in_array(false,$validation))
   return $validation;

   /* maybe pass in correctly formatted start & end times (no date prefix) */
   try {
   		Shift::create($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime);
   }
   catch(Exception $e){
   		return null;
   }
   return $validation;
  }
  
  function removeShift($dataBlob) {
   $validation = array();
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
$validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
$validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
  
   if(in_array(false,$validation))
   return $validation;
  
   if(!Shift::exists($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime))
   return null;
  
   Shift::delete($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime);
   return $validation;
  }
  
  function decideSwap($dataBlob) {
	$validation = array();
    $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
	$validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
	$validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
	$validation['approved'] = (int)isValidBool($dataBlob->approved);
	$validation['swapper'] = (int)isValidUserLogin($dataBlob->swapper);

	if(in_array(false,$validation))
		return $validation;

    if(!Shift::exists($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime))
		return null;

	$shift = Shift::load($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime);
	if($dataBlob->approved)
      $shift->approve($dataBlob->swapper);
    else
      $shift->reject($dataBlob->swapper);
    return $validation;
  }

  function releaseShift($dataBlob) {
   $validation = array();
$validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
$validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
  
   if(in_array(false,$validation))
   return $validation;
  
   if(!Shift::exists($_COOKIE['login'], $dataBlob->startTime, $dataBlob->endTime))
   return null;
  
   $shift = Shift::load($_COOKIE['login'], $dataBlob->startTime, $dataBlob->endTime);
   $shift->release();
   return $validation;
  }
  
  function pickUpShift($dataBlob) {
   $validation = array();
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
   $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
  
   if(in_array(false,$validation))
   return $validation;
  
   if(!Shift::exists($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime))
   return null;
  
   $shift = Shift::load($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime);
   $shift->pickup($_COOKIE['login']);
    return $validation;
  }

  function viewSchedule($dataBlob){
   $validation = array();
   $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
   $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);

   if(in_array(false,$validation))
   return $validation;
  
   $list;
   try {
   $list = Shift::getAllShifts($dataBlob->startTime,$dataBlob->endTime); //pass a start time and end time to define the range of shifts that should be passed back
   }
   catch (Exception $e) {
   return null;
   }
   return $list;
  }
  
  function viewQueue($dataBlob){
    $validation = array();
    $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
    $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
    
    if(in_array(false,$validation))
      return $validation;
     
    $sList = array();
    $vList = array();
    $tList = array();
	try {
		$sList = Shift::getAllUndecidedSwaps($dataBlob->startTime, $dataBlob->endTime);
		$vList = VacationRequest::getUndecidedVacationRequests();
		$tList = TimeOffRequest::getUndecidedTimeOffRequests(); 
	}
    catch (Exception $e) { 
    	echo($e->getMessage()); 
    }
    foreach($sList as &$elm) $elm['type'] = 'Swap';
    foreach($vList as &$elm) $elm['type'] = 'Vacation';
    foreach($tList as &$elm) $elm['type'] = 'TimeOff';
    return array_merge($sList, $vList, $tList);
  }

  function userAvailability($dataBlob) {
   $validation = array();
   $validation['login'] = isValidUserLogin($dataBlob->login);
   $list;
  
   try {
   		$list = Availability::getUsersAvailability($dataBlob->login);
   }
   catch (Exception $e) {
   		return null;
   }
   return $list;
  }
  function addAvailability($dataBlob){
   $validation = array();
   $validation['day'] = (int)isValidDay($dataBlob->day);
   $validation['start'] = (int)isValidTime($dataBlob->start);
   $validation['end'] = (int)isValidTime($dataBlob->end);
  
   if(in_array(false,$validation))
   return $validation;
  
   try
   {
   	Availability::create($_COOKIE['login'],$dataBlob->day,$dataBlob->start,$dataBlob->end);
   }
   catch (Exception $e)
   {
   	return null;
   }
   
   return $validation;
  }
  function removeAvailability($dataBlob) {
   $validation = array();
   $validation['day'] = (int)isValidDay($dataBlob->day);
   $validation['start'] = (int)isValidTime($dataBlob->start);
   $validation['end'] = (int)isValidTime($dataBlob->end);
  
   if(in_array(false,$validation))
   return $validation;
  
   try
   {
   	Availability::delete($_COOKIE['login'],$dataBlob->day,$dataBlob->start,$dataBlob->end);
   }
   catch (Exception $e)
   {
   	return null;
   }
   
   return $validation;
  }
  
  function decideVacation($dataBlob) {
   $validation = array();
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
   $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
   $validation['approved'] = (int)isValidBool($dataBlob->approved);
  
   if(in_array(false,$validation))
   return $validation;
  
   if(!VacationRequest::exists($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime))
   return null;
  
   $vacation = VacationRequest::load($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime);
   if($dataBlob->approved)
   $vacation->approve();
   else
   $vacation->reject();
   return $validation;
  }
  
  function decideTimeOff($dataBlob) {
   $validation = array();
   $validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
   $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
   $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
   $validation['approved'] = (int)isValidBool($dataBlob->approved);
  
   if(in_array(false,$validation))
   return $validation;
  
   if(!TimeOffRequest::exists($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime))
   return null;
  
   $timeOff = TimeOffRequest::load($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime);
   if($dataBlob->approved)
   $timeOff->approve();
   else
   $timeOff->reject();
   return $validation;
  }
  
  function getUnapprovedRequests($dataBlob) {
  	$vList;
  	$tList;
  	
  	try {
  		$vlist = VacationRequest::getUndecidedVacationRequests();
  		$tList = TimeOffRequest::getUndecidedTimeOffRequests();
  	}
  	catch (Exception $e) {
  		return null;
  	}
  	return array_merge($vList, $tList);
  }

  function export($dataBlob) {
   $validation = array();
   $validation['start'] = (int)isValidDateTime($dataBlob->start);
   $validation['end'] = (int)isValidDateTime($dataBlob->end);
  
   if(in_array(false,$validation))
     return $validation;
   return DB::getCSVExport($dataBlob->start, $dataBlob->end);
  }
  
  function getMainActivityLog($dataBlob) { //TODO
   $validation = array();
   $validation['startDate'] = (int)isValidDateTime($dataBlob->startDate);
   $validation['endDate'] = (int)isValidDateTime($dataBlob->endDate);
  
   if(in_array(false,$validation))
   return $validation;
  
   //get the main log
  }
  
  function viewTemplate($dataBlob) { //TODO
  	try{
   		return Template::loadAllTemplates();
  	}
  	catch(Exception $e){
  		return null;
  	}
  }
  
  function createTemplate($dataBlob) { //TODO
   $validation = array();
   $validation['title'] = (int)isValidTitle($dataBlob->title);
   $validation['startDate'] = (int)isValidDateTime($dataBlob->startDate);
   $validation['endDate'] = (int)isValidDateTime($dataBlob->endDate);
  
   if(in_array(false,$validation))
   		return $validation;
   try{
   		Template::create($dataBlob->startDate,$dataBlob->endDate);
   }
   catch(Exception $e) {
   	return null;
   }
   return $validation;
  }
  
  function loadTemplate($dataBlob) { //TODO
   $validation = array();
   $validation['title'] = (int)isValidTitle($dataBlob->title);
  
   if(in_array(false,$validation))
   return $validation;
  
     $template = Template::load($dataBlob->title);
     return $template->getShiftData();
  }
  
  function removeTemplate($dataBlob) { //TODO
   $validation = array();
   $validation['title'] = (int)isValidTitle($dataBlob->title);
  
   if(in_array(false,$validation))
   return $validation;
  
   Template::remove($dataBlob->name);
   return validation;
  }
  
  function getUserActivityLog($dataBlob) { //TODO
   $validation = array();
    $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
    $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
    
   if(in_array(false,$validation))
     return $validation;
  
    try {
		$sList = Shift::getAllUserDecidedSwaps($_COOKIE['login'],$dataBlob->startTime, $dataBlob->endTime);
		$vList = VacationRequest::getUserDecidedVacationRequests($_COOKIE['login']);
		$tList = TimeOffRequest::getUserDecidedTimeOffRequests($_COOKIE['login']); 
	}
    catch (Exception $e) { 
    	echo($e->getMessage()); 
    }
    foreach($sList as &$elm) $elm['type'] = 'Swap';
    foreach($vList as &$elm) $elm['type'] = 'Vacation';
    foreach($tList as &$elm) $elm['type'] = 'TimeOff';
    return array_merge($sList, $vList, $tList);
  }

  function getFullActivityLog($dataBlob) { //TODO
    $validation = array();
    $validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
    $validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
  
    if(in_array(false,$validation))
      return $validation;
  
    try {
      $sList = Shift::getAllDecidedSwaps($dataBlob->startTime, $dataBlob->endTime);
      $vList = VacationRequest::getDecidedVacationRequests();
      $tList = TimeOffRequest::getDecidedTimeOffRequests();
    }
    catch (Exception $e) {
      echo($e->getMessage());
    }
    foreach($sList as &$elm) $elm['type'] = 'Swap';
    foreach($vList as &$elm) $elm['type'] = 'Vacation';
    foreach($tList as &$elm) $elm['type'] = 'TimeOff';
    return array_merge($sList, $vList, $tList);
  }
  
  
  function requestVacation($dataBlob) { //TODO
   $validation = array();
   $validation['startDate'] = (int)isValidDateTime($dataBlob->startDate);
   $validation['endDate'] = (int)isValidDateTime($dataBlob->endDate);
  
   if(in_array(false,$validation))
   	return $validation;
   $user = User::load($_COOKIE['login']);
   $remaining = $user->getRemainingVacationDays();
   $total = $user->getVacationDays();
   //$start = new DateTime($dataBlob->startDate);
   //$end = new DateTime($dataBlob->endDate);
   
   $start = DateTime::createFromFormat('Y-m-d H:i:s',$dataBlob->startDate);
   $end   = DateTime::createFromFormat('Y-m-d H:i:s',$dataBlob->endDate);   
   $t     = $start->diff($end,true);
   $days  = $t->d;
   
   if($days + $remaining >= $total)
   		return null;
   $timeOff = VacationRequest::create($_COOKIE['login'], $dataBlob->startDate, $dataBlob->endDate);
   	return $validation;
  }
  
  function requestTimeOff($dataBlob) { //TODO
   $validation = array();
   $validation['startDate'] = (int)isValidDateTime($dataBlob->startDate);
   $validation['endDate'] = (int)isValidDateTime($dataBlob->startDate);
  
   if(in_array(false,$validation))
   return $validation;
  
   $timeOff = TimeOffRequest::create($_COOKIE['login'], $dataBlob->startDate, $dataBlob->endDate);
   return $validation;
  }
  
  function overTimeCheck($dataBlob) {
  	$validation = array();
  	$validation['userID'] = (int)isValidUserLogin($dataBlob->userID);
  	$validation['startTime'] = (int)isValidDateTime($dataBlob->startTime);
  	$validation['endTime'] = (int)isValidDateTime($dataBlob->endTime);
  	
  	if(in_array(false,$validation))
  		return $validation;
  	//$result = null;
  	try {
  		//var_dump(json_encode((int)Shift::checkOverTimeForUser($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime)));
  		return Shift::checkOverTimeForUser($dataBlob->userID, $dataBlob->startTime, $dataBlob->endTime);
  	}
  	catch(Exception $e)
  	{
  		return null;
  	}
  	//var_dump($result);
  	//return $result;
  }

  function logoutUser() {
   $user = getUserObj($_COOKIE['login']);
   if($user === null)
   return null;
   terminateSessionCommunication($user,$_COOKIE['login'],$_COOKIE['authToken']);
   return 1;
  }
  
  function getUserObj($login) {
   $user;
   try { $user = User::load($login); }
   catch(Exception $e) { return null; }
    return $user;
  }

  function getShiftObj($login,$start,$end) {
   $shift;
   try { $shift = Shift::load($login,$start,$end); }
   catch(Exception $e) { return null; }
    return $shift;
  }
?>
