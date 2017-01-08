<!DOCTYPE html>
<?php 
//Seteaza variabilele de sesiune
session_start();
if($_SERVER['REQUEST_METHOD'] == "POST")//Provine din home.php
{
	$conv_uid = "";
	$usr_name = "";
	if(isset($_POST['idConversatie']))
	{
		$conv_uid = $_POST['idConversatie'];
	}
	else
	{
		die("<br>NULL CONV ID!<br>");
	}
	if(isset($_POST['nickName']))
	{
		$usr_name = $_POST['nickName'];
	}
	else {
		die ("No User Name!<b>");
	}
}

<<<<<<< HEAD
$conv_uid = 0;
$usr_name = 0;

?>
<html>
	<head>
		<meta charset="UTF-8">
=======
//DEBUG ONLY!!!
$usr_name = "Ana";
$conv_uid = "";
?>
<html>
	<head>
>>>>>>> e65f751bacdaa29d8c02ba0c892e28938413f6b6
		<link rel="stylesheet" type="text/css" href="css/w3.css">
	</head>
	<body>
	<form action="return false;">
		<input type="hidden" name="nickName" value=<?php echo '"' . $usr_name . '"'?>/>
		<input type="hidden" name="idConversatie" value=<?php echo '"' . $conv_uid . '"' ?>/>
	</form>
		<main> <section>
			<header class="w3-container w3-theme w3-padding w3-center">
				<h1 class ="w3-animate-bottom w3-xxxlarge">Lowerthyi ghhui</h1>
			</header>

			<section class =" w3-container">
				<section class ="w3-card-2 w3-padding w3-group" style="height:450px;">
				<div class="message">
					<p class="msg-body">
				</div>
					<p style="color: blue;text-align: left;display: left">Hello<br/><i>12:12:22&nbsp; Admin:</i></p>
					<p style="color: green;text-align:right"> Salut<br/> <i> 1:23:09 &nbsp; Florin:</i></p>
				</section>
			</section>
		</section></main>
		<footer class="w3-container w3-theme w3-padding w3-center">
			&copy;TI PROIECT 2017
		</footer>
	
	</body>
</html>