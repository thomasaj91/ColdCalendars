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
  	$validation[0] = isValidPassword($dataBlob['password']);
  	//... 
  	
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

  function isValidUser($str) {
  	global $MIN_PASSWORD_LENGTH;
  	return strlen($str) >= $MIN_PASSWORD_LENGTH
  	    && preg_match('^[^\x00-\x1f\x7F]$',$str);
  }
  
?>