<?php
require_once 'baza.php';
session_start();

if (!isset($_SESSION['idu'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['ime_skupine']) || empty(trim($_GET['ime_skupine']))) {
    die("Ime skupine ni določeno.");
}

$ime_skupine = trim($_GET['ime_skupine']);

// Poiščemo ID skupine iz imena
$sql = "SELECT id FROM skupine WHERE ime = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $ime_skupine);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
if (!$row) {
    die("Skupina s tem imenom ne obstaja.");
}
$skupina_id = $row['id'];

// Nadaljujemo kot prej — poiščemo vodjo
$sql = "SELECT vs.uporabnik_id FROM skupine s JOIN vodje_skupine vs ON s.vodja_id = vs.id WHERE s.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
if (!$row) {
    die("Skupina nima določenega vodje.");
}
$vodja = $row['uporabnik_id'];
$je_vodja = $_SESSION['idu'] == $vodja;

// Dodajanje uporabnika (če je vodja in je poslan POST z uporabniškim imenom)
$msg = '';
if ($je_vodja && isset($_POST['dodaj_uporabnika'])) {
    $username = trim($_POST['username']);
    $sql = "SELECT id FROM uporabniki WHERE uporabnisko_ime = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $nov_id = $row['id'];
        $sql = "INSERT IGNORE INTO uporabniki_skupine (uporabnik_id, skupina_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $nov_id, $skupina_id);
        mysqli_stmt_execute($stmt);
        $msg = "Uporabnik dodan.";
    } else {
        $msg = "Uporabnik ne obstaja.";
    }
}

// Pridobimo uporabnike skupine
$sql = "SELECT u.uporabnisko_ime FROM uporabniki_skupine us JOIN uporabniki u ON us.uporabnik_id = u.id WHERE us.skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$uporabniki = mysqli_stmt_get_result($stmt);

// Pridobimo projekt skupine
$sql = "SELECT p.naslov FROM projekti p WHERE p.skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$projekt = mysqli_fetch_assoc($res)['naslov'];

// Pridobimo taski skupine preko projekta
$sql = "SELECT t.id, t.naslov FROM taski t JOIN projekti p ON t.projekt_id = p.id WHERE p.skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$taski = mysqli_stmt_get_result($stmt);

$kliknjen_task = $_POST['kliknjen_task'];
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Podrobnosti skupine</title>
    <link rel="stylesheet" href="css/stil.css">
</head>
<body>
<h2>Skupina: <?= htmlspecialchars($ime_skupine) ?></h2>

<h3>Projekt: <?= htmlspecialchars($projekt) ?></h3>

<h3>Člani skupine:</h3>
<ul>
    <?php while ($u = mysqli_fetch_assoc($uporabniki)): ?>
        <li><?= htmlspecialchars($u['uporabnisko_ime']) ?></li>
    <?php endwhile; ?>
</ul>

<h3>Vodja: <?= $je_vodja ? '<strong>Ti si vodja</strong>' : 'Uporabnik ID: ' . htmlspecialchars($vodja) ?></h3>

<?php if ($je_vodja): ?>
    <form method="post">
        <input type="text" name="username" placeholder="Uporabniško ime" required>
        <input type="submit" name="dodaj_uporabnika" value="Dodaj uporabnika">
    </form>
    <p><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<h3>Taski:</h3>
<ul>
    <?php while ($t = mysqli_fetch_assoc($taski)): ?>
        <li>
            <?php if ($kliknjen_task == $t['id']): ?>
                <?= htmlspecialchars($_SESSION['polno_ime']) ?>
            <?php else: ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="kliknjen_task" value="<?= $t['id'] ?>">
                    <input type="submit" value="<?= htmlspecialchars($t['naslov']) ?>">
                </form>
            <?php endif; ?>
        </li>
    <?php endwhile; ?>
</ul>

<a href="skupine.php">Nazaj na skupine</a>
</body>
</html>
