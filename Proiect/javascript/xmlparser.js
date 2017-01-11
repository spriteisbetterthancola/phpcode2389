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

		tmStamp = messages[i].getElementsByTagName("timestamp")[0].childNodes[0].nodeValue;
		tmStamp = tmStamp * 1000;//PHP returneaza ts in secunde si JS in milisecunde
		d = new Date(parseInt(tmStamp));
		tms = d.getHours() + " : " + d.getMinutes() + " : " + d.getSeconds();

		sender=messages[i].getElementsByTagName("sender_name")[0].childNodes[0].nodeValue;
		body_m =messages[i].getElementsByTagName("message_body")[0].childNodes[0].nodeValue;



		if(sender == admin)
		{
			sectiune += "<section class=\"css-container\"><section class=\"css-row css-left css-padding-0 \">" ;
  			sectiune+="<section class=\"css-right css-small css-text-red\"><b>"+admin + "</b></section><section class=\"css-left css-padding-right css-tiny\"><i>"+tms +"</i></section></section><br/>";
			sectiune += "<section class=\"css-row css-left css-padding-0 css-margin-0\"><article class=\"css-padding css-amber\">"+ body_m + "</article </section></section>";
		}
		else if(sender == nickname)
		{
			sectiune += "<section class=\"css-container\"><section class=\"css-row css-right css-padding-0 \">" ;
			sectiune +="<section class=\"css-right css-tiny\"><i>"+ tms +"</i></section>";
			sectiune += "</section><br/><section class=\"css-row css-right css-padding-0 css-margin-0\"><article class=\"css-padding css-pale-blue\"> "+body_m +"</article> </section><br/> </section>";
		}
		else
		{
			sectiune += "<section class=\"css-container\"><section class=\"css-row css-left css-padding-0 \">" ;
			sectiune +="<section class=\"css-right css-small\" ><b>"+sender+"</b></section>";
		    sectiune += "<section class=\"css-left css-padding-right css-tiny\"><i>"+ tms + "</i></section></section><br/><section class=\"css-row css-left css-padding-0 css-margin-0\">";
			sectiune +=  "<article class=\"css-padding css-pale-green\">"+ body_m + "</article></section></section>";
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
		
		xhttp.open("POST", reqString, false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("conv_uid="+uid+"&nick_name="+nn+"&msg="+x );
	}
    
    var sectiune = "<br/><section class=\"css-container\"><section class=\"css-row css-right css-padding-0 \">";
	sectiune +="<section class=\"css-right css-tiny\"><i>"+ "12:20:30" +"</i></section>";
	sectiune += "</section><br/><section class=\"css-row css-right css-padding-0 css-margin-0\"><article class=\"css-padding css-pale-blue\"> "+ document.getElementById("msg_text").value +"</article> </section><br/> </section>";
    document.getElementById("msg-box").innerHTML += sectiune;
    document.getElementById("msg_text").value = "";
    //cnv_get_conversation();
	
		/*xhttp.onreadystatechange = function() {

    if (this.readyState == 4 && this.status == 200) {
    	document.getElementById("diva_lu_ana").innerHTML = this.responseText;
    }
};*/
}