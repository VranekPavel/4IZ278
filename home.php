
<?php
	session_start();
	require "db.php";
	
	if (isset($_SESSION['user'])){
	$stmt = $db->prepare("SELECT * FROM Student WHERE ID_Stu=?");
	$stmt->execute(array ($_SESSION['user']));
	$student = $stmt->fetch();
	
	$stmt = $db->prepare("SELECT distinct pre.Nazev, pre.ID_Pre FROM Info inf join Predmet pre on inf.ID_Pre=pre.ID_Pre WHERE inf.ID_Stu=? ORDER BY pre.ID_Pre");
	$stmt->execute(array ($_SESSION['user']));
	$predmety = $stmt->fetchAll();
	}
	else {
	echo "<script type='text/javascript'>alert('Nejste přihlášen.');</script>";
	echo("<script>window.location = history.go(-1);</script>");
	die();
	}
	
?>

<!DOCTYPE html>

<html lang="cs">
	
	<head>
		<meta charset="utf-8" />
		<title>Home page</title>
		
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#nav").load("nav.php", setActive);
				//$("#id").load("select.txt");
				function setActive (){
				$("#home").addClass("active");
			}});
		</script>
	</head>

	<body>
		<div id='nav'></div>
		<div class="container">
			<div class="col-md-6" >
				<H1><?php echo $student["Jmeno"] . " " . $student["Prijmeni"]?> <a href='student.php' >Edit</a></H1>
				<table class='table'>
				<tr>
					<td>Věk:</td>
					<td><?php echo $student["Vek"]?></td>
				</tr>
				<tr>
					<td>Telefon:</td>
					<td><?php echo $student["Telefon"]?></td>
				</tr>
				<tr>
					<td>E-mail:</td>
					<td><?php echo $student["Email"]?></td>
				</tr>
				<tr>
					<td>Rocnik:</td>
					<td><?php echo $student["Rocnik"]?></td>
				</tr>
				</table>
			</div>
			<div class="col-md-6" >
				<H1>Studované předměty <a href='predmety.php' >Přidat</a></H1>
				<table class="table">
				<tbody>
				<?php foreach ($predmety as $row){?>
				<tr>
				<td><?php echo $row["ID_Pre"]?></td>
				<td><a href='predmet_detail.php?ID_Pre=<?php echo $row["ID_Pre"]?>'><?php echo $row["Nazev"];?></a></td>
				<td><a href="predmet.php?ID_Pre=<?php echo $row['ID_Pre'] ?>&Nazev=<?php echo $row['Nazev']?>">Detail</a></td>
				</tr>
					<?php } ?>
				</tbody>
				</table>
			</div>
		</div>
	</body>
	
</html>
