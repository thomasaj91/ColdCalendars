<?php
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Manager view</title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script src='../js/main.js'></script>
  <script src="../js/queue.js"></script> 
  <style type="text/css">
  <!-- 
 
  #navbar ul { 
          margin: 0; 
          padding: 5px; 
          list-style-type: none; 
          text-align: center; 
          background-color: #000; 
          } 
 
  #navbar ul li {  
          display: inline; 
          } 
  
  #navbar ul li a { 
          text-decoration: none; 
          padding: .2em 1em; 
          color: #fff; 
          background-color: #000; 
          } 
 
  #navbar ul li a:hover { 
          color: #000; 
          background-color: #fff; 
          } 
  #footer  { 
          font-size: 10px; 
          }
  #container {
          width: 500px;
          height: 500px;
          background-color: #FDA;
          margin: 0 auto;
          overflow: hidden;
}     
  --> 
  </style> 
  </head> 
  <body>

  <div id="wrapper">
    <header>
      <h1 style="font-family:Comic Sans Ms";font-size:20pt; color:#00FF00;><center> Manager's View</center></h1>
  <div id="navbar"> 
    <ul> 
        <li><a href="#">Home</a></li> 
        <li><a href="#">Requests</a></li> 
        <li><a href="#">Schedules</a></li> 
        <li><a href="#">Reports</a></li> 
        <li><a href="#">Log Out</a></li> 
    </ul> 
  </div> 
  </header>

  </div>
  <hr size="3" color ="black">
   <h1>Queue</h1>
  <ul id="Display_Queue"></ul>
  <br>
  <br>
  <footer id="footer" >
  <p>Copyright Â© 2014 Cold Calendars</p>
  </footer>
</body>
</html>
