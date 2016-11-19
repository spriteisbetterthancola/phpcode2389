function refreshFunction()
{
	chBox = document.getElementById('chatbox');
	//chBox.innerHTML = 'Updated';
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("chatbox").innerHTML = this.responseText;
	    }
	  };
	  xhttp.open("GET", "php/chat.php", true);
	  xhttp.send();
	return;
}
function pushMsg()
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		refreshFunction();
	};
	reqString = "php/chat.php?text=";
	reqString += document.getElementById("usermsg").value;//strip slashes html etc
	xhttp.open("GET", reqString, true);
	xhttp.send();
	document.getElementById("usermsg").value = "";
}

