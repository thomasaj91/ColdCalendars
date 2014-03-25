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
		$user;
		try { $user = getUserObj($_COOKIE['login']); }
		catch(Exception $e) { $fail=true; }
		if(!$fail) {
			$fail = !$user->isAuthenticated($_COOKIE['authToken']);
		}
	}
	
	if($fail)
		header('Location: home.php');
}


?>