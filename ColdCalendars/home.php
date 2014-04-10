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
    <title>
    Login page
    </title>
    </head>
    <body>
    <h1 style="font-family:Comic Sans Ms; text-align="center"; font-size:20pt; color:#00FF00;">
    Cold Calendars
    </h1>
    <?php
      if(isset($error) && $error)
       echo '<div>Bad Username and/or password</div>';
    ?>
    <form name="login" action="home.php" method="POST">
    Username<input type="text" name="login"/> </br>
    Password<input type="password" name="passwd"/> </br>
<!--<input type="button" onclick="return check(this.form)" value="Login"/>
-->
    <input type="submit" value="Login"/>
    <input type="reset" value="Cancel"/>
    </form>
    
    <p> It's like Hot Schedules, but <em>cooler!</em> </p>
    
    </body>
    </html>

