<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', 3 );
require_once (__DIR__ . '/../lib/User.php');
require_once (__DIR__ . '/../lib/Availability.php');
require_once (__DIR__ . '/../DB.php');

  DB::buildDatabase();
  $login   = 'winner';
  $savior  = 'supar';
  $weekday = 'Mon';
  $start   = '09:00';
  $end     = '19:00';
  $user    = User::create ( $login, 'supersecret', $login . '_Fname', $login . '_Lname', 'Manager', true, 10, '8675309', $login . '@uwm.edu' );
  $user    = User::create ( $savior, 'supersecret', $login . '_Fname', $login . '_Lname', 'Manager', true, 10, '8675309', $login . '@uwm.edu' );
  $avail   = Availability::create( $login, $weekday, $start, $end);
  
  var_dump(Availability::exists( $login, $weekday, $start, $end));
  $avail   = Availability::load( $login, $weekday, $start, $end);
  var_dump($avail);
  var_dump($avail->getInfo());
  Availability::delete( $login, $weekday, $start, $end);
  var_dump(Availability::exists( $login, $weekday, $start, $end));
  try { $avail = Availability::load($login, $weekday, $start, $end); }
  catch(Exception $e) { echo "Good, Exception Thrown\n"; }
    
/**
  sleep(2);
  $conn  = DB::getNewConnection();
  var_dump(DB::query($conn, 'SELECT * FROM Availability'));
  $conn->close();
  /**/
?>