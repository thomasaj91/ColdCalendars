<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
require_once(__DIR__.'/auth/authentication.php');
require_once(__DIR__.'/lib/User.php');
//  var_dump($_POST);

/**/
  if(isset($_POST) && isset($_POST['login']) && isset($_POST['passwd'])) {

  	$error = false;
  	try {
  		$user = User::load($_POST['login']);
  	}
  	catch(exception $e){
  		$error = true;
  	}
  	if(!$error && !$user->correctPassword($_POST['passwd']))
  	  //die('bad username & password');
  	  $error = true;
  	if(!$error)  {
	  	if(isset($_COOKIE['authToken']) && $user->isAuthenticated($_COOKIE['authToken'])) {
	  		updateSessionCommunication($user,$_COOKIE['login'],$_COOKIE['authToken']);
	  	}
	  	else {
	  		$user->generateAuthenticationToken();
	  		/* Set Cookie info twice */
	  		updateSessionCommunication($user,$_POST['login'],$user->getAuthToken());
	  		header('Location: contacts.php');
	  	}
  	}
  }
 /**/
?>
	<!DOCTYPE html>
    <html>
    	<head>
    		<meta charset='utf-8' />
    		<title>
    			Login page
    		</title>
    		<link rel="stylesheet" href="../css/coldcalendar.css" />
    	</head>
    <body>
    <h1 style="font-family:Comic Sans Ms; text-align=center; font-size:20pt; color:#99AAFF;">
    Cold Calendars
    </h1>
    <?php
      if(isset($error) && $error)
       echo '<div>Bad Username and/or password</div>';
    ?>
    <form name="login" action="home.php" method="POST" class='center'>
    	<table class='center'>
    		<tr>
    			<td>Username: <input type="text" name="login"/></td>
    		</tr>
    		<tr>
    			<td>Password: <input type="password" name="passwd"/></td>
    		</tr>
    	</table>
    	<input type="submit" value="Login"/>
    </form>
    
    <p> It's like Hot Schedules, but <em>cooler!</em> </p>
    
    </body>
    </html>
