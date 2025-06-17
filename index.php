<?php
require_once 'baza.php';
session_start();

$napaka = '';
if (isset($_SESSION['idu'])) {
    header("Location: main.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['mail'];
    $geslo = $_POST['geslo'];

    $sql = "SELECT id, ime, priimek, uporabnisko_ime, `e-posta`, geslo, pravica_id 
            FROM uporabniki 
            WHERE `e-posta` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $rezultat = mysqli_stmt_get_result($stmt);
    $uporabnik = mysqli_fetch_assoc($rezultat);

    if ($uporabnik) {
        if (password_verify($geslo, $uporabnik['geslo'])) {
            $_SESSION['idu'] = $uporabnik['id'];
            $_SESSION['ime'] = $uporabnik['ime'];
            $_SESSION['priimek'] = $uporabnik['priimek'];
            $_SESSION['u_ime'] = $uporabnik['uporabnisko_ime'];
            $_SESSION['mail'] = $uporabnik['e-posta'];
            $_SESSION['log'] = true;
			$_SESSION['polno_ime'] = $uporabnik['ime'] . ' ' . $uporabnik['priimek'];

            header("Location: main.php");
            exit();
        } else {
            $napaka = "Napačno geslo.";
        }
    } else {
        $napaka = "Uporabnik s tem e-mailom ne obstaja.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?><!DOCTYPE html>
<html lang="sl">
<head>
	<meta charset="UTF-8">
	<title>Prijava</title>
	<link rel="stylesheet" href="css/stil.css">
</head>
<body>

<div class="login">
	<h1>Prijava</h1>
	<form method="post">
		<input type="text" name="mail" placeholder="E-mail" required class="polja"><br>
		<input type="password" name="geslo" placeholder="Geslo" required class="polja"><br>
		<input type="submit" name="login" value="Prijava" class="prijava">
	</form>

    <?php if (!empty($napaka)): ?>
		<div class="error"><?= htmlspecialchars($napaka) ?></div>
    <?php endif; ?>

	<div id="registracija_zun">
		<a href="registracija.php" id="registracija">Registracija</a><br>
	</div>
</div>
</body>
</html>
