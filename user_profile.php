<?php
// Verifică dacă utilizatorul este autentificat și are un ID de utilizator


$user_id = $_SESSION['user_id'];

// Presupunând că ai o conexiune $conn la baza de date
$sql = "SELECT * FROM volunteers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Afișează datele utilizatorului
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["NumarContract"] . "</td>
                <td>" . $row["NumePrenume"] . "</td>
                <td>" . $row["Domiciliu"] . "</td>
                <td>" . $row["Cnp"] . "</td>
                <td>" . $row["SeriaCI"] . "</td>
                <td>" . $row["NumarCI"] . "</td>
                <td>" . $row["EliberatCI"] . "</td>
                <td>" . $row["EmitereCI"] . "</td>
                <td>" . $row["ExpirareCI"] . "</td>
                <td>" . $row["DataNastere"] . "</td>
                <td>" . $row["Telefon"] . "</td>
                <td>
                    <a href='edit_volunteer.php?id=" . $row["Id"] . "'>Editează</a>
                    <a href='delete_volunteer.php?id=" . $row["Id"] . "' onclick='return confirm(\"Ești sigur că vrei să ștergi acest voluntar?\");'>Șterge</a>
                </td>
              </tr>";
    }
} else {
    echo "Nu s-au găsit date ale utilizatorului.";
}

$stmt->close();
