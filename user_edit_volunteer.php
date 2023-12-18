<?php
session_start();

@include 'config.php';

require 'C:\xampp\htdocs\BFO\vendor\autoload.php'; // Încarcă toate bibliotecile Composer

// Inițializează variabila pentru mesajele de eroare;
$errorMessage = '';

// Setează detaliile pentru conectarea la baza de date MySQL;
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Inițializează o nouă conexiune MySQLi cu baza de date;
$conn = new mysqli($host, $user, $password, $dbname);

// Verifică dacă conexiunea la baza de date a reușit sau nu;
if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

// Verificați dacă Id-ul a fost transmis
if (isset($_GET['NumarContract']) && is_numeric($_GET['NumarContract'])) {
    $volunteer_id = $_GET['NumarContract'];

    // Pregătiți interogarea pentru a obține detaliile voluntarului
    $stmt = $conn->prepare("SELECT * FROM volunteer WHERE NumarContract = ?");
    $stmt->bind_param("i", $volunteer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Uplode</title>
            <link rel="stylesheet" href="css/style.css">
        </head>

        <body>
            <div class="recentCustomers">
                <div class="cardHeader">
                    <h2 align='center'>
                        Editează date personale:
                    </h2>
                </div>
                <div class="cardHeader">
                    <div class="container">
                        <form action='user_update_volunteer.php' method='post'>
                            <input type='hidden' name='NumarContract' value='<?php echo $row['NumarContract']; ?>'>
                            <div class='form-field'>
                                <label for='NumePrenume'>Nume și prenume:</label>
                                <input type='text' id='NumePrenume' name='NumePrenume' value='<?php echo $row['NumePrenume']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for='Domiciliu'>Domiciliu:</label>
                                <input type='text' id='Domiciliu' name='Domiciliu' value='<?php echo $row['Domiciliu']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for='Cnp'>Cnp:</label>
                                <input type='text' id='Cnp' name='Cnp' value='<?php echo $row['Cnp']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for='SeriCI'>SeriaCI:</label>
                                <input type='text' id='SeriaCI' name='SeriaCI' value='<?php echo $row['SeriaCI']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for='NumarCI'>NumarCI:</label>
                                <input type='text' id='NumarCI' name='NumarCI' value='<?php echo $row['NumarCI']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for='EliberatCI'>EliberatCI:</label>
                                <input type='text' id='EliberatCI' name='EliberatCI' value='<?php echo $row['EliberatCI']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for="EmitereCI">CI valabil de la:</label>
                                <input type='date' id='EmitereCI' name='EmitereCI' value='<?php echo $row['EmitereCI']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for="ExpirareCI">CI valabil până la:</label>
                                <input type='date' id='ExpirareCI' name='ExpirareCI' value='<?php echo $row['ExpirareCI']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for="DataNastere">Data nașterii:</label>
                                <input type='date' id='DataNastere' name='DataNastere' value='<?php echo $row['DataNastere']; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for='Telefon'>Telefon:</label>
                                <input type='text' id='Telefon' name='Telefon' value='<?php echo isset($row['Telefon']) ? $row['Telefon'] : ''; ?>' required>
                            </div>
                            <div class='form-field'>
                                <label for='Email'>Email:</label>
                                <input type='text' id='Email' name='Email' value='<?php echo isset($row['Email']) ? $row['Email'] : ''; ?>' required>
                            </div>
                            <div class='form-field'>
                                <button type='submit' name='user_update_volunteer' class='button'>Actualizează</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Aici se termină formularul de editare -->
        </body>
<?php
    } else {
        $_SESSION['response'] = "Voluntarul nu a fost găsit.";
        $_SESSION['res_type'] = "error";
        header("Location: user_profile.php");
        exit;
    }

    $stmt->close();
} else {
    $_SESSION['response'] = "ID invalid.";
    $_SESSION['res_type'] = "error";
    header("Location: user_profile.php");
    exit;
}

// Închide conexiunea cu baza de date;
$conn->close();
?>