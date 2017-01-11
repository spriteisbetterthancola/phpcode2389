<!DOCTYPE html>
<?php
$conv_uid = "";

date_default_timezone_set("Europe/Bucharest");
if($_SERVER['REQUEST_METHOD'] == "GET") {
	if(isset($_GET['id'])) {
		$conv_uid = htmlspecialchars($_GET['id']);
		if (strlen($conv_uid) > 10) {
			$conv_uid = "";
		}
	}
}
?>
<html>
	<head>
		<title> Home </title>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" type="text/css" href="css/customize_style.css">
		<script type="text/javascript" src="javascript/script.js"></script>

	</head>
	<body class="css-display-container">
	<header class="css-container css-teal css-padding css-center">
		<h1 class ="css-animate-top css-xxxlarge">Meeting Pit</h1>
	</header>
	<!-- <div id="eu"> DiV EU </div> -->
	<section id="main-wrapper" class="css-row-padding css-center css-margin-top">
		<section id="join-wrapper" class="css-half">
			<section id="join-card" class="css-card-2" style="min-height:469px">
				<head id="join-head">
				  <h3 class="css-teal css-padding-top">Join</h3>
				    <section id="join-form-wrapper" class="css-row-padding css-center">
					  <form class="css-panel" style="border: 0px solid !important;"  action="pit.php" 
					  onsubmit="return js_validate(this);" method="post" name ="join_form">
								<div class="css-group">
									<input class="css-input " type ="text" required="required" name="idConversatie"  id="idConversatie"
									onchange="js_validate(this)" id="idConversatie" value="<?php echo $conv_uid; ?>" autocomplete="off">
									<label class="css-label css-validate">Id conversație</label>
								</div>

								<div class="css-group">
									<input class="css-input" type ="text" required="required"  name ="nickName" id="nickName"
									onchange="js_validate(this)" id= "nickname">
									<label class="css-label css-validate">Utilizator</label>
								</div>

								<div class="css-group">
									<input type="hidden" name="isValidJoin" id="isValidJoin" value="<?php echo (int)($conv_uid != "") ?>"/>
									<input type="submit" class="css-btn css-teal" name="join" id="join" value="Join"/>
								</div>	
					  </form>
					</section>
				</head>
			</section>
		</section>
		<section id="new-wrapper" class="css-half">
			<section id="new-card" class="css-card-2" style="min-height:469px">
			<head id="join-head">
					<h3 class="css-teal css-padding-top">New Conversation</h3>
					<section id="join-form-wrapper" class="css-row-padding css-center">
					  <form class="css-panel" style="border: 0px solid !important;"  action="php/conv_create.php" 
					  onsubmit="return js_validate_new(this);" method="post">
								<div class="css-group">
									<input class="css-input" type ="text" required="required" name ="conversationName" 
									onchange="js_validate_new(this)" id ="conversationName">
									<label class="css-label css-validate">Nume conversație</label>
								</div>

								<div class="css-group">
									<input class="css-input" type="text" required="required"  name="adminName" 
									onchange="js_validate_new(this)" id="adminName">
									<label class="css-label css-validate">Nume Administrator</label>
								</div>

								<div class="css-group">
									<input class="css-input" type ="password" required="required"  name="pswd" 
									onchange="js_validate_new(this)" id="passwordInput">
									<label class="css-label css-validate">Parola</label>
								</div>

								<div class="css-group">
									<input type="hidden" name="isValidJoin" id="isValidNew" value="0"/>
									<input type="submit" class="css-btn css-teal" name="newConvBtn" id="newConvBtn" value="Create"/>
								</div>	
					  </form>
					  </section>
				</head>
			</section>
		</section>
	</section>


	<footer class="css-row css-theme css-padding css-center css-margin-top css-teal" >
	&copy;TI PROIECT 2017
	</footer>
	</body>
</html>