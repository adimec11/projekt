<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Glavna stran</title>
	<link rel="stylesheet" href="css/stil.css">
    <link rel="icon" href="img/logo.ico">
</head>
<body>
<img src="img/logo.jpg" class="logo">
<table border="0">
	<tr>
        <td><a href="login.php" id="registracija"><span class="more">☰</span></a></td>
		<td> <?php echo 'Andraž Dimec'?></td>
	</tr>
</table>
<table class="koledar">
	<tr>
		<td><a href="koledar/Jan.php" class="meseci">Jan</td>
		<td><a href="koledar/Feb.php" class="meseci">Feb</td>
		<td><a href="koledar/Mar.php" class="meseci">Mar</td>
		<td><a href="koledar/Apr.php" class="meseci">Apr</td>
	</tr>
	<tr>
		<td><a href="koledar/Maj.php" class="meseci">Maj</td>
		<td><a href="koledar/Jun.php" class="meseci">Jun</td>
		<td><a href="koledar/Jul.php" class="meseci">Jul</td>
		<td><a href="koledar/Avg.php" class="meseci">Avg</td>
	</tr>
	<tr>
		<td><a href="koledar/Sep.php" class="meseci">Sep</td>
		<td><a href="koledar/Okt.php" class="meseci">Okt</td>
		<td><a href="koledar/Nov.php" class="meseci">Nov</td>
		<td><a href="koledar/Dec.php" class="meseci">Dec</td>
	</tr>
</table>

</body>
</html>