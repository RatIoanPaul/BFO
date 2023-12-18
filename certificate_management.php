<?php

// Începe sesiunea
session_start();

// Se încearcă încărcarea fișierului autoload.php din directorul vendor.
require 'C:\xampp\htdocs\BFO\vendor\autoload.php';

// Verifică dacă utilizatorul este autentificat ca admin;
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

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', TRUE);
$options->set('chroot', __DIR__);

// Verifică dacă butonul de generare PDF a fost apăsat
if (isset($_POST['generate_pdf'])) {
   $sql = "SELECT * FROM volunteer";
   $result = $conn->query($sql);

   // Verifică dacă s-au găsit înregistrări
   if ($result->num_rows > 0) {
      $pdfDir = 'C:/Users/grati/Downloads/Pdfs';

      if (!file_exists($pdfDir)) {
         mkdir($pdfDir, 0777, true);
      }

      // Procesează fiecare înregistrare și creează un PDF
      while ($row = $result->fetch_assoc()) {
         $dompdf = new Dompdf($options);
         $dompdf->setPaper('A4', 'portrait');
         $currentDate = date('d.m.Y');
         // Încarcă template-ul HTML și înlocuiește placeholder-ele
         $html = file_get_contents("template.html");
         $html = str_replace(
            ["{{ NumePrenume }}", "{{ Domiciliu }}", "{{ Cnp }}", "{{ SeriaCI }}", "{{ EliberatCI }}", "{{ NumarCI }}", "{{ DataNastere }}", "{{ Data }}"],
            [$row['NumePrenume'], $row['Domiciliu'], $row['Cnp'], $row['SeriaCI'], $row['EliberatCI'], $row['NumarCI'], $row['DataNastere'], $currentDate],
            $html
         );

         $dompdf->loadHtml($html);
         $dompdf->render();

         $pdfFileName = $pdfDir . DIRECTORY_SEPARATOR . 'Voluntar_' . $row['NumarContract'] . '.pdf';
         file_put_contents($pdfFileName, $dompdf->output());
      }
   }
}
?>

<!DOCTYPE html>
<html lang="ro">

<head>
   <meta charset="UTF-8">
   <title>Certificat de Voluntariat</title>

   <!-- Stiluri personalizate -->
   <style>
      body {
         font-family: 'Times New Roman', serif;
         margin: 40px;
         background-color: #fff;
         color: #333;
      }

      h1 {
         font-size: 28px;
         margin-bottom: 40px;
      }

      p {
         font-size: 18px;
         margin: 20px 0;
      }

      .signature {
         margin-top: 40px;
         text-align: right;
         font-style: italic;
      }

      .logo {
         width: 100px;
         height: auto;
         margin-bottom: 20px;
      }

      body {
         margin: 0;
         font-family: Arial, sans-serif;
         background-color: #f4f4f4;
      }

      .main-content {
         margin-left: 250px;
         /* Același cu lățimea sidebar-ului */
         padding: 0 20px;
         /* Elimină padding-ul superior */
         background-color: #fff;
         /* fundal alb pentru lizibilitate */
         min-height: 100vh;
         /* înălțime completă */
      }

      .certificate-header {
         padding-top: 20px;
         /* Spațiu pentru header */
         margin-bottom: 20px;
         text-align: center;
         font-size: 24px;
         color: #333;
         background-color: #fff;
         /* Asigură-te că fundalul este alb */
         border-bottom: 1px solid #ddd;
         /* Adaugă o linie de separare */
      }

      .certificate-container {
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
         border: 1px solid #ddd;
         padding: 20px;
         margin: 20px auto;
         /* Spațiu pentru container */
         max-width: 800px;
         /* Lățime maximă pentru container */
      }

      .generate-pdf-button {
         display: block;
         width: 200px;
         padding: 10px;
         margin: 20px auto;
         background-color: #5cb85c;
         color: white;
         text-align: center;
         text-decoration: none;
         border: none;
         border-radius: 5px;
         cursor: pointer;
      }

      .generate-pdf-button:hover {
         background-color: #4cae4c;
      }
   </style>
</head>

<body>
   <?php include 'admin_page.php'; ?>
   <div class="main-content">
      <!-- Antetul se află în afara containerului pentru a permite fundal complet alb -->
      <div class="certificate-header">
         Gestionare Certificate
      </div>
      <div class="certificate-container">
         <!-- Încorporarea conținutului din template.html -->
         <?php echo file_get_contents("template.html"); ?>
         <!-- Butonul pentru generarea PDF-urilor -->
         <form method="post" action="">
            <input type="submit" class="generate-pdf-button" name="generate_pdf" value="Generează PDF-uri">
         </form>
      </div>
   </div>
</body>

</html>