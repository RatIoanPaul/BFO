<?php
if (isset($_POST['post_announcement'])) {
    $announcementText = $_POST['announcementText']; // preluați textul anunțului din formular
    $currentDateTime = date('Y-m-d H:i:s'); // obțineți data și ora curentă

    // conectați-vă la baza de date și inserați anunțul
    $conn = new mysqli("localhost", "root", "", "bfo");

    // verificați conexiunea
    if ($conn->connect_error) {
        die("Conexiune eșuată: " . $conn->connect_error);
    }

    // pregătiți interogarea SQL pentru a include și data postării
    $stmt = $conn->prepare("INSERT INTO announcements (author_name, post_datetime, announcement_text) VALUES (?, ?, ?)");
    $authorName = 'Raț Ioan-Paul';
    $stmt->bind_param("sss", $authorName, $currentDateTime, $announcementText);

    // Executați interogarea și verificați succesul
    if ($stmt->execute()) {
        header('Location: announcement_management.php'); // Redirecționare după succes
    } else {
        echo "Eroare la inserarea anunțului: " . $stmt->error;
    }

    // Închideți statement-ul și conexiunea
    $stmt->close();
    $conn->close();
}
