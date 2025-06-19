<?php
require_once 'baza.php';
session_start();

if (!isset($_SESSION['idu'])) {
    header("Location: index.php");
    exit;
}

$uporabnik = $_SESSION['polno_ime'];
$uporabnik_id = $_SESSION['idu'];
$projekt_id = '';
$naslov_taska = '';
$datum_konca = '';
$obvestilo = '';

if (isset($_POST['projekt_id'])) {
    $projekt_id = intval($_POST['projekt_id']);
} elseif (isset($_GET['projekt_id'])) {
    $projekt_id = intval($_GET['projekt_id']);
} else {
    die("Projekt ni določen.");
}

$sql_preveri = "SELECT id, naslov FROM projekti WHERE id = ? AND lastnik_id = ? AND skupina_id IS NULL";
$stmt_preveri = mysqli_prepare($conn, $sql_preveri);
mysqli_stmt_bind_param($stmt_preveri, "ii", $projekt_id, $uporabnik_id);
mysqli_stmt_execute($stmt_preveri);
$result_preveri = mysqli_stmt_get_result($stmt_preveri);

if ($projekt = mysqli_fetch_assoc($result_preveri)) {
    $naslov_projekta = $projekt['naslov'];
} else {
    die("Projekt ne obstaja ali nimate dovoljenja.");
}


if (isset($_POST['dodaj_task'])) {
    $naslov_taska = trim($_POST['naslov_taska']);
    $datum_konca = trim($_POST['datum_konca']);

    if (empty($naslov_taska) || empty($datum_konca)) {
        $obvestilo = "Izpolnite vsa polja.";
    } else {
        $danes = date('Y-m-d');

        if ($datum_konca < $danes) {
            $obvestilo = "Neveljaven datum – ne morete izbrati preteklega datuma.";
        } else {
            $sql_insert = "INSERT INTO taski (naslov, datum_konca, projekt_id, status) VALUES (?, ?, ?, 0)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "ssi", $naslov_taska, $datum_konca, $projekt_id);

            if (mysqli_stmt_execute($stmt_insert)) {
                $obvestilo = "Task uspešno dodan.";
                $naslov_taska = '';
                $datum_konca = '';
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
    <title>Dodaj Osebni Task</title>
    <link rel="stylesheet" href="css/stil.css">
    <link rel="icon" href="img/logo.ico">
</head>
<body>
<img src="img/logo.jpg" class="logo">

<!-- Sidebar -->
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

<!-- Dodaj Task -->
<table class="tabela">
    <tr>
        <td class="button"><h2>Dodajanje taska v projekt: <?= htmlspecialchars($naslov_projekta) ?></h2></td>
    </tr>
    <tr>
        <td style="all:unset;">
            <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
            <form method="post">
                <input type="hidden" name="projekt_id" value="<?= $projekt_id ?>">

                <label for="naslov_taska" style="font-size: 30px;">Naslov taska:</label><br>
                <input type="text" id="naslov_taska" name="naslov_taska" class="polja" value="<?= htmlspecialchars($naslov_taska) ?>" required><br><br>

                <label for="datum_konca" style="font-size: 30px;">Datum konca:</label><br>
                <input type="date" id="datum_konca" name="datum_konca" class="polja" value="<?= htmlspecialchars($datum_konca) ?>" required><br><br>

                <input type="submit" name="dodaj_task" value="Dodaj task" class="button">
            </form>
        </td>
    </tr>
    <tr>
        <td style="all:unset;">
            <a href="up_projekti.php" class="button" style="width:200px; height:25px;">Nazaj na seznam projektov</a>
        </td>
    </tr>
</table>

</body>
<?php include "footer.php"; ?>
</html>
