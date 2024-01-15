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
// Setează codarea caracterelor pentru conexiune ca UTF-8
$conn->set_charset("utf8mb4");

// Verifică dacă conexiunea a reușit
if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

use Dompdf\Dompdf;
use Dompdf\Options;

// Crearea și configurarea obiectului Options pentru Dompdf
$options = new Options();
$options->set('isRemoteEnabled', TRUE); // Permite încărcarea resurselor de la distanță
$options->set('chroot', __DIR__); // Restricționează accesul la anumite directoare

// Crearea obiectului Dompdf și aplicarea opțiunilor
$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'portrait'); // Setarea dimensiunii și orientării hârtiei

// Obținerea NumarContract pentru utilizatorul autentificat
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT NumarContract FROM volunteer WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Eroare la prepararea interogării: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $NumarContract = $user['NumarContract'];
    $stmt->close();

    // Pregătirea interogării pentru obținerea detaliilor voluntarului
    $stmt = $conn->prepare("SELECT * FROM volunteer WHERE NumarContract = ?");
    $stmt->bind_param("i", $NumarContract);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Încarcă template-ul HTML și înlocuiește placeholder-ele
        $currentDate = date('d.m.Y');
        $html = file_get_contents("template.html");
        $html = str_replace(
            ["{{ NumePrenume }}", "{{ Domiciliu }}", "{{ Cnp }}", "{{ SeriaCI }}", "{{ EliberatCI }}", "{{ NumarCI }}", "{{ DataNastere }}", "{{ Data }}"],
            [$row['NumePrenume'], $row['Domiciliu'], $row['Cnp'], $row['SeriaCI'], $row['EliberatCI'], $row['NumarCI'], $row['DataNastere'], $currentDate],
            $html
        );

        // Converteste HTML-ul în entități HTML compatibile cu UTF-8
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Încărcarea conținutului HTML convertit în Dompdf
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render(); // Generarea PDF-ului

        // Trimiterea fișierului PDF la browser
        $dompdf->stream("Voluntar_$NumarContract.pdf", array("Attachment" => 0));
    } else {
        echo "Nu s-au găsit înregistrări pentru utilizatorul cu acest email.";
    }
} else {
    echo "Utilizatorul nu este autentificat.";
}

// Închide conexiunea cu baza de date;
$conn->close();
