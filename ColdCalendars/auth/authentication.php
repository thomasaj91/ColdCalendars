<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__.'/validation.php');
require_once(__DIR__.'/../lib/User.php');

function validAjaxGet() {
	return isset($_GET)
	    && isset($_GET['json']);
}

function validCookieDataSent() {
  return isset($_COOKIE)
      && isset($_COOKIE['login'])
      && isset($_COOKIE['authToken'])
      && isValidUserlogin($_COOKIE['login'])
      && isValidAuthenticationToken($_COOKIE['authToken']);
}

function assertValidUserPageAccess() {
	$fail = !validCookieDataSent();
	
	if(!$fail) {
		try {
		  $user = User::load($_COOKIE['login']); 
		  $fail = !$user->isAuthenticated($_COOKIE['authToken']);
		  if(!$fail)
            updateSessionCommunication($user,$_COOKIE['login'],$_COOKIE['authToken']);
		}
		catch(Exception $e) {
		  $fail = true; 
		}
	}
	
    if($fail)
      header('Location: home.php');
}

function updateSessionCommunication($user,$login,$auth) {
	$user->aknowledgeCommunication();
	$user->commitUserData();
	$expireTime = time()+User::getAuthenticationTimeOut();
	setcookie('login',$login,$expireTime);
	setcookie('authToken',$auth,$expireTime);
}

function terminateSessionCommunication($user,$login,$auth) {
	$user->terminateCommunication();
	$user->commitUserData();
	$expireTime = time() - User::getAuthenticationTimeOut();
    unset($_COOKIE['login']);
    unset($_COOKIE['authToken']);
// 	setcookie('login',$login,$expireTime);
// 	setcookie('authToken',$auth,$expireTime);
//  	setcookie('login','',$expireTime);
//  	setcookie('authToken','',$expireTime);
}


?>