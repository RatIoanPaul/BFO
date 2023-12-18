<?php

include 'user_page.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Funcție pentru obținerea numărului de like-uri și dislike-uri
function getLikesDislikesCount($announcementId, $conn)
{
    // Presupunând că avem tabele separate pentru like-uri și dislike-uri
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
    echo "<table class='table-announcements'>";
    echo "<thead><tr><th>Postat la</th><th>Autor</th><th>Anunț</th><th>Actiuni</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        $dataFormatata = date('H:i ; d.m.Y', strtotime($row["post_datetime"]));
        echo "<tr onmouseenter='showPieChart(" . $row["id"] . ")'>
                <td>" . htmlspecialchars($dataFormatata) . "</td>
                <td>" . htmlspecialchars($row["author_name"]) . "</td>
                <td class='announcement-text' data-id='" . $row["id"] . "'>" . htmlspecialchars($row["announcement_text"]) . "
                    <div id='tooltip-" . $row["id"] . "' class='tooltip'></div>
                </td>
                <td class='actiuni'>
                    <button onclick='likeDislike(" . $row["id"] . ", true, this)' class='like-button'>👍</button>
                    <span id='like-count-" . $row["id"] . "' class='like-count'>0</span>
                    <button onclick='likeDislike(" . $row["id"] . ", false, this)' class='dislike-button'>👎</button>
                    <span id='dislike-count-" . $row["id"] . "' class='dislike-count'>0</span>
                </td>
              </tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "0 rezultate";
}

echo "</div>";
echo "</div>";
echo "</div>";

$conn->close();

?>
<script>
    // Presupunem că 'userUniqueId' este un ID unic al utilizatorului curent. Acesta ar trebui gestionat server-side.
    var userUniqueId = 'unique_user_id_example'; // Acest ID ar trebui să fie unic pentru fiecare utilizator

    function likeDislike(announcementId, isLike, element) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'like_dislike_handler.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status === 200) {
                var response = JSON.parse(this.responseText);
                // Actualizăm numărul de like-uri și dislike-uri în UI
                document.getElementById('like-count-' + announcementId).textContent = response.likes;
                document.getElementById('dislike-count-' + announcementId).textContent = response.dislikes;
            }
        };
        xhr.send('id=' + announcementId + '&like=' + isLike + '&user_id=' + userUniqueId);
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