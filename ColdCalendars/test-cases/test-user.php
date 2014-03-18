<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 3);
  include_once(__DIR__.'/../user/User.php');
  $testUser = new User('Alex');
?>