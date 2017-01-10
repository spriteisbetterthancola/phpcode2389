function cnv_get_conversation()
{

	var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

       // Typical action to be performed when the document is ready:
       //document.getElementById("diva_lu_ana").innerHTML = this.responseText;
       cnv_display_mesage_from_xml(this.responseXML);
    }
};

var xml_name = "/php/logs/conv_" + document.getElementById("iC").value + "/log.xml";
//document.getElementById("diva_lu_ana").innerHTML = xml_name;
xhttp.open("GET", xml_name, true);
xhttp.send();
}

function cnv_display_mesage_from_xml (elem)
{	
	var messages = elem.getElementsByTagName("message");
	var sectiune = "";
	var tsm = "";
	var sender = "";
	var body_m = "";
	var admin = document.getElementById("nA").value;
	var nickname = document.getElementById("nN").value;
	for( i= 0;i<messages.length;i++)
	{

		tms =messages[i].getElementsByTagName("timestamp")[0].childNodes[0].nodeValue;
		sender=messages[i].getElementsByTagName("sender_name")[0].childNodes[0].nodeValue;
		body_m =messages[i].getElementsByTagName("message_body")[0].childNodes[0].nodeValue;



		if(sender == admin)
		{
				sectiune += "<section class=\"w3-container\"><section class=\"w3-row w3-left w3-padding-0 \">" ;
  		sectiune+="<section class=\"w3-right w3-small w3-text-red\"><b>"+admin + "</b></section><section class=\"w3-left w3-padding-right w3-tiny\"><i>"+tms +"</i></section></section><br/>";
		sectiune += "<section class=\"w3-row w3-left w3-padding-0 w3-margin-0\"><article class=\"w3-padding w3-amber\">"+body_m+ "</article </section></section>";
		}
		else{
		if(sender == nickname)
		{
					sectiune += "<section class=\"w3-container\"><section class=\"w3-row w3-right w3-padding-0 \">" ;
			sectiune +="<section class=\"w3-right w3-tiny\"><i>"+ tms +"</i></section>";
			sectiune += "</section><br/><section class=\"w3-row w3-right w3-padding-0 w3-margin-0\"><article class=\"w3-padding w3-pale-blue\"> "+body_m +"</article> </section><br/> </section>";
		}
		else
		{
					sectiune += "<section class=\"w3-container\"><section class=\"w3-row w3-left w3-padding-0 \">" ;
			sectiune +="<section class=\"w3-right w3-small\" ><b>"+sender+"</b></section>";
		    sectiune += "<section class=\"w3-left w3-padding-right w3-tiny\"><i>"+ tms + "</i></section></section><br/><section class=\"w3-row w3-left w3-padding-0 w3-margin-0\">";
			sectiune +=  "<article class=\"w3-padding w3-pale-green\">"+ body_m + "</article></section></section>";
		}
	}
		sectiune +="<br>";
	}
	document.getElementById("msg-box").innerHTML = sectiune;
}

function send_message(){
	var x = document.getElementById("msg_text").value;
	var uid = document.getElementById("iC").value;
	var nn = document.getElementById("nN").value;
	if(x!="")
	{
	var xhttp = new XMLHttpRequest();
	var reqString = "php/chat_v2.php";
		
		xhttp.open("POST", reqString, true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("conv_uid="+uid+"&nick_name="+nn+"&msg="+x );
	}
    cnv_get_conversation();
    document.getElementById("msg_text").value = "";
		/*xhttp.onreadystatechange = function() {
			
    if (this.readyState == 4 && this.status == 200) {
    	document.getElementById("diva_lu_ana").innerHTML = this.responseText;
    }
};*/
}