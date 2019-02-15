<?php
include 'datalogin.php';

$league = isset($_GET['league'])? $_GET['league'] : null;

$stmt = $conn->prepare("SELECT team_ID, name FROM Team WHERE league = ?") or die($conn->error);
$stmt->bind_param("i", $league);
$stmt->execute();
$result = $stmt->get_result();

$rows = array();
$returnString = '<label>Team:</label> <select name="Team" id="Team">';
while ($row = $result->fetch_array()) {
	$returnString .= '\n<option value="' . $row['team_ID'] . '">' . $row['name'] . '</option>';
}

mysqli_close($conn);
echo $returnString;
?>