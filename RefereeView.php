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
<center><font size="200" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
</br>
<center><font size="120" color ="black">Referee Page</font></center>

</br>


<body>

	<form action="CreateGame.php" method="post">

		<button type="submit">Create Game</button>

	</div>

</form>

<form action="ScoreAGame.php" method="post">

	<button type="submit">Score A Game</button>

</div>

</form>



</body>
</html>



