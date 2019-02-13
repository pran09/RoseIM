<?php
session_start();

if (!isset($_SESSION['emailAddress'])) {
    header('location: Login.php');
    exit(); // <-- terminates the current script
  }

?>
<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>League Standings</title>
      <link rel="stylesheet" href="normalize.css">
      <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="css/main.css">

  </head>
   <center><font size="200" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
  <div class = "container" style = "background-color:#f4f7f8">
   </br>
   <h1>League Standings</h1>

  	<?php
	
    $LeagueID = $_GET['LeagueID'];

?> 

</div>


  <body>
	

  </body>
  
  
</html>



















