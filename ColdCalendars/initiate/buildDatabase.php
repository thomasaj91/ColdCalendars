<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);

require_once(__DIR__.'/../DB.php');

if(DB::buildDatabase())
  echo 'Built Successfully';
else
  echo 'Failed to Build Database';

?>