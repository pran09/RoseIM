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
   </br>
   
  	<?php
	
$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");




$stmt = $conn->prepare("SELECT name FROM Team WHERE team_ID = ?") or die($conn->error);
$stmt->bind_param("s", $_GET['TeamName']);

            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_array())
            {
              
            echo '<center><font size="118">'  .  $row['name']  .  '</font></center>';
            echo '</br>';

            }
      

        $stmt->close();








$stmt = $conn->prepare("SELECT wins, losses FROM Team WHERE team_ID = ?") or die($conn->error);
$stmt->bind_param("s", $_GET['TeamName']);

            $stmt->execute();
            $result = $stmt->get_result();
           echo ' <font size="118">Record</font>';
           echo '</br>';
            while ($row = $result->fetch_array())
            {
              
            echo '<span style = "font-size: 150%">' . $row['wins'] . ' Wins and ' . $row['losses']. ' Losses' . '</span>';
            echo '</br>';

            }
      

        $stmt->close();






$stmt = $conn->prepare("CALL Get_Schedule (?)") or die($conn->error);
$stmt->bind_param("s", $_GET['TeamName']);

      			$stmt->execute();
      			$result = $stmt->get_result();
				   echo ' <font size="118">Schedule</font>';
           echo '</br>';
      			while ($row = $result->fetch_array())
      			{
          		
						echo '<span style = "font-size: 150%">' . '<a href = TeamView.php?TeamName=', urlencode( $row['Team1']), '> ' . $row['Team1'] . ' </a>' . ' VS ' . '<a href = TeamView.php?TeamName=', urlencode( $row['Team2']), '> ' . $row['Team2'] . ' </a>'. ' AT ' .$row['StartTime'] .'  ' . $row['Location']. ' | Score: ' . $row['Team1Score'] . ' - ' . $row['Team2Score'] . '</span>';
						echo '</br>';

      			}
			<a href = TeamView.php?TeamName=', urlencode( $row['Team1']), '> ' . $row['Team1'] . ' </a>

  			$stmt->close();





        $stmt = $conn->prepare("CALL Get_Roster (?)") or die($conn->error);
$stmt->bind_param("s", $_GET['TeamName']);

            $stmt->execute();
            $result = $stmt->get_result();
           echo ' <font size="118">Roster</font>';
           echo '</br>';
            while ($row = $result->fetch_array())
            {
              
            echo '<span style = "font-size: 150%">' . $row['First'] . ' ' . $row['Last']. ' ' . $row['Role'] . '</span>';
            echo '</br>';

            }
      

        $stmt->close();


			
mysqli_close($conn);

?> 

</div>


  <body>
	

  </body>
  
  
</html>



















