<?php
	session_start();
	if (!isset($_SESSION['emailAddress'])) {
		header('location: Login.php');
		exit();
	}
	if ($_SESSION['permission'] != 'referee') {
		
		header('location: TeamSelect.php');
		exit();
	}
?>