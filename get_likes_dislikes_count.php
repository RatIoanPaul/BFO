<?php
// get_likes_dislikes_count.php

include 'user_page.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Verifică dacă ID-ul anunțului a fost transmis
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $announcementId = $_GET['id'];

    // Prepară interogările pentru a afla numărul de like-uri și dislike-uri
    $likesQuery = "SELECT COUNT(*) AS likes_count FROM likes WHERE announcement_id = ?";
    $dislikesQuery = "SELECT COUNT(*) AS dislikes_count FROM dislikes WHERE announcement_id = ?";

    // Execută interogarea pentru like-uri
    $likesStmt = $conn->prepare($likesQuery);
    $likesStmt->bind_param("i", $announcementId);
    $likesStmt->execute();
    $likesResult = $likesStmt->get_result();
    $likesData = $likesResult->fetch_assoc();

    // Execută interogarea pentru dislike-uri
    $dislikesStmt = $conn->prepare($dislikesQuery);
    $dislikesStmt->bind_param("i", $announcementId);
    $dislikesStmt->execute();
    $dislikesResult = $dislikesStmt->get_result();
    $dislikesData = $dislikesResult->fetch_assoc();

    // Închide statement-urile
    $likesStmt->close();
    $dislikesStmt->close();

    // Pregătește și trimite răspunsul în format JSON
    $response = [
        'likes' => $likesData['likes_count'],
        'dislikes' => $dislikesData['dislikes_count']
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'ID-ul anunțului lipsește sau este invalid.']);
}

$conn->close();
