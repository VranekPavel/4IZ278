<?php

session_start();
require 'db.php';

$ID = $_GET['prom'];
$ID_Pre = $_GET['pre'];
$message;

if (isset($_SESSION['user'])){
	$stmt = $db->prepare("SELECT count(inf.ID) FROM Info inf JOIN Vyucovani vyuc ON  vyuc.ID=inf.ID_Vyuc WHERE inf.ID_Stu=? and inf.ID_Vyuc=?");
	$stmt->execute(array ($_SESSION['user'], $ID));
	$predmet = $stmt->fetchColumn();
	
	$stmt = $db->prepare("SELECT count(stu.ID_Stu) as rok FROM Student stu, Predmet pre WHERE stu.Rocnik=pre.Rocnik and stu.ID_Stu=? and pre.ID_Pre=?");
	$stmt->execute(array ($_SESSION['user'], $ID_Pre));
	$rok = $stmt->fetchColumn();
	
	if ($predmet > 0){
		$message = 'Tento cas vyucovani jiz mas pridany. Zkus jiny, sprte.';
	}
	elseif ($rok == 0){
		$message='Tento predmet nemuzes studovat ve svem rocniku.';
	}
	else {
		
		$stmt = $db->prepare("INSERT INTO Info(ID_Stu, ID_Pre, ID_Vyuc) VALUES (?, ?, ?)");
		$stmt->execute(array($_SESSION['user'], $ID_Pre, $ID));
		
		$message= 'Predmet pridan.';
	} 


}
else {
	$message = "Nejste pøihlášen.";
}

echo "$message";
?>

