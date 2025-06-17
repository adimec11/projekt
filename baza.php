<?php
$host='sql209.infinityfree.com';
$user='if0_39102746';
$password='aMCW0WPZpCCuQUh';
$database='todolist';

$conn=mysqli_connect($host,$user,$password,$database)
or die('napaka pri povezavi z bazo');

mysqli_set_charset($conn, "utf8");


