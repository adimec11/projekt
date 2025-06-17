<?php
session_start();
require_once 'baza.php';

$taski_po_mesecih = array_fill(1, 12, []); 
	$uporabnik = '';
if (isset($_SESSION['idu'])) {
    $sql = "SELECT naslov, datum_začetka FROM taski WHERE uporabnik_id = ? ORDER BY datum_začetka LIMIT 50";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['idu']);
    mysqli_stmt_execute($stmt);

    $rezultat = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($rezultat)) {
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
    <td>
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
        <div class="dropdown">
            <span class="menu-btn">☰</span>
            <div class="dropdown-content">
                <a href="skupine.php">skupine</a>
                <a href="taski.php">taksi</a>
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
        <div class="sidebar">
            <span class="sidebar-gumb">☰</span>
            <div class="sidebar-vsebina">
                <a href="index.php">Profil</a>
                <a href="main.php">Koledar</a>
>>>>>>> Stashed changes
                <a href="logout.php">Odjava</a>
            </div>
        </div>
    </td>
	<td><?=htmlspecialchars($uporabnik) ?></>

</table>

<table class="koledar">
	<tr>
		<td><a href="koledar/Jan.php" class="meseci">Jan</a>
            <?php foreach ($taski_po_mesecih[1] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Feb.php" class="meseci">Feb</a>
            <?php foreach ($taski_po_mesecih[2] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Mar.php" class="meseci">Mar</a>
            <?php foreach ($taski_po_mesecih[3] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Apr.php" class="meseci">Apr</a>
            <?php foreach ($taski_po_mesecih[4] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
	</tr>
	<tr>
		<td><a href="koledar/Maj.php" class="meseci">Maj</a>
            <?php foreach ($taski_po_mesecih[5] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Jun.php" class="meseci">Jun</a>
            <?php foreach ($taski_po_mesecih[6] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Jul.php" class="meseci">Jul</a>
            <?php foreach ($taski_po_mesecih[7] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Avg.php" class="meseci">Avg</a>
            <?php foreach ($taski_po_mesecih[8] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
	</tr>
	<tr>
		<td><a href="koledar/Sep.php" class="meseci">Sep</a>
            <?php foreach ($taski_po_mesecih[9] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Okt.php" class="meseci">Okt</a>
            <?php foreach ($taski_po_mesecih[10] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Nov.php" class="meseci">Nov</a>
            <?php foreach ($taski_po_mesecih[11] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
		<td><a href="koledar/Dec.php" class="meseci">Dec</a>
            <?php foreach ($taski_po_mesecih[12] as $task): ?>
				<div class="task"><?= htmlspecialchars($task) ?></div>
            <?php endforeach; ?>
		</td>
	</tr>
</table>
</body>
</html>
