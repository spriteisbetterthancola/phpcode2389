<!DOCTYPE html>
<html>
	<head>
		<title>Chat - Customer Module</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="script.js"></script>
	</head>
	<script>
		window.setInterval(refreshFunction(), 2500);//Refresh chat la fiecare 2.5 s
	</script>
	<body>
		<div id="wrapper">
		    <div id="menu">
		        <p class="welcome">Welcome, <b></b></p>
		        <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
		        <div style="clear:both"></div>
		    </div>
		     
		    <div id="chatbox"></div>
		     
		    <form name="message" action="">
		        <input name="usermsg" type="text" id="usermsg" size="63" />
		        <input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
		    </form>
		</div>

</body>
</html>
