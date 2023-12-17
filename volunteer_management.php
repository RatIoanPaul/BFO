<?php

// Începe sesiunea
if (session_status() == PHP_SESSION_NONE) {
      session_start();
}

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

$error = '';

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
                        <h2>Listă voluntari:</h2></br>
                        <?php include 'volunteer_list.php'; ?>
                  </div>

                  <div class="recentCustomers">
                        <div class="cardHeader">
                              <h2>Adaugă voluntar nou:</h2>
                        </div>
                        <div class="container">
                              <form action="process_add_volunteer.php" method="post">
                                    <input type="text" name="NumePrenume" placeholder="Nume și prenume:" required>
                                    <input type="text" name="Domiciliu" placeholder="Domiciliu:" required>
                                    <input type="text" id="Cnp" name="Cnp" placeholder="CNP:" required>
                                    <input type="text" id="SeriaCI" name="SeriaCI" placeholder="Seria CI:" required>
                                    <input type="text" id="NumarCI" name="NumarCI" placeholder="Număr CI:" required>
                                    <input type="text" id="EliberatCI" name="EliberatCI" placeholder="CI emis de:" required><br>
                                    <label for="emitereCI">CI valabil de la:</label>
                                    <input type="date" id="EmitereCI" name="EmitereCI" required><br>
                                    <label for="expirareCI">CI valabil până la:</label>
                                    <input type="date" id="ExpirareCI" name="ExpirareCI" required><br>
                                    <label for="dataNastere">Data nașterii:</label>
                                    <input type="date" id="DataNastere" name="DataNastere" required>
                                    <input type="text" id="Telefon" name="Telefon" placeholder="Telefon:" required>
                                    <input type="text" id="Email" name="Email" placeholder="Email:" required>
                                    <form action="process_add_volunteer.php" method="post">
                                          <button type="submit" name="add_volunteer" class="button">Adaugă Voluntar</button>
                                    </form>


                              </form>
                        </div>
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