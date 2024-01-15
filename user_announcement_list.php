<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'user_page.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

function getLikesDislikesCount($announcementId, $conn)
{
    $likesQuery = "SELECT COUNT(*) AS likes_count FROM likes WHERE announcement_id = $announcementId";
    $dislikesQuery = "SELECT COUNT(*) AS dislikes_count FROM dislikes WHERE announcement_id = $announcementId";

    $likesResult = $conn->query($likesQuery);
    $dislikesResult = $conn->query($dislikesQuery);

    if ($likesResult && $dislikesResult) {
        $likesRow = $likesResult->fetch_assoc();
        $dislikesRow = $dislikesResult->fetch_assoc();
        return [
            'likes' => $likesRow['likes_count'],
            'dislikes' => $dislikesRow['dislikes_count']
        ];
    }
    return ['likes' => 0, 'dislikes' => 0];
}

$sql = "SELECT * FROM announcements ORDER BY post_datetime DESC";
$result = $conn->query($sql);

echo "<div class='main'>";
echo "<div class='details'>";
echo "<div class='recentOrders'>";
echo "<h2>Anunțurii:</h2></br>";

if ($result->num_rows > 0) {
    echo "<div id='announcement-container'></div>";
    echo "<div class='custom-buttons'>";
    echo "<button class='custom-button' onclick='navigate(-1)'>Înapoi</button>";
    echo "<button class='custom-button' onclick='navigate(1)'>Înainte</button>";
    echo "</div>";
} else {
    echo "0 rezultate";
}

echo "</div>";
echo "</div>";
echo "</div>";

$conn->close();
?>
<script>
    var announcements = <?php echo json_encode($result->fetch_all(MYSQLI_ASSOC)); ?>;
    var currentIndex = 0;

    function updateAnnouncementDisplay() {
        var announcement = announcements[currentIndex];
        var container = document.getElementById('announcement-container');
        container.innerHTML = '<div>' +
            '<p>Postat la: ' + announcement.post_datetime + '</p>' +
            '<p>Autor: ' + announcement.author_name + '</p>' +
            '<p>Anunț: ' + announcement.announcement_text + '</p>' +
            '<br><br>' + // Două linii libere pentru spațiu
            '<button id="like-button-' + announcement.id + '" class="like-button">👍 Like</button>' +
            '<span id="like-count-' + announcement.id + '" class="like-count">' + '&emsp;' + (announcement.likes || 0) + '</span>' +
            '&emsp;&emsp;&emsp;' + // Adaugă 10 spații
            '<button id="dislike-button-' + announcement.id + '" class="dislike-button">👎 Dislike</button>' +
            '<span id="dislike-count-' + announcement.id + '" class="dislike-count">' + '&emsp;' + (announcement.dislikes || 0) + '</span>' +
            '</div>';


        document.getElementById('like-button-' + announcement.id).onclick = function() {
            likeDislike(announcement.id, true);
        };
        document.getElementById('dislike-button-' + announcement.id).onclick = function() {
            likeDislike(announcement.id, false);
        };
    }

    function navigate(step) {
        currentIndex += step;
        if (currentIndex < 0) {
            currentIndex = 0;
        } else if (currentIndex >= announcements.length) {
            currentIndex = announcements.length - 1;
        }
        updateAnnouncementDisplay();
    }

    // Inițializăm afișarea cu primul anunț
    updateAnnouncementDisplay();
    var userUniqueId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

    function likeDislike(announcementId, isLike) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'like_dislike_handler.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status === 200) {
                var response = JSON.parse(this.responseText);
                // Actualizăm numărul de like-uri și dislike-uri în pagina web
                document.getElementById('like-count-' + announcementId).textContent = response.likes;
                document.getElementById('dislike-count-' + announcementId).textContent = response.dislikes;

                // Redirecționează către user_announcement_list.php
                window.location.href = 'user_announcement_list.php';
            }
        };
        var data = 'id=' + announcementId + '&like=' + isLike + '&user_id=' + userUniqueId;
        xhr.send(data);
    }


    function showPieChart(announcementId) {
        var tooltip = document.getElementById('tooltip-' + announcementId);
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_likes_dislikes.php?id=' + announcementId, true);
        xhr.onload = function() {
            if (this.status === 200) {
                var response = JSON.parse(this.responseText);
                var total = response.likes + response.dislikes;
                var likesPercentage = (response.likes / total) * 100;
                var dislikesPercentage = (response.dislikes / total) * 100;
                tooltip.innerHTML = 'Likes: ' + likesPercentage.toFixed(2) + '%<br>Dislikes: ' + dislikesPercentage.toFixed(2) + '%';
                tooltip.style.display = 'block'; // Afișăm tooltip-ul
            }
        };
        xhr.send();
    }

    // Adăugăm event listeners pentru hover pe rândul tabelului de anunțuri
    document.querySelectorAll('.table-announcements tr').forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            var announcementId = this.querySelector('.announcement-text').dataset.id;
            showPieChart(announcementId);
        });
        row.addEventListener('mouseleave', function() {
            var announcementId = this.querySelector('.announcement-text').dataset.id;
            var tooltip = document.getElementById('tooltip-' + announcementId);
            tooltip.style.display = 'none'; // Ascundem tooltip-ul când mouse-ul nu mai este deasupra
        });
    });
</script>
<style>
    .custom-buttons {
        display: flex;
        gap: 10px;
        /* Spațiu între butoane */
    }

    .custom-button {
        padding: 5px 10px;
        background-color: red;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
        width: 150px;
        /* Setează lățimea dorită în pixeli */
        height: 40px;
        /* Setează înălțimea dorită în pixeli */
    }

    .custom-button:hover {
        background-color: #45A049;
    }
</style>