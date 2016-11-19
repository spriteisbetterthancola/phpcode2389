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
	  xhttp.open("POST", "php/chat.php", true);
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
	reqString += "&name=" + document.getElementById("userID").value;
	xhttp.open("POST", reqString, true);
	xhttp.send();
	document.getElementById("usermsg").value = "";
}

function inputTextToDiv(elem)
{
	elem.style.display = "none";
	var para = document.createElement("P");                       // Create a <p> element
	//para.style = "padding-top:3px; padding-bottom: 3px; font-weight: bold;";
	para.id = "username2";
	var t = document.createTextNode(elem.value);		          // Create a text node
	para.appendChild(t);                                          // Append the text to <p>
	elem.parentNode.appendChild(para);                              // Append <p> to parent of elem
}
