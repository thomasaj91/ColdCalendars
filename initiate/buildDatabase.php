<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);


$host = 'preumbranet.domaincommysql.com';
$name = 'cold_calendars_test';
$user = 'backend';
$pass = 'wearewinners';
$dbConn = new Mysqli($host, $user, $pass, $name);

if ($dbConn->connect_error)
  die("Could not connect to database $name"
  	. "\nat host: $host"
    . "\nas user: $user"
    . "\nerrorno: " . $dbConn->connect_errorno
    . "\nerror: "   . $dbConn->connect_error);
else 
  echo "Connected Successfully\n";

$sqlPayload = file_get_contents(__DIR__.'/schema.txt');

//$payloads = explode(';', $sqlPayload);

$success = $dbConn->multi_query($sqlPayload);
if(!$success)
  die("Failed to build database"
    . "\nerrorno: " . $dbConn->errno
    . "\nerror: " . $dbConn->error
    );
else 
  echo "Succefully built\n";
  $dbConn->close();
  /**/
  
sleep(2);
  
include_once(__DIR__.'/../user/User.php');
	try {
		User::create('root','lolsecurity','Fname','LName','Admin',true,0,'5558675309','admin@coldcalendars.preumbra.net');
	}
	catch(Exception $e) {
		die('failed to create user root\n'.$e->getMessage());
	}
  
?>