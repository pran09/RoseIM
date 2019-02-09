<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width-device-width, initial-scale=1.0">
	<title>New Team</title>
	<link rel="stylesheet" href="normalize.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Nunito:400,300">
</head>
<body>
	<center><font size = "128" color="black">Rose</font><font size="128" color="red">IM</font></center>
	<form action= <?php echo $_SERVER['PHP_SELF']; ?> method="post">
		<h1>New Team</h1>
		<fieldset>
			<?php
				$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
				$stmt = $conn->prepare("SELECT name FROM Sport") or die($conn->error);
				$stmt->execute();
				$result = $stmt->get_result();
					echo '<label>Choose Sport:</label>';
					echo '<select name="Sport" required>';
				$sport = 5;
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					foreach ($row as $r) {
						echo '<option name="' . $r . '":>' . $r . '</option>';
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
						echo '<option name="' . $r . '">' . $r . '</option>';
					}
				}
				echo "</select";
				$stmt->close();
				mysqli_close($conn);
			?>
			<label>Team Name:</label>
			<input type="text" name="teamName">
		</fieldset>
		<button type="submit">Create Team</button>
	</form>

	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$sport = $_REQUEST['sport'];
			$league = $_REQUEST['league'];
			$name = $_REQUEST['teamName'];
			if ($sport == Unselected or $league == Unselected or $name == Unselected) {
				echo "Inputs cannot be empty";
			} else {
				$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");
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
          		$result = $stmt->get_result();
          		$stmt->close();
          		mysql_close();
			}
		}
	?>
</body>
