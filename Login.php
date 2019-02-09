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

    <form action="#" method="post">
      <h1>Login</h1>

      <fieldset>
        <legend><span class="number">1</span>Login Information</legend>
        <label>Email Address:</label>
        <input type="text" name="emailAddress" placeholder="@rose-hulman.edu" required>

        <label>Password:</label>
        <input type="password" name="password" required>


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
                    
                    header("Location: TeamSelect.php");
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

  <body>
<form action="Register.php" method="post">
        <button type="submit">Sign Up</button>
    </form>
  </body>

</html>



