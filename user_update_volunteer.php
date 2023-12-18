<?php
session_start();

@include 'config.php';

require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă toate bibliotecile Composer

$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

if (isset($_POST['user_update_volunteer'])) {
    $NumarContract = $conn->real_escape_string($_POST['NumarContract']);
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
        $stmt = $conn->prepare("UPDATE volunteer SET NumePrenume=?, Domiciliu=?, Cnp=?, SeriaCI=?, NumarCI=?, EliberatCI=?, EmitereCI=?, ExpirareCI=?, DataNastere=?, Telefon=?, Email=? WHERE NumarContract=?");
        $stmt->bind_param("sssssssssssi", $NumePrenume, $Domiciliu, $Cnp, $SeriaCI, $NumarCI, $EliberatCI, $EmitereCI, $ExpirareCI, $DataNastere, $Telefon, $Email, $NumarContract);
        $stmt->execute();
    }

    if ($errorMessage == '') {
        $stmt = $conn->prepare("UPDATE volunteer SET NumePrenume=?, Domiciliu=?, Cnp=?, SeriaCI=?, NumarCI=?, EliberatCI=?, EmitereCI=?, ExpirareCI=?, DataNastere=?, Telefon=?, Email=? WHERE NumarContract=?");
        $stmt->bind_param("sssssssssssi", $NumePrenume, $Domiciliu, $Cnp, $SeriaCI, $NumarCI, $EliberatCI, $EmitereCI, $ExpirareCI, $DataNastere, $Telefon, $Email, $NumarContract);

        if ($stmt->execute()) {
            $_SESSION['response'] = "Voluntarul a fost actualizat cu succes!";
            $_SESSION['res_type'] = "success";
        } else {
            $_SESSION['response'] = "Eroare la actualizarea voluntarului: " . $stmt->error;
            $_SESSION['res_type'] = "error";
        }
        $stmt->close();
    } else {
        $_SESSION['response'] = $errorMessage;
        $_SESSION['res_type'] = "error";
    }

    $conn->close();
    header("Location: user_profile.php");
    exit;
}
