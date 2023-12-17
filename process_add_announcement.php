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


    header('Location: announcement_management.php'); // Înlocuiește cu numele fișierului formularului

    $stmt->close();
    $conn->close();
}
