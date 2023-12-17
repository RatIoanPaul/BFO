<?php
// delete_volunteer.php
$volunteer_id = $_GET['NumarContract'];
$email_id = $_GET['Email'];
// Detalii pentru conectarea la baza de date
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Crearea conexiunii MySQLi
$conn = new mysqli($host, $user, $password, $dbname);
// Detalii pentru conectarea la baza de date
// [...] (Codul de conectare la baza de date este omis pentru brevitate)

// Pregătiți interogarea pentru a șterge voluntarul
$stmt = $conn->prepare("DELETE FROM volunteer WHERE NumarContract = ?");
$stmt->bind_param("i", $volunteer_id);
$stmt->execute();

$stmtUser = $conn->prepare("DELETE FROM users WHERE email = ?");
$stmtUser->bind_param("s", $email_id);
$stmtUser->execute();

$stmt->close();
$stmtUser->close();
$conn->close();

// Redirecționează înapoi la lista de voluntari
header("Location: volunteer_management.php");
exit();
