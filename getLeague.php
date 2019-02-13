<!DOCTYPE html>
<html>
<body>
<?php
	$q = intval($_GET['q']);

	$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

	$stmt = $conn->prepare("SELECT name FROM League WHERE sport = ?") or die($conn->error);
	$stmt->bind_param("s", $q);
	$stmt->execute();
	$result = $stmt->get_result();

	$data = array();
	while ($row = fetch_assoc($result)) {
		$data[] = $row;
	}
	echo json_encode($data);
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