<?php
// Verificăm dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Detalii pentru conectarea la baza de date
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "bfo";

    // Crearea conexiunii MySQLi
    $conn = new mysqli($host, $user, $password, $dbname);

    // Preluăm datele trimise prin formular
    $id = $_POST['id'];
    $anunt = $_POST['anunt'];
    $creat_la = date('Y-m-d H:i:s'); // Preluăm data și ora curentă

    // Creăm și executăm query-ul de actualizare
    $sql = "UPDATE announcements SET anunt = ?, creat_la = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Legăm parametrii la statement
        $stmt->bind_param("ssi", $anunt, $creat_la, $id);

        // Executăm statement-ul
        if ($stmt->execute()) {
            // Redirecționăm către pagina de admin după actualizare
            header('Location: admin_page.php');
            exit();
        } else {
            echo "Eroare la actualizarea anunțului: " . $stmt->error;
        }

        // Închidem statement-ul
        $stmt->close();
    } else {
        echo "Eroare: nu s-a putut pregăti statement-ul: " . $conn->error;
    }

    // Închidem conexiunea
    $conn->close();
} else if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Preluăm ID-ul anunțului pentru editare
    $id = $_GET['id'];

    // Detalii pentru conectarea la baza de date
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "bfo";

    // Crearea conexiunii MySQLi
    $conn = new mysqli($host, $user, $password, $dbname);

    // Preluăm anunțul actual din baza de date
    $sql = "SELECT anunt, creat_la FROM announcements WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Legăm parametrii la statement
        $stmt->bind_param("i", $id);

        // Executăm statement-ul
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $anunt = $row['anunt'];
            // Nu este necesar să preluăm 'creat_la' deoarece acesta va fi resetat
        } else {
            echo "Eroare la preluarea anunțului: " . $stmt->error;
        }

        // Închidem statement-ul
        $stmt->close();
    } else {
        echo "Eroare: nu s-a putut pregăti statement-ul: " . $conn->error;
    }

    // Închidem conexiunea
    $conn->close();
?>
    <!-- Aici putem afișa un formular HTML pentru editarea anunțului -->
    <form method="post" action="edit_announcement.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <textarea name="anunt"><?php echo htmlspecialchars($anunt); ?></textarea>
        <input type="submit" value="Actualizează anunțul">
    </form>

<?php
}
?>