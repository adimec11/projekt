<?php
require_once "baza.php";
session_start();
$uporabnik= '';
if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
    $dodaj_skupine="";
}


    $sql = "SELECT DISTINCT s.ime AS ime_skupine FROM skupine s LEFT JOIN vodje_skupine vs ON s.vodja_id = vs.id LEFT JOIN uporabniki_skupine us ON s.id = us.skupina_id WHERE vs.uporabnik_id = ? OR us.uporabnik_id = ?;";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['idu'], $_SESSION['idu']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die("Napaka pri poizvedbi: " . mysqli_error($conn));
    }

    $stevec = 0;
    while (($row = mysqli_fetch_assoc($result)) && $stevec < 12) {
        if ($stevec % 4 == 0) {
            $dodaj_skupine .= "<tr>";
        }

        $dodaj_skupine .= "<td style='height:150px;width:250px;'>"
            // Link za ogled skupine (GET)
            . "<a href='skupina_pod.php?ime_skupine=" . urlencode($row['ime_skupine']) . "'style='text-decoration:none;color:black;'>"
            . htmlspecialchars($row['ime_skupine'])
            . "</a>"

            // Link za zapustitev skupine (GET s parametrom zapusti)
            . "<br><br><a href='izpis_skupine.php?ime_skupine=" . urlencode($row['ime_skupine']) . "&zapusti=1' "
            . "style='float:right; color:red; font-size:16px; text-decoration:none;'>ZAPUSTI</a>"

            . "</td>";

        $stevec++;

        if ($stevec % 4 == 0 || $stevec == 12) {
            $dodaj_skupine .= "</tr>";
        }
    }

    if ($stevec == 0) {
        $dodaj_skupine = "<tr><td colspan='4'>Ni najdenih skupin</td></tr>";

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
                <a href="up_projekti.php">Moji Projekti</a>
                <a href="logout.php">Odjava</a>
            </div>
        </div>
    </td>
    <td><?=htmlspecialchars($uporabnik) ?></td>
</table>

<table class="koledar">


        <?=$dodaj_skupine?>
        <td style="all:unset;">
            <form method="post" action="dodajanje_skupin.php">
                <input type="submit" name="dodaj_skupine" value="+" class="button" style="height:150px; width: 250px;">
            </form>
        </td>
    </tr>

</table>

</body><?php include "footer.php";?>
</html>