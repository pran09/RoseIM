<!DOCTYPE html>

<head><title>DatabaseConnect</title></head>
<body>

<?php

  //session_start();


 $emailAddress = $_POST["emailAddress"];
 $getPassword = $_POST["password"];
 $getPasswordConfirmation = $_POST["passwordConfirmation"];
 $notExistingEmail = true;

//echo "session didnt work";
 //$_SESSION["emailAddress"] = $emailAddress
 //echo "session worked";

         $conn = new mysqli("roseim.csse.rose-hulman.edu", "test", "test", "RoseIM");

  #Get permission

echo "here1";
$stmt = $mysqli->prepare("CALL get_permission(?, @permission)") or die($conn->error);
$stmt->bind_param("s", $emailAddress);
echo "here2";
$r = $mysqli->query('SELECT @permission as output');
$row = $r->fetch_assoc();                       
echo "here3";
echo $row['output'];


  

#Register Query
if($getPasswordConfirmation != null){

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
              			echo "logged in";
                    //header("Location: TeamSelect.php");
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


}


?>

</body>
</html>

