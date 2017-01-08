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
}

$conv_uid = 0;
$usr_name = 0;

?>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="css/w3.css">
	</head>
	<body>
	<form action="return false;">
		<input type="hidden" name="nickName" value=<?php echo '"' . $usr_name . '"'?>/>
		<input type="hidden" name="idConversatie" value=<?php echo '"' . $conv_uid . '"' ?>/>
	</form>
		<main> <section>
			<header class="w3-container w3-theme w3-padding w3-center">
				<h1 class ="w3-animate-top w3-xxxlarge">Conversation Title</h1>
			</header>
<section class ="w3-container" id="msg-box">



  <section class ="w3-container w3-card-4 w3-padding-top" style="height:450px;">

<section class="w3-container">
  <section class="w3-row w3-left w3-padding-0 ">
    <section class="w3-right w3-small w3-text-red"><b>Admin</b></section>
    <section class="w3-left w3-padding-right w3-tiny"><i>23:59:59</i></section>
  </section><br/>
  <section class="w3-row w3-left w3-padding-0 w3-margin-0">
  	<article class="w3-padding w3-amber">Hello World</article>
  </section>
</section><br/>

<section class="w3-container">
  <section class="w3-row w3-left w3-padding-0 ">
    <section class="w3-right w3-small"><b>UserName</b></section>
    <section class="w3-left w3-padding-right w3-tiny"><i>23:59:59</i></section>
  </section><br/>
  <section class="w3-row w3-left w3-padding-0 w3-margin-0">
  	<article class="w3-padding w3-pale-green">Hello World</article>
  </section>
</section><br/>

<section class="w3-container">
  <section class="w3-row w3-right w3-padding-0 ">
    <section class="w3-right w3-tiny"><i>23:59:59</i></section>
  </section><br/>
  <section class="w3-row w3-right w3-padding-0 w3-margin-0">
  	<article class="w3-padding w3-pale-blue">Hello World</article>
  </section><br/>

  
</section><br/>
</section>

<section id="send-box" class="w3-panel">
    <form class="w3-panel w3-row w3-display-container" action="" onsubmit="false;">
    	<input type="text" class="w3-input w3-threequarter w3-display-left" name="msg_text"/>
    	<input type="submit" class="w3-input w3-quarter w3-display-right" name="send-btn" value="SEND"/>
    </form>
</section>


  </main>
  <!--
		<footer class="w3-container w3-theme w3-padding w3-center">
			&copy;TI PROIECT 2017
		</footer>
	-->
	</body>
</html>
