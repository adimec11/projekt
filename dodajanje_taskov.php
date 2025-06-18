<?php
require_once "baza.php";
session_start();

$uporabnik = '';
$obvestilo = '';
$naziv_taska = '';
$rok_taska = '';

if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
    $uporabnik_id = $_SESSION['idu'];

    if (isset($_POST['dodaj_taska'])) {
        $naziv_taska = trim(isset($_POST['naziv_taska']) ? $_POST['naziv_taska'] : '');
        $rok_taska = isset($_POST['rok_taska']) ? $_POST['rok_taska'] : null;

        if (!empty($naziv_taska) && !empty($rok_taska)) {
            // Vstavi novi task
            $sql = "INSERT INTO taski (naslov, datum_konca, uporabnik_id) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $naziv_taska, $rok_taska, $uporabnik_id);

            if (mysqli_stmt_execute($stmt)) {
                $obvestilo = "Task '$naziv_taska' je bil uspešno dodan.";
            } else {
                $obvestilo = "Napaka pri dodajanju taska.";
            }
        } else {
            $obvestilo = "Prosimo, izpolnite vsa polja.";
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
        <title>Dodaj task</title>
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
                        <a href="taski.php">Taski</a>
                        <a href="logout.php">Odjava</a>
                    </div>
                </div>
            </td>
            <td><?=htmlspecialchars($uporabnik) ?></td>
        </tr>
    </table>

    <table class="tabela">
        <tr><td class="button"><h2>Dodajanje novega taska</h2></td></tr>
        <tr>
            <td style="all: unset;">
                <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
                <form method="post">
                    Naziv taska: <input type="text" name="naziv_taska" class="polja" style="margin-right: 90px;"><br>
                    Rok oddaje: <input type="date" name="rok_taska" class="polja" style="margin-right: 90px;"><br>
                    <input type="submit" name="dodaj_taska" value="Ustvari" class="button">
                </form>
            </td>
        </tr>
        <tr>
            <td style="all:unset;"><a href="taski.php" class="button" style="width: 200px; height:25px;">Nazaj na taske</a></td>
        </tr>

