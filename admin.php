<?php
require_once 'baza.php';

// --- Brisanje skupine ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brisanje_id'])) {
    $brisanje_id = intval($_POST['brisanje_id']);

    // Najprej brišemo morebitne povezane podatke (npr. člane skupine)
    $sql_delete_clani = "DELETE FROM uporabniki_skupine WHERE skupina_id = ?";
    $stmt_clani = mysqli_prepare($conn, $sql_delete_clani);
    mysqli_stmt_bind_param($stmt_clani, "i", $brisanje_id);
    mysqli_stmt_execute($stmt_clani);
    mysqli_stmt_close($stmt_clani);

    // Nato brišemo samo skupino
    $sql_delete = "DELETE FROM skupine WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $brisanje_id);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);

    // Refresh strani
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// --- Pridobimo vse skupine ---
$sql_skupine = "SELECT id, ime FROM skupine ORDER BY ime ASC";
$result_skupine = mysqli_query($conn, $sql_skupine);
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Glavna stran - Skupine</title>
    <link rel="stylesheet" href="css/stil.css">
    <link rel="icon" href="img/logo.ico">
</head>
<body>
<h1>Seznam skupin</h1>

<table border="1" style="margin:0 auto;">
    <thead>
    <tr>
        <th>Ime skupine</th>
        <th>Dejanje</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result_skupine)): ?>
        <tr>
            <td><?= htmlspecialchars($row['ime']) ?></td>
            <td>
                <a href="a_uredi_skupino.php?id=<?= $row['id'] ?>">Uredi</a>
                |
                <form method="post" action="" style="display:inline;" onsubmit="return confirm('Si prepričan, da želiš izbrisati skupino?');">
                    <input type="hidden" name="brisanje_id" value="<?= $row['id'] ?>">
                    <button type="submit">Izbriši</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    <tr>
        <td>
            <a href="main.php" style="text-decoration: none; color:black;">Domov</a>
        </td>
    </tr>

    </tbody>
</table>
</body>
</html>
