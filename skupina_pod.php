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

// Pridobi ID skupine in vodjo
$sql = "SELECT id, vodja_id FROM skupine WHERE ime = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $ime_skupine);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$skupina = mysqli_fetch_assoc($res);

if (!$skupina) {
    die("Skupina ne obstaja.");
}

$skupina_id = $skupina['id'];
$vodja_skupine_id = $skupina['vodja_id'];

// Pridobi uporabnika, ki je vodja (iz tabele vodje_skupine)
$vodja_id = null;
if ($vodja_skupine_id) {
    $sql = "SELECT uporabnik_id FROM vodje_skupine WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $vodja_skupine_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        $vodja_id = $row['uporabnik_id'];
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
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $nov_id = $row['id'];
        // Dodaj uporabnika v skupino (če še ni član)
        $sql = "INSERT IGNORE INTO uporabniki_skupine (uporabnik_id, skupina_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $nov_id, $skupina_id);
        mysqli_stmt_execute($stmt);
        $msg = "Uporabnik uspešno dodan.";
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
$sql = "SELECT naslov FROM projekti WHERE skupina_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$projekt = mysqli_fetch_assoc($res)['naslov'] || 'Ni projekta';

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
$kliknjen_task = $_POST['kliknjen_task'] || null;
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8" />
    <title>Podrobnosti skupine</title>
    <link rel="stylesheet" href="css/stil.css" />
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

<h3>Vodja skupine:
    <?= $je_vodja ? '<strong>Ti si vodja</strong>' : 'Uporabnik ID: ' . htmlspecialchars($vodja_id) ?>
</h3>

<?php if ($je_vodja): ?>
    <form method="post">
        <input type="text" name="username" placeholder="Uporabniško ime" required />
        <input type="submit" name="dodaj_uporabnika" value="Dodaj uporabnika" />
    </form>
    <p><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<h3>Taski:</h3>
<ul>
    <?php while ($t = mysqli_fetch_assoc($taski)): ?>
        <li>
            <?php if ($je_vodja): ?>
                <?= htmlspecialchars($t['naslov']) ?> (<?= htmlspecialchars($t['uporabnisko_ime'] || 'brez uporabnika') ?>)
            <?php else: ?>
                <?php if ($kliknjen_task == $t['id']): ?>
                    <?= htmlspecialchars($_SESSION['uporabnisko_ime']) ?>
                <?php else: ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="kliknjen_task" value="<?= $t['id'] ?>" />
                        <input type="submit" value="<?= htmlspecialchars($t['naslov']) ?>" />
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </li>
    <?php endwhile; ?>
</ul>

<a href="skupine.php">Nazaj na seznam skupin</a>

</body>
</html>
