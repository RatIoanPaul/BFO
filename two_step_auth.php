<?php

session_start();

@include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include Composer's autoloader
require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă toate bibliotecile Composer

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
        $mail->Body = 'Codul site-uli BFO de autentificare este: ' . $code;

        $mail->send();
    } catch (Exception $e) {
        error_log('Mesajul nu a putut fi trimis. Mailer Error: ' . $mail->ErrorInfo); // Logare eroare
        return false;
    }
    return true;
}

$error = 'Verifică mail!'; // Mesaj pentru utilizator

if (isset($_POST['verify'])) {
    $user_code = $_POST['code'] ?? '';
    $two_step_code = $_SESSION['two_step_code'] ?? '';

    if ($user_code !== '' && $user_code == $two_step_code) {
        // Codul introdus este corect
        header('location: user_page.php'); // Verifică calea către 'user_page.php'
        exit();
    } else if ($user_code !== '' && $user_code != $two_step_code) {
        // Cod incorect, retrimite un nou cod
        $two_step_code = rand(100000, 999999);
        $_SESSION['two_step_code'] = $two_step_code;
        if (send_verification_code($_SESSION['email'], $two_step_code)) {
            $error = 'Cod incorect!Un cod nou a fost trimis pe emailul tău.';
        } else {
            $error = 'Eroare la trimiterea codului. Te rugăm să încerci din nou.';
        }
    } else {
        // Câmpul codului a fost lăsat gol
        $error = 'Te rugăm să introduci codul de verificare.';
    }
} else if (isset($_POST['resend'])) {
    $two_step_code = rand(100000, 999999);
    $_SESSION['two_step_code'] = $two_step_code;
    if (send_verification_code($_SESSION['email'], $two_step_code)) {
        $error = 'Un cod nou a fost trimis pe emailul tău.';
    } else {
        $error = 'Eroare la trimiterea codului. Te rugăm să încerci din nou.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificare în Doi Pași </title>
    <link rel="stylesheet" href="css/login_style.css">
</head>

<body>

    <div class="form-container">
        <form action="" method="post">
            <h3>Two step verification</h3>
            <?php if (!empty($error)) : ?>
                <div class="error-msg"> <?php echo $error; ?></div>
            <?php endif; ?>
            <input type="text" name="code" placeholder="Validation code:">
            <input type="submit" name="verify" value="Verify code" class="form-btn">
            <input type="submit" name="resend" value="Resend code" class="form-btn">
        </form>
    </div>

</body>

</html>