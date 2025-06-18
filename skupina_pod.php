<?php
require_once 'baza.php';
session_start();

if (!isset($_SESSION['idu'])) {
    header("Location: index.php");
    exit;
}else{
    $uporabnik = $_SESSION['polno_ime'];
}

if (!isset($_GET['ime_skupine']) || empty(trim($_GET['ime_skupine']))) {
    die("Ime skupine ni določeno.");
}

$ime_skupine = trim($_GET['ime_skupine']);

// Pridobi ID skupine in vodjo
$sql = "SELECT id, vodja_id FROM skupine WHERE ime = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $ime_skupine);
mysqli_stmt_execute($stmt);
$rezultat = mysqli_stmt_get_result($stmt);
$skupina = mysqli_fetch_array($rezultat);

if (!$skupina) {
    die("Skupina ne obstaja.");
}

$skupina_id = $skupina['id'];
$vodja_skupine_id = $skupina['vodja_id'];

// Pridobi uporabnika, ki je vodja (iz tabele vodje_skupine)
$vodja_id = null;

if ($vodja_skupine_id) {
    $sql = "SELECT u.id, u.ime, u.priimek 
            FROM vodje_skupine vs
            JOIN uporabniki u ON vs.uporabnik_id = u.id
            WHERE vs.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $vodja_skupine_id);
    mysqli_stmt_execute($stmt);
    $rezultat = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_array($rezultat)) {
        $vodja_id = $row['id'];
        $vodja_ime = $row['ime'];
        $vodja_priimek = $row['priimek'];
    }
}

$je_vodja = $_SESSION['idu'] == $vodja_id;

// Dodajanje uporabnika v skupino (če je vodja in je poslan POST)
$msg = '';
if ($je_vodja && isset($_POST['dodaj_uporabnika'])) {
    $username = trim($_POST['username']);
    // Poišči ID uporabnika po uporabniškem imenu
    $sql = "SELECT id FROM uporabniki WHERE uporabnisko_ime = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $rezultat = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_array($rezultat)) {
        $nov_id = $row['id'];
        // Dodaj uporabnika v skupino (če še ni član)
        $sql = "INSERT IGNORE INTO uporabniki_skupine (uporabnik_id, skupina_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $nov_id, $skupina_id);
        mysqli_stmt_execute($stmt);

    } else {
        $msg = "Uporabnik ne obstaja.";
    }
}

// Pridobi vse člane skupine
$sql = "SELECT u.uporabnisko_ime FROM uporabniki_skupine us JOIN uporabniki u ON us.uporabnik_id = u.id WHERE us.skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$uporabniki = mysqli_stmt_get_result($stmt);

// Pridobi projekt skupine (prvi projekt)
$sql = "SELECT naslov FROM projekti WHERE skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$projekti = mysqli_stmt_get_result($stmt);

// Pridobi taske glede na to, ali je uporabnik vodja
if ($je_vodja) {
    $sql = "SELECT t.id, t.naslov, u.uporabnisko_ime 
            FROM taski t 
            LEFT JOIN uporabniki u ON t.uporabnik_id = u.id
            JOIN projekti p ON t.projekt_id = p.id 
            WHERE p.skupina_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $skupina_id);
    mysqli_stmt_execute($stmt);
    $taski = mysqli_stmt_get_result($stmt);
} else {
    $uporabnik_id = $_SESSION['idu'];
    $sql = "SELECT t.id, t.naslov 
            FROM taski t 
            JOIN projekti p ON t.projekt_id = p.id 
            WHERE p.skupina_id = ? AND t.uporabnik_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $skupina_id, $uporabnik_id);
    mysqli_stmt_execute($stmt);
    $taski = mysqli_stmt_get_result($stmt);
}

// Obdelava klika na task brez JavaScript
$kliknjen_task = isset($_POST['kliknjen_task']) ? $_POST['kliknjen_task'] : null;
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Glavna stran</title>
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
                    <a href="skupine.php">Skupine</a>
                    <a href="taski.php">Taski</a>
                    <a href="logout.php">Odjava</a>
                </div>
            </div>
        </td>
        <td><?=htmlspecialchars($uporabnik) ?></td></tr>
</table>

<h2 style="font-size: 40px;"><?= htmlspecialchars($ime_skupine) ?> </h2>

<h3 style="font-size: 30px;">Projekti:</h3>
<ul style="font-size:20px;">
    <?php
    if (mysqli_num_rows($projekti) > 0):
        while ($p = mysqli_fetch_array($projekti)):
            ?>
            <li><?= htmlspecialchars($p['naslov']) ?></li>
        <?php endwhile; else: ?>
        <li>Ni projektov</li>
    <?php endif; ?>
</ul>
<table class="tabela">
    <tr>
        <!-- VODJA SKUPINE -->
        <td>
            <h3>Vodja skupine:</h3>
            <p>
                <?= $je_vodja
                    ? '<strong>Ti si vodja</strong>'
                    : htmlspecialchars($vodja_ime . ' ' . $vodja_priimek); ?>
            </p>
        </td>

        <!-- ČLANI SKUPINE -->
        <td>
            <h3>Člani skupine:</h3>
            <ul>
                <?php while ($u = mysqli_fetch_array($uporabniki)): ?>
                    <li style="float:left;"><?= htmlspecialchars($u['uporabnisko_ime']) ?></li><br>
                <?php endwhile; ?>
            </ul>

            <?php if ($je_vodja): ?>
                <form method="post">
                    <input type="text" name="username" placeholder="Uporabniško ime" required class="polja">
                    <input type="submit" name="dodaj_uporabnika" value="Dodaj uporabnika" class="button">
                </form>
                <?php if (!empty($msg)): ?>
                    <p><?= htmlspecialchars($msg) ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </td>

        <!-- TASKI -->
        <td>
            <h3>Taski ki so na voljo:</h3>
            <ul>
                <?php while ($t = mysqli_fetch_array($taski)): ?>
                    <li>
                        <?php if ($je_vodja): ?>
                            <?= htmlspecialchars($t['naslov']) ?> (<?= htmlspecialchars(isset($t['uporabnisko_ime']) ? $t['uporabnisko_ime'] : 'brez uporabnika') ?>)
                        <?php else: ?>
                            <?php if ($kliknjen_task == $t['id']): ?>
                                <?= htmlspecialchars($uporabnik) ?>
                            <?php else: ?>
                                <form method="post">
                                    <input type="hidden" class="polja" name="kliknjen_task" value="<?= $t['id'] ?>">
                                    <input type="submit"  class="button" value="<?= htmlspecialchars($t['naslov']) ?>">
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>

                    </li><br>
                <?php endwhile; ?>
            </ul><?php if ($je_vodja): ?>
                <form method="post" action="dodajanje_taskov.php"><input type="submit" name="dodaj_task" value="dodaj_task" class="button"></form>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>
            <a href="skupine.php" style="text-decoration: none;color:black;">Nazaj na seznam skupin</a>
        </td>
    </tr>
</table>


</body>
</html>
