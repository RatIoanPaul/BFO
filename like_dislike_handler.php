<?php
// like_dislike_handler.php

include 'user_page.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

$response = ['success' => false];

// Verifică dacă ID-ul anunțului a fost transmis
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $announcementId = $_POST['id'];
    $isLike = $_POST['like'] === 'true'; // Convert string to boolean

    // Încearcă să actualizezi baza de date
    $conn->begin_transaction();
    try {
        if ($isLike) {
            // Logică pentru adăugarea unui like
            $conn->query("INSERT INTO likes (announcement_id, user_id) VALUES ($announcementId, $userUniqueId)");
        } else {
            // Logică pentru adăugarea unui dislike
            $conn->query("INSERT INTO dislikes (announcement_id, user_id) VALUES ($announcementId, $userUniqueId)");
        }

        // Obține numărul actualizat de like-uri și dislike-uri
        $likesResult = $conn->query("SELECT COUNT(*) AS likes_count FROM likes WHERE announcement_id = $announcementId")->fetch_assoc();
        $dislikesResult = $conn->query("SELECT COUNT(*) AS dislikes_count FROM dislikes WHERE announcement_id = $announcementId")->fetch_assoc();

        $conn->commit();

        // Pregătește răspunsul cu noile numere de like-uri și dislike-uri
        $response = [
            'success' => true,
            'likes' => $likesResult['likes_count'],
            'dislikes' => $dislikesResult['dislikes_count']
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $response['error'] = $e->getMessage();
    }
} else {
    $response['error'] = 'ID-ul anunțului lipsește sau este invalid.';
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
