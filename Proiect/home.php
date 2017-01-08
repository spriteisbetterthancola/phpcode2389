<!DOCTYPE html>
<html>
	<head>
		<title> Home </title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script type="text/javascript" src="javascript/script.js"></script>
	</head>
	<body>
		<main>
			<header id="banner">
				<h1 class ="banner_title">Lorem Ipsum</h1>
			</header>
			<section id = "new_section">
				<div id="new_button">NEW</div>
			</section>
			<section id ="join_section">
				<!-- <div  id ="join_button">JOIN</div><br/> -->
				<form action="pagina.php" onsubmit="return js_validate()" method="post">

				Id Conversatie:
				<input type="text" name="idConversatie" id="idConversatie" onchange="js_validate()"><br/>
				Nickname:
				<input type="text" name="nickName" id="nickName" onchange="js_validate()"><br/>
				<input type="hidden" name="isValidJoin" value="0"/>
				<input type="submit" name="join_button" id="join_button" value="JOIN"/> 					
				</form>
			</section>
		</main>
		<footer >&copy;TI PROIECT 2017</footer>
	</body>

</html>