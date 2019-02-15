<?php
session_start();
?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up Form</title>
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

  input[type=text]:focus {
    border: 3px solid #555;
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
  input[type=email] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 3px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
  }

  input[type=email]:focus {
    border: 3px solid #555;
  }

</style>
</head>
<body>
  <center><font size="200" color ="red">Rose</font><font size ="128" color="black">IM</font></center>
  <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">

    <h1>Sign Up</h1>

    <fieldset>
      <legend><span class="number">1</span>Your basic info</legend>
      <label>First Name:</label>
      <input type="text" name="firstName" required>

      <label>Last Name:</label>
      <input type="text" name="lastName" required>

      <label>Email:</label>
      <input type="email" name="emailAddress" placeholder="@rose-hulman.edu" required>

      <label>Password:</label>
      <input type="password"name="password" required>

      <label>Confirm Password:</label>
      <input type="password" name="passwordConfirmation" required>

      <label>Sex:</label>
      <select name = "sex">
        <option name="unselected">Unselected</option>
        <option name="Male">Male</option>
        <option name="Female">Female</option>
      </select>

    </fieldset>

    <input type="submit" name="submit" value="Sign Up"/>
  </form>

  <?php

  if (isset($_POST['submit'])) {
    
   $emailAddress = $_POST["emailAddress"];
   $getPassword = $_POST["password"];
   $getPasswordConfirmation = $_POST["passwordConfirmation"];
   $notExistingEmail = true;

   include 'datalogin.php';


   $NotInDatabase = true;

   $_SESSION["emailAddress"] = $emailAddress;                    

   $_SESSION["permission"] = 'Player';




   if($getPassword != $getPasswordConfirmation){
    mysqli_close($conn);
    echo "Passwords do not match.";
  }
  else{

    $s = $conn->prepare("SELECT email FROM Person") or die($conn->error);
    $s->execute();
    $re = $s->get_result();
    while ($row = $re->fetch_array(MYSQLI_NUM))
    {
      foreach ($row as $r)
      {
        if($r == $emailAddress){
          $notExistingEmail = false;
          echo "<center><font color = red>Email Address already exists.</font></center>";

        }
      }
    }



    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $sex = $_POST["sex"];
    $password = password_hash($getPassword, PASSWORD_DEFAULT);

    if($notExistingEmail){
      $stmt = $conn->prepare("SELECT Create_Player(?, ?, ?, ?, ?) as return_value") or die($conn->error);
      $stmt->bind_param("sssss", $firstName, $lastName, $emailAddress, $password, $sex);

      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_array())
      {
        foreach ($row as $r)
        {
          if($r == 0){
         //echo "success";
            function redirect($url, $statusCode = 303) {
              header('Location: ' . $url, true, $statusCode);
              die();
            }
            redirect("TeamSelect.php");
        //header("Location: TeamSelect.php");
          }
        }

      }   
      echo "<center><font color = red>All required fields must be filled out.</font></center>";
      $stmt->close();
    }
    mysqli_close($conn);
  }
}
?>
</body>
</html>

