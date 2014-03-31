<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', 3 );
require_once (__DIR__ . '/../lib/Shift.php');
require_once (__DIR__ . '/../DB.php');

  DB::buildDatabase();
  $login = 'winner';
  $user  = User::create ( $login, 'supersecret', $login . '_Fname', $login . '_Lname', 'Employee', true, 10, '8675309', $login . '@uwm.edu' );
  $shift = Shift::create( $login, DB::getSystemTime(), DB::getSystemTime());

  $conn = DB::getNewConnection();
  $res = DB::query($conn, 'SELECT * FROM Shift');
  var_dump($res);
  $res = DB::query($conn, 'SELECT * FROM Swap');
  var_dump($res);
  
?>