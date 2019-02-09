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
    <form action="#" method="post">

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

    <?php

    if (isset($_POST['submit'])) {
      
 $emailAddress = $_POST["emailAddress"];
 $getPassword = $_POST["password"];
 $getPasswordConfirmation = $_POST["passwordConfirmation"];
 $notExistingEmail = true;

$conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");


$NotInDatabase = true;

  if($emailAddress == null){
  echo "Please enter an email address";
  }
  else{

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
              echo "Email address already exists. ";
            }
          }
        }



    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $sex = $_POST["sex"];
    $password = password_hash($getPassword, PASSWORD_DEFAULT);

    if($firstName == null OR $lastName == null OR $sex == Unselected){
      echo "Please make sure all fields are filled out.";
    }
    else{


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
         header("Location: TeamSelect.php");
        }
            }

            }   
        echo "Make sure all fields are filled out.";
        $stmt->close();
  }
}

      
mysqli_close($conn);

}






}
}


    }
  ?>

  </body>
</html>

