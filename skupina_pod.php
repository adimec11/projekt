<?php
require_once 'baza.php';
session_start();

if (!isset($_SESSION['idu'])) {
    header("Location: index.php");
    exit;
} else {
    $uporabnik = $_SESSION['polno_ime'];
}

if (!isset($_GET['ime_skupine']) || empty(trim($_GET['ime_skupine']))) {
    die("Ime skupine ni določeno.");
}

$ime_skupine = trim($_GET['ime_skupine']);

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

$vodja_id = null;
$vodja_ime = '';
$vodja_priimek = '';

if ($vodja_skupine_id) {
    $sql = "SELECT u.id, u.ime, u.priimek 
            FROM vodje_skupine vs
            INNER JOIN uporabniki u ON vs.uporabnik_id = u.id
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

if (isset($_POST['projekt_opravljen']) && $je_vodja) {
    $projekt_id = intval($_POST['projekt_id']);
    $sql = "UPDATE projekti SET status = 1 WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $projekt_id);
    mysqli_stmt_execute($stmt);
    header("Location: skupina_pod.php?ime_skupine=" . urlencode($ime_skupine));
    exit;
}

if (isset($_POST['prevzemi_task']) && !$je_vodja) {
    $task_id = (int)$_POST['prevzemi_task'];
    $uporabnik_id = $_SESSION['idu'];

    $sql = "SELECT t.id 
            FROM taski t
            INNER JOIN projekti p ON t.projekt_id = p.id
            WHERE t.id = ? AND p.skupina_id = ? AND t.uporabnik_id IS NULL";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $skupina_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $sql = "UPDATE taski SET uporabnik_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $uporabnik_id, $task_id);
        mysqli_stmt_execute($stmt);
    }
}

$msg = '';
if ($je_vodja && isset($_POST['dodaj_uporabnika'])) {
    $username = trim($_POST['username']);
    $sql = "SELECT id FROM uporabniki WHERE uporabnisko_ime = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $rezultat = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_array($rezultat)) {
        $nov_id = $row['id'];
        $sql = "INSERT IGNORE INTO uporabniki_skupine (uporabnik_id, skupina_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $nov_id, $skupina_id);
        mysqli_stmt_execute($stmt);
    } else {
        $msg = "Uporabnik ne obstaja.";
    }
}

$sql = "SELECT u.uporabnisko_ime 
        FROM uporabniki_skupine us 
        INNER JOIN uporabniki u ON us.uporabnik_id = u.id 
        WHERE us.skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$uporabniki = mysqli_stmt_get_result($stmt);

$sql = "SELECT id, naslov, status FROM projekti WHERE skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$projekti = mysqli_stmt_get_result($stmt);

$sql = "SELECT t.id, t.naslov, t.opis, t.status, u.uporabnisko_ime, p.naslov AS projekt
        FROM taski t
        INNER JOIN projekti p ON t.projekt_id = p.id
        LEFT JOIN uporabniki u ON t.uporabnik_id = u.id
        WHERE p.skupina_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$taski = mysqli_stmt_get_result($stmt);

if (isset($_POST['task_opravljen'])) {
    $task_id = intval($_POST['task_id']);
    $sql_opravljeno = "UPDATE taski SET status = 1 WHERE id = ? LIMIT 1";
    $stmt_opravljeno = mysqli_prepare($conn, $sql_opravljeno);
    mysqli_stmt_bind_param($stmt_opravljeno, "i", $task_id);
    mysqli_stmt_execute($stmt_opravljeno);
    header("Location: skupina_pod.php?ime_skupine=" . urlencode($ime_skupine));
    exit;
}
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

<h2 style="font-size: 40px;"><?= htmlspecialchars($ime_skupine) ?></h2>

<h3 style="font-size: 30px;">Projekti:</h3>
<span style="font-size:30px;">
    <?php if (mysqli_num_rows($projekti) > 0): ?>
        <?php while ($p = mysqli_fetch_array($projekti)): ?>

                <?= htmlspecialchars($p['naslov']) ?>
                <?php if ($je_vodja): ?>
                    <?php if ($p['status'] == 1): ?>
						✅ (Opravljen)
                    <?php else: ?>
						<form method="post" style="display:inline;">
							<input type="hidden" name="projekt_id" value="<?= (int)$p['id'] ?>">
							<button type="submit" name="projekt_opravljen" class="button" style="font-size:14px;">Označi kot opravljeno</button>
						</form>
                    <?php endif; ?>
                <?php else: ?>
                    <?= $p['status'] == 1 ? '✅ (Opravljen)' : '' ?>
                <?php endif; ?>

        <?php endwhile; ?>
    <?php else: ?>
		Ni projektov
    <?php endif; ?>
</span>

<table class="tabela">
	<tr>
		<td style="vertical-align: top;">
			<h3>Vodja skupine:</h3>
			<p><?= $je_vodja ? '<strong>Ti si vodja</strong>' : htmlspecialchars($vodja_ime . ' ' . $vodja_priimek); ?></p>
		</td>
		<td style="vertical-align: top;">
			<h3>Člani skupine:</h3>

                <?php while ($u = mysqli_fetch_array($uporabniki)): ?>
					<li style="float:left;"><?= htmlspecialchars($u['uporabnisko_ime']) ?></li><br>
                <?php endwhile; ?>

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
		<td style="vertical-align: top;">
			<h3>Taski:</h3>
			<form method="post">

                    <?php while ($t = mysqli_fetch_array($taski)): ?>
                        <?php
                        $je_dodeljen = !empty($t['uporabnisko_ime']);
                        $je_moj_task = isset($_SESSION['uporabnisko_ime']) && $t['uporabnisko_ime'] === $_SESSION['uporabnisko_ime'];
                        $je_opravljen = $t['status'] == 1;
                        ?>

                            <?php if (!$je_vodja && !$je_dodeljen): ?>
								<button type="submit" name="prevzemi_task" value="<?= (int)$t['id'] ?>" class="button">
                                    <?= htmlspecialchars($t['naslov']) ?> (<?= htmlspecialchars($t['projekt']) ?>)
								</button>
                            <?php else: ?>
								<strong><?= htmlspecialchars($t['naslov']) ?></strong>
                                <?= $je_opravljen ? '✅' : '' ?><br>
								Projekt: <?= htmlspecialchars($t['projekt']) ?>,
								Uporabnik: <?= htmlspecialchars($t['uporabnisko_ime']) ?><br>
                                <?php if ((!$je_opravljen) && ($je_moj_task || $je_vodja)): ?>
									<form method="post" style="display:inline;">
										<input type="hidden" name="task_id" value="<?= $t['id'] ?>">
										<button type="submit" name="task_opravljen" class="button">Označi kot opravljeno</button>
									</form>
                                <?php endif; ?>
                            <?php endif; ?>
						<br>
                    <?php endwhile; ?>

			</form>
            <?php if ($je_vodja): ?>
				<form method="get" action="dodajanje_taskov_sk.php">
					<input type="hidden" name="ime_skupine" value="<?= htmlspecialchars($ime_skupine) ?>">
					<input type="submit" name="dodaj_task" value="Dodaj task" class="button">
				</form>
				<form method="get" action="dodajanje_projektov_sk.php">
					<input type="hidden" name="ime_skupine" value="<?= htmlspecialchars($ime_skupine) ?>">
					<input type="submit" name="dodaj_projekt" value="Dodaj projekt" class="button">
				</form>
            <?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>
			<a href="skupine.php" style="text-decoration: none;color:black;">Nazaj na seznam skupin</a>
		</td>
		<td style="all:unset; float:left;">
			<form method="post" action="izpis_skupine.php">
				<input type="hidden" name="ime_skupine" value="<?= htmlspecialchars($ime_skupine) ?>">
				<input type="submit" name="zapusti_skupino" value="Zapusti skupino" class="button" style="border-color:red; color:red;">
			</form>
		</td>
	</tr>
</table>
</body>
<?php include "footer.php"; ?>
</html>
