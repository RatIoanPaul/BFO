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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>CodingDung | Profile Template</title>
   <link rel="stylesheet" href="css/style.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
   <!-- =============== Navigation ================ -->
   <div class="container">
      <div class="navigation">
         <ul>
            <li>
               <a href="user_page.php">
                  <span class="icon">
                     <ion-icon name="home-outline"></ion-icon>
                  </span>
                  <span class="title">BFO</span>
               </a>
            </li>

            <li>
               <a href="user_management.php">
                  <span class="icon">
                     <ion-icon name="people-outline"></ion-icon>
                  </span>
                  <span class="title">GESTIONARE CONT</span>
               </a>
            </li>

            <li>
               <a href="announcement_management.php">
                  <span class="icon">
                     <ion-icon name="chatbubble-outline"></ion-icon>
                  </span>
                  <span class="title">VIZUALIZARE ANUNȚURI</span>
               </a>
            </li>
         </ul>
      </div>
   </div>

   <!-- =========== Scripts =========  -->
   <script src="assets/js/main.js"></script>

   <!-- ====== ionicons ======= -->
   <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
   <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>