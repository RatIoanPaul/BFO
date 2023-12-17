<?php
// Verificăm dacă ID-ul este prezent
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Detalii pentru conectarea la baza de date
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "bfo";

    // Crearea conexiunii MySQLi
    $conn = new mysqli($host, $user, $password, $dbname);

    // Preluăm ID-ul anunțului pentru ștergere
    $id = $_GET['id'];

    // Creăm și executăm query-ul de ștergere
    $sql = "DELETE FROM announcements WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Legăm parametrii la statement
        $stmt->bind_param("i", $id);

        // Executăm statement-ul
        if ($stmt->execute()) {
            echo "Anunțul a fost șters.";
        } else {
            echo "Eroare la ștergerea anunțului: " . $stmt->error;
        }

        // Închidem statement-ul
        $stmt->close();
    } else {
        echo "Eroare: nu s-a putut pregăti statement-ul: " . $conn->error;
    }

    // Închidem conexiunea
    $conn->close();

    // Redirecționăm către pagina de unde s-a făcut cererea
    header("Location: admin_page.php"); // Înlocuiți cu pagina dvs. cu anunțuri
    exit;
}
