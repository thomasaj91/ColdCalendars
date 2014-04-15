<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
$MAX_STR_LEN = 255;
$MIN_PASSWORD_LENGTH = 8;
$MIN_PHONE_LEN = 7;
$MAX_PHONE_LEN = 16;
$AUTH_STR_LEN = 1024;

function isValidUserLogin($str) {
	global $MAX_STR_LEN;
	return strlen($str) <= $MAX_STR_LEN
	&& preg_match('/^[a-zA-Z0-9]+$/',$str);
}

function isValidPassword($str) {
	global $MIN_PASSWORD_LENGTH;
	return strlen($str) >= $MIN_PASSWORD_LENGTH
	&& preg_match('/^\S+$/',$str);
}

function isValidAuthenticationToken($str) {
	global $AUTH_STR_LEN;
	return strlen($str) === $AUTH_STR_LEN;
}

function isValidName($str) {
	global $MAX_STR_LEN;
	return strlen($str) <= $MAX_STR_LEN
	&& preg_match('/^[a-zA-z]+$/',$str);
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
	&& preg_match('/^[0-9]+$/',$str);
}

function isValidPhone($str) {
	global $MAX_PHONE_LEN, $MIN_PHONE_LEN;
	return strlen($str) <= $MAX_PHONE_LEN
	&& strlen($str) >= $MIN_PHONE_LEN
	&& preg_match('/^[0-9]+([xX][0-9]{1-5})?$/',$str);
}

function isValidEmail($str) {
	global $MAX_STR_LEN;
	return strlen($str) <= $MAX_STR_LEN
	&& preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD',$str);
}

function isValidPriority($str) {
	global $MAX_STR_LEN;
	return strlen($stgr) <= $MAX_STR_LEN
	&& preg_match('/^[0-9]+$/')
	&& $str !== '0';
}

function isValidTime($str) {
	global $MAX_STR_LEN;
	$parts = explode(':',$str);
	list($hour,$minute) = explode(':',$str);
	return strlen($str) <= $MAX_STR_LEN
	&& preg_match('/^[0-9]+[:][0-9]+$/',$str)
	&& ((int)$hour >= 0 && (int)$hour <= 23)
	&& ((int)$minute >= 0 && (int)$minute <= 59);
}

function isValidDate($str) {
	global $MAX_STR_LEN;
	list($year,$month,$day) = explode('-',$str);
	return strlen($str) <= MAX_STR_LEN
	&& preg_match('/^[0-9]+[-][0-9]+[-][0-9]$/',$str)
	&& (int)$year > 0
	&& ((int)$month > 0 && (int)$month < 12)
	&& ((int)$day > 0 && (int)$day < 31);
}

function isValidDay($str) {
	global $MAX_STR_LEN;
	return strlen($str) <= $MAX_STR_LEN
	&& preg_match('/^[a-zA-Z]+$/',$str)
	&& (strcasecmp($str,'Sun') 
	|| strcasecmp($str,'Mon')
	|| strcasecmp($str,'Tue')
	|| strcasecmp($str,'Wed')
	|| strcasecmp($str,'Thu')
	|| strcasecmp($str,'Fri')
	|| strcasecmp($str,'Sat'));
}

/* Expects 'YYYY-MM-DD HH:MM:SS' */
function isValidDateTime($str) {
	$example = 'YYYY-MM-DD HH:MM:SS';
	if( strlen($str) !== strlen($example)
     || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $str))
		return false;

	$year  = (int)substr($str, 0,4);
	$month = (int)substr($str, 5,2);
	$date  = (int)substr($str, 8,2);
	$hour  = (int)substr($str,11,2);
	$min   = (int)substr($str,14,2);
	$sec   = (int)substr($str,17,2);
	return 1970 <= $year  && $year  <= 9999
	    &&    1 <= $month && $month <= 12 
	    &&    1 <= $date  && $month <= 31 
	    &&    0 <= $hour  && $hour  <= 23 
	    &&    0 <= $min   && $min   <= 59
	    &&    0 <= $sec   && $sec   <= 59;
}

?>
