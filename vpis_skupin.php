<?php
require_once "baza.php";
session_start();

$uporabnik = '';
$obvestilo = '';

if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
    $uporabnik_id = $_SESSION['idu'];

    if (isset($_POST['dodaj_skupine'])) {
        $ime_skupine = trim($_POST['ime_skupine']);

        if (!empty($ime_skupine)) {
            // 1. Preveri, če uporabnik že obstaja v vodje_skupine
            $sql = "SELECT id FROM vodje_skupine WHERE uporabnik_id = $uporabnik_id";
            $rezultat = mysqli_query($conn, $sql);

            if (mysqli_num_rows($rezultat) == 0) {
                // 2. Vstavi novega vodjo
                $sql = "INSERT INTO vodje_skupine (uporabnik_id) VALUES ($uporabnik_id)";
                mysqli_query($conn, $sql);
                $vodja_id = mysqli_insert_id($conn);
            } else {
                $vrstica = mysqli_fetch_assoc($rezultat);
                $vodja_id = $vrstica['id'];
            }

            // 3. Vstavi novo skupino
            $ime_skupine_esc = mysqli_real_escape_string($conn, $ime_skupine);
            $sql = "INSERT INTO skupine (ime, vodja_id) VALUES ('$ime_skupine_esc', $vodja_id)";
            if (mysqli_query($conn, $sql)) {
                $obvestilo = "Skupina '$ime_skupine' je bila uspešno ustvarjena.";
            } else {
                $obvestilo = "Napaka pri ustvarjanju skupine.";
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
    <td>
        <div class="sidebar">
            <span class="sidebar-gumb">☰</span>
            <div class="sidebar-vsebina">
                <a href="main.php">Domov</a>
                <a href="skupine.php">Skupine</a>
                <a href="taski.php">Taski</a>
                <a href="logout.php">Odjava</a>
            </div>
        </div>
    </td>
    <td><?= htmlspecialchars($uporabnik) ?></td>
</table>

<table class="koledar">
    <tr><td class="button"><h2>Registracija nove skupine</h2></td></tr>
    <tr>
        <td style="all: unset;">
            <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
            <form method="post">
                <input type="text" name="ime_skupine" class="polja" placeholder="Ime skupine" class="button">
                <input type="submit" name="dodaj_skupine" value="Ustvari" class="button">
            </form>
        </td>
    </tr>
    <tr>
        <td style="all:unset;" ><a href="skupine.php" class="button" style="width: 200px; height:25px;">Nazaj na skupine</a></td>
    </tr>
</table>

</body>
</html>
