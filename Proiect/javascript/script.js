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

function js_validate_set_state(elem, isValid)
{
	if(isValid)
		elem.style.backgroundColor = "#00FF00";
	else
		elem.style.backgroundColor = "#FF0000";
	return elem;
}



function js_validate()
{
	//Verifica daca Sectiunea de Join de pe pagina Home este completata corect
	var input_conv = document.getElementById("idConversatie");
	var vj =  document.getElementById("isValidJoin");
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var input_conv = document.getElementById("idConversatie");
			var vj =  document.getElementById("isValidJoin");
			if(this.responseText == "1")
			{
				input_conv =  js_validate_set_state(input_conv, true);
				var nick_conv = document.getElementById("nickName");
				vj.value = vj.value | 1;
			}
			else
			{
				input_conv = js_validate_set_state(input_conv, false);
				if(vj.value % 2 == 1)
				{
					vj.value = vj.value - 1;
				}
			}
		}
	};
	reqString = "php/conversation.php";//80bf99c
	reqString = reqString;
	xhttp.open("POST", reqString, true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("conv_exist=" + input_conv.value);

	var input_nick = document.getElementById("nickName");
	if(input_nick.value == "")//TODO vezi daca numele introdus e valid
	{
		input_nick = js_validate_set_state(input_nick, false);
		if((vj.value & 2) != 0){
			vj.value = vj.value - 2;
		}
	}
	else
	{
		input_nick = js_validate_set_state(input_nick, true);
		vj.value = vj.value | 2;
	}

	if(vj.value == 3)
	{
		return true;
	}
	else
	{
		return false;
	}
}