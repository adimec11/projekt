<?php
require_once "baza.php";
session_start();

$uporabnik = '';
$obvestilo = '';
$ime_skupine = '';

if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
    $uporabnik_id = $_SESSION['idu'];

    if (isset($_POST['dodaj_skupine'])) {
        $ime_skupine = isset($_POST['ime_skupine']) ? trim($_POST['ime_skupine']) : '';

        if (!empty($ime_skupine)) {
            // Preveri, če skupina s tem imenom že obstaja
            $sql = "SELECT id FROM skupine WHERE ime = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $ime_skupine);
            mysqli_stmt_execute($stmt);
            $rezultat = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($rezultat) > 0) {
                $obvestilo = "Skupina s tem imenom že obstaja. Izberite drugo ime.";
            } else {
                // Preveri, če uporabnik že obstaja v vodje_skupine
                $sql = "SELECT id FROM vodje_skupine WHERE uporabnik_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $uporabnik_id);
                mysqli_stmt_execute($stmt);
                $rezultat = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($rezultat) == 0) {
                    $sql = "INSERT INTO vodje_skupine (uporabnik_id) VALUES (?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $uporabnik_id);
                    mysqli_stmt_execute($stmt);
                    $vodja_id = mysqli_insert_id($conn);
                } else {
                    $vrstica = mysqli_fetch_array($rezultat);
                    $vodja_id = $vrstica['id'];
                }

                // Vstavi novo skupino
                $sql = "INSERT INTO skupine (ime, vodja_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $ime_skupine, $vodja_id);
                if (mysqli_stmt_execute($stmt)) {
                    $obvestilo = "Skupina '" . htmlspecialchars($ime_skupine) . "' je bila uspešno ustvarjena.";
                } else {
                    $obvestilo = "Napaka pri ustvarjanju skupine.";
                }
            }
        } else {
            $obvestilo = "Ime skupine ne sme biti prazno.";
        }
    }
} else {
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Registracija skupine</title>
    <link rel="stylesheet" href="css/stil.css">
    <link rel="icon" href="img/logo.ico">
</head>
<body>
<img src="img/logo.jpg" class="logo">

<table border="0">
    <tr>
        <td>
            <div class="sidebar">
                <span class="sidebar-gumb">☰</span>
                <div class="sidebar-vsebina">
                    <?php if (!isset($_SESSION['idu'])) echo '<a href="index.php">Login</a>'; ?>
                    <a href="main.php">Domov</a>
                    <a href="skupine.php">Skupine</a>
                    <a href="up_projekti.php">Moji Projekti</a>
                    <a href="logout.php">Odjava</a>
                </div>
            </div>
        </td>
        <td><?=htmlspecialchars($uporabnik) ?></td></tr>
</table>

<table class="tabela">
    <tr><td class="button"><h2>Registracija nove skupine</h2></td></tr>
    <tr>
        <td style="all: unset;">
            <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
            <form method="post">
                <input type="text" name="ime_skupine" class="polja" placeholder="Ime skupine">
                <input type="submit" name="dodaj_skupine" value="Ustvari" class="button">
            </form>
        </td>
    </tr>
    <tr>
        <td style="all:unset;" ><a href="skupine.php" class="button" style="width: 200px; height:25px;">Nazaj na skupine</a></td>
    </tr>
</table>

</body><?php include "footer.php";?>
</html>
