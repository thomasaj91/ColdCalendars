<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', 3 );
require_once (__DIR__ . '/../lib/Shift.php');
require_once (__DIR__ . '/../DB.php');

  DB::buildDatabase();
  $login = 'winnar';
  $savior = 'supar';
  $start = $end = DB::getSystemTime();
  $user  = User::create ( $login,  'supersecret', $login  . '_Fname', $login  . '_Lname', 'Manager', true, 10, '8675309', $login . '@uwm.edu' );
  $user  = User::create ( $savior, 'supersecret', $savior . '_Fname', $savior . '_Lname', 'Manager', true, 10, '8675309', $login . '@uwm.edu' );
  $shift = Shift::create( $login, $start, $end);
  $shift = Shift::load( $login, $start, $end);
  var_dump($shift);

  $shift->release();
  $shift = Shift::load( $login, $start, $end);
  var_dump($shift);

  $shift->pickUp($savior);
  $shift = Shift::load( $login, $start, $end);
  var_dump($shift);
  
  $shift->approve($savior);
  $shift = Shift::load( $savior, $start, $end);
  var_dump($shift);

  Shift::delete( $login, $start, $end);
  try { $shift = Shift::load( $savior, $start, $end); }
  catch(Exception $e) { echo "good,got error...\n".$e->getMessage(); }
  var_dump($shift);
    
  /**
  sleep(2);
  var_dump(DB::query($conn, 'SELECT * FROM Shift'));
  var_dump(DB::query($conn, 'SELECT * FROM Swap' ));
  /**/
?>