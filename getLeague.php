<!DOCTYPE html>
<html>
<body>
<?php
	$league = mysql_real_escape_string($_GET["league"]);
	echo $league;

	$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

	$stmt = $conn->prepare("SELECT name FROM League WHERE sport = ?") or die($conn->error);
	$stmt->bind_param("s", $q);
	$stmt->execute();
	$result = $stmt->get_result();

	echo "<select>";
	while ($row = $result->fetch_row()) {
		echo "<option value=\"" .$row[0]. "\">" .$row[0]."</option>";
	}
	echo "</select>";
	// echo "
	// 	<label>League:</label>
	// 	<select name='League' required>
	// 	<option name='team1'>team 1</option>
	// 	<option name='team2>team 2</option>
	// 	</select>";
	// mysql_close($conn);
?>
</body>
</html>