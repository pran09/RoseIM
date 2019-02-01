<!DOCTYPE html>

<head><title>DatabaseConnect</title></head>
<body>

<?php

 $emailAddress = $_POST["emailAddress"];
 $getPassword = $_POST["password"];
 $getPasswordConfirmation = $_POST["passwordConfirmation"];

 #header("Location: index.html");
         $conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

#Register Query
if($getPasswordConfirmation != null){

  if($getPassword != $getPasswordConfirmation){
    mysqli_close($conn);
    echo "Passwords do not match.";
  }
  else{

    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $sex = $_POST["sex"];
    $password = password_hash($getPassword, PASSWORD_DEFAULT);
    $unExistingEmail = true;

    $s = $conn->prepare("SELECT email FROM Person") or die($conn->error);
    $s->execute();
      $re = $s->get_result();
      while ($row = $re->fetch_array(MYSQLI_NUM))
      {
          foreach ($row as $r)
          {
            if($r == $emailAddress){
              $unExistingEmail = false;
              echo "Email address already exists.";
            }
          }
        }


  if($unExistingEmail){
        $stmt = $conn->prepare("SELECT Create_Player(?, ?, ?, ?, ?)") or die($conn->error);
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
        else{
          echo "Make sure all fields are filled out."
        }
        /*
        if ($r == 1){
          echo "Enter a non null first name.";
         }
         else if ($r == 2){
           echo "Enter a non null last name.";
         }
        else if ($r == 3){
          echo "Enter a non null email address.";
         }
         else if ($r == 4){
           echo "Enter a non null password.";
        }
         else if ($r == 5){
           echo "Enter a non null sex.";
         }
         else if ($r == 6){
           echo "Not a @rose-hulman.edu email address.";
         }
         else if ($r == 7){
           echo "Your email address already exists.";
         }
         else if ($r == 8){
           echo "Your password cannot be null.";
         }
         else if ($r == 9){
           echo "Sex is not male or female.";
        }
        */
          			}

      			}		
            $stmt->close();
    }
  			      
mysqli_close($conn);

}
}
#Login Query
else{

$NotInDatabase = true;

  if($getPassword == null){
    echo "Please enter a valid password.";
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
	  
	  if ($NotInDatabase){
	  	echo "This email address is not registered.";
	  }

  $s->close();


mysqli_close($conn);


}


}


?>

</body>
</html>

