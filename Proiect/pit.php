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
?>
<html>
	<head>
		
	</head>
	<body>
	<form action="return false;">
		<input type="hidden" name="nickName" value=<?php echo '"' . $usr_name . '"' ?>/>
		<input type="hidden" name="idConversatie" value=<?php echo '"' . $conv_uid . '"' ?>/>
	</form>
		<main> <section>
			<header><section>
				HEADER		
			</section></header>
			<aside>
				<section>
					Lorem Ipsum Aside
				</section>
			</aside>
			<section>
				<section>
					Main Chat boddy
				</section>
			</section>
			<footer><section>
				Footer - f.subtire (ascuns pe mobil)
			</section></footer>
		</section></main>
	</body>
</html>