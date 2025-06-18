<?php
session_start();
require_once 'baza.php';

$taski_po_mesecih = array_fill(1, 12, []); //stackpverflow

	$uporabnik = '';
if (isset($_SESSION['idu'])) {
	
    $sql = "SELECT naslov, datum_začetka FROM taski WHERE uporabnik_id = ? ORDER BY datum_začetka ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['idu']);
    mysqli_stmt_execute($stmt);

    $rezultat = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_array($rezultat)) {
        $mesec = (int)date('n', strtotime($row['datum_začetka'])); // 1 = jan, 12 = dec
        if (count($taski_po_mesecih[$mesec]) < 5) {
            $taski_po_mesecih[$mesec][] = $row['naslov'];
        }
    }


    mysqli_stmt_close($stmt);
	if (isset($_SESSION['idu'])) {
		$uporabnik = $_SESSION['polno_ime'];
	}
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
                <a href="skupine.php">Skupine</a>
                <a href="taski.php">Moji Projekti</a>
                <a href="logout.php">Odjava</a>
            </div>
        </div>
    </td>
    <td><?=htmlspecialchars($uporabnik) ?></td></tr>
</table>

<table class="koledar">
	<tr>
		<td><a href="koledar.php?mesec=1" class="meseci">Jan</a>
            <?php foreach ($taski_po_mesecih[1] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=2" class="meseci">Feb</a>
            <?php foreach ($taski_po_mesecih[2] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=3" class="meseci">Mar</a>
            <?php foreach ($taski_po_mesecih[3] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=4" class="meseci">Apr</a>
            <?php foreach ($taski_po_mesecih[4] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
	</tr>
	<tr>
		<td><a href="koledar.php?mesec=5" class="meseci">Maj</a>
            <?php foreach ($taski_po_mesecih[5] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=6" class="meseci">Jun</a>
            <?php foreach ($taski_po_mesecih[6] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=7" class="meseci">Jul</a>
            <?php foreach ($taski_po_mesecih[7] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=8" class="meseci">Avg</a>
            <?php foreach ($taski_po_mesecih[8] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
	</tr>
	<tr>
		<td><a href="koledar.php?mesec=9" class="meseci">Sep</a>
            <?php foreach ($taski_po_mesecih[9] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=10" class="meseci">Okt</a>
            <?php foreach ($taski_po_mesecih[10] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=11" class="meseci">Nov</a>
            <?php foreach ($taski_po_mesecih[11] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar.php?mesec=12" class="meseci">Dec</a>
            <?php foreach ($taski_po_mesecih[12] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
	</tr>
</table>
</body>
</html>
