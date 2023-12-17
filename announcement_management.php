<?php

// Începe sesiunea
session_start();

//Se încearcă încărcarea fișierului autoload.php din directorul vendor.
require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă toate bibliotecile Composer

// Verifică dacă utilizatorul este autentificat ca admin; 
// Dacă nu, îl redirecționează la formularul de login;
if (!isset($_SESSION['email'])) {
    header('location:login_form.php');
    exit();
}

// Detalii pentru conectarea la baza de date
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Crearea conexiunii MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Verifică dacă conexiunea a reușit
if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

// Închide conexiunea la baza de date
$conn->close();

?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionare Voluntari</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include 'admin_page.php'; ?> // Aici se include meniul de navigație

    <div class="main">
        <div class="details">
            <div class="recentOrders">
                <h2>Anunțurii:</h2></br>
                <?php include 'announcement_list.php'; ?>
            </div>

            <div class="recentCustomers">
                <div class="cardHeader">
                    <h2>Scrie un anunț nou:</h2>
                </div>
                <form action="process_add_announcement.php" method="post">
                    <div class="form-field">
                        <textarea id="announcementText" name="announcementText" placeholder="Scrieți anunțul aici..." rows="25" cols="35" style="resize: none;"></textarea>
                    </div>
                    <div class="form-field">
                        <button type="submit" name="post_announcement" class="button">Postează Anunțul</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- =========== Scripts =========  -->
    <script src="assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>