<?php
require_once 'baza.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Napaka: Ni ID skupine.');
}

$skupina_id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ime'])) {
    $novo_ime = trim($_POST['ime']);

    if ($novo_ime !== '') {
        $sql_update = "UPDATE skupine SET ime = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $novo_ime, $skupina_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        header("Location: skupine.php");
        exit();
    } else {
        $napaka = "Ime skupine ne sme biti prazno.";
    }
}

$sql_skupina = "SELECT ime FROM skupine WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql_skupina);
mysqli_stmt_bind_param($stmt, "i", $skupina_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $trenutno_ime = $row['ime'];
} else {
    die('Napaka: Skupina ne obstaja.');
}

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Uredi skupino</title>
    <link rel="stylesheet" href="css/stil.css">
    <link rel="icon" href="img/logo.ico">
</head>
<body>
<h1>Uredi skupino</h1>

<?php if (isset($napaka)): ?>
    <p style="color: red;"><?= htmlspecialchars($napaka) ?></p>
<?php endif; ?>
    <form method="post" action="">
        <label for="ime">Ime skupine:</label><br>
        <input type="text" id="ime" name="ime" value="<?= htmlspecialchars($trenutno_ime) ?>" class="polja"><br><br>

        <button type="submit" class="button">Shrani spremembe</button>
    </form>


<p><a href="admin.php" style="text-decoration: none; color:black; font-weight: bolder; font-size:20px;">← Nazaj na seznam skupin</a></p>
</body>
</html>
