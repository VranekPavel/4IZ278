<?php
session_start();
require 'db.php';

//Kontrola prav
if(isset($_SESSION['privileges']) == FALSE || $_SESSION['privileges'] != 1){
	//header('location: index.php');
	echo("<script>window.location = history.go(-1);</script>");
	die();
}

// Seznam predmetu
if (isset($_GET['offset_pre'])) {
$offset_pre = (int)$_GET['offset_pre'];
} else {
$offset_pre = 0;
}
$count_pre = $db->query("SELECT COUNT(ID_Pre) FROM Predmet")->fetchColumn();

$stmt = $db->prepare("SELECT * FROM Predmet LIMIT 5 OFFSET ?");
$stmt->bindValue(1, $offset_pre, PDO::PARAM_INT);
$stmt->execute();
$predmety = $stmt->fetchAll();


// Seznam vyuc
if (isset($_GET['offset_vyuc'])) {
$offset_vyuc = (int)$_GET['offset_vyuc'];
} else {
$offset_vyuc = 0;
}
$count_vyuc = $db->query("SELECT COUNT(ID) FROM Vyucovani")->fetchColumn();

$stmt = $db->prepare("SELECT * FROM Vyucovani LIMIT 5 OFFSET ?");
$stmt->bindValue(1, $offset_vyuc, PDO::PARAM_INT);
$stmt->execute();
$vyucovani = $stmt->fetchAll();


//Seznam studentu
if (isset($_GET['offset_stu'])) {
$offset_stu = (int)$_GET['offset_stu'];
} else {
$offset_stu = 0;
}
$count_stu = $db->query("SELECT COUNT(ID_Stu) FROM Student")->fetchColumn();

$stmt = $db->prepare("SELECT * FROM Student LIMIT 5 OFFSET ?");
$stmt->bindValue(1, $offset_stu, PDO::PARAM_INT);
$stmt->execute();
$studenti = $stmt->fetchAll();


//Seznam useru
if (isset($_GET['offset_usr'])) {
$offset_usr = (int)$_GET['offset_usr'];
} else {
$offset_usr = 0;
}
$count_usr = $db->query("SELECT COUNT(id) FROM users")->fetchColumn();

$stmt = $db->prepare("SELECT * FROM users LIMIT 5 OFFSET ?");
$stmt->bindValue(1, $offset_usr, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

//////////////////////////////////////////////// Cast pro upravy////////////////////////////////////////////////
//Patri k predmetum
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['Akce'] == 'Predmety') {
	$typ = $_POST['radio'];

	if ($typ == 'Insert_pre') {
		try{
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$stmt = $db->prepare("INSERT INTO Predmet(ID_Pre, Nazev, Rocnik, Popis) VALUES (?, ?, ?, ?)");
		$stmt->execute(array($_POST['id_pre'], $_POST['nazev'], $_POST['rocnik'], $_POST['popis']));
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
		header('Location: edit.php#predmet');

	}
	if ($typ == 'Delete_pre'){
		$stmt = $db->prepare("DELETE FROM Predmet WHERE ID_Pre IN (?)");
		$stmt->execute(array($_POST['id_pre']));
		
		header('Location: edit.php#predmet');
	}
	if ($typ == 'Update_pre'){
		$stmt = $db->prepare("SELECT Last_updated_at FROM Predmet WHERE ID_Pre=?");
		$stmt->execute(array ($_POST['id_pre']));
		$update = $stmt->fetchColumn();
		
		if($update == $_POST['update']){
		$stmt = $db->prepare("UPDATE Predmet SET  Nazev=?, Rocnik=?, Popis=? WHERE ID_Pre=?" );
		$stmt->execute(array($_POST['nazev'], $_POST['rocnik'], $_POST['popis'], $_POST['id_pre']));
		
		header('Location: edit.php#predmet');
		}
		else{
			echo "<script type='text/javascript'>alert('Predmet byl v mezicase updatovan.');</script>";
		}
	}
}

//Patri k vyucovani
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['Akce'] == 'Vyucovani') {
	$typ = $_POST['radio1'];

	if ($typ == 'Insert_vyuc') {
		$stmt = $db->prepare("INSERT INTO Vyucovani(ID_Pre, Vyucujici, Mistnost, Den, Cas_od, Cas_do) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->execute(array($_POST['id_pre'], $_POST['vyucujici'], $_POST['mistnost'], $_POST['den'], $_POST['cas_od'], $_POST['cas_do']));
		
		header('Location: edit.php#vyucovani');
	}
	if ($typ == 'Delete_vyuc'){
		$stmt = $db->prepare("DELETE FROM Vyucovani WHERE ID IN (?)");
		$stmt->execute(array($_POST['id']));
		
		header('Location: edit.php#vyucovani');
	}
	if ($typ == 'Update_vyuc'){
		$stmt = $db->prepare("UPDATE Vyucovani SET  ID_Pre=?, Vyucujici=?, Mistnost=?, Den=?, Cas_od=?, Cas_do=? WHERE ID=?" );
		$stmt->execute(array($_POST['id_pre'], $_POST['vyucujici'], $_POST['mistnost'], $_POST['den'], $_POST['cas_od'], $_POST['cas_do'], $_POST['id']));
		
		header('Location: edit.php#vyucovani');
	}
}

//Patri k studentum
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['Akce'] == 'Studenti') {
	$typ = $_POST['radio2'];

	if ($typ == 'Insert_stu') {
		$stmt = $db->prepare("INSERT INTO Student (Jmeno, Prijmeni, Vek, Telefon, Email, Rocnik) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->execute(array($_POST['jmeno'], $_POST['prijmeni'], $_POST['vek'], $_POST['telefon'], $_POST['email'], $_POST['rocnik']));
		
		header('Location: edit.php#student');
	}
	if ($typ == 'Delete_stu'){
		$stmt = $db->prepare("DELETE FROM Student WHERE ID_Stu IN (?)");
		$stmt->execute(array($_POST['id_stu']));
		
		header('Location: edit.php#student');
	}
	if ($typ == 'Update_stu'){
		$stmt = $db->prepare("UPDATE Student SET  Jmeno=?, Prijmeni=?, Vek=?, Telefon=?, Email=?, Rocnik=? WHERE ID_Stu=?" );
		$stmt->execute(array($_POST['jmeno'], $_POST['prijmeni'], $_POST['vek'], $_POST['telefon'], $_POST['email'], $_POST['rocnik'], $_POST['id_stu']));
		
		header('Location: edit.php#student');
	}
}

//Patri k userum
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['Akce'] == 'Users') {
	$typ = $_POST['radio3'];

	if ($typ == 'Insert_usr') {
		$pass = generateRandomString();
		$stmt = $db->prepare("SELECT Email FROM Student WHERE ID_Stu=?");
		$stmt ->execute(array($_POST['id_stu']));
		$to = $stmt->fetchColumn();
		$subject = 'Vytvoreni uzivatelskeho uctu.';
		$message = 'Uzivatelske jmeno: ' . $_POST['name'] . '  '. 'Uzivatelske heslo: ' . $pass;
		$headers = 'From: secretariat@skola.cz';
		mail($to, $subject, $message, $headers);
		
		$pass = password_hash($pass, PASSWORD_DEFAULT);
		
		$stmt = $db->prepare("INSERT INTO users (ID_Stu, name, pass, priv) VALUES (?, ?, ?, ?)");
		$stmt->execute(array($_POST['id_stu'], $_POST['name'], $pass, $_POST['priv']));
		
		
		
		header('Location: edit.php#user');
	}
	if ($typ == 'Delete_usr'){
		$stmt = $db->prepare("DELETE FROM users WHERE id IN (?)");
		$stmt->execute(array($_POST['id']));
		
		header('Location: edit.php#user');
	}
	if ($typ == 'Update_usr'){
		$stmt = $db->prepare("UPDATE users SET  ID_Stu=?, name=?, priv=? WHERE id=?" );
		$stmt->execute(array($_POST['id_stu'], $_POST['name'], $_POST['priv'], $_POST['id']));
		
		header('Location: edit.php#user');
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<title>Edit</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#nav").load("nav.php", setActive);
				//$("#id").load("select.txt");
				function setActive (){
				$("#edit").addClass("active");
			}});
			
			//Patri k predmetum
			function ins(){
				document.getElementById('input').innerHTML = '<td><input name="id_pre" class="form-control"></td><td><input name="nazev" class="form-control"></td><td><input name="rocnik" class="form-control"></td><td><textarea name="popis" class="form-control"></textarea></td>';
			}
			function upd(){
				document.getElementById('input').innerHTML = '<td><input name="id_pre" class="form-control" placeholder="podminka"></td><td><input name="nazev" class="form-control"></td><td><input name="rocnik" class="form-control"></td><td><textarea name="popis" class="form-control"></textarea></td>';
			}
			function del(){
				document.getElementById('input').innerHTML = '<td><input name="id_pre" class="form-control"></td><td colspan="3"></td>';
			}
			
			//Patri k vyucovani
			function ins1(){
				document.getElementById('input1').innerHTML = '<td></td><td><input name="id_pre" class="form-control"><td><input name="den" class="form-control"></td><td><input name="cas_od" class="form-control"></td><td><input name="cas_do" class="form-control"></td><td><input name="mistnost" class="form-control"></td><td><input name="vyucujici" class="form-control"></td>';
			}
			function upd1(){
				document.getElementById('input1').innerHTML = '<td><input name="id" class="form-control" placeholder="podminka"><td></td><td><input name="den" class="form-control"></td><td><input name="cas_od" class="form-control"></td><td><input name="cas_do" class="form-control"></td><td><input name="mistnost" class="form-control"></td><td><input name="vyucujici" class="form-control"></td>';
			}
			function del1(){
				document.getElementById('input1').innerHTML = '<td><input name="id" class="form-control"></td><td colspan="5"></td>';
			}
			
			//Patri ke studentum
			function ins2(){
				document.getElementById('input2').innerHTML = '<td></td><td><input name="jmeno" class="form-control"></td><td><input name="prijmeni" class="form-control"></td><td><input name="vek" class="form-control"></td><td><input name="telefon" class="form-control"></td><td><input name="email" class="form-control"></td><td><input name="rocnik" class="form-control"></td>';
			}
			function upd2(){
				document.getElementById('input2').innerHTML = '<td><input name="id_stu" class="form-control" placeholder="podminka"></td><td><input name="jmeno" class="form-control"></td><td><input name="prijmeni" class="form-control"></td><td><input name="vek" class="form-control"></td><td><input name="telefon" class="form-control"></td><td><input name="email" class="form-control"></td><td><input name="rocnik" class="form-control"></td>';
			}
			function del2(){
				document.getElementById('input2').innerHTML = '<td><input name="id_stu" class="form-control"></td><td colspan="6"></td>';
			}
			
			//Patri k userum
			function ins3(){
				document.getElementById('input3').innerHTML = '<td></td><td><input name="id_stu" class="form-control"></td><td><input name="name" class="form-control"></td><td><input name="priv" class="form-control"></td>';
			}
			function upd3(){
				document.getElementById('input3').innerHTML = '<td><input name="id" class="form-control" placeholder="podminka"></td><td><input name="id_stu" class="form-control"></td><td><input name="name" class="form-control"></td><td><input name="priv" class="form-control"></td>';
			}
			function del3(){
				document.getElementById('input3').innerHTML = '<td><input name="id" class="form-control"></td><td colspan="4"></td>';
			}
	</script>
</head> 

<body>
	<div id='nav'></div>
	
	<!-- Seznam predmetu  -->
	<?php if ($count_pre > 0) { ?>
	
	<div class='container'>
	<H1 id='predmet'>Seznam predmetu</H1>
	<form action='' method='POST'>
		<table class='table'>
			<thead>
				<tr>
					<th style='width:15%'>ID Predmetu</th>
					<th>Nazev</th>
					<th style='width:10%'>Rocnik</th>
					<th style='width:60%'>Popis</th>
				</tr>

			</thead>
			<?php foreach($predmety as $row) {?>
				<tr>
					<td><?php echo $row['ID_Pre'] ?></td>
					<td><a href='predmet_detail.php?ID_Pre=<?php echo $row["ID_Pre"]?>'><?php echo $row['Nazev'] ?></a></td>
					<td><?php echo $row['Rocnik'] ?></td>
					<td><?php echo $row['Popis'] ?></td>
					<input type="hidden" name="update" value="<?php echo $row['Last_updated_at']?>">
				</tr>
			<?php } ?>
			<tr id='input'></tr>
				<tr>
					<td colspan='3'>
					<form >
					<div class="radio-inline">
						<label><input type="radio" name="radio" value='Insert_pre' onclick='ins()'>Insert</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio' value='Update_pre' onclick='upd()'>Update</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio' value='Delete_pre' onclick='del()'>Delete</label>
					</div>
					</form>
					</td>
					<td>
						<button  style='float:right' type="submit" class="btn" name='Akce' value='Predmety'>Odeslat</button>
					</td>
				</tr>
		</table>
		</form>
		<ul class='pagination' style='margin-top:0px'>
			<?php for($i=1; $i<=ceil($count_pre/5); $i++) { ?>
			<li class="<?= $offset_pre/5+1==$i ? "active" : ""  ?>">
				<a href="./edit.php?offset_pre=<?= ($i-1)*5 ?>#predmet"><?= $i ?></a>
			</li>
			<?php } ?>
		</ul>

		<br/>
	</div>
	
	<?php } ?>
	
	<!-- Seznam vyucovani  -->
	<?php if ($count_vyuc > 0) { ?>
	
	<div class='container'>
	<H1 id='vyucovani'>Seznam vyucovani</H1>
	<form action='' method='POST'>
		<table class='table'>
		<thead>
			<tr>
				<th>ID</th>
				<th>ID Predmetu</th>
				<th>Den</th>
				<th>Cas od</th>
				<th>Cas do</th>
				<th>Mistnost</th>
				<th>Vyucujici</th>
			</tr>
		</thead>
		<?php foreach($vyucovani as $row) {?>
			<tr>
				<td><?php echo $row['ID']?></td>
				<td><?php echo $row['ID_Pre']?></td>
				<td><?php echo $row['Den']?></td>
				<td><?php echo $row['Cas_od']?></td>
				<td><?php echo $row['Cas_do']?></td>
				<td><?php echo $row['Mistnost']?></td>
				<td><?php echo $row['Vyucujici']?></td>
			</tr>
		<?php } ?>
			<tr id='input1'></tr>
				<tr>
					<td colspan='6'>
					<form >
					<div class="radio-inline">
						<label><input type="radio" name="radio1" value='Insert_vyuc' onclick='ins1()'>Insert</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio1' value='Update_vyuc' onclick='upd1()'>Update</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio1' value='Delete_vyuc' onclick='del1()'>Delete</label>
					</div>
					</form>
					</td>
					<td>
						<button  style='float:right' type="submit" class="btn" name='Akce' value='Vyucovani'>Odeslat</button>
					</td>
				</tr>
		</table>
		</form>
		<ul class='pagination' style='margin-top:0px'>
			<?php for($i=1; $i<=ceil($count_vyuc/5); $i++) { ?>
			<li class="<?= $offset_vyuc/5+1==$i ? "active" : ""  ?>">
				<a href="./edit.php?offset_vyuc=<?= ($i-1)*5 ?>#vyucovani"><?= $i ?></a>
			</li>
			<?php } ?>
		</ul>

		<br/>
	</div>
		<?php } 
		else { ?>
		<p>Zadny predmet se nevyucuje.</p>
		<?php } ?>
	
	
	<!-- Seznam studentu -->
	<?php if ($count_stu > 0) { ?>
	
	<div class='container'>
	<H1 id='student'>Seznam studentu</H1>
	<form action='' method='POST'>
		<table class='table'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Jmeno</th>
					<th>Prijmeni</th>
					<th>Vek</th>
					<th>Telefon</th>
					<th>E-mail</th>
					<th>Rocnik</th>
				</tr>

			</thead>
			<?php foreach($studenti as $row) {?>
				<tr>
					<td><?php echo $row['ID_Stu'] ?></td>
					<td><?php echo $row['Jmeno'] ?></td>
					<td><?php echo $row['Prijmeni'] ?></td>
					<td><?php echo $row['Vek'] ?></td>
					<td><?php echo $row['Telefon'] ?></td>
					<td><?php echo $row['Email'] ?></td>
					<td><?php echo $row['Rocnik'] ?></td>
				</tr>
			<?php } ?>
			<tr id='input2'></tr>
				<tr>
					<td colspan='6'>
					<form >
					<div class="radio-inline">
						<label><input type="radio" name="radio2" value='Insert_stu' onclick='ins2()'>Insert</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio2' value='Update_stu' onclick='upd2()'>Update</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio2' value='Delete_stu' onclick='del2()'>Delete</label>
					</div>
					</form>
					</td>
					<td>
						<button  style='float:right' type="submit" class="btn" name='Akce' value='Studenti'>Odeslat</button>
					</td>
				</tr>
		</table>
		</form>
		<ul class='pagination' style='margin-top:0px'>
			<?php for($i=1; $i<=ceil($count_stu/5); $i++) { ?>
			<li class="<?= $offset_stu/5+1==$i ? "active" : ""  ?>">
				<a href="./edit.php?offset_stu=<?= ($i-1)*5 ?>#student"><?= $i ?></a>
			</li>
			<?php } ?>
		</ul>

		<br/>
	</div>
	
	<?php } ?>
	
	<!-- Seznam useru -->
	<?php if ($count_usr > 0) { ?>
	
	<div class='container'>
	<H1 id='user'>Seznam useru</H1>
	<form action='' method='POST'>
		<table class='table'>
			<thead>
				<tr>
					<th style='width:25%'>ID</th>
					<th style='width:25%'>ID_Stu</th>
					<th style='width:15%'>Name</th>
					<th style='width:10%'>Privileges</th>
				</tr>

			</thead>
			<?php foreach($users as $row) {?>
				<tr>
					<td><?php echo $row['id'] ?></td>
					<td><?php echo $row['ID_Stu'] ?></td>
					<td><?php echo $row['name'] ?></td>
					<td><?php echo $row['priv'] ?></td>
				</tr>
			<?php } ?>
			<tr id='input3'></tr>
				<tr>
					<td colspan='3'>
					<form >
					<div class="radio-inline">
						<label><input type="radio" name="radio3" value='Insert_usr' onclick='ins3()'>Insert</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio3' value='Update_usr' onclick='upd3()'>Update</label>
					</div>
					<div class="radio-inline">
						<label><input type="radio" name='radio3' value='Delete_usr' onclick='del3()'>Delete</label>
					</div>
					</form>
					</td>
					<td>
						<button  style='float:right' type="submit" class="btn" name='Akce' value='Users'>Odeslat</button>
					</td>
				</tr>
		</table>
		</form>
		<ul class='pagination' style='margin-top:0px'>
			<?php for($i=1; $i<=ceil($count_usr/5); $i++) { ?>
			<li class="<?= $offset_usr/5+1==$i ? "active" : ""  ?>">
				<a href="./edit.php?offset_usr=<?= ($i-1)*5 ?>#user"><?= $i ?></a>
			</li>
			<?php } ?>
		</ul>

		<br/>
	</div>
	
	<?php } ?>
	
</body>
		
</html>
