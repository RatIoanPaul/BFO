<?php

// Începe sesiunea
session_start();

//Se încearcă încărcarea fișierului autoload.php din directorul vendor.
require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă toate bibliotecile Composer

// Detalii pentru conectarea la baza de date
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Crearea conexiunii MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Verifică dacă conexiunea a reușit
if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

@include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

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
    $reset_code = $_SESSION['reset_code'] ?? '';
    $email = $_SESSION['reset_email'] ?? '';

    if ($user_code !== '' && $user_code == $reset_code) {
        // Codul introdus este corect
        // Codul introdus este corect, resetează parola și redirecționează
        $hashed_password = md5($_POST['password']);;
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        if ($stmt->execute()) {
            // Dacă actualizarea a reușit, redirecționează către 'login_form.php'
            $stmt->close();
            $conn->close();
            header('Location: login_form.php');
            exit();
        } else {
            $error = 'A apărut o eroare la resetarea parolei.';
        }
    } else if ($user_code !== '' && $user_code != $reset_code) {
        // Cod incorect, retrimite un nou cod
        $reset_code = rand(100000, 999999);
        $_SESSION['reset_code'] = $reset_code;
        if (send_verification_code($_SESSION['email'], $reset_code)) {
            $error = 'Cod incorect!Un cod nou a fost trimis pe emailul tău.';
        } else {
            $error = 'Eroare la trimiterea codului. Te rugăm să încerci din nou.';
        }
    } else {
        // Câmpul codului a fost lăsat gol
        $error = 'Te rugăm să introduci codul de verificare.';
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
            <h3>Reset password</h3>
            <?php if (!empty($error)) : ?>
                <div class="error-msg"> <?php echo $error; ?></div>
            <?php endif; ?>
            <input type="text" name="code" placeholder="Reset code:">
            <input type="password" name="password" placeholder="New password:">
            <input type="submit" name="verify" value="Reset" class="form-btn">
        </form>
    </div>

</body>

</html>