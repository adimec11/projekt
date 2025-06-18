<?php
require_once "baza.php";
session_start();

$uporabnik = '';
$obvestilo = '';
$ime_projekta = '';

if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
    $uporabnik_id = $_SESSION['idu'];

    if (isset($_POST['dodaj_projekt'])) {
        $ime_projekta = isset($_POST['ime_projekta']) ? trim($_POST['ime_projekta']) : '';

        if (!empty($ime_projekta)) {
            // Escape string
            $ime_projekta_esc = mysqli_real_escape_string($conn, $ime_projekta);

            // Vstavi osebni projekt brez skupine in brez lastnik_id
            $sql = "INSERT INTO projekti (naslov, skupina_id) VALUES ('$ime_projekta_esc', NULL)";
            if (mysqli_query($conn, $sql)) {
                $obvestilo = "Projekt '$ime_projekta' je bil uspešno ustvarjen.";
            } else {
                $obvestilo = "Napaka pri ustvarjanju projekta: " . mysqli_error($conn);
            }
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
    <title>Osebni projekt</title>
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
        <td><?=htmlspecialchars($uporabnik) ?></td>
    </tr>
</table>

<table class="tabela">
    <tr><td class="button"><h2>Ustvari osebni projekt</h2></td></tr>
    <tr>
        <td style="all: unset;">
            <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
            <form method="post">
                <input type="text" name="ime_projekta" class="polja" placeholder="Ime projekta">
                <input type="submit" name="dodaj_projekt" value="Ustvari" class="button">
            </form>
        </td>
    </tr>
    <tr>
        <td style="all:unset;"><a href="up_projekti.php" class="button" style="width: 200px; height:25px;">Nazaj na projekte</a></td>
    </tr>
</table>

</body>
</html>
