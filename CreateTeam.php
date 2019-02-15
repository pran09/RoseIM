<?php
session_start();

if (!isset($_SESSION['emailAddress'])) {
    header('location: Login.php');
    exit(); // <-- terminates the current script
  }

   if ($_SESSION['permission'] == 'Referee') {
    
    header('location: RefereeView.php');
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
	<center><font size = "200" color="red">Rose</font><font size="128" color="black">IM</font></center>
	<form action= "<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<h1>New Team</h1>
		<fieldset>
			<?php
				//$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
				$stmt = $conn->prepare("SELECT name FROM Sport") or die($conn->error);
				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Sport:</label>';
					echo '<select name="Sport">';
				$sport = 5;
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					foreach ($row as $r) {
						echo '<option value="' . $r . '":>' . $r . '</option>';
						$sport = $r;
					}
				}
				echo "</select>";

				$stmt->close();

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
				echo "</select";
				$stmt->close();
				mysqli_close($conn);
			?>
			<label>Team Name:</label>
			<input type="text" name="teamName" required>
		</fieldset>
		<input type="submit" name="submit" value="Create Team">
	</form>
	<?php
		if (isset($_POST['submit'])) {
			$sport = $_POST['Sport'];
			$league = $_POST['League'];
			$name = $_POST['teamName'];
			if ($sport == Unselected or $league == Unselected or $name == Unselected) {
				echo "Inputs cannot be empty";
			} else {
				//$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
				$leagueid;
				$get_league = $conn->prepare("SELECT league_ID FROM League WHERE name = ? AND sport = ?") or die($conn->error);
				$get_league->bind_param("ss", $league, $sport);
				$get_league->execute();
				$result = $get_league->get_result();
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					$i = 0;
					foreach ($row as $r) {
						$i++;
						$leagueid = $r;
						if ($i == 2) {
							echo "Something went wrong, Line 70: CreateTeam.php";
						}
					}
				}
				$get_league->close();
				$stmt = $conn->prepare("SELECT Create_Team(?, ?) as return_value") or die($conn->error);
				$stmt->bind_param("si", $name, $leagueid);
          		$stmt->execute();
          		$stmt->close();

          		$stmt = $conn->prepare("SELECT Create_PlaysOn(  (SELECT person_ID FROM Person WHERE email = ?)  ,  (SELECT team_ID FROM Team ORDER BY team_ID DESC LIMIT 1), 'Captain' )") or die($conn->error);
				$stmt->bind_param("s", $_SESSION["emailAddress"]);
          		$stmt->execute();
          		$stmt->close();

          		$_SESSION["permission"] = 'Captain';
				mysqli_close($conn);
          		function redirect($url, $statusCode = 303) {
					header('Location: ' . $url, true, $statusCode);
					die();
				}

				redirect("TeamSelect.php");
          		
			}
		}
	?>
</body>
