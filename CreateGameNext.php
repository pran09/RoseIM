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
	<title>Create a Team!</title>
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
<center><font size = "200" color="black">Rose</font><font size="128" color="red">IM</font></center>



<form action= "<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<h1>Create A Game:</h1>

<?php

			$_SESSION['Sport'] = $_POST['Sport'];
			$_SESSION['League'] = $_POST['League'];
			$_SESSION['Referee'] = $_POST['Referee'];
			$_SESSION['Facility'] = $_POST['Facility'];
			$_SESSION['Time and Date'] = $_POST['Time and Date'];

			echo $_POST['Time and Date'];

		$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");


		$stmt = $conn->prepare("SELECT name, team_ID FROM Team WHERE league = (SELECT league_ID FROM League WHERE sport = ? AND name = ?)") or die($conn->error);
		$stmt->bind_param("ss", $_SESSION['Sport'], $_SESSION['League']);

				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Home Team:</label>';
					echo '<select name="Home Team">';
				$sport = 5;
				while ($row = $result->fetch_array()) {
					
						echo '<option value="' . $row['team_ID'] . '">' . $row['name'] . '</option>';
						
					
				}
				echo "</select>";

				$stmt->close();

echo '</br>';


				$stmt = $conn->prepare("SELECT name, team_ID FROM Team WHERE league = (SELECT league_ID FROM League WHERE sport = ? AND name = ?)") or die($conn->error);
		$stmt->bind_param("ss", $_SESSION['Sport'], $_SESSION['League']);

				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Away Team:</label>';
					echo '<select name="Away Team">';
				$sport = 5;
				while ($row = $result->fetch_array()) {
					
						echo '<option value="' . $row['team_ID'] . '">' . $row['name'] . '</option>';
						
					
				}
				echo "</select>";

				$stmt->close();


echo '</br>';

		?>
</br>

		</fieldset>
		<input type="submit" name="SuperSub" value="Create Game">
	</form>
	<?php
		if (isset($_POST['SuperSub'])) {
			$Sport = $_SESSION['Sport'];
			$League = $_SESSION['League'];
			$HomeTeam = $_POST['Home Team'];
			$AwayTeam = $_POST['Away Team'];
			$Referee = $_SESSION['Referee'];
			$Facility = $_SESSION['Facility'];
			$DateTime = $_SESSION['Time and Date'];
			echo 'here1';
			echo $Sport;
			echo $League;
			echo $HomeTeam;
			echo $AwayTeam;
			echo $Referee;
			echo $Facility;
			echo $DateTime;
			echo 'here2';

			if($HomeTeam == $AwayTeam){
				echo 'The home team and away team must be different.';
			}
			else{

				$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
				
				$stmt = $conn->prepare("SELECT Create_Game(?, ?, ?, ?, ?) as return_value") or die($conn->error);
				$stmt->bind_param("ssss", $Sport, $Referee, $Facility, $League, $DateTime);
          		$stmt->execute();
          		$stmt->close();

          		$stmt = $conn->prepare("SELECT Create_Plays(null, null, ?, ?, (SELECT game_ID FROM Game ORDER BY game_ID DESC LIMIT 1))") or die($conn->error);
				$stmt->bind_param("s,s", $HomeTeam, $AwayTeam);
          		$stmt->execute();
          		$stmt->close();

          	


				mysqli_close($conn);
          		function redirect($url, $statusCode = 303) {
					header('Location: ' . $url, true, $statusCode);
					die();
				}
				mysql_close();
				redirect("RefereeView.php");
          		
			}
		}
	?>

</body>


