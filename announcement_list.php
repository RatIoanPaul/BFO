<?php
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
$sql = "SELECT * FROM announcements ORDER BY post_datetime DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Începerea tabelului
    echo "<div class='table-container'>
    <table class='table-announcements'><tr><th>Postat la</th><th>Anunț</th><th>Acțiuni</th></tr>";
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Formatarea datei și orei
        $dataFormatata = date('H:i ; d.m.Y', strtotime($row["post_datetime"]));
        echo "<tr>
                <td>" . $dataFormatata . "</td>
                <td>" . $row["announcement_text"] . "</td>
                <td class='actiuni'>
                <a href='edit_volunteer.php?id=" . $row["id"] . "' class='btn-edit'>Editează</a>
                <a href='delete_volunteer.php?id=" . $row["id"] . "' class='btn-delete'>Șterge</a>
                </td>
              </tr>";
    }
    echo "</table></div>";
} else {
    echo "0 rezultate";
}
$conn->close();
