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
$naslov_taska = '';
$projekt_id = '';
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

$sql = "SELECT id, naslov FROM projekti WHERE skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$projekti = mysqli_stmt_get_result($stmt);

if (isset($_POST['dodaj_task'])) {
    $naslov_taska = trim($_POST['naslov_taska']);
    $datum_konca = trim($_POST['datum_konca']);
    $projekt_id = $_POST['projekt_id'];

    if (empty($naslov_taska) || empty($datum_konca) || empty($projekt_id)) {
        $obvestilo = "Izpolnite vsa polja.";
    } else {
        $danes = date('Y-m-d');

        if ($datum_konca < $danes) {
            $obvestilo = "Neveljaven datum – ne morete izbrati preteklega datuma.";
        } else {
            $sql = "INSERT INTO taski (naslov, datum_konca, projekt_id) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $naslov_taska, $datum_konca, $projekt_id);


            if (mysqli_stmt_execute($stmt)) {
                $obvestilo = "Task uspešno dodan.";
                $naslov_taska = '';
                $datum_konca = '';
                $projekt_id = '';
            } else {
                $obvestilo = "Napaka pri dodajanju taska.";
            }
        }
    }
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
                    <a href="up_projekti.php">Moji Projekti</a>
                    <a href="logout.php">Odjava</a>
                </div>
            </div>
        </td>
        <td><?= htmlspecialchars($uporabnik) ?></td>
    </tr>
</table>

<table class="tabela">
    <tr><td class="button"><h2>Dodajanje novega taska</h2></td></tr>
    <tr>
        <td style="all: unset;">
            <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
            <form method="post">
                <label for="naslov_taska" style="font-size: 30px;">Naslov taska:</label><br>
                <input type="text" id="naslov_taska" name="naslov_taska" class="polja" value="<?= htmlspecialchars($naslov_taska) ?>" required><br><br>

                <label for="datum_konca" style="font-size: 30px;">Datum konca:</label><br>
                <input type="date" id="datum_konca" name="datum_konca" class="polja" value="<?= htmlspecialchars($datum_konca) ?>" required><br><br>

                <label for="projekt_id" style="font-size: 30px;">Izberi projekt:</label><br>
                <select id="projekt_id" name="projekt_id" required style="width:200px; height:30px; border-radius:10px;">
                    <option value="">Izberi projekt</option>
                    <?php while ($p = mysqli_fetch_assoc($projekti)): ?>
                        <option value="<?= $p['id'] ?>" <?= ($projekt_id == $p['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['naslov']) ?>
                        </option>
                    <?php endwhile; ?>
                </select><br><br>

                <input type="submit" name="dodaj_task" value="Dodaj task" class="button">
            </form>
        </td>
    </tr>
    <tr>
        <td style="all:unset;">
            <a href="skupina_pod.php?ime_skupine=<?= urlencode($ime_skupine) ?>" class="button" style="width: 200px; height:25px;">Nazaj na skupino</a>
        </td>
    </tr>
</table>
</body><?php include "footer.php";?>
</html>
