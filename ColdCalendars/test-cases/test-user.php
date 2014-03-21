<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 3);
//  include_once(__DIR__.'/../initiate/buildDatabase.php');
  include_once(__DIR__.'/../user/User.php');
  
  /* Do the users exist? */
  echo "Alex: ";
  var_dump(User::userExists('AlexW'));
  echo "Jon: ";
  var_dump(User::userExists('Jon'));
  echo "Root: ";
  var_dump(User::userExists('root'));
  
  var_dump(User::userExists('AustinT'));
  var_dump(User::userExists('AustinT2'));
  
  /* Can I load the user data? */
  $alex = User::load('AlexW');
  var_dump($alex);
  $alex->generateAuthenticationToken();
  var_dump($alex);
  $root = User::load('root');
  var_dump($root);
  User::create('JonZ', 'supersecret', 'Jon', 'Zanura', 'Employee', true, 10, '8675309', ' jczamora@uwm.edu');
  $jon = User::load('JonZ');
  var_dump($jon);
  //  echo $testUser->correctPassword('');
?>