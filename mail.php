<?php 
session_start();
require'db.php';

if (isset($_SESSION['user'])){
$stmt = $db->prepare("SELECT Email FROM Student WHERE ID_Stu=?");
$stmt ->execute(array($_SESSION['user']));
$mail = $stmt->fetchColumn();
$to = $_GET['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST" ){
$headers = 'From:' .  $mail;
$subject = $_POST['titulek'];
$message = $_POST['text'];
$message = wordwrap($message, 70);

mail($to,$subject,$message, $headers);
header('location: studenti.php');
}
}
else {
echo "<script type='text/javascript'>alert('Nejste pøihlášen.');</script>";
echo("<script>window.location = history.go(-1);</script>");
die();
}
?>
<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<title>Mail</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		
</head> 

<body>
	<form class="form-horizontal" action='' method='POST'>
  <div class="form-group">
    <label class="control-label col-sm-2" for="titulek">Titulek:</label>
    <div class="col-sm-8"> 
      <input type="text" class="form-control" name="titulek" placeholder="Hlavicka">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-sm-2" for="text">Text:</label>
    <div class="col-sm-8"> 
      <textarea class="form-control" name="text" placeholder="Text" rows='6'></textarea>
    </div>
  </div>
  <div class="form-group"> 
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">Odeslat</button>
    </div>
  </div>
</form>
</body>
		
</html>

