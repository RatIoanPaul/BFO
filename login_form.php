<?php
session_start();

@include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă bibliotecile Composer pentru PHPMailer

$host = 'localhost'; // sau IP-ul serverului tău de baze de date
$db_user = 'root'; // sau numele de utilizator pentru baza de date
$db_pass = ''; // sau parola pentru utilizatorul bazei de date
$db_name = 'bfo'; // numele bazei de date

$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

if (!$conn) {
   die("Conexiunea la baza de date a eșuat: " . mysqli_connect_error());
}

function send_verification_code($email, $code)
{
   $mail = new PHPMailer(true);

   try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'g.rat.ioan.paul@gmail.com';
      $mail->Password = 'rifncaupdicnbfwn';
      $mail->SMTPSecure = 'ssl';
      $mail->Port = 465;

      $mail->setFrom('g.rat.ioan.paul@gmail.com', 'BFO');
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Codul de autentificare';
      $mail->Body = 'Codul site-uli BFO pentru autentificare este: ' . $code;

      $mail->send();
   } catch (Exception $e) {
      error_log('Mesajul nu a putut fi trimis. Mailer Error: ' . $mail->ErrorInfo); // Logare eroare
      return false;
   }
   return true;
}


if (isset($_POST['submit'])) {

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $_SESSION['email'] = $email;
   $pass = md5($_POST['password']); // Consider using password_hash() in the real-world application

   // Verifică întâi în tabelul de admini
   $select_admin = "SELECT * FROM admins WHERE email = '$email' AND password = '$pass'";
   $result_admin = mysqli_query($conn, $select_admin);

   // Verifică apoi în tabelul de useri
   $select_user = "SELECT * FROM users WHERE email = '$email' AND password = '$pass'";
   $result_user = mysqli_query($conn, $select_user);

   if (mysqli_num_rows($result_admin) > 0) {
      // Logarea ca admin
      $row_admin = mysqli_fetch_assoc($result_admin);
      $_SESSION['email'] = $row_admin['email'];
      header('location:admin_page.php');
      exit();
   } else if (mysqli_num_rows($result_user) > 0) {
      // Generarea și trimiterea codului de autentificare în doi pași
      $two_step_code = rand(100000, 999999); // Cod simplu de 6 cifre
      $_SESSION['two_step_code'] = $two_step_code; // Stocarea codului în sesiune

      // Trimiterea codului pe email
      send_verification_code($email, $two_step_code);

      // Redirecționarea către two_step_auth.php
      header('location: two_step_auth.php');
      exit();
   } else {
      $error = 'Email sau parolă greșită!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login Form</title>
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/login_style.css">
</head>

<body>

   <div class="form-container">

      <form action="" method="post">
         <h3>login now</h3>
         <?php
         if (isset($error)) {
            echo '<span class="error-msg">' . $error . '</span>';
         }
         ?>
         <input type="email" name="email" required placeholder="Email:">
         <input type="password" name="password" required placeholder="Password:">
         <input type="submit" name="submit" value="login now" class="form-btn">
         <p>You forgot the password? <a href="code_reset.php">reset the password</a></p>
      </form>

   </div>

</body>

</html>