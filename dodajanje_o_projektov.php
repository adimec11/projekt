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
        $datum_konca = isset($_POST['datum_konca']) ? trim($_POST['datum_konca']) : '';

        if (!empty($ime_projekta) && !empty($datum_konca)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO projekti (naslov, datum_konca, lastnik_id, skupina_id) VALUES (?, ?, ?,NULL)");

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssi", $ime_projekta, $datum_konca, $uporabnik_id);

                if (mysqli_stmt_execute($stmt)) {
                    $obvestilo = "Projekt '$ime_projekta' je bil uspešno ustvarjen.";
                } else {
                    $obvestilo = "Napaka pri izvajanju stavka: " . mysqli_stmt_error($stmt);
                }

                mysqli_stmt_close($stmt);
            } else {
                $obvestilo = "Napaka pri pripravi SQL stavka: " . mysqli_error($conn);
            }
        } else {
            $obvestilo = "Prosim, izpolnite ime projekta in datum konca.";
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
        <td><?= htmlspecialchars($uporabnik) ?></td>
    </tr>
</table>

<table class="tabela">
    <tr><td class="button"><h2>Ustvari osebni projekt</h2></td></tr>
    <tr>
        <td style="all: unset;">
            <?php if (!empty($obvestilo)) echo '<p style="color:white; font-weight:bold;">' . htmlspecialchars($obvestilo) . '</p>'; ?>
            <form method="post">
                <label for="ime_projekta" style="font-size: 30px;">Ime projekta:</label><br>
                <input type="text" name="ime_projekta" class="polja" placeholder="Ime projekta" required><br><br>

                <label for="datum_konca" style="font-size: 30px;">Datum konca:</label><br>
                <input type="date" name="datum_konca" class="polja" min="<?= date('Y-m-d') ?>" required><br><br>

                <input type="submit" name="dodaj_projekt" value="Ustvari" class="button">
            </form>
        </td>
    </tr>
    <tr>
        <td style="all:unset;"><a href="up_projekti.php" class="button" style="width: 200px; height:25px;">Nazaj na projekte</a></td>
    </tr>
</table>

</body><?php include "footer.php";?>
</html>
