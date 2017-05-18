<?php
session_start();
require 'db.php';
	
if (isset($_GET['offset'])) {
$offset = (int)$_GET['offset'];
} else {
$offset = 0;
}
if (isset($_GET['pis'])) {
$pismeno = $_GET['pis'];
} else {
$pismeno = '';
}
$pismena = $db->query("SELECT distinct mid(Nazev,1,1) as pismeno FROM Predmet ORDER BY 1")->fetchAll();
$count = $db->query("SELECT COUNT(ID_Pre) FROM Predmet")->fetchColumn();


if($pismeno == ''){
	$stmt = $db->prepare("SELECT * FROM Predmet LIMIT 5 OFFSET ?");
	$stmt->bindValue(1, $offset, PDO::PARAM_INT);
	$stmt->execute();
	$predmety = $stmt->fetchAll();
	$count = $db->query("SELECT COUNT(ID_Pre) FROM Predmet")->fetchColumn();
}
else{
	$stmt = $db->prepare("SELECT * FROM Predmet WHERE Nazev like '$pismeno%' ");
	$stmt->execute();
	$predmety = $stmt->fetchAll();
	$count = 1;
}


?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<title>Predmety</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#nav").load("nav.php", setActive);
				//$("#id").load("select.txt");
				function setActive (){
				$("#predmety").addClass("active");
			}});
	</script>
</head> 

<body>
	<div id='nav'></div>
	
	<?php if ($count > 0) { ?>
	
	<div class='container'>
	<H1>Seznam predmetu</H1>
		<ul class='pagination'>
		<?php foreach($pismena as $row){?>
				<li><a href='predmety.php?pis=<?php echo $row['pismeno']?>'><?php echo $row['pismeno']?></a></li>
		<?php } ?>
				<li><a href='predmety.php?offset=0'>*</a></li>
		</ul>
		<table class='table'>
			<thead>
				<tr>
					<th style='width:20%'>ID Predmetu</th>
					<th style='width:60%'>Nazev</th>
					<th style='width:20%'>Rocnik</th>
				</tr>

			</thead>
			<?php foreach($predmety as $row) {?>
				<tr>
					<td><?php echo $row['ID_Pre'] ?></td>
					<td><a href='predmet_detail.php?ID_Pre=<?php echo $row["ID_Pre"]?>'><?php echo $row['Nazev'] ?></a></td>
					<td><?php echo $row['Rocnik'] ?></td>
				</tr>
			<?php } ?>
		</table>
		
		<br/>
		
		<ul class='pagination'>
			<?php for($i=1; $i<=ceil($count/5); $i++) { ?>
			<li class="<?= $offset/5+1==$i ? "active" : ""  ?>">
				<a href="./predmety.php?offset=<?= ($i-1)*5 ?>"><?= $i ?></a>
			</li>
			<?php } ?>
		</ul>

		<br/>
	</div>
	
	<?php } ?>
	
</body>
		
</html>
