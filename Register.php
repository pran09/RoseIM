<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Sign Up Form</title>
      <link rel="stylesheet" href="normalize.css">
      <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="css/main.css">
  </head>
  <body>
	 <center><font size="128" color ="black">Rose</font><font size ="128" color="red">IM</font></center>
    <form action="DatabaseConnect.php" method="post">

      <h1>Sign Up</h1>

      <fieldset>
        <legend><span class="number">1</span>Your basic info</legend>
        <label>First Name:</label>
        <input type="text" name="firstName">

        <label>Last Name:</label>
        <input type="text" name="lastName">

        <label>Email:</label>
        <input type="email" name="emailAddress" placeholder="@rose-hulman.edu">

        <label>Password:</label>
        <input type="password"name="password">

        <label>Confirm Password:</label>
        <input type="password" name="passwordConfirmation">

        <label>Sex:</label>
        <select name = "sex">
          <option name="unselected">Unselected</option>
          <option name="Male">Male</option>
          <option name="Female">Female</option>
        </select>

      </fieldset>

      <button type="submit">Sign Up</button>
    </form>

  </body>
</html>

