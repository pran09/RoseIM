<?php
session_start();
if(!isset($_SESSION['emailAddress'])) {
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
    	<title>Join a Team!</title>
    	<link rel="stylesheet" href="normalize.css">
    	<link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    	<script src="ajax.js"></script>
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
	<center><font size="200" color ="red">Rose</font><font size ="128" color="black">IM</font></center>
	<form action="TeamSelect.php" method="post">
		<h1>Join Team</h1>
		<fieldset>
			<?php
			function debug_to_console( $data ) {
				$output = $data;
				if (is_array($output))
					$output = implode( ',', $output);

				echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
			}

			include 'datalogin.php';

			$stmt = $conn->prepare("SELECT name FROM Sport") or die($conn->error);

			$stmt->execute();
			$result = $stmt->get_result();
			echo '<div id="sportDiv">';
			echo '<label>Sport:</label>';
			echo '<select name="Sport" id="sport">';
			$aResult = 5;
			while ($row = $result->fetch_array(MYSQLI_NUM)) {
				foreach ($row as $r) {
					echo '<option value="' . $r . '">' . $r . '</option>';
					debug_to_console($r);
					$aResult = $r;
				}
			}
			echo '</select>';
			echo '</div>';
			echo "<br />";
			$stmt->close();


			// $stmt = $conn->prepare("SELECT League.name FROM League, Sport WHERE Sport.name = League.sport AND Sport.name = '" . $aResult . "'") or die($conn->error);

   //    			$stmt->execute();
   //    			$result = $stmt->get_result();
			// 	echo '<label>League: </label>';
			// 	echo '<select name="League" required>';
   //    			while ($row = $result->fetch_array(MYSQLI_NUM))
   //    			{
   //        		foreach ($row as $r)
   //        			{
			// 			echo '<option name="' . $r . '">' . $r . '</option>';
   //        			}

   //    			}
			// echo '</select>';

			// $stmt->close();
			// $stmt = $conn->prepare("SELECT name FROM Team") or die($conn->error);

   //    			$stmt->execute();
   //    			$result = $stmt->get_result();
			// 	echo '<label>Team: </label>';
			// 	echo '<select name="Team" required>';
   //    			while ($row = $result->fetch_array(MYSQLI_NUM)) {
   //        			foreach ($row as $r) {
			// 			echo '<option name="' . $r . '">' . $r . '</option>';
   //        			}
   //    			}
			// echo '</select>';
  	// 		$stmt->close();
			mysqli_close($conn);
			?>
			<div id="league"></div>
			<div id="team"></div>
		</fieldset>
		<button type="submit">Join Team</button>
	</form>
</body>
</html>