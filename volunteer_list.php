<?php
// Detalii pentru conectarea la baza de date
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Crearea conexiunii MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Interogarea bazei de date pentru a obține toți voluntarii
$sql = "SELECT Id, NumarContract, NumePrenume, Domiciliu, Cnp, SeriaCI, NumarCI, EliberatCI, EmitereCI, ExpirareCI, DataNastere, Telefon, Email FROM volunteer";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <title>Lista de Voluntari</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function applyFilters() {
            var checkboxes = document.querySelectorAll('#filterForm input[type=checkbox]');
            checkboxes.forEach(function(checkbox) {
                var columnClass = checkbox.value;
                var columns = document.querySelectorAll('.' + columnClass);
                columns.forEach(function(column) {
                    column.style.display = checkbox.checked ? "" : "none";
                });
            });
        }
    </script>
</head>

<body>
    <form id="filterForm">
        <label><input type="checkbox" name="columns" value="numarContract" checked> Număr Contract</label>
        <label><input type="checkbox" name="columns" value="numePrenume" checked> Nume și Prenume</label>
        <label><input type="checkbox" name="columns" value="domiciliu" checked> Domiciliu</label>
        <label><input type="checkbox" name="columns" value="cnp" checked> CNP</label>
        <label><input type="checkbox" name="columns" value="serieCI" checked> Serie CI</label>
        <label><input type="checkbox" name="columns" value="numarCI" checked> Numar CI</label>
        <label><input type="checkbox" name="columns" value="eliberatCI" checked> CI eliberat de</label>
        <label><input type="checkbox" name="columns" value="emitereCI" checked> CI emis la</label>
        <label><input type="checkbox" name="columns" value="expirareCI" checked> CI expiră la</label>
        <label><input type="checkbox" name="columns" value="dataNastere" checked> Data Nașterii</label>
        <label><input type="checkbox" name="columns" value="telefon" checked> Telefon</label>
        <label><input type="checkbox" name="columns" value="email" checked> Email</label>
        <button type="button" onclick="applyFilters()">Aplică Filtre</button>
    </form>

    <div class="table-container">
        <table class="table-volunteers">
            <tr>
                <th class="numarContract">Număr Contract</th>
                <th class="numePrenume">Nume și Prenume</th>
                <th class="domiciliu">Domiciliu</th>
                <th class="cnp">CNP</th>
                <th class="serieCI">Serie CI</th>
                <th class="numarCI">Numar CI</th>
                <th class="eliberatCI">CI eliberat de</th>
                <th class="emitereCI">CI emis la</th>
                <th class="expirareCI">CI expiră la</th>
                <th class="dataNastere">Data Nașterii</th>
                <th class="telefon">Telefon</th>
                <th class="email">Email</th>
                <th class="actiuni">Acțiuni</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='numarContract'>" . $row["NumarContract"] . "</td>";
                    echo "<td class='numePrenume'>" . $row["NumePrenume"] . "</td>";
                    echo "<td class='domiciliu'>" . $row["Domiciliu"] . "</td>";
                    echo "<td class='cnp'>" . $row["Cnp"] . "</td>";
                    echo "<td class='serieCI'>" . $row["SeriaCI"] . "</td>";
                    echo "<td class='numarCI'>" . $row["NumarCI"] . "</td>";
                    echo "<td class='eliberatCI'>" . $row["EliberatCI"] . "</td>";
                    echo "<td class='emitereCI'>" . $row["EmitereCI"] . "</td>";
                    echo "<td class='expirareCI'>" . $row["ExpirareCI"] . "</td>";
                    echo "<td class='dataNastere'>" . $row["DataNastere"] . "</td>";
                    echo "<td class='telefon'>" . $row["Telefon"] . "</td>";
                    echo "<td class='email'>" . $row["Email"] . "</td>";
                    echo "<td class='actiuni'>";
                    echo "<a href='edit_volunteer.php?NumarContract=" . $row["NumarContract"] . "' class='btn-edit'>Editează</a>";
                    echo "<a href='process_delete_volunteer.php?NumarContract=" . $row["NumarContract"] . "&Email=" . $row["Email"] . "' class='btn-delete'>Șterge</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Nu există voluntari înregistrati</td></tr>";
            }
            ?>
        </table>
    </div>


</body>

</html>
<?php
$conn->close();
?>