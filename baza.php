<?php
$host='localhost';
$user='root';
$password='';
$database='todolist';

$conn=mysqli_connect($host,$user,$password,$database)
or die('napaka pri povezavi z bazo');

mysqli_set_charset($conn, "utf8");


