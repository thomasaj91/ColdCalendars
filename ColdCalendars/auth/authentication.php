<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__.'/validation.php');
require_once(__DIR__.'/../user/User.php');

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
            updateSessionCommunication($user);
		}
		catch(Exception $e) {
		  $fail = true; 
		}
	}
	
    if($fail)
      header('Location: home.php');
}

function updateSessionCommunication($user) {
	$user->aknowledgeCommunication();
	$user->commitUserData();
	$expireTime = time()+User::getAuthenticationTimeOut();
	setcookie('login',$_COOKIE['login'],$expireTime);
	setcookie('authToken',$_COOKIE['authToken'],$expireTime);
}

?>