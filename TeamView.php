<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Team Information</title>
      <link rel="stylesheet" href="normalize.css">
      <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="css/main.css">
  </head>
   <center><font size="128" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
  <div class = "container" style = "background-color:#f4f7f8">
   <font size="118">My Teams</font>
   </br>
   
   
  	<?php
	
	$TeamName = $_GET['TeamName'];
	echo $TeamName;
	
/*$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

$stmt = $conn->prepare("SELECT Team.name AS Team, League.name AS League, Sport.name AS Sport FROM Sport JOIN League ON Sport.name = League.sport JOIN Team ON Team.league = league_ID; ") or die($conn->error);

      			$stmt->execute();
      			$result = $stmt->get_result();
				
      			while ($row = $result->fetch_array())
      			{
          		
						echo '<span style = "font-size: 150%">Team <a href = TeamView.php?TeamName=', urlencode( $row['Team']), '> ' . $row['Team'] . ' </a> | ' . $row['League']. ' ' . $row['Sport']. '</span>';
						echo '</br>';

      			}
			

  			$stmt->close();
			*/
mysqli_close($conn);

?> 

</div>


  <body>
	

  </body>
  
  
</html>



















