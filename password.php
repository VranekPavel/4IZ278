<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$mail = $_POST['email'];
	$name = $_POST['name'];
	
	$stmt = $db->prepare("SELECT stu.ID_Stu, usr.name FROM Student stu JOIN users usr on stu.ID_Stu=usr.ID_Stu WHERE stu.Email=? and usr.name=?");
	$stmt->execute(array($mail, $name));
	$student = $stmt->fetch();
	
	if (!$student){
		echo "<script type='text/javascript'>alert('Spatny E-mail.');</script>";
	}
	else {
		$pass = generateRandomString();
		$subject = 'Obnoveni hesla.';
		$message = 'Uzivatelske heslo: ' . $pass;
		$headers = 'From: secretariat@skola.cz';
		mail($mail, $subject, $message, $headers);
		echo "<script type='text/javascript'>alert('E-mail odeslan.');</script>";
		$pass = password_hash($pass, PASSWORD_DEFAULT);
		
		$stmt = $db->prepare("UPDATE users SET pass=? WHERE ID_Stu=?");
		$stmt->execute(array($pass, $student['ID_Stu']));
		
		
		
		header('Location: index.php');
	}
}
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

<html lang='cs'>

<head>
	<meta charset="utf-8" />
	<title>Password</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head> 

<body>
	<div class='container'>

	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="col-sm-offset-3 col-sm-6">
		<div class="form-group">
			<label for="name">User name</label>
			<input type="text" class="form-control" name="name">
		</div>
	    <div class="form-group">
			<label for="email">E-mail</label>
			<input type="text" class="form-control" name="email">
		</div>
		<button type="submit" class="btn btn-default" >Odeslat</button>

		
	</form>
	</div>
</body>

</html>
