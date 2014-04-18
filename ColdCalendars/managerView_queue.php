<?php
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Manager view</title>
  <link rel="stylesheet" href="../css/coldcalendar.css" />
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script src='../js/main.js'></script>
  <script src="../js/queue.js"></script> 
  </head> 
  <body>

  <div id="wrapper" class="center">
    <header>
      <h1 style="font-family:Comic Sans Ms";font-size:20pt; color:#00FF00;><center> Manager's View</center></h1>
  <div id="navbar"> 
    <ul>
        <li><a href="contacts.php">Contacts</a></li> 
        <li><a href="managerView_queue.php">Requests</a></li> 
        <li><a href="schedule.php">Schedule</a></li> 
        <li><a href="#">Reports</a></li> 
        <li><a id='Logout' href="#" onclick="logUserOut();return false;">Log Out</a></li>
    </ul> 
  </div> 
  </header>

  </div>
  <div class="center">
    <hr size="3" color ="black">
     <h1>Queue</h1>
    <ul id="Display_Queue">
    </ul>
  </div>
  <br>
  <br>
  <footer id="footer" class="center">
  <p>Copyright Â© 2014 Cold Calendars</p>
  </footer>
</body>
</html>
