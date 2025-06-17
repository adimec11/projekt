<?php
require_once "baza.php";
session_start();
$uporabnik= '';
if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
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
        <div class="sidebar">
            <span class="sidebar-gumb">☰</span>
            <div class="sidebar-vsebina">
                <?php if (!isset($_SESSION['idu'])) echo '<a href="index.php">LOGIN</a>'; ?>
                <a href="main.php">Domov</a>
                <a href="skupine.php">Skupine</a>
                <a href="taski.php">Taski</a>
                <a href="logout.php">Odjava</a>
            </div>
        </div>
    </td>
    <td><?=htmlspecialchars($uporabnik) ?></td>
</table>

<table class="koledar">
    <tr>
        <td><a href="vpis_skupin.php"></a></td>


    </tr>
</table>

</body>
</html>