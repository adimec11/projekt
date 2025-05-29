<?php

?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>To do list</title>
    <link rel="stylesheet" href="css/stil.css">
    <link rel="icon" href="img/logo.ico" style="height:16px; width:16px;">
</head>
<body>
<img src="img/logo.jpg" class="logo" >
<div class="login">
    <h1>Prijava</h1>
    <form action="#" method="post" class="form">
        <input type="text" name="username" placeholder="Vpišite svoje ime" required class="polja">* <br>
        <input type="text" name="surname" placeholder="Vpišite svoj priimek" required class="polja">* <br>
        <input type="text" name="username" placeholder="Vpišite svojo tel. številko" class="polja"> <br>
        <input type="text" name="mail" placeholder="Vpišite e-mail" required class="polja">* <br>
        <input type="password" name="geslo" placeholder="Vpišite geslo" required class="polja">* <br>
        <input type="submit" name="sub" value="Prijava" class="button">
    </form>

    <div id="registracija_zun">
        <a href="index.php" id="registracija">Registracija</a> <br>
    </div>

</div>
</body>

</html>
