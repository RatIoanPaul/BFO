<?php
// Verifică dacă utilizatorul este autentificat și are un ID de utilizator
// Începe sesiunea
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'user_page.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Obținerea NumarContract pentru utilizatorul autentificat
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT NumarContract FROM volunteer WHERE Email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Eroare la prepararea interogării: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['NumarContract'];
    $stmt->close();
} else {
    die("Utilizatorul nu este autentificat.");
}

// Utilizare $user_id pentru a obține informații despre voluntar
$sql = "SELECT * FROM volunteer WHERE NumarContract = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Eroare la prepararea interogării: " . $conn->error);
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();


// Începutul partii HTML
echo "<!DOCTYPE html>
<html>
<head>
    <title>Profilul Voluntarului</title>
    <link rel='stylesheet' type='text/css' href='/css/user_style.css'>
</head>
<body>";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<div class='main'>
    <div class='details'>
          <div class='recentOrders'>
          <div class='user-profile'>
            <div class='user-information'>
                <h3>" . $row["NumePrenume"] . "</h3>
                <p><strong>Număr Contract:</strong> " . $row["NumarContract"] . "</p>
                <p><strong>Domiciliu:</strong> " . $row["Domiciliu"] . "</p>
                <p><strong>CNP:</strong> " . $row["Cnp"] . "</p>
                <p><strong>Serie CI:</strong> " . $row["SeriaCI"] . "</p>
                <p><strong>Număr CI:</strong> " . $row["NumarCI"] . "</p>
                <p><strong>Eliberat CI:</strong> " . $row["EliberatCI"] . "</p>
                <p><strong>Emitere CI:</strong> " . $row["EmitereCI"] . "</p>
                <p><strong>Expirare CI:</strong> " . $row["ExpirareCI"] . "</p>
                <p><strong>Data Naștere:</strong> " . $row["DataNastere"] . "</p>
                <p><strong>Telefon:</strong> " . $row["Telefon"] . "</p>
                <a href='user_edit_volunteer.php?NumarContract=" . $row["NumarContract"] . "' class='edit-btn'>Editează Profilul</a>
                <a href='generare_contract.php?NumarContract=" . $row["NumarContract"] . "' class='edit-btn'>Generează contractul</a>
            </div>
            </div>
            </div>
            </div>
        </div>";
}

// Sfârșitul partii HTML
echo "</body>
</html>";
echo "</body>
</html>";
