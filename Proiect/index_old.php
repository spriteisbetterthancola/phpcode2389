<!DOCTYPE html>
<html>
	<head>
		<title>Chat - Customer Module</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="javascript/script.js"></script>
	</head>
	<script>
		window.setInterval(refreshFunction, 2500);//Refresh chat la fiecare 2.5 s
	</script>
	<body>
		<div id="wrapper">
		    <div id="menu">
		        <p class="welcome">Welcome, 
		        	<p id="username">
		        		<input type="text" name="name" id="userID" onblur="inputTextToDiv(this)">
		        	</p>
		        </p>

		        <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
		        <div style="clear:both"></div>
		    </div>
		     
		    <div id="chatbox"></div>
		     
		    <form name="message" action="" onsubmit="return false;" autocomplete="off">
		    <!-- onsubmit=return false = Pagina nu face refresh la fiecare submit de form 
				 autocomplete="off" = nu se afiseaza sugestii pentru input 
				 	(nu se incearca completarea automata a mesajului de trimis)
		    -->
		        <input name="text" type="text" id="usermsg" size="63" />
		        <input name="submitmsg" type="submit"  id="submitmsg" value="Send" onclick="pushMsg()" />
		    </form>
		</div>

</body>
</html>
