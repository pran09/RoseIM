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
      <title>Team Information</title>
      <link rel="stylesheet" href="normalize.css">
      <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="css/main.css">
  </head>
   <center><font size="200" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
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
          	

            $st = $conn->prepare("SELECT name FROM Team WHERE  team_ID = ?") or die($conn->error);
            $st->bind_param("s", $row['Team1_ID']);

            $st->execute();
            $res = $st->get_result();
            while ($ro = $res->fetch_array())
            {
              $_SESSION['Team1'] = $ro['name'];
            }
            $st->close();

              $st = $conn->prepare("SELECT name FROM Team WHERE  team_ID = ?") or die($conn->error);
            $st->bind_param("s", $row['Team2_ID']);

            $st->execute();
            $res = $st->get_result();
            while ($ro = $res->fetch_array())
            {
              $_SESSION['Team2'] = $ro['name'];
            }
            $st->close();

            echo '<span style = "font-size: 150%">';
						echo '<a href = TeamView.php?TeamName=', urlencode( $row['Team1_ID']), '> ' . $_SESSION['Team1'] . ' </a>';
           // echo $row['Team1'];
            echo ' VS ';
          //  echo $row['Team2'];
            echo '<a href = TeamView.php?TeamName=', urlencode( $row['Team2_ID']), '> ' . $_SESSION['Team2'] . ' </a>';
            echo ' AT ' . $row['StartTime'] . '  ' . $row['Location'] . ' | Score: ' . $row['Team1Score'] . ' - ' . $row['Team2Score'];
            echo '</span>';
						echo '</br>';
        

      			}
			

  			$stmt->close();





        $stmt = $conn->prepare("CALL Get_Roster (?)") or die($conn->error);
$stmt->bind_param("s", $_GET['TeamName']);

            $stmt->execute();
            $result = $stmt->get_result();
           echo ' <font size="118">Roster</font>';
           echo '</br>';
            while ($row = $result->fetch_array())
            {
              
            echo '<span style = "font-size: 150%">' . $row['First'] . ' ' . $row['Last']. ' | ' . $row['Role'] . '</span>';
            echo '</br>';

            }
      

        $stmt->close();


			
mysqli_close($conn);

?> 

</div>


  <body>
	

  </body>
  
  
</html>



















