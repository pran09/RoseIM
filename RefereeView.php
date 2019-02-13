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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Referee Page</title>
	<link rel="stylesheet" href="normalize.css">
	<link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/main.css">
	<style>
	input[type=submit] {
  			padding: 9px 18px 9px 18px;
 			color: #FFF;
 			background-color: #800000;
  			font-size: 16px;
  			text-align: center;
  			font-style: normal;
  			border-radius: 5px;
  			width: 50%;
  			border: 1px solid #000000;
  			border-width: 1px 1px 3px;
  			box-shadow: 0 -1px 0 rgba(100,100,100,0.1) inset;
  			margin-bottom: 7px;
		}
	</style>

</head>
<center><font size="200" color ="red">Rose</font><font size ="128" color="black">IM</font></center>
</br>
<center><font size="120" color ="black">Referee Page</font></center>

</br>


<div class = "container" style = "background-color:#f4f7f8">

	<form align="center" method="post" action= "<?php echo $_SERVER['PHP_SELF'];?>">
  <label>
  <input type="submit" name = "submit" value="Log Out">
  </label>
</form>
<?php
		if (isset($_POST['submit'])) {
          		$_SESSION["emailAddress"] = null;
          		$_SESSION["permission"] = null;
			
          		function redirect($url, $statusCode = 303) {
					header('Location: ' . $url, true, $statusCode);
					die();
				}
				redirect("Login.php");
			}
	?>


<?php
$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
	$stmt = $conn->prepare("SELECT firstName, lastName FROM Person WHERE email = ?") or die($conn->error);
	 $stmt->bind_param("s", $_SESSION["emailAddress"]);

	$stmt->execute();
	$result = $stmt->get_result();

	while ($row = $result->fetch_array()) {

		echo '<font size="48" color ="black">Welcome ' . $row['firstName'] . ' ' . $row['lastName'] . '!'.'</font>';
		echo '</br>';
	}

	$stmt->close();
	mysqli_close($conn);

	?>

	<form action="CreateGame.php" method="post">

		<button type="submit">Create Game</button>

	

</form>





<?php

	$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

echo '<font size="118">My Games</font>';
	echo '</br>';



	$stmt = $conn->prepare("CALL Get_Games_Ref(?)") or die($conn->error);
	 $stmt->bind_param("s", $_SESSION["emailAddress"]);

	$stmt->execute();
	$result = $stmt->get_result();

	while ($row = $result->fetch_array()) {

		echo '<span style = "font-size: 150%">';
						echo '<a href = TeamView.php?TeamName=', urlencode( $row['Team1_ID']), '> ' . $row['Team1'] . ' </a>';
           // echo $row['Team1'];
            echo ' VS ';
          //  echo $row['Team2'];
            echo '<a href = TeamView.php?TeamName=', urlencode( $row['Team2_ID']), '> ' . $row['Team2'] . ' </a>';
            echo ' AT ' . $row['StartTime'] . '  ' . $row['Location'] . ' | Score: ' . $row['Team1Score'] . ' - ' . $row['Team2Score'];
            echo '<a href = ScoreAGame.php?GameID=', urlencode( $row['game_ID']), '>  | SCORE GAME </a>';
            echo '</span>';
            
						echo '</br>';
	}

	$stmt->close();
	mysqli_close($conn);

	?>



</div>
</html>



