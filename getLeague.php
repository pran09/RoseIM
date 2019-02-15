<?php
include 'datalogin.php';

$sport = isset($_GET['sport'])? $_GET['sport'] : null;

$stmt = $conn->prepare("SELECT league_ID, name FROM League WHERE sport = ?") or die($conn->error);
$stmt->bind_param("s", $sport);
$stmt->execute();
$result = $stmt->get_result();

$rows = array();
$returnString = '<label>League:</label> <select name="League" id="leagueSelect">';
while ($row = $result->fetch_array()) {
	$returnString .= '\n<option value="' . $row['league_ID'] . '">' . $row['name'] . '</option>';
}

mysqli_close($conn);
echo $returnString;
?>