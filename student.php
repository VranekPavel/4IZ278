<?php
session_start();
require 'db.php';

	$stmt = $db->prepare("SELECT * FROM Student WHERE ID_Stu=?");
	$stmt->execute(array ($_SESSION['user']));
	$student = $stmt->fetch();
	
	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['Akce'] == 'info') {	
		
		$jmeno = test_input($_POST["Jmeno"]);
		$prijmeni = test_input($_POST["Prijmeni"]);
		$vek = test_input($_POST["Vek"]);
		$telefon = test_input($_POST["Telefon"]);
		$email = test_input($_POST["Email"]);
		$num = test_input($_POST["num"]);
		$jmenoerr='';
		$prijmenierr='';
		$mailerr='';
		$telefonerr='';
		
		if($num != $_SESSION['num']){
			echo "<script type='text/javascript'>alert('Vsichni tady umreme.');</script>";
			echo "<script type='text/javascript'>location.assign('index.php');</script>";
			die();
		}
		
		if(empty($jmeno)){
			$jmenoerr = 'Jmeno musi byt vyplneno.';
		}
		if(empty($prijmeni)){
			$prijmenierr = 'Prijmeni musi byt vyplneno.';
		}
		if(!preg_match('/^\d{9}$/', $telefon)){
			$telefonerr = 'Telefonni cislo musi obsahovat 9 cislic.';
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$mailerr = 'E-mail je ve spatnem formatu.';
		}
		if (!empty($jmeno) && !empty($prijmeni) && empty($telefonerr) && empty($mailerr)){
		$stmt = $db->prepare("UPDATE Student SET Jmeno=?, Prijmeni=?, Vek=?, Telefon=?, Email=? WHERE ID_Stu=?");
		$stmt->execute(array($jmeno, $prijmeni, (int)$vek, (int)$telefon, $email, $_SESSION["user"]));

		header('location: home.php');
		}
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['Akce'] == 'pass'){
		
		$stmt = $db->prepare("SELECT * FROM users WHERE ID_Stu=?");
		$stmt->execute(array($_SESSION['user']));
		$user = $stmt->fetch();
	
		$heslo = test_input($_POST["heslo"]);
		$heslon = test_input($_POST["heslon"]);
		$heslon1 = test_input($_POST["heslon1"]);
		
		$hesloerr = '';
		$heslonerr = '';
		$heslon1err = '';
		$err ='';
		
		if (empty($heslo)){
			$hesloerr = 'Musite vyplnit puvodni heslo';
		}
		if (empty($heslon)){
			$heslonerr = 'Musite vyplnit nove heslo';
		}
		if (empty($heslon1)){
			$heslon1err = 'Musite vyplnit nove heslo';
		}
		if ($heslon != $heslon1){
			$err = 'Nove heslo se neshoduje';
		}
		if (!password_verify($heslo, $user['pass'])){
			$err = 'Puvodni heslo neodpovida';
		}
		if(empty($hesloerr) && empty($heslonerr) && empty($heslon1err) && empty($err)){
			$heslo = password_hash($heslon, PASSWORD_DEFAULT);
			$stmt = $db->prepare("UPDATE users SET pass=? WHERE ID_Stu=?");
			$stmt->execute(array($heslo, $user['ID_Stu']));
			echo "<script type='text/javascript'>alert('Heslo zmeneno.');</script>";
		}
	}
	
	function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
	return $data;
	}
	 
?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<title>Student</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#nav").load("nav.php", setActive);
				//$("#id").load("select.txt");
				function setActive (){
				$("#student").addClass("active");
			}});
		</script>
		<style>
		.error {color: #FF0000;}
		</style>
</head> 

<body>
	<div id="nav"></div>
		<div class='container'>
		<form class="form col-sm-offset-2 col-sm-4" action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method='POST'>
			<div class='form-group'>
				<h1><?php echo $student["Jmeno"] . " " . $student["Prijmeni"]?></h1>
			</div>
			<div class="form-group">
				<label for="Jmeno">Jmeno:</label><span class='error'> <?php if(isset($jmenoerr)){echo $jmenoerr;}?></span>
				<input type="text" class="form-control" name="Jmeno" value='<?php echo $student["Jmeno"]?>'>
			</div>
			<div class="form-group">
				<label for="Prijmeni">Prijmeni:</label><span class='error'> <?php if(isset($prijmenierr)){echo $prijmenierr;}?></span>
				<input type="text" class="form-control" name="Prijmeni" value='<?php echo $student["Prijmeni"]?>'>
			</div>
			<div class="form-group">
				<label for="Vek">Vek:</label>
				<input type="text" class="form-control" name="Vek" value='<?php echo $student["Vek"]?>'>
			</div>
			<div class="form-group">
				<label for="Telefon">Telefon:</label><span class='error'> <?php if(isset($telefonerr)){echo $telefonerr;}?></span>
				<input type="text" class="form-control" name="Telefon" value='<?php echo $student["Telefon"]?>'>
			</div>
			<div class="form-group">
				<label for="Email">E-mail:</label><span class='error'> <?php if(isset($mailerr)){echo $mailerr;}?></span>
				<input type="text" class="form-control" name="Email" value='<?php echo $student["Email"]?>'>
			</div>
				<button type="submit" class="btn btn-default" name='Akce' value='info'>Submit</button>
				
				<input type="hidden" name="num" value="<?php echo $_SESSION['num']?>">
		</form>
		
		<form class="form col-sm-4" action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method='POST'>
				<h1>Zmena hesla</h1>
			<div class="form-group">
				<label for="heslo">Stare heslo:</label><span class='error'> <?php if(!empty($hesloerr)){echo $hesloerr;}?></span>
				<input type="password" class="form-control" name="heslo">
			</div>
			<div class="form-group">
				<label for="heslon">Nove heslo:</label><span class='error'> <?php if(!empty($heslonerr)){echo $heslonerr;}?></span>
				<input type="password" class="form-control" name="heslon">
			</div>
			<div class="form-group">
				<label for="heslon1">Nove heslo jeste jednou:</label><span class='error'> <?php if(!empty($heslon1err)){echo $heslon1err;}?></span>
				<input type="password" class="form-control" name="heslon1">
			</div>
			<span class='error'><?php if(!empty($err)){echo $err;}?></span><br>
				<button type="submit" class="btn btn-default" name='Akce' value='pass'>Submit</button>
		</form>
		</div>
	</body>
		
</html>

</body>

</html>


