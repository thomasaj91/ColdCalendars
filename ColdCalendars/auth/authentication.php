<?php

function validAjaxGet() {
	return isset($_GET) && isset($_GET['json']);
}

function validCookieDataSent() {
  return isset($_COOKIE) && isset($_COOKIE['login']) && isset($_COOKIE['authToken']);
}

?>