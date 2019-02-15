<?php
$sport = isset($_POST['sport']))? $_POST['sport'] : null;

include 'datalogin.php';

$stmt = $conn->prepare("SELECT name FROM League WHERE sport = ?") or die($conn->error);
$stmt->bind_param("s", $sport);
$stmt->execute();
$result = $stmt->get_result();

$rows = array();
$returnString = '<label>League:</label>
<select name="League" id="league">';
while ($row = $result->fetch_array(MYSQLI_NUM)) {
	foreach ($row as $r) {
		$returnString .= '\n<option value="' . $r . '">' . $r . '</option>';
	}
}

mysql_close($conn);
echo $returnString;
?>