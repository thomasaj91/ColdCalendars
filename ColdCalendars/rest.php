<?php
  $MAX_STR_LEN = 255;
  $MIN_PASSWORD_LENGTH = 8;
  
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
  
  $user = new User($requestData['login']);
  if(!$user->isAuthenticated($requestData['authToken']))
  	die('Invlaid user authentication');

  switch($requestData['requestType']) {
  	case 'CreateUser': createUser($requestData); break;
  	default: die('Invalid Request Specification');
  }
  
  
  function createUser($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['userLogin']);
  	$validation[1] = isValidPassword($dataBlob['password']);
  	$validation[2] = isValidName($dataBlob['firstName']);
  	$validation[3] = isValidName($dataBlob['lastName']);
  	$validation[4] = isValidBool($dataBlob['ptft']);
  	$validation[5] = isValidTitle($dataBlob['title']);
  	$validation[6] = isValidRange($dataBlob['vacationDays']);

  	
  	$goodData=true;
  	foreach($validation as $valid)
  	  	$goodData &= $valid;
  	
  	if(!goodData)
  		die(json_encode($validation));
  	//do new user work
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
  
  function deleteUser($dataBlob) {
  	$validation = array();
  	$validation[0] = isValidUserLogin($dataBlob['login']);
    if(!$validation[0])
    	return $validation;
    $user;
    try {
    	$user = new User($dataBlob['login']);
    }
    catch (Exception $e) {
    	return null;
    }
    $user->terminateUser();
  }

  
?>