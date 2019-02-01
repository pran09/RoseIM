<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width-device-width, initial-scale=1.0">
	<title>New Team</title>
	<link rel="stylesheet" href="normalize.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Nunito:400,300">
</head>
<body>
	<center><font size = "128" color="black">Rose</font><font size="128" color="red">IM</font></center>
	<form action="TeamSelect.php" method="post">
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
</body>
