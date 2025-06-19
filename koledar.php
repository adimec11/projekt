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

$leto = date('Y');

$uporabnik = '';
if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
}

$tasks_po_dnevih_konec = [];

$sql_konec = "SELECT naslov, datum_konca FROM taski WHERE uporabnik_id = ? AND MONTH(datum_konca) = ? AND YEAR(datum_konca) = ?";
$stmt_konec = mysqli_prepare($conn, $sql_konec);
mysqli_stmt_bind_param($stmt_konec, "iii", $_SESSION['idu'], $mesec, $leto);
mysqli_stmt_execute($stmt_konec);
$rezultat_konec = mysqli_stmt_get_result($stmt_konec);

while ($row = mysqli_fetch_assoc($rezultat_konec)) {
    $dan = (int)date('j', strtotime($row['datum_konca']));
    $tasks_po_dnevih_konec[$dan][] = $row['naslov'];
}
mysqli_stmt_close($stmt_konec);


$prviDanMeseca = mktime(0, 0, 0, $mesec, 1, $leto);
$zacetniOffset = date('N', $prviDanMeseca);
$stDniVMesecu = date('t', $prviDanMeseca);
$imeMeseca = date('F', $prviDanMeseca);
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Koledar – <?= htmlspecialchars($imeMeseca) ?> <?= $leto ?></title>
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

<h2 style="text-align: center;"><?= htmlspecialchars($imeMeseca) ?></h2>

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
                echo "<td style='width:100px;'><strong>$dan</strong>";

                if (isset($tasks_po_dnevih_konec[$dan])) {
                    foreach ($tasks_po_dnevih_konec[$dan] as $task) {
                        echo "<div class='task'>" . htmlspecialchars($task) . "</div>";
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
<?php include "footer.php"; ?>
</html>
