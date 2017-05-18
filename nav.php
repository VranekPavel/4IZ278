<?php 
session_start();
?>
<style>
p{
color:grey;
font-size:20px;
padding-top:10px;
padding-right:5px;
}
</style>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <p>RadejiSiSpravujZnamkyRucne Aplikace </p>
    </div>
    <ul class="nav navbar-nav">
      <li class="" id="home"><a href="home.php">Home</a></li>
	  <li class="" id="studenti"><a href="studenti.php">Studenti</a></li>
	  <li class="" id="predmety"><a href="predmety.php">Predmety</a></li>
	  <li class="" id="edit"><?php if(isset($_SESSION['privileges']) && $_SESSION['privileges'] == 1 ){echo '<a href="edit.php">Edit</a>';}?></li>
    </ul>
	<ul style='float:right' class="nav navbar-nav">
		<li><a>User: <?= isset($_COOKIE['name']) ? $_COOKIE['name'] : 'Guest'?></a></li>
		<li><a href="index.php"><?= isset($_COOKIE['name']) ? 'Odhlasit' : 'Prihlasit'?></a></li>
	</ul>
  </div>
</nav>
	
