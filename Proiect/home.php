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
			<br/><br/><br/>
				<div id="new_button">NEW</div>
			</section>
			<section id ="join_section">
				<!-- <div  id ="join_button">JOIN</div><br/> -->

				<!-- <form action="pit.php" onsubmit="return js_validate(this)" method="post"> -->
				<form action="pit.php" onsubmit="return js_validate(this);" method="post">

<!--
				Id Conversatie:
				<input type="text" name="idConversatie" id="idConversatie" onchange="js_validate(this)"><br/>
				Nickname:
				<input type="text" name="nickName" id="nickName" onchange="js_validate(null)"><br/>
				<input type="hidden" name="isValidJoin" id="isValidJoin" value="0"/>
-->
				<br/><br/><br/>
				<form action="pagina.php" onsubmit="return js_validate(this)" method="post">
				
				Id Conversatie:
				<input type="text" name="idConversatie" id="idConversatie" onchange="js_validate(this)"><br/><br/>

				Nickname:
				<input type="text" name="nickName" id="nickName" onchange="js_validate(this)"><br/><br/>
				<input type="hidden" name="isValidJoin" id="isValidJoin" value="0"/>
				</form>
			</section>
		</main>
		<footer >&copy;TI PROIECT 2017</footer>
	</body>

</html>