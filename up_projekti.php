<?php
require_once "baza.php";
session_start();

$uporabnik = '';
$uporabnik_id = null;
$dodaj_projekti = '';

if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
    $uporabnik_id = $_SESSION['idu'];
}

// Pridobi projekte, ki NIMAJO skupine in kjer ima uporabnik svoje taske
$sql = "
    SELECT DISTINCT p.id, p.naslov 
    FROM projekti p
    LEFT JOIN taski t ON p.id = t.projekt_id
    WHERE p.skupina_id IS NULL AND t.uporabnik_id = ?
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $uporabnik_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Napaka pri poizvedbi: " . mysqli_error($conn));
}

$stevec = 0;
while (($row = mysqli_fetch_assoc($result)) && $stevec < 12) {
    if ($stevec % 4 == 0) {
        $dodaj_projekti .= "<tr>";
    }

    $dodaj_projekti .= "<td style='height:150px;width:250px;'>"
        . "<a href='projekt_pod.php?projekt_id=" . urlencode($row['id']) . "' style='text-decoration:none;color:black;'>"
        . htmlspecialchars($row['naslov'])
        . "</a>"

        . "<br><br><a href='izbris_projekta.php?projekt_id=" . urlencode($row['id']) . "' "
        . "style='float:right; color:red; font-size:16px; text-decoration:none;'>IZBRIŠI</a>"

        . "</td>";

    $stevec++;

    if ($stevec % 4 == 0 || $stevec == 12) {
        $dodaj_projekti .= "</tr>";
    }
}

if ($stevec == 0) {
    $dodaj_projekti = "<tr><td colspan='4'>Ni osebnih projektov</td></tr>";
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Osebni projekti</title>
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
                    <?php if (!isset($_SESSION['idu'])) echo '<a href="index.php">LOGIN</a>'; ?>
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

<table class="koledar">
    <?=$dodaj_projekti?>
    <td style="all:unset;">
        <form method="post" action="dodajanje_projektov.php">
            <input type="submit" name="dodaj_projekt" value="+" class="button" style="height:150px; width: 250px;">
        </form>
    </td>
    </tr>
</table>

</body>
</html>
