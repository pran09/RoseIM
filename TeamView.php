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
	
$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

$stmt = $conn->prepare("CALL Get_Schedule (?)") or die($conn->error);
$stmt->bind_param("s", $_GET['TeamName']);

      			$stmt->execute();
      			$result = $stmt->get_result();
				   echo ' <font size="118">My Schedule</font>';
      			while ($row = $result->fetch_array())
      			{
          		
						echo '<span style = "font-size: 150%">' . $row['Team1'] . ' VS ' . $row['Team2']. ' AT ' .$row['StartTime'] .' | ' . $row['Location']. '</span>';
						echo '</br>';

      			}
			

  			$stmt->close();
			
mysqli_close($conn);

?> 

</div>


  <body>
	

  </body>
  
  
</html>



















