<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă toate bibliotecile Composer

@include 'config.php';


if (isset($_POST['send_code'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Utilizați instrucțiuni pregătite pentru a preveni SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reset_code = rand(100000, 999999);
        $_SESSION['reset_code'] = $reset_code;
        $_SESSION['reset_email'] = $email;

        send_verification_code($email, $reset_code);
    } else {
        echo 'Email-ul nu a fost găsit în baza noastră de date.';
    }
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
        $mail->Subject = 'Codul de resetare a parolei';
        $mail->Body = 'Codul site-uli BFO de resetare a parolei este: ' . $code;

        $mail->send();

        header('location:password_reset.php');
    } catch (Exception $e) {
        error_log('Mesajul nu a putut fi trimis. Mailer Error: ' . $mail->ErrorInfo); // Logare eroare
        return false;
    }
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/login_style.css">
</head>

<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>Reset code</h3>
            <input type="email" name="email" required placeholder="Email:">
            <input type="submit" name="send_code" value="Send Code" class="form-btn">
        </form>
    </div>
</body>

</html>