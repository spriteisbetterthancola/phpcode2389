<!DOCTYPE html>
<?php 
//Seteaza variabilele de sesiune
require_once("php/conversation.php");
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
//Tot aici trebuie sa creem o noua conversatie daca este necesar
}

//$conv_uid = 0;
//$usr_name = 0;
//$numeAdmin = 0;

?>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="css/customize_style.css">
		<script type="text/javascript" src="javascript/xmlparser.js"></script>
	</head>
	<body>
	
	<form action="return false;">
		<input type="hidden" name="nickName" id="nN" value=<?php echo '"' . $usr_name . '"'?>/>
		<input type="hidden" name="idConversatie" id ="iC" value=<?php echo '"' . $conv_uid . '"' ?>/>
		<input type="hidden" name="numeAdmin"  id ="nA" value=<?php echo '"' . get_admin_name($conv_uid) . '"' ?>/>

	</form>
	<script>	window.setInterval(cnv_get_conversation, 1500);//Refresh chat la fiecare 2.5 s	</script>
	<div id="diva_lu_ana">Diva Lu Ana</div>
		<main> <section>
			<header class="css-container css-teal css-padding css-center">
				<h1 class ="css-animate-top css-xxxlarge">Conversation Title</h1>
			</header>
<section class ="css-container" >



  <section class="css-container css-card-4 css-padding-top" style="height:450px; overflow: scroll;" id="msg-box"></section>






  


<section id="send-box" class="css-panel">
    <form class="css-panel css-row css-display-container" action="" onsubmit="return false;">
    	<input type="text" class="css-input css-threequarter css-display-left" id="msg_text"/>
    	<input type="submit" class="css-input css-quarter css-display-right" name="send-btn"  id ="SEND"value="SEND" onclick="send_message();" />
    </form>
</section>


  </main>
  <!--
		<footer class="css-container css-theme css-padding css-center">
			&copy;TI PROIECT 2017
		</footer>
	-->
	</body>
</html>
