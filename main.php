<?php
session_start();
require_once 'baza.php';

$taski_po_mesecih = array_fill(1, 12, []);

$uporabnik = '';
if (isset($_SESSION['idu'])) {

    $sql_pravica = "SELECT pravica_id FROM uporabniki WHERE id = ?";
    $stmt_pravica = mysqli_prepare($conn, $sql_pravica);
    mysqli_stmt_bind_param($stmt_pravica, "i", $_SESSION['idu']);
    mysqli_stmt_execute($stmt_pravica);
    $rez_pravica = mysqli_stmt_get_result($stmt_pravica);
    if ($row_pravica = mysqli_fetch_assoc($rez_pravica)) {
        $_SESSION['pravica_id'] = $row_pravica['pravica_id'];
    }
    mysqli_stmt_close($stmt_pravica);

    $sql = "
        SELECT p.id AS projekt_id, p.naslov AS projekt_naslov, 
               t.naslov AS task_naslov, t.datum_začetka
        FROM taski t
        JOIN projekti p ON t.projekt_id = p.id
        LEFT JOIN skupine s ON p.skupina_id = s.id
        WHERE t.uporabnik_id = ?
        ORDER BY t.datum_začetka
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['idu']);
    mysqli_stmt_execute($stmt);
    $rezultat = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($rezultat)) {
        $mesec = (int)date('n', strtotime($row['datum_začetka']));
        $projekt_id = $row['projekt_id'];
        $projekt_naslov = $row['projekt_naslov'];
        $task_naslov = $row['task_naslov'];

        if (!isset($taski_po_mesecih[$mesec][$projekt_id])) {
            $taski_po_mesecih[$mesec][$projekt_id] = [
                'naslov' => $projekt_naslov,
                'taski' => []
            ];
        }

        $taski_po_mesecih[$mesec][$projekt_id]['taski'][] = $task_naslov;
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
                    <?php if (!isset($_SESSION['idu'])): ?>
                        <a href="index.php">Login</a>
                    <?php else: ?>
                        <a href="skupine.php">Skupine</a>
                        <a href="up_projekti.php">Moji Projekti</a>
                        <?php if (isset($_SESSION['pravica_id']) && $_SESSION['pravica_id'] == 3): ?>
                            <a href="admin.php">Administracija</a>
                        <?php endif; ?>
                        <a href="logout.php">Odjava</a>
                    <?php endif; ?>
                </div>
            </div>
        </td>
        <td><?= htmlspecialchars($uporabnik) ?></td>
    </tr>
</table>

<table class="koledar">
    <?php for ($vrstica = 0; $vrstica < 3; $vrstica++): ?>
        <tr>
            <?php for ($stolpec = 1; $stolpec <= 4; $stolpec++):
                $mesec = $vrstica * 4 + $stolpec; ?>
                <td><a href="koledar.php?mesec=<?= $mesec ?>" class="meseci">
                        <?= date("M", mktime(0, 0, 0, $mesec, 1)) ?>
                    </a>
                    <?php if (!empty($taski_po_mesecih[$mesec])): ?>
                        <?php foreach ($taski_po_mesecih[$mesec] as $projekt): ?>
                            <div class="projekt-naslov"><strong><?= htmlspecialchars($projekt['naslov']) ?></strong></div>
                            <?php foreach ($projekt['taski'] as $task): ?>
                                <div class="task">– <?= htmlspecialchars($task) ?></div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
        </tr>
    <?php endfor; ?>
</table>
</body>
<?php include "footer.php"; ?>
</html>
