<?php
include 'datalogin.php';

function debug_to_console( $data ) {
	$output = $data;
	if (is_array($output))
		$output = implode( ',', $output);

	echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

$sport = isset($_GET['sport'])? $_GET['sport'] : null;
debug_to_console($sport);

$stmt = $conn->prepare("SELECT league_ID, name FROM League WHERE sport = ?") or die($conn->error);
$stmt->bind_param("s", $sport);
$stmt->execute();
$result = $stmt->get_result();

$rows = array();
$returnString = '<label>League:</label> <select name="League" id="leagueSelect">';
while ($row = $result->fetch_array()) {
	$returnString .= '\n<option value="' . $row['league_ID'] . '">' . $row['name'] . '</option>';
}
$returnString .= '</select>';
mysqli_close($conn);
echo $returnString;
?>