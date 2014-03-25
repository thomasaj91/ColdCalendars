<?php

require_once('validation.php');

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
		  $user = getUserObj($_COOKIE['login']); 
		  $fail = !$user->isAuthenticated($_COOKIE['authToken']);
		}
		catch(Exception $e) {
		  $fail = true; 
		}
	}
	
    if($fail)
      header('Location: home.php');
}


?>