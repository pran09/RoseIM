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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Team Options</title>
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

	/* Popup container */
	.popup {
		position: relative;
		display: inline-block;
		cursor: pointer;
	}

	/* The actual popup (appears on top) */
	.popup .popuptext {
		visibility: hidden;
		width: 160px;
		background-color: #555;
		color: #fff;
		text-align: center;
		border-radius: 6px;
		padding: 8px 0;
		position: absolute;
		z-index: 1;
		bottom: 125%;
		left: 50%;
		margin-left: -80px;
	}

	/* Popup arrow */
	.popup .popuptext::after {
		content: "";
		position: absolute;
		top: 100%;
		left: 50%;
		margin-left: -5px;
		border-width: 5px;
		border-style: solid;
		border-color: #555 transparent transparent transparent;
	}

	/* Toggle this class when clicking on the popup container (hide and show the popup) */
	.popup .show {
		visibility: visible;
		-webkit-animation: fadeIn 0.5s;
		animation: fadeIn 0.5s
	}

	/* Add animation (fade in the popup) */
	@-webkit-keyframes fadeIn {
		from {opacity: 0;} 
		to {opacity: 1;}
	}

	@keyframes fadeIn {
		from {opacity: 0;}
		to {opacity:1 ;}
	}
</style>

</head>
<center><font size="200" color ="red">Rose</font><font size ="128" color="black">IM</font></center>

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


</br>
<script>
// When the user clicks on <div>, open the popup
function myFunction() {
	var popup = document.getElementById("myPopup");
	popup.classList.toggle("show");
}
</script>

<?php


$sport = $_POST["Sport"];
$league = $_POST["League"];
$name = $_POST["teamName"];
include 'datalogin.php';



$stmt = $conn->prepare("SELECT firstName, lastName FROM Person WHERE email = ?") or die($conn->error);
$stmt->bind_param("s", $_SESSION["emailAddress"]);

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_array()) {

	echo '<font size="48" color ="black">Welcome ' . $row['firstName'] . ' ' . $row['lastName'] . '!'.'</font>';
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

	echo '<span style = "font-size: 150%">Team <a href = TeamView.php?TeamName=', urlencode( $row['team_ID']), '> ' . $row['Team'] . ' </a> | ';
	echo '<a href = LeagueStandings.php?LeagueID=', urlencode( $row['league_ID']), '> ' . $row['League'] . ' </a> </span>';
	echo '<div class="popup" onclick="myFunction()"> <span style = "font-size: 150%">' . $row['Sport']. '</span><span class="popuptext" id="myPopup">' . $row['Rules'] . '</span> </div>';
	echo '<span style = "font-size: 150%"> | Rules : ' . $row['Rules'] . '</span>';
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