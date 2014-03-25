<?php

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

?>