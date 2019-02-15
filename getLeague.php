<?php
	function debug_to_console( $data ) {
		$output = $data;
		if (is_array($output))
			$output = implode( ',', $output);

		echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
	}
	$test = "hello world";
	debug_to_console($test);


	$sport = $POST["sport"])
	debug_to_console($sport);

	$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

	$stmt = $conn->prepare("SELECT name FROM League WHERE sport = ?") or die($conn->error);
	$stmt->bind_param("s", $sport);
	$stmt->execute();
	$result = $stmt->get_result();

	$rows = array();
	while($r = mysqli_fetch_assoc($result)) {
    	$rows[] = $r;
	}
	echo json_encode($rows);
	 // echo "
		// <label>League:</label>
		// <select name='League' required>
		// <option name='team1'>team 1</option>
		// <option name='team2'>team 2</option>
		// </select>";
	mysql_close($conn);
?>