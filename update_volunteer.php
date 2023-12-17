<?php
// update_volunteer.php

session_start(); // Începeți sau continuați sesiunea pentru a putea utiliza variabilele de sesiune.

// Detalii pentru conectarea la baza de date
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Crearea conexiunii MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Verificați dacă conexiunea a reușit
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Verificați dacă formularul a fost trimis
if (isset($_POST['update_volunteer'])) {
    // Colectează și validează datele din formular
    $id = $conn->real_escape_string($_POST['Id']);
    $numePrenume = $conn->real_escape_string($_POST['NumePrenume']);
    $domiciliu = $conn->real_escape_string($_POST['Domiciliu']);
    $cnp = $conn->real_escape_string($_POST['Cnp']);
    $seriaCI = $conn->real_escape_string($_POST['SeriaCI']);
    $numarCI = $conn->real_escape_string($_POST['NumarCI']);
    $eliberatCI = $conn->real_escape_string($_POST['EliberatCI']);
    $emitereCI = $conn->real_escape_string($_POST['EmitereCI']);
    $expirareCI = $conn->real_escape_string($_POST['ExpirareCI']);
    $dataNastere = $conn->real_escape_string($_POST['DataNastere']);
    $telefon = $conn->real_escape_string($_POST['Telefon']);
    $email = $conn->real_escape_string($_POST['Email']); // Verificăm că există un câmp Email în formularul de editare

    // Inițializează un mesaj de eroare
    $errorMessage = '';

    // Verifică validitatea datelor introduse;
    if (strlen($cnp) != 13) {
        $errorMessage = "CNP-ul trebuie să aibă 13 cifre.";
    } elseif (strlen($seriaCI) != 2) {
        $errorMessage = "Seria CI trebuie să aibă 2 caractere.";
    } elseif (strlen($numarCI) != 6) {
        $errorMessage = "Numărul CI trebuie să aibă 6 cifre.";
    } elseif (new DateTime($emitereCI) > new DateTime()) {
        $errorMessage = "Data emiterii CI nu poate fi în viitor.";
    } elseif (new DateTime($expirareCI) < new DateTime()) {
        $errorMessage = "Data expirării CI nu poate fi în trecut.";
    } elseif (new DateTime($dataNastere) > new DateTime()) {
        $errorMessage = "Data nașterii nu poate fi în viitor.";
    } elseif (strlen($telefon) != 10 || strpos($telefon, "07") !== 0) {
        $errorMessage = "Numărul de telefon trebuie să aibă 10 cifre și să înceapă cu 07.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Adresa de email nu este validă.";
    }

    // Dacă există erori, setează mesajul de eroare și redirecționează înapoi la pagina de editare
    if (!empty($errorMessage)) {
        $_SESSION['message'] = $errorMessage;
        header('Location: admin_page.php?id=' . $id);
        exit();
    }

    // Dacă nu există erori, continuă cu actualizarea
    $sql = "UPDATE volunteer SET NumarContract=?, NumePrenume=?, Domiciliu=?, Cnp=?, SeriaCI=?, NumarCI=?, EliberatCI=?, EmitereCI=?, ExpirareCI=?, DataNastere=?, Telefon=?, Email=? WHERE Id=?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['message'] = "Eroare la pregătirea interogării: " . $conn->error;
        header('Location: admin_page.php');
        exit();
    }

    $stmt->bind_param("issssssssissi", $numarContract, $numePrenume, $domiciliu, $cnp, $seriaCI, $numarCI, $eliberatCI, $emitereCI, $expirareCI, $dataNastere, $telefon, $email, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Voluntarul a fost actualizat cu succes.";
    } else {
        $_SESSION['message'] = "Eroare la actualizarea datelor: " . $stmt->error;
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Informațiile nu au fost trimise corect.";
}

$conn->close();

// Redirecționează înapoi la pagina de admin
header('Location: admin_page.php');
exit();
