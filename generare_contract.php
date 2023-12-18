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

    // Pregătiți interogarea pentru a obține detaliile voluntarului
    $stmt = $conn->prepare("SELECT * FROM volunteer WHERE NumarContract = ?");
    $stmt->bind_param("i", $NumarContract);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
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
