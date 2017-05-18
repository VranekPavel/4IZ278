<?php
session_start();
require 'db.php';

$ID_Pre = $_GET['ID_Pre'];
$Nazev = $_GET['Nazev'];
	
	$stmt = $db->prepare("SELECT * FROM Znamky WHERE ID_Stu=? and ID_Pre=?");
	$stmt->execute(array ($_SESSION['user'], $ID_Pre));
	$znamky = $stmt->fetchAll();
	
	$stmt = $db->prepare("SELECT vyuc.* FROM Info inf JOIN Vyucovani vyuc on inf.ID_Pre=vyuc.ID_Pre and inf.ID_Vyuc=vyuc.ID WHERE inf.ID_Stu=? and inf.ID_Pre=?");
	$stmt->execute(array ($_SESSION['user'], $ID_Pre));
	$predmet = $stmt->fetchAll();
	
	$stmt = $db->prepare("SELECT Vaha,Znamka FROM Znamky WHERE ID_Stu=? and ID_Pre=?");
	$stmt->execute(array ($_SESSION['user'], $ID_Pre));
	$vystup = $stmt->fetchAll();
	$vysledek = 0;
	if (sizeof($vystup) > 0){
	foreach($vystup as $row){
				$vysledek +=  $row['Vaha'] * $row['Znamka'] * 0.01;
			}
		$vysledek /= sizeof($vystup);
	}
	else {
		$vysledek = '';
	}
	
	//Patri k vyucovani
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check1']) && $_POST['Akce'] == 'Del') {
		$checked_arr = $_POST['check1'];
		$question_marks = str_repeat('?,', count($checked_arr) - 1) . '?';
		//echo count($checked_arr);

		$stmt = $db->prepare("DELETE FROM Info WHERE ID_Vyuc IN ($question_marks)");
		$stmt->execute(array_values($checked_arr));
		
		$stmt = $db->prepare("SELECT COUNT(ID) as pocet FROM Info WHERE ID_Stu=? and ID_Pre=?");
		$stmt->execute(array ($_SESSION['user'], $ID_Pre));
		$pocet = $stmt->fetchColumn();
		if ($pocet == 0){
			$stmt = $db->prepare("DELETE FROM Znamky WHERE ID_Stu=? and ID_Pre=?");
			$stmt->execute(array($_SESSION['user'], $ID_Pre));
			
			header('Location: home.php');
			die;
		}

		header('Location: predmet.php?ID_Pre=' . $ID_Pre . '&Nazev=' . $Nazev);
	}
	
	//Patri ke znamkam
	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['Akce'] == 'Ulozit') {
		$stmt = $db->prepare("INSERT INTO Znamky(ID_Stu, ID_Pre, Znamka, Nazev, Vaha) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute(array($_SESSION ["user"], $ID_Pre, $_POST['znamka'], $_POST['nazev'], $_POST['vaha']));
		
		header('Location: predmet.php?ID_Pre=' . $ID_Pre . '&Nazev=' . $Nazev);
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check']) && $_POST['Akce'] == 'Odebrat'){
		$checked_arr = $_POST['check'];
		$question_marks = str_repeat('?,', count($checked_arr) - 1) . '?';
		//echo count($checked_arr);

		$stmt = $db->prepare("DELETE FROM Znamky WHERE ID IN ($question_marks)");
		$stmt->execute(array_values($checked_arr));
		
		header('Location: predmet.php?ID_Pre=' . $ID_Pre . '&Nazev=' . $Nazev);
	}
?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<title>Predmet</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script>
			$(document).ready(function(){
				$("#nav").load("nav.php");
				//$("#id").load("select.txt");
			});
			
			function pridej (){
				document.getElementById('input1').innerHTML = '<td colspan="2"><input name="nazev" class="form-control"></td><td><input name="vaha" class="form-control"></td><td><input name="znamka" class="form-control"></td>';
			}

	</script>
</head> 

<body>
	<div id="nav"></div>
	<div class='container'>
	<div class='col-sm-12'>
		<h1><a href='predmet_detail.php?ID_Pre=<?php echo $ID_Pre ?>'><?php echo $ID_Pre . ' ' . $Nazev?></a></h1>
		<form action='' method='POST'>
		<table class='table'>
		<thead>
			<tr>
				<th></th>
				<th>Vyucujici</th>
				<th>Mistnost</th>
				<th>Den</th>
				<th>Cas_od</th>
				<th>Cas_do</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($predmet AS $row){ ?>
			<tr>
				<td>
					<input type="checkbox" name='check1[]' value="<?php echo $row['ID']?>">
				</td>
				<td> <?php echo $row['Vyucujici'] ?></td>	
				<td> <?php echo $row['Mistnost'] ?></td>	
				<td> <?php echo $row['Den'] ?></td>	
				<td> <?php echo $row['Cas_od'] ?></td>
				<td> <?php echo $row['Cas_do'] ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan='6'>
					<button type="submit" class="btn" name='Akce' value='Del'>Odebrat</button>
				</td>
			</tr>
		</tbody>
		</table>
		</form>
	</div>
	</div>
	<div class='container'>
	<div class='col-sm-12'>
		<h1>Znamky</h1>
		<form action='' method='POST'>
		<table class='table'>
		<thead>
			<tr>
				<th></th>
				<th>Typ</th>
				<th>Vaha</th>
				<th>Znamka</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($znamky AS $row){ ?>
			<tr>
				<td>
					<input type="checkbox" name='check[]' value="<?php echo $row['ID']?>">
				</td>
				<td> <?php echo $row['Nazev'] ?></td>	
				<td> <?php echo $row['Vaha'] ?>%</td>
				<td> <?php echo $row['Znamka'] ?></td>
			</tr>
		<?php } ?>
		<tr id='input1'></tr>
			<tr>
				<td colspan='3'>Prumer:</td>
				<td><?php echo round($vysledek, 2)?></td>
			</tr>
			<tr>
				<td colspan='3'>
					<button type="submit" class="btn" name='Akce' value='Odebrat'>Odebrat</button>
					<button type="button" class="btn" onClick='pridej()'>Pridat</button>
				</td>
				<td>
					<button type="submit" class="btn" name='Akce' value='Ulozit'>Ulozit</button>
					<a href='predmet.php?ID_Pre=<?php echo $ID_Pre ?>&Nazev=<?php echo $Nazev?>'><button type="button" class="btn">Zrusit</button></a>
				</td>
			</tr>
		</tbody>
		</table>
		</form>
		</div>
	</div>
	</body>
		
</html>

