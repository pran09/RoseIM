<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Join a Team!</title>
      <link rel="stylesheet" href="normalize.css">
      <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
      <!-- <link rel="stylesheet" href="css/main.css"> -->
  </head>
  <body>
 <center><font size="128" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
    <form action="TeamSelect.php" method="post">
	<h1>Join Team</h1>
      <fieldset>
        <?php


$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

$stmt = $conn->prepare("SELECT name FROM Sport") or die($conn->error);

      			$stmt->execute();
      			$result = $stmt->get_result();
				echo '<label>Sport:</label>';
				echo '<select name="Sport" required>';
			$aResult = 5;
      			while ($row = $result->fetch_array(MYSQLI_NUM))
      			{
          		foreach ($row as $r)
          			{
						echo '<option name="' . $r . '">' . $r . '</option>';
						$aResult = $r;
          			}

      			}
			echo '</select>';

  			$stmt->close();


			$stmt = $conn->prepare("SELECT League.name FROM League, Sport WHERE Sport.name = League.sport AND Sport.name = '" . $aResult . "'") or die($conn->error);

      			$stmt->execute();
      			$result = $stmt->get_result();
				echo '<label>League: </label>';
				echo '<select name="League" required>';
      			while ($row = $result->fetch_array(MYSQLI_NUM))
      			{
          		foreach ($row as $r)
          			{
						echo '<option name="' . $r . '">' . $r . '</option>';
          			}

      			}
			echo '</select>';

			$stmt->close();




			$stmt = $conn->prepare("SELECT name FROM Team") or die($conn->error);

      			$stmt->execute();
      			$result = $stmt->get_result();
				echo '<label>Team: </label>';
				echo '<select name="Team" required>';
      			while ($row = $result->fetch_array(MYSQLI_NUM))
      			{
          		foreach ($row as $r)
          			{
						echo '<option name="' . $r . '">' . $r . '</option>';
          			}

      			}
			echo '</select>';

  			$stmt->close();

mysqli_close($conn);


	?>
</fieldset>
  <button type="submit">Join Team</button>

    </form>


  </body>
</html>

