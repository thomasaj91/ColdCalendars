<!DOCTYPE html>
    <html>
    <head>
    <title>
    Login page
    </title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#magic').click(important());
  });
  function important() {
    var x      = $('#unique').val();
    var retVal = $.ajax('coldcalendars-production.preumbra.net/rest.php?json='+x);
  }
</script>

    </head>
    <body>
    <h1 style="font-family:Comic Sans Ms;text-align="center";font-size:20pt;
    color:#00FF00;>
    Simple Login Page
    </h1>
    
    <form name="login" action="home.php" method="POST">
    Enter Your Data:<input id="unique" class="grabme" type="text" name="data"/>
    </form>
    <button id="magic"> send the data to the server </button>
    
    
    <script language="javascript">
    function check(form)/*function to check userid & password*/
    {
      var x      = $('#unique').val();
      var retVal = $.ajax('coldcalendars-production.preumbra.net/rest.php?json='+JSON.stringify(x));
      
    }
    </script>
    </body>
    </html>

