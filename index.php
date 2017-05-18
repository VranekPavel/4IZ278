<?php
session_start();
require 'db.php';

if(isset($_SESSION['user'])){
	unset($_SESSION['user']);
	unset($_SESSION['privileges']);
	setcookie('name', '', -1);
}
else {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$name = test_input($_POST["name"]);
		$pass = test_input($_POST["pass"]);
		$nameerr = '';
		$passerr = '';
		
		if(empty($name)){
		$nameerr = 'Jmeno musi byt vyplneno.';
		}
		if(empty($pass)){
		$passerr = 'Heslo musi byt vyplneno.';
		}
		if(!empty($name) && !empty($pass)){
		$stmt = $db->prepare("SELECT * FROM users WHERE name=?");
		$stmt->execute(array($name));
		$user = $stmt->fetch();
		
			if(!password_verify($pass, $user['pass'])){
				unset($user);
			}
		}
	
		if (!isset($user)){
			echo "<script type='text/javascript'>alert('Spatne heslo, nebo jmeno.');</script>";
		}
		else {
		$_SESSION["privileges"]=$user["priv"];
		//echo gettype($_SESSION["privileges"]);
		//echo $_SESSION["privileges"];
	
		$_SESSION ["user"]=$user["ID_Stu"];
		//echo gettype($_SESSION["user"]);
		//echo $_SESSION["user"];
		$num=generateRandomNum();
		$_SESSION ['num']=$num;
		
		setcookie('name', $user['name']);
		
		if($_SESSION['privileges'] == 1){
			header ('location: edit.php');
			die();
		}
		header('Location: home.php');
		}
	}
}

		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
		return $data;
		}
		
	function generateRandomNum($length = 20) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomNum = '';
    for ($i = 0; $i < $length; $i++) {
        $randomNum .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomNum;
	}
		
?>

<!DOCTYPE html>

<html lang='cs'>

<head>
	<meta charset="utf-8" />
	<title>Log in</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#nav").load("nav.php");
				//$("#id").load("select.txt");
			});
		</script>
		<style>
		.error {color: #FF0000;}
		</style>
</head> 

<body>
	<div id="nav"></div>
	<div class='container'>

	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="col-sm-offset-3 col-sm-6">
		<h1>Log in</h1>
	    <div class="form-group">
			<label for="name">Jmeno</label><span class='error'> <?php if(isset($nameerr)){echo $nameerr;}?></span>
			<input type="text" class="form-control" name="name" id='name' value='<?php if(!empty($name)){echo $name;}?>'>
		</div>
		<div class="form-group">
			<label for="pass">Heslo</label><span class='error'> <?php if(isset($passerr)){echo $passerr;}?></span>
			<input type="password" class="form-control" name="pass" id='pass'>
		</div>
		<button type="submit" class="btn btn-default" >Prihlasit</button>
		<a href='password.php'><button type="button" class="btn btn-default" >Zapomenute heslo</button></a>
		
	</form>
	</div>
</body>

</html>
