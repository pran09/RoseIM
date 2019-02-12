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
    <!-- <link rel="stylesheet" href="css/main.css"> -->
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
<script type="text/javascript">
	function showLeague(league) {
		if (league == "") {
			document.getElementById("txtHint").innerHTML="";
			return;
		} else {
			if(window.XMLHttpRequest) {
			xmlhttp = new XMLHttpRequest();
			}
			xmlhttp.onreadystatechange=function() {
				if (this.readyState==4 && this.status==200) {
					document.getElementById("txtHint").innerHTML=this.responseText;
				}
			}
			xmlhttp.open("GET", "getLeague.php?q=" + league, true);
			xmlhttp.send();
		}
	}
</script>
<body>
 	<center><font size="200" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
    <form action="TeamSelect.php" method="post">
		<h1>Join Team</h1>
      	<fieldset>
        	<?php
          		$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

				$stmt = $conn->prepare("SELECT name FROM Sport") or die($conn->error);

      			$stmt->execute();
      			$result = $stmt->get_result();
				echo '<label>Sport:</label>';
				echo '<select name="Sport">';
				$aResult = 5;
      			while ($row = $result->fetch_array(MYSQLI_NUM)) {
          			foreach ($row as $r) {
						echo '<option value="' . $r . '">' . $r . '</option>';
						$aResult = $r;
						print(this.name);
          			}
      			}
			echo '</select>';

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
	<div id="txtHint"><b>Leagues will be listed here</b></div>
	<div id="txtHint2"><b>Teams will be listed here</b></div>
</fieldset>
  <button type="submit">Join Team</button>
    </form>
  </body>
</html>

