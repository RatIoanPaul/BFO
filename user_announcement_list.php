<?php
session_start(); // Inițializarea sesiunii la începutul scriptului

// Detalii pentru conectarea la baza de date
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Crearea conexiunii MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Interogarea bazei de date pentru a obține toate anunțurile în ordinea descrescătoare a datei de adăugare
$sql = "SELECT * FROM announcements ORDER BY creat_la DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Începerea tabelului
    echo "<table><tr><th>Publicat la</th><th>Anunț</th></tr>";
    // Afisarea fiecărui rând
    while ($row = $result->fetch_assoc()) {
        // Formatarea datei și orei
        $dataFormatata = date('H:i ; d.m.Y', strtotime($row["creat_la"]));
        echo "<tr><td>" . $dataFormatata . "</td><td>" . $row["anunt"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 rezultate";
}
$conn->close();
