<?php
// edit_volunteer.php

// Detalii pentru conectarea la baza de date
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bfo";

// Crearea conexiunii MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Verificați dacă Id-ul a fost transmis
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $volunteer_id = $_GET['id'];

    // Pregătiți interogarea pentru a obține detaliile voluntarului
    $stmt = $conn->prepare("SELECT * FROM volunteer WHERE Id = ?");
    $stmt->bind_param("i", $volunteer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Formularul de editare
        echo "<div id='GestionareVoluntarii' class='tabcontent'>
                 <h2>
                    <p align='center'>GESTIONARE BAZA DE DATE VOLUNTARII</p>
                 </h2>
                 <h3>Editează voluntar:</h3>
                 <form action='update_volunteer.php' method='post'>
                    <input type='hidden' name='Id' value='" . $row['Id'] . "'>
                    <div class='form-field'>
                       <input type='text' id='NumePrenume' name='NumePrenume' placeholder='Nume și prenume:' value='" . $row['NumePrenume'] . "' required>
                    </div>
                    <div class='form-field'>
                       <input type='text' id='Domiciliu' name='Domiciliu' placeholder='Domiciliu:' value='" . $row['Domiciliu'] . "' required>
                    </div>
                    <div class='form-field'>
                       <input type='text' id='Cnp' name='Cnp' placeholder='CNP:' value='" . $row['Cnp'] . "' required>
                    </div>
                    <div class='form-field'>
                       <input type='text' id='SeriaCI' name='SeriaCI' placeholder='Seria CI:' value='" . $row['SeriaCI'] . "' required>
                    </div>
                    <div class='form-field'>
                       <input type='text' id='NumarCI' name='NumarCI' placeholder='Număr CI:' value='" . $row['NumarCI'] . "' required>
                    </div>
                    <div class='form-field'>
                       <input type='text' id='EliberatCI' name='EliberatCI' placeholder='CI emis de:' value='" . $row['EliberatCI'] . "' required>
                    </div>
                    <div class='form-field'>
                       <label for='EmitereCI'>CI valabil de la:</label>
                       <input type='date' id='EmitereCI' name='EmitereCI' value='" . $row['EmitereCI'] . "' required>
                    </div>
                    <div class='form-field'>
                       <label for='ExpirareCI'>CI valabil până la:</label>
                       <input type='date' id='ExpirareCI' name='ExpirareCI' value='" . $row['ExpirareCI'] . "' required>
                    </div>
                    <div class='form-field'>
                       <label for='DataNastere'>Data nașterii:</label>
                       <input type='date' id='DataNastere' name='DataNastere' value='" . $row['DataNastere'] . "' required>
                    </div>
                    <div class='form-field'>
                       <input type='text' id='Telefon' name='Telefon' placeholder='Telefon:' value='" . $row['Telefon'] . "' required>
                    </div>
                    <div class='form-field'>
                       <input type='email' id='Email' name='Email' placeholder='Email:' value='" . (isset($row['Email']) ? $row['Email'] : '') . "' required>
                    </div>
                    <div class='form-field'>
                       <button type='submit' name='update_volunteer' class='button'>Actualizează Voluntar</button>
                    </div>
                 </form>
              </div>";
    } else {
        echo "Voluntarul nu a fost găsit.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID invalid.";
}
