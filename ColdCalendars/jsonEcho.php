<?php

if(!isset($_GET) || !isset($_GET['json']))
	die('Improperly formated request');

var_dump($_GET);
var_dump(json_decode($_GET['json']));

?>