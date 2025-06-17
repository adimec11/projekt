<?php
session_start();
include 'baza.php';

$query = '';
$kategorija = 0;

if (isset($_GET['query'])) {
    $query = $_GET['query'];
}

if (isset($_GET['kategorija'])) {
    $kategorija = $_GET['kategorija'];
}

// kategorije (vrste)
$kategorije = array();
$kat_sql = "SELECT * FROM vrste ORDER BY naziv ASC";
$kat_rez = mysqli_query($conn, $kat_sql);
while ($kat = mysqli_fetch_array($kat_rez)) {
    $kategorije[] = $kat;
}

$uporabnik = ' ';

if (isset($_SESSION['idu'])) {
        $uporabnik = $_SESSION['ime'];
    }
?>


<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Začetna stran zavetišča</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<header>
    <h1>Zavetišče za živali</h1>
    <nav>
        <a href="index.php">Domov</a>
        <a href="prijava.php">Prijava</a>
        <a href="registracija.php">Registracija</a>
    </nav>
    <p style="float:right"><?=$uporabnik ?> </p>
    <?php if (isset($_SESSION['idu'])) {?>
        <a style="float:right" href="odjava.php">Odjava</a>
    <?php
    }
    ?>

</header>

<main>
    <h2>Naše živali</h2>
    <div class="zivali">
<form method="get" action="index.php">
    <input type="text" name="query" placeholder="Išči" value="<?= $query ?>">
    <select name="kategorija">
        <option value="0">Vrste živali</option>
        <?php for ($i = 0; $i < count($kategorije); $i++) { ?>
            <option value="<?= $kategorije[$i]['id'] ?>" <?php if ($kategorija == $kategorije[$i]['id']) echo 'selected'; ?>>
                <?= $kategorije[$i]['naziv'] ?>
            </option>
        <?php } ?>
    </select>
    <button type="submit">Išči</button>
</form>


</body>
</html>


    </div>
</main>



</body>
</html>