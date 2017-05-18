<?php
session_start();
require 'db.php';

$ID_Pre = $_GET['ID_Pre'];

	$stmt = $db->prepare("SELECT vyuc.* FROM Vyucovani vyuc WHERE vyuc.ID_Pre =?");
	$stmt->execute(array ($ID_Pre));
	$vyucovani = $stmt->fetchAll();
	
	$stmt = $db->prepare("SELECT pre.* FROM Predmet pre WHERE pre.ID_Pre =?");
	$stmt->execute(array ($ID_Pre));
	$predmet = $stmt->fetch();
	
?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<title>Detail predmetu</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#nav").load("nav.php");
				//$("#id").load("select.txt");
			});
			
			function pridej() {
			var prom = document.activeElement.value;
			var predmet = document.getElementById('pre').innerHTML;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
				alert (this.responseText);
				}
			};
			xhttp.open("GET", "pridat.php?prom=" + prom + '&pre=' + predmet, true);
			xhttp.send();
			}
	</script>
</head> 

<body>
	<div id='nav'></div>
	<div class='container'>

		<h1 style='display:inline' id='pre' ><?php echo $predmet['ID_Pre']?></h1>
		<h1 style='display:inline'><?php  echo  '&nbsp;' . $predmet['Nazev']?></h1>
		
		<p>Rocnik vyuky: <?php echo $predmet['Rocnik']?></p>
		
		<h2>Popis</h2>
		<p><?php echo $predmet['Popis']?></p>
		
		<h2>Vyucovani</h2>
		
		<?php if (count($vyucovani) > 0) { ?>
		<table class='table'>
		<thead>
			<tr>
				<th>Den</th>
				<th>Cas od</th>
				<th>Cas do</th>
				<th>Mistnost</th>
				<th>Vyucujici</th>
			</tr>
		</thead>
		<?php foreach($vyucovani as $row) {?>
			<tr>
				<td><?php echo $row['Den']?></td>
				<td><?php echo $row['Cas_od']?></td>
				<td><?php echo $row['Cas_do']?></td>
				<td><?php echo $row['Mistnost']?></td>
				<td><?php echo $row['Vyucujici']?></td>
				<td><button class='btn-success btn-xs' name='btn' value='<?php echo $row['ID']?>' onclick='pridej()'>Pridat</button></td>
			</tr>
		<?php } ?>
		</table>
		<?php } 
		else { ?>
		<p>Predmet se nevyucuje.</p>
		<?php } ?>
	</div>
</body>
		
</html>
