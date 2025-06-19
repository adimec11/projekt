<?php
session_start();
require_once 'baza.php';

$uporabnik = '';
$uporabnik_id = null;

if (isset($_SESSION['idu'])) {
    $uporabnik = $_SESSION['polno_ime'];
    $uporabnik_id = $_SESSION['idu'];
} else {
    header("Location: index.php");
    exit;
}

if (isset($_POST['task_opravljen'])) {
    $task_id = intval($_POST['task_id']);
    $sql_opravljeno = "UPDATE taski SET status = 1 WHERE id = ? LIMIT 1";
    $stmt_opravljeno = mysqli_prepare($conn, $sql_opravljeno);
    mysqli_stmt_bind_param($stmt_opravljeno, "i", $task_id);
    mysqli_stmt_execute($stmt_opravljeno);
    header("Location: up_projekti.php?projekt_id=" . intval($_POST['projekt_id']));
    exit;
}

if (isset($_POST['izbrisi_projekt'])) {
    $projekt_id = intval($_POST['projekt_id']);
    $sql_delete_taski = "DELETE FROM taski WHERE projekt_id = ?";
    $stmt_delete_taski = mysqli_prepare($conn, $sql_delete_taski);
    mysqli_stmt_bind_param($stmt_delete_taski, "i", $projekt_id);
    mysqli_stmt_execute($stmt_delete_taski);

    $sql_delete_projekt = "DELETE FROM projekti WHERE id = ? AND lastnik_id = ?";
    $stmt_delete_projekt = mysqli_prepare($conn, $sql_delete_projekt);
    mysqli_stmt_bind_param($stmt_delete_projekt, "ii", $projekt_id, $uporabnik_id);
    mysqli_stmt_execute($stmt_delete_projekt);

    header("Location: up_projekti.php");
    exit;
}

$projekt_id_izbran = isset($_GET['projekt_id']) ? intval($_GET['projekt_id']) : 0;

$sql_projekt = "
    SELECT id, naslov 
    FROM projekti
    WHERE id = ? AND lastnik_id = ? AND skupina_id IS NULL
";
$stmt_projekt = mysqli_prepare($conn, $sql_projekt);
mysqli_stmt_bind_param($stmt_projekt, "ii", $projekt_id_izbran, $uporabnik_id);
mysqli_stmt_execute($stmt_projekt);
$result_projekt = mysqli_stmt_get_result($stmt_projekt);
$projekt = mysqli_fetch_assoc($result_projekt);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Moji Projekti</title>
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
                    <a href="skupine.php">Skupine</a>
                    <a href="up_projekti.php">Moji Projekti</a>
                    <a href="logout.php">Odjava</a>
                </div>
            </div>
        </td>
        <td><?= htmlspecialchars($uporabnik) ?></td>
    </tr>
</table>

<div>
    <table class="tabela">
        <tr>
            <td>Projekt in Taski</td>
            <td>Dodaj / Izbriši</td>
        </tr>

        <?php
        if ($projekt) {
            echo "<tr>";
            
            echo "<td>";
            echo "<strong>" . htmlspecialchars($projekt['naslov']) . "</strong><br><br>";

            $sql_taski = "SELECT id, naslov, status FROM taski WHERE projekt_id = ?";
            $stmt_taski = mysqli_prepare($conn, $sql_taski);
            mysqli_stmt_bind_param($stmt_taski, "i", $projekt['id']);
            mysqli_stmt_execute($stmt_taski);
            $result_taski = mysqli_stmt_get_result($stmt_taski);

            if (mysqli_num_rows($result_taski) > 0) {
                echo "<ul style='list-style-type: none; padding-left: 0;'>";
                while ($task = mysqli_fetch_assoc($result_taski)) {
                    echo "<li>";

                    if ($task['status'] == 1) {
                        echo htmlspecialchars($task['naslov']) . "✅ ";
                    } else {
                        echo "<form method='post' style='display:inline;'>";
                        echo "<input type='hidden' name='task_id' value='" . $task['id'] . "'>";
                        echo "<input type='hidden' name='projekt_id' value='" . $projekt['id'] . "'>";
                        echo "<button type='submit' name='task_opravljen' class='button'>" . htmlspecialchars($task['naslov']) . "</button>";
                        echo "</form>";
                    }

                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "Ni dodanih taskov.";
            }

            echo "</td>";

            echo "<td>";
            
            echo "<form method='post' action='dodajanje_o_taskov.php' style='margin-bottom: 5px;'>";
            echo "<input type='hidden' name='projekt_id' value='" . $projekt['id'] . "'>";
            echo "<input type='submit' value='Dodaj Task' class='button'>";
            echo "</form>";

            echo "<form method='post'>";
            echo "<input type='hidden' name='projekt_id' value='" . $projekt['id'] . "'>";
            echo "<input type='submit' name='izbrisi_projekt' value='Izbriši projekt' class='button' style='border-color:red; color:red;'>";
            echo "</form>";

            echo "</td>";
            echo "</tr>";
        } else {
            echo "<tr><td colspan='2'>Izberi projekt za prikaz.</td></tr>";
        }
        ?>

        <tr>
            <td colspan="2">
                <a href="up_projekti.php" class="button">Nazaj na seznam projektov</a>
            </td>
        </tr>
    </table>
</div>

</body>
<?php include "footer.php"; ?>
</html>
