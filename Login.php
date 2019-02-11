<?php
session_start();
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login Form</title>
	<link rel="stylesheet" href="normalize.css">
	<link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/main.css">
	<style>
	input[type=submit] {
		padding: 19px 39px 18px 39px;
		color: #FFF;
		background-color: #800000;
		font-size: 18px;
		text-align: center;
		font-style: normal;
		border-radius: 5px;
		width: 100%;
		border: 1px solid #000000;
		border-width: 1px 1px 3px;
		box-shadow: 0 -1px 0 rgba(255,255,255,0.1) inset;
		margin-bottom: 10px;
	}

	input[type=text] {
		width: 100%;
		padding: 12px 20px;
		margin: 8px 0;
		box-sizing: border-box;
		border: 3px solid #ccc;
		-webkit-transition: 0.5s;
		transition: 0.5s;
		outline: none;
	}
	input[type=password] {
		width: 100%;
		padding: 12px 20px;
		margin: 8px 0;
		box-sizing: border-box;
		border: 3px solid #ccc;
		-webkit-transition: 0.5s;
		transition: 0.5s;
		outline: none;
	}
	input[type=password]:focus {
		border: 3px solid #555;
	}
	input[type=text]:focus {
		border: 3px solid #555;
	}
</style>
</head>
<body>

	<center><font size="200" color ="black">Rose</font><font size ="128" color="red">IM</font></center>

	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<h1>Login</h1>

		<fieldset>
			<legend><span class="number">1</span>Login Information</legend>
			<label>Email Address:</label>
			<input type="text" name="emailAddress" placeholder="@rose-hulman.edu" required>

			<label>Password:</label>
			<input type="password" name="password" required>


		</fieldset>

		<input type="submit" name="submit" value="Login"/>

		<label>
			<input type="checkbox" name="remember"> Remember me
		</label>
	</div>
</form>

<?php
if (isset($_POST['submit'])) {
	
	$emailAddress = $_POST["emailAddress"];
	$getPassword = $_POST["password"];
	$notExistingEmail = true;

	$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

	$NotInDatabase = true;

  #Get permission

	$_SESSION["emailAddress"] = $emailAddress;


	$stmt = $conn->prepare("CALL get_permission(?, @permission)") or die($conn->error);
	$stmt->bind_param("s", $emailAddress);
	$stmt->execute();
	$r = $conn->query('SELECT @permission as output');
	$row = $r->fetch_assoc();                       

	$_SESSION["permission"] = $row['output'];

	$stmt->close();



	$s = $conn->prepare("SELECT email FROM Person") or die($conn->error);
	$s->execute();
	$re = $s->get_result();
	while ($row = $re->fetch_array(MYSQLI_NUM))
	{
		foreach ($row as $r)
		{
			if($r == $emailAddress){
				$NotInDatabase = false;
				$stmt = $conn->prepare("SELECT password FROM Person WHERE email = ?") or die($conn->error);
				$stmt->bind_param("s", $emailAddress);

				$stmt->execute();
				$result = $stmt->get_result();
				while ($row = $result->fetch_array(MYSQLI_NUM))
				{
					foreach ($row as $r)
					{
						if(password_verify($getPassword, $r)){
							
                    //header("Location: TeamSelect.php");
							function redirect($url, $statusCode = 303) {
								header('Location: ' . $url, true, $statusCode);
								die();
							}
							redirect("TeamSelect.php");
						}
						else{
							echo "Username or password are incorrect.";
						}
					}

				}   

				$stmt->close();
			}

		}
	}
	
	If ($NotInDatabase){
		echo "This email address is not registered.";
	}

	$s->close();


	mysqli_close($conn);

}


?>

<form action="Register.php" method="post">
	<button type="submit">Sign Up</button>
</form>
</body>

</html>



