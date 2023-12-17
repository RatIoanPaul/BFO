<?php

session_start();

@include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include Composer's autoloader
require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă toate bibliotecile Composer

// Inițializează variabila pentru mesajele de eroare;
$errorMessage = '';

// Setează detaliile pentru conectarea la baza de date MySQL;
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Inițializează o nouă conexiune MySQLi cu baza de date;
$conn = new mysqli($host, $user, $password, $dbname);

// Verifică dacă conexiunea la baza de date a reușit sau nu;
if ($conn->connect_error) {
    $errorMessage = "Conexiune eșuată: " . $conn->connect_error;
}

function send_verification_code($Email, $parolaAleatorie)
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
        $mail->addAddress($Email);

        $mail->isHTML(true);
        $mail->Subject = 'Cont BFO';
        $mail->Body = 'Parola contului dumneavoastră BFO este: ' . $parolaAleatorie;

        $mail->send();
    } catch (Exception $e) {
        error_log('Mesajul nu a putut fi trimis. Mailer Error: ' . $mail->ErrorInfo); // Logare eroare
        return false;
    }
    return true;
}

// Verifică dacă a fost apăsat butonul de adăugare a unui nou voluntar;
if (isset($_POST['add_volunteer'])) {
    // Colectează și pregătește datele din formular pentru inserarea în baza de date;
    // Validează datele primite pentru a preveni atacurile de tip SQL Injection;
    $NumePrenume = $conn->real_escape_string($_POST['NumePrenume']);
    $Domiciliu = $conn->real_escape_string($_POST['Domiciliu']);
    $Cnp = $conn->real_escape_string($_POST['Cnp']);
    $SeriaCI = $conn->real_escape_string($_POST['SeriaCI']);
    $NumarCI = $conn->real_escape_string($_POST['NumarCI']);
    $EliberatCI = $conn->real_escape_string($_POST['EliberatCI']);
    $EmitereCI = $conn->real_escape_string($_POST['EmitereCI']);
    $ExpirareCI = $conn->real_escape_string($_POST['ExpirareCI']);
    $DataNastere = $conn->real_escape_string($_POST['DataNastere']);
    $Telefon = $conn->real_escape_string($_POST['Telefon']);
    $Email = $conn->real_escape_string($_POST['Email']);

    // Obține data curentă pentru validări;
    $DataCurenta = date("Y-m-d");

    // Verifică validitatea datelor introduse;
    if (strlen($Cnp) != 13) {
        $errorMessage = "CNP-ul trebuie să aibă 13 cifre.";
    } elseif (strlen($SeriaCI) != 2) {
        $errorMessage = "Seria CI trebuie să aibă 2 caractere.";
    } elseif (strlen($NumarCI) != 6) {
        echo "Numărul CI trebuie să aibă 6 cifre.";
    } elseif (new DateTime($EmitereCI) > new DateTime($DataCurenta)) {
        echo "Data emiterii CI nu poate fi în viitor.";
    } elseif (new DateTime($ExpirareCI) < new DateTime($DataCurenta)) {
        echo "Data expirării CI nu poate fi în trecut.";
    } elseif (new DateTime($DataNastere) > new DateTime($DataCurenta)) {
        echo "Data nașterii nu poate fi în viitor.";
    } elseif (strlen($Telefon) != 10 || strpos($Telefon, "07") !== 0) {
        echo "Numărul de telefon trebuie să aibă 10 cifre și să înceapă cu 07.";
    } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresa de email nu este validă.";
    } else {
        // Construiește și execută interogarea SQL pentru inserarea datelor;
        $stmt = $conn->prepare("INSERT INTO volunteer (NumePrenume, Domiciliu, Cnp, SeriaCI, NumarCI, EliberatCI, EmitereCI, ExpirareCI, DataNastere, Telefon, Email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $NumePrenume, $Domiciliu, $Cnp, $SeriaCI, $NumarCI, $EliberatCI, $EmitereCI, $ExpirareCI, $DataNastere, $Telefon, $Email);
        $stmt->execute();

        // Generarea unei parole aleatorii
        $parolaAleatorie = bin2hex(random_bytes(3)); // Generează o parolă aleatorie de 10 caractere

        // Criptarea parolei folosind un algoritm de hash sigur
        $parolaCriptata = md5($parolaAleatorie);

        // Trimiterea unui email cu parola folosind PHPMailer
        if (send_verification_code($Email, $parolaAleatorie)) {
            // Adăugarea în tabela users
            $sqlUser = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $sqlUser->bind_param("ss", $Email, $parolaCriptata);
            $sqlUser->execute();
            header("Location: volunteer_management.php");
        }
    }
}

// Închide conexiunea cu baza de date;
$conn->close();
exit;
