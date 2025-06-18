<?php
session_start();
require_once 'baza.php';

if (!isset($_SESSION['idu'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['mesec'])) {
    $mesec = (int)$_GET['mesec'];
} else {
    $mesec = date('n');
}




$uporabnik =  '';
if (isset($_SESSION['idu'])) {
	$uporabnik = $_SESSION['polno_ime'];
}
	
$tasks_po_dnevih = [];

$sql = "SELECT naslov, datum_začetka FROM taski WHERE uporabnik_id = ? AND MONTH(datum_začetka) = ? AND YEAR(datum_začetka) = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "iii", $_SESSION['idu'], $mesec, $leto);
mysqli_stmt_execute($stmt);

$rezultat = mysqli_stmt_get_result($stmt);


while ($row = mysqli_fetch_assoc($rezultat)) {
    $dan = (int)date('j', strtotime($row['datum_začetka']));
    $tasks_po_dnevih[$dan][] = $row['naslov'];
}

mysqli_stmt_close($stmt);
//stackoverflow meseci
$prviDanMeseca = mktime(0, 0, 0, $mesec, 1, $leto);
$zacetniOffset = date('N', $prviDanMeseca); // 1 = pon, 7 = ned
$stDniVMesecu = date('t', $prviDanMeseca);
$imeMeseca = date('F', $prviDanMeseca); 
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Koledar – <?= htmlspecialchars($imeMeseca) ?> 2025</title>
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
        <td><?=htmlspecialchars($uporabnik) ?></td></tr>
</table>

<h2 style="text-align: center;"><?= htmlspecialchars($imeMeseca) ?> </h2>

<table class="tabela">
    <tr id="dnevi_v_ted">
        <td>Pon</td><td>Tor</td><td>Sre</td><td>Čet</td><td>Pet</td><td>Sob</td><td>Ned</td>
    </tr>
    <?php
    $dan = 1;
    $zacetek = true;

    while ($dan <= $stDniVMesecu) {
        echo "<tr>";
        for ($i = 1; $i <= 7; $i++) {
            if ($zacetek && $i < $zacetniOffset) {
                echo "<td></td>";
            } elseif ($dan <= $stDniVMesecu) {
                echo "<td><strong>$dan</strong>";
                if (isset($tasks_po_dnevih[$dan])) {
                    foreach ($tasks_po_dnevih[$dan] as $task) {
                        echo "<span class='task'>" . htmlspecialchars($task) . "</span>";
                    }
                }
                echo "</td>";
                $dan++;
                $zacetek = false;
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }
    ?>
</table>
</body>
</html>
