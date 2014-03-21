<?php
error_reporting(E_ALL);
ini_set('display_errors', 3);
//  var_dump($_POST);

/**/
  if(isset($_POST) && isset($_POST['login']) && isset($_POST['passwd'])) {

  	include_once(__DIR__.'/user/User.php');
  	$user = User::load($_POST['login']);
  	if(!$user->correctPassword($_POST['passwd']))
  	  die('bad username & password');
  	else if(isset($_COOKIE['authToken']) && $user->isAuthenticated($_COOKIE['authToken'])) { ; }
  	else {
  		$user->generateAuthenticationToken();
  		
  		setcookie('login',$_POST['login'],time()+User::getAuthenticationTimeOut());
  		setcookie('authToken',$user->getAuthToken(),time()+User::getAuthenticationTimeOut());
  		 
  		$user->updateCommunication();
  		$user->commitUserData();
  		header('Location: contacts.php');
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
    <h1 style="font-family:Comic Sans Ms;text-align="center";font-size:20pt;
    color:#00FF00;>
    Simple Login Page
    </h1>
    
    <form name="login" action="home.php" method="POST">
    Username<input type="text" name="login"/>
    Password<input type="password" name="passwd"/>
<!--<input type="button" onclick="return check(this.form)" value="Login"/>
-->
    <input type="submit" value="Login"/>
    <input type="reset" value="Cancel"/>
    </form>
    <script language="javascript">
    </script>
    </body>
    </html>

