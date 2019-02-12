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



<form>
	<h1>Create A Game:</h1>

<?php
		$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
		$sport = 5;

		$stmt = $conn->prepare("SELECT League.name FROM League, Sport WHERE Sport.name = League.sport AND Sport.name = '" . $sport . "'") or die($conn->error);
				$stmt->execute();
				$result = $stmt->get_result();
					echo "<label>League:</label>";
					echo '<select name="League" required>';
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					foreach ($row as $r) {
						echo '<option value="' . $r . '">' . $r . '</option>';
					}
				}
				echo "</select>";
				$stmt->close();


echo '</br>';
				
				$stmt = $conn->prepare("SELECT name FROM Team") or die($conn->error);
				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Home Team:</label>';
					echo '<select name="Home Team">';
				$sport = 5;
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					foreach ($row as $r) {
						echo '<option value="' . $r . '":>' . $r . '</option>';
						$sport = $r;
					}
				}
				echo "</select>";

				$stmt->close();

echo '</br>';


				$stmt = $conn->prepare("SELECT name FROM Team") or die($conn->error);
				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Away Team:</label>';
					echo '<select name="Away Team">';
				$sport = 5;
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					foreach ($row as $r) {
						echo '<option value="' . $r . '":>' . $r . '</option>';
						$sport = $r;
					}
				}
				echo "</select>";

				$stmt->close();


echo '</br>';

				$stmt = $conn->prepare("SELECT name FROM Person JOIN Referee ON Referee.person_ID = Person.person_ID") or die($conn->error);
				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Referee:</label>';
					echo '<select name="Referee">';
				$sport = 5;
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					foreach ($row as $r) {
						echo '<option value="' . $r . '":>' . $r . '</option>';
						$sport = $r;
					}
				}
				echo "</select>";

				$stmt->close();


echo '</br>';



				$stmt = $conn->prepare("SELECT location FROM Facility") or die($conn->error);
				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Facility:</label>';
					echo '<select name="Facility">';
				$sport = 5;
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					foreach ($row as $r) {
						echo '<option value="' . $r . '":>' . $r . '</option>';
						$sport = $r;
					}
				}
				echo "</select>";

				$stmt->close();
echo '</br>';
				
				mysqli_close($conn);
			?>

			<label>Date and Time (YYYY-MM-DD HH:MM:SS):</label>
			<input type="text" name="Time and Date" required>


		</fieldset>
		<input type="submit" name="submit" value="Create Game"/>
	</form>
	<?php
		if (isset($_POST['submit'])) {
			$League = $_POST['League'];
			$HomeTeam = $_POST['Home Team'];
			$AwayTeam = $_POST['Away Team'];
			$Referee = $_POST['Referee'];
			$Facility = $_POST['Facility'];
			$DateTime = $_POST['Time and Date'];

			if($HomeTeam == $AwayTeam){
				echo 'The home team and away team must be differe.';
			}
			else{

				$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
				
				// $stmt = $conn->prepare("SELECT Create_Team(?, ?) as return_value") or die($conn->error);
				// $stmt->bind_param("si", $name, $leagueid);
    //       		$stmt->execute();
    //       		$stmt->close();

    //       		$stmt = $conn->prepare("SELECT Create_PlaysOn(  (SELECT person_ID FROM Person WHERE email = ?)  ,  (SELECT team_ID FROM Team ORDER BY team_ID DESC LIMIT 1), 'Captain' )") or die($conn->error);
				// $stmt->bind_param("s", $_SESSION["emailAddress"]);
    //       		$stmt->execute();
    //       		$stmt->close();

          	


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


