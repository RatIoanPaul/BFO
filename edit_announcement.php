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
    $announcement_text = $_POST['announcement_text'];
    $creat_la = date('Y-m-d H:i:s'); // Preluăm data și ora curentă

    // Creăm și executăm query-ul de actualizare
    $sql = "UPDATE announcements SET post_datetime = ?, announcement_text = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Legăm parametrii la statement
        $stmt->bind_param("ssi", $creat_la, $announcement_text, $id);


        // Executăm statement-ul
        if ($stmt->execute()) {
            // Redirecționăm către pagina de admin după actualizare
            header('Location: announcement_management.php');
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
    $sql = "SELECT author_name, post_datetime, announcement_text FROM announcements WHERE id = ?";


    if ($stmt = $conn->prepare($sql)) {
        // Legăm parametrii la statement
        $stmt->bind_param("i", $id);

        // Executăm statement-ul
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $announcement_text = $row['announcement_text'];
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
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Uplode</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <div class="recentCustomers">
            <div class="cardHeader">
                <h2 align='center'>
                    Editează anunț:
                </h2>
            </div>
            <div class="recentCustomers">
                <div class="cardHeader">
                    <div class="container">
                        <!-- Aici putem afișa un formular HTML pentru editarea anunțului -->
                        <form method="post" action="edit_announcement.php">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <textarea name="announcement_text" class="textarea-square"><?php echo htmlspecialchars($announcement_text); ?></textarea>
                            <input type="submit" value="Actualizează anunțul" class="button">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>

<?php
}
?>