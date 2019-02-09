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
  </head>
  <body>

	<center><font size="128" color ="black">Rose</font><font size ="128" color="red">IM</font></center>

    <form action="DatabaseConnect.php" method="post">
      <h1>Login</h1>

      <fieldset>
        <legend><span class="number">1</span>Login Information</legend>
        <label>Email Address:</label>
        <input type="text" name="emailAddress" placeholder="@rose-hulman.edu">

        <label>Password:</label>
        <input type="password" name="password">


      </fieldset>

      <button type="submit">Login</button>

      <label>
      <input type="checkbox" name="remember"> Remember me
    </label>
    </div>
    </form>
  </body>

  <?php
    if (isset($_POST['submit'])) {
      $emailAddress = $_POST['emailAddress'];
      if ($emailAddress == null) {
        echo "EmailAddress can not be null.";
      } else {
        $_SESSION["emailAddress"] = $emailAddress;
      }
    }
  ?>

  <body>
<form action="Register.php" method="post">
        <button type="submit">Sign Up</button>
    </form>
  </body>

</html>



