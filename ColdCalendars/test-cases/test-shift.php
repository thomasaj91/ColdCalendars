<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', 3 );
require_once (__DIR__ . '/../lib/Shift.php');
require_once (__DIR__ . '/../DB.php');

  DB::buildDatabase();
  $login = 'winner';
  $savior = 'supar';
  $start = $end = DB::getSystemTime();
  $user  = User::create ( $login, 'supersecret', $login . '_Fname', $login . '_Lname', 'Employee', true, 10, '8675309', $login . '@uwm.edu' );
  $user  = User::create ( $savior, 'supersecret', $login . '_Fname', $login . '_Lname', 'Employee', true, 10, '8675309', $login . '@uwm.edu' );
  $shift = Shift::create( $login, $start, $end);
  $shift = Shift::load( $login, $start, $end);
  var_dump($shift);

  $shift->relsease();
  $shift = Shift::load( $login, $start, $end);
  var_dump($shift);

  $shift->pickUp($savior);
  $shift = Shift::load( $login, $start, $end);
  var_dump($shift);
  
  
  /*
  $conn = DB::getNewConnection();
  $res = DB::query($conn, 'SELECT * FROM Shift');
  var_dump($res);
  $res = DB::query($conn, 'SELECT * FROM Swap');
  var_dump($res);
  */
?>