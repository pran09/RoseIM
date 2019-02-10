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
	<title>Team Options</title>
	<link rel="stylesheet" href="normalize.css">
	<link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/main.css">

	<form align="right" name="form1" method="post" action= "<?php echo $_SERVER['PHP_SELF'];?>">
  <label class="logoutLblPos">
  <input name="submit2" type="submit" id="submit2" value="Log Out">
  </label>
</form>
<?php
		if (isset($_POST['submit'])) {
          		$_SESSION["emailAddress"] = null;
			
          		function redirect($url, $statusCode = 303) {
					header('Location: ' . $url, true, $statusCode);
					die();
				}
				redirect("Login.php");
          		mysql_close();
			}
		}
	?>
</head>
<center><font size="128" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
<div class = "container" style = "background-color:#f4f7f8">
</br>


<?php

	$sport = $_POST["Sport"];
	$league = $_POST["League"];
	$name = $_POST["teamName"];
	$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");



	$stmt = $conn->prepare("SELECT firstName, lastName FROM Person WHERE email = ?") or die($conn->error);
	 $stmt->bind_param("s", $_SESSION["emailAddress"]);

	$stmt->execute();
	$result = $stmt->get_result();

	while ($row = $result->fetch_array()) {

		echo '<center><font size="48" color ="black">' . $row['firstName'] . ' ' . $row['lastName'] .'</font></center>';
		echo '</br>';
	}

	$stmt->close();

	echo '<font size="118">My Teams</font>';
	echo '</br>';



	$stmt = $conn->prepare("CALL Get_Teams(?)") or die($conn->error);
	 $stmt->bind_param("s", $_SESSION["emailAddress"]);

	$stmt->execute();
	$result = $stmt->get_result();

	while ($row = $result->fetch_array()) {

		echo '<span style = "font-size: 150%">Team <a href = TeamView.php?TeamName=', urlencode( $row['team_ID']), '> ' . $row['Team'] . ' </a> | ' . $row['League']. ' ' . $row['Sport']. '</span>';
		echo '</br>';
	}

	$stmt->close();
	mysqli_close($conn);
?>

</div>


<body>

	<form action="JoinTeam.php" method="post">

		<button type="submit">Join Team</button>

	</div>

</form>

<form action="CreateTeam.php" method="post">

	<button type="submit">Create Team</button>

</div>

</form>



</body>
</html>































































































































































































































































