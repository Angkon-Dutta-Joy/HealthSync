<?php

require('connection.php');
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function sendMail($email,$v_code)
{
  require './PHPMailer/PHPMailer.php';
  require './PHPMailer/SMTP.php';
  require './PHPMailer/Exception.php';

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = '';                     //SMTP username
    $mail->Password   = '';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('from@example.com', 'BRACU Hospital');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Email verification from BRACU Hospital';
    $mail->Body    = "Thanks for registration!
      Click on the link below to verify the email address
      <a href='https://localhost/login_register/verify.php?email=$email&v_code=$v_code'>Verify</a>";
    $mail->send();
    return true;
  } catch (Exception $e) {
    return false;
  }
  
}
#for login
if (isset($_POST['login']))
{
  $query="SELECT * FROM `registerd_user` WHERE `email`='$_POST[email_username]' OR `username`= '$_POST[email_username]'";
  $result=mysqli_query($con,$query);
  if($result)
  {
    if(mysqli_num_rows($result)==1)
    {
      $result_fetch=mysqli_fetch_assoc($result);
      if($result_fetch['is_verified']==1){
        if (password_verify($_POST['password'],$result_fetch['password']))
        {
          $_SESSION['logged_in']=true;
          $_SESSION['username']=$result_fetch['username'];
          header("location:index.php");
  
  
        }
        else
        {
          echo"
            <script>
              alert('Incorrect Password');
              window.location.href='index.php';
            </script>
          ";
        }
      }
      else{
        echo"
        <script>
          alert('Email Not verified');
          window.location.href='index.php';
        </script>
      ";  
      }

    }
    else
    {
      echo"
        <script>
          alert('Email or Username Not registered');
          window.location.href='index.php';
        </script>
      ";
    }
  }
  else
  {
    echo"
      <script>
        alert('Cannot Run Query');
        window.location.href='index.php';
      </script>
    ";
  }
}



#for registration
if (isset($_POST['register']))
{
    $user_exist_query="SELECT * FROM `registerd_user` WHERE `username`='$_POST[username]' OR `email`= '$_POST[email]'";
    $result=mysqli_query($con,$user_exist_query);
    if($result)
    {
       
       if(mysqli_num_rows($result)>0)#username email already registered
       {
        #if any user
        $result_fetch=mysqli_fetch_assoc($result);
        if($result_fetch['username']==$_POST['username'])
        {
            #error for uswenamw=e already has
            echo"
              <script>
                alert('$result_fetch[username] - Username already taken');
                window.location.href='index.php';
              </script>
            ";
        }
        else
        {
            #for emsil already registered
            echo" 
              <script>
                alert('$result_fetch[email] - Email already taken');
                window.location.href='index.php';
              </script>
            ";
        }
       }
       else # if no one taken
       {
          $password=password_hash($_POST['password'],PASSWORD_BCRYPT);
          $v_code=bin2hex(random_bytes(16));
          $query="INSERT INTO `registerd_user`(`full_name`, `username`, `email`, `password`,`verification_code`,`is_verified`) VALUES ('$_POST[full_name]','$_POST[username]','$_POST[email]','$password','$v_code','0')";
          if (mysqli_query($con,$query) && sendMail($_POST['email'],$v_code))
          {
            echo"
               <script>
                 alert('Registration sucessfull');
                 window.location.href='index.php';
               </script>
            ";
          }
          else
          {
            echo"
              <script>
                 alert('Server Down');
                 window.location.href='index.php';
              </script>
            ";

          }
        }
    }
    else
    {
       echo"
         <script>
           alert('Cannot Run Query');
           window.location.href='index.php';
         </script>
        ";
    }
}
?>