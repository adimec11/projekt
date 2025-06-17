<?php
require_once 'baza.php';
session_start();

if (($_SERVER['REQUEST_METHOD'] === 'POST')&&(isset($_POST['sub']))) {
    $ime = $_POST['ime'];
    $priimek = $_POST['priimek'];
    $up_ime = $_POST['up_ime'];
    $mail = $_POST['mail'];
    $geslo = $_POST['geslo'];
    $tel = $_POST['telefonska'];
    $pravica = '1';
    $sql = "INSERT INTO uporabniki (ime, priimek, uporabnisko_ime, `e-posta`, geslo, tel_st, pravica_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $geslo_hash = password_hash($geslo, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "ssssssi", $ime, $priimek, $up_ime, $mail, $geslo_hash, $tel, $pravica);
    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    echo "Registracija uspešna!";
mysqli_close($conn);

    }




    

    
   


?>