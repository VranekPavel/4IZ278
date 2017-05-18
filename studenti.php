<?php
session_start();
require 'db.php';
	
if (isset($_GET['offset'])) {
$offset = (int)$_GET['offset'];
} else {
$offset = 0;
}

# celkovy pocet zbozi pro strankovani
$count = $db->query("SELECT COUNT(ID_Stu) FROM Student")->fetchColumn();

$stmt = $db->prepare("SELECT * FROM Student LIMIT 5 OFFSET ?");
$stmt->bindValue(1, $offset, PDO::PARAM_INT);
$stmt->execute();
$studenti = $stmt->fetchAll();
?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<title>Studenti</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#nav").load("nav.php", setActive);
				//$("#id").load("select.txt");
				function setActive (){
				$("#studenti").addClass("active");
			}});
	</script>
</head> 

<body>
	<div id='nav'></div>
	
	<?php if ($count > 0) { ?>
	
	<div class='container'>
	<H1>Seznam studentu</H1>
		<table class='table'>
			<thead>
				<tr>
					<th style='width:25%'>Jmeno</th>
					<th style='width:25%'>Prijmeni</th>
					<th style='width:15%'>Vek</th>
					<th style='width:25%'>E-mail</th>
					<th style='width:10%'>Rocnik</th>
				</tr>

			</thead>
			<?php foreach($studenti as $row) {?>
				<tr>
					<td><?php echo $row['Jmeno'] ?></td>
					<td><?php echo $row['Prijmeni'] ?></td>
					<td><?php echo $row['Vek'] ?></td>
					<td><a href='mail.php?email=<?php echo $row['Email']?>'><?php echo $row['Email'] ?></a></td>
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
