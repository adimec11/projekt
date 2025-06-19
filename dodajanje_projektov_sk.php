<?php
require_once 'baza.php';
session_start();

if (!isset($_SESSION['idu'])) {
    header("Location: index.php");
    exit;
}

$uporabnik = $_SESSION['polno_ime'];
$uporabnik_id = $_SESSION['idu'];
$skupina_id = null;
$obvestilo = '';
$naslov_projekta = '';
$datum_konca = '';

if (!isset($_GET['ime_skupine']) || empty(trim($_GET['ime_skupine']))) {
    die("Ime skupine ni določeno.");
}

$ime_skupine = trim($_GET['ime_skupine']);

$sql = "SELECT id FROM skupine WHERE ime = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $ime_skupine);
mysqli_stmt_execute($stmt);
$rezultat = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($rezultat)) {
    $skupina_id = $row['id'];
} else {
    die("Skupina ne obstaja.");
}

if (isset($_POST['dodaj_projekt'])) {
    $naslov_projekta = trim($_POST['naslov_projekta']);
    $datum_konca = trim($_POST['datum_konca']);

    if (empty($naslov_projekta) || empty($datum_konca)) {
        $obvestilo = "Izpolnite vse podatke.";
    } else {
        $danes = date('Y-m-d');
        if ($datum_konca < $danes) {
            $obvestilo = "Neveljaven datum – ne morete izbrati preteklega datuma.";
        } else {
            $sql = "INSERT INTO projekti (naslov, datum_konca, skupina_id) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $naslov_projekta, $datum_konca, $skupina_id);
            if (mysqli_stmt_execute($stmt)) {
                $obvestilo = "Projekt uspešno dodan.";
                $naslov_projekta = '';
                $datum_konca = '';
            } else {
                $obvestilo = "Napaka pri dodajanju projekta.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj projekt</title>
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
                    <a href="main.php">Domov</a>
                    <a href="skupine.php">Skupine</a>
                    <a href="up_projekti.php">Moji Projekti</a>
                    <a href="logout.php">Odjava</a>
                </div>
            </div>
        </td>
        <td><?= htmlspecialchars($uporabnik) ?></td>
    </tr>
</table>

<table class="tabela">
    <tr><td class="button"><h2>Dodajanje novega projekta</h2></td></tr>
    <tr>
        <td style="all: unset;">
            <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
            <form method="post">
                <label for="naslov_projekta">Naslov projekta:</label><br>
                <input type="text" id="naslov_projekta" name="naslov_projekta" class="polja" value="<?= htmlspecialchars($naslov_projekta) ?>" required><br><br>

                <label for="datum_konca">Datum konca:</label><br>
                <input type="date" id="datum_konca" name="datum_konca" class="polja" value="<?= htmlspecialchars($datum_konca) ?>" required><br><br>

                <input type="submit" name="dodaj_projekt" value="Dodaj projekt" class="button">
            </form>
        </td>
    </tr>
    <tr>
        <td style="all: unset;">
            <a href="skupina_pod.php?ime_skupine=<?= urlencode($ime_skupine) ?>" class="button" style="width: 200px; height:25px;">Nazaj na skupino</a>
        </td>
    </tr>
</table>
</body><?php include "footer.php";?>
</html>
