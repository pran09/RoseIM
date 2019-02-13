<?php
	session_start();
	if (!isset($_SESSION['emailAddress'])) {
		header('location: Login.php');
		exit();
	}

	if ($_SESSION['permission'] != 'Referee') {
		
		header('location: TeamSelect.php');
		exit();
	}
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width-device-width, initial-scale=1.0">
	<title>Score a Game!</title>
	<link rel="stylesheet" href="normalize.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Nunito:400,300">
	<style>
		input[type=submit] {
  			padding: 19px 39px 18px 39px;
 			color: #FFF;
 			background-color: #800000;
  			font-size: 18px;
  			text-align: center;
  			font-style: normal;
  			border-radius: 5px;
  			width: 100%;
  			border: 1px solid #000000;
  			border-width: 1px 1px 3px;
  			box-shadow: 0 -1px 0 rgba(255,255,255,0.1) inset;
  			margin-bottom: 10px;
		}

		input[type=text] {
			width: 100%;
			padding: 12px 20px;
			margin: 8px 0;
			box-sizing: border-box;
			border: 3px solid #ccc;
			-webkit-transition: 0.5s;
			transition: 0.5s;
			outline: none;
		}

		input[type=text]:focus {
			border: 3px solid #555;
		}
	</style>
</head>

<body>
<center><font size = "200" color="red">Rose</font><font size="128" color="black">IM</font></center>



<form action= "<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<h1>Score A Game:</h1>

<?php
		$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");





		$stmt = $conn->prepare("SELECT (SELECT name FROM Team WHERE team_ID = team1) as team1, (SELECT name FROM Team WHERE team_ID = team2) as team2 FROM Game WHERE game_ID = ?") or die($conn->error);
		$stmt->bind_param("s", $_GET["GameID"]);
				$stmt->execute();
				$result = $stmt->get_result();
					
				while ($row = $result->fetch_array()) {
					echo '<label>'. $row['team1'].' Score</label>';
					echo '<input type="text" name="Team1" required>';

					echo '</br>';

					echo '<label>'. $row['team2'].' Score</label>';
					echo '<input type="text" name="Team2" required>';
					
					
				}
				echo "</select>";
				$stmt->close();


echo '</br>';
			

	
				
				mysqli_close($conn);
			?>


		</fieldset>
		<input type="submit" name="submit" value="Next">
	</form>


</body>


