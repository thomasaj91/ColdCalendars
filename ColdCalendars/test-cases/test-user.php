<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 3);
//  include_once(__DIR__.'/../initiate/buildDatabase.php');
  include_once(__DIR__.'/../user/User.php');
  
  /* Do the isers exist? */
  echo "Alex: ";
  var_dump(User::userExists('AlexW'));
  echo "Jon: ";
  var_dump(User::userExists('Jon'));
  echo "Root: ";
  var_dump(User::userExists('root'));
  
  /* Can I load the user data? */
  $testUser = User::load('AlexW');
  var_dump($testUser);
  $testUser = User::load('root');
  var_dump($testUser);
  User::create('JonZ', 'supersecret', 'Jon', 'Zanura', 'Employee', true, 10, '8675309', ' jczamora@uwm.edu');
  $testUser = User::load('JonZ');
  var_dump($testUser);
  //  echo $testUser->correctPassword('');
?>