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

if (isset($_POST['id'], $_POST['like'], $_POST['user_id']) && is_numeric($_POST['id']) && is_numeric($_POST['user_id'])) {
    $announcementId = intval($_POST['id']);
    $isLike = filter_var($_POST['like'], FILTER_VALIDATE_BOOLEAN);
    $userId = intval($_POST['user_id']);

    $conn->begin_transaction();
    try {
        // Verificăm dacă există deja o reacție a utilizatorului la acest anunț
        $stmt = $conn->prepare("SELECT id FROM reactions WHERE announcement_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $announcementId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingReaction = $result->fetch_assoc();

        $reactionType = $isLike ? 'like' : 'dislike';

        if ($existingReaction) {
            // Actualizăm reacția existentă
            $stmt = $conn->prepare("UPDATE reactions SET reaction_type = ? WHERE id = ?");
            $stmt->bind_param("si", $reactionType, $existingReaction['id']);
        } else {
            // Adăugăm o nouă reacție
            $stmt = $conn->prepare("INSERT INTO reactions (announcement_id, user_id, reaction_type) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $announcementId, $userId, $reactionType);
        }
        $stmt->execute();

        // Actualizăm numărul de like-uri și dislike-uri în tabela 'announcements'
        $updateLikes = $conn->prepare("UPDATE announcements SET likes = (SELECT COUNT(*) FROM reactions WHERE announcement_id = ? AND reaction_type = 'like') WHERE id = ?");
        $updateLikes->bind_param("ii", $announcementId, $announcementId);
        $updateLikes->execute();

        $updateDislikes = $conn->prepare("UPDATE announcements SET dislikes = (SELECT COUNT(*) FROM reactions WHERE announcement_id = ? AND reaction_type = 'dislike') WHERE id = ?");
        $updateDislikes->bind_param("ii", $announcementId, $announcementId);
        $updateDislikes->execute();

        // Recuperăm noile valori de like-uri și dislike-uri pentru a le returna ca răspuns
        $finalResult = $conn->prepare("SELECT likes, dislikes FROM announcements WHERE id = ?");
        $finalResult->bind_param("i", $announcementId);
        $finalResult->execute();
        $result = $finalResult->get_result();
        $data = $result->fetch_assoc();

        $conn->commit();

        $response = [
            'success' => true,
            'likes' => $data['likes'],
            'dislikes' => $data['dislikes']
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $response['error'] = 'Eroare la procesarea cererii: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'ID-ul anunțului lipsește sau este invalid sau nu a fost specificată reacția.';
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
