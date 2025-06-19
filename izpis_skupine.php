<?php
require_once "baza.php";
session_start();

if (!isset($_POST['ime_skupine'])) {
    die("Napaka: manjkajoči podatki o skupini.");
}

$ime_skupine = $_POST['ime_skupine'];
$idu = $_SESSION['idu'];

// 1. Pridobi ID skupine po imenu
$sql = "SELECT id FROM skupine WHERE ime = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Napaka pri pripravi poizvedbe (1): " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "s", $ime_skupine);
mysqli_stmt_execute($stmt);
$rezultat = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($rezultat);
mysqli_stmt_close($stmt);

if (!$row) {
    die("Napaka: Skupina ni bila najdena.");
}

$skupina_id = $row['id'];

// 2. Preveri, če je uporabnik vodja skupine
$sql = "SELECT s.id 
        FROM skupine s 
        JOIN vodje_skupine vs ON s.vodja_id = vs.id 
        WHERE s.id = ? AND vs.uporabnik_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Napaka pri pripravi poizvedbe (2): " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "ii", $skupina_id, $idu);
mysqli_stmt_execute($stmt);
$rezultat = mysqli_stmt_get_result($stmt);
$is_vodja = mysqli_num_rows($rezultat) > 0;
mysqli_stmt_close($stmt);

// 3. Odstrani uporabnika iz uporabniki_skupine
$sql = "DELETE FROM uporabniki_skupine WHERE uporabnik_id = ? AND skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Napaka pri pripravi poizvedbe (3): " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "ii", $idu, $skupina_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// 4. Če je bil vodja, poišči novega vodjo
if ($is_vodja) {
    $sql = "SELECT uporabnik_id FROM uporabniki_skupine WHERE skupina_id = ? ORDER BY RAND() LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Napaka pri pripravi poizvedbe (4): " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $skupina_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $novi_vodja_uporabnik_id = $row['uporabnik_id'];

        // Ustvari nov zapis v vodje_skupine
        $sql = "INSERT INTO vodje_skupine (uporabnik_id) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            die("Napaka pri pripravi poizvedbe (5): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $novi_vodja_uporabnik_id);
        mysqli_stmt_execute($stmt);
        $nova_vodja_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Posodobi skupino z novim vodjo
        $sql = "UPDATE skupine SET vodja_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            die("Napaka pri pripravi poizvedbe (6): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "ii", $nova_vodja_id, $skupina_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        // Skupina je prazna -> najprej izbriši skupino
        $sql = "DELETE FROM skupine WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            die("Napaka pri pripravi poizvedbe (7): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $skupina_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Po želji še počisti vodjo (če želiš imeti bazo "čisto")
        // (vendar to ni nujno, saj je FK ON DELETE SET NULL)
    }
}

// Preusmeri nazaj na skupine.php
header("Location: skupine.php");
exit;
?>
