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

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

\PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
\PhpOffice\PhpWord\Settings::setPdfRendererPath(__DIR__ . '/vendor/dompdf/dompdf');

// Verifică dacă butonul de generare PDF a fost apăsat
if (isset($_POST['generate_pdf'])) {

   $sql = "SELECT * FROM volunteer";
   $result = $conn->query($sql);

   // Verifică dacă s-au găsit înregistrări
   if ($result->num_rows > 0) {
      $pdfDir = './Pdfs';

      if (!file_exists($pdfDir)) {
         mkdir($pdfDir, 0777, true);
      }


      // Procesează fiecare înregistrare și creează un PDF
      while ($row = $result->fetch_assoc()) {
         $phpWord = new PhpWord();
         $section = $phpWord->addSection();
         $section->addText("Nume și Prenume: " . $row['NumePrenume']);
         $section->addText("Domiciliu: " . $row['Domiciliu']);
         $section->addText("CNP: " . $row['Cnp']);
         $section->addText("Seria CI: " . $row['SeriaCI']);
         $section->addText("Eliberat CI: " . $row['EliberatCI']);
         $section->addText("Numar CI: " . $row['NumarCI']);
         $section->addText("Data nașterii CI: " . $row['DataNastere']);
         $pdfFileName = $pdfDir . DIRECTORY_SEPARATOR . 'Voluntar_' . $row['NumarContract'] . '.pdf';
         $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
         $pdfWriter->save($pdfFileName);
      }

      echo 'Toate documentele pentru voluntari au fost generate.';
   } else {
      echo "Nu s-au găsit înregistrări.";
   }
}

// Închide conexiunea la baza de date
$conn->close();
