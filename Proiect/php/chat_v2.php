<?php
/** @file chat_v2.php
*Prelucrarea fisierelor XML de conversatie
*In functie de parametrii trimisi prin POST, se modifica sau se afiseaza fisierul log.xml al fiecarei conversatii


*/
?>
<?php require_once "conversation.php"; ?>

<?php



/*
if($_SERVER['REQUEST_METHOD'] == "GET") #DEBUG ONLY!
{
	if(isset($_GET["conv_uid"]) && isset($_GET["nick_name"]) && isset($_GET["msg"]))
	{
		//scrie un mesaj in log
		$ce =  conv_exist($_GET["conv_uid"]);
		if(!$ce)
		{
			die("<error>Invalid UID</error>");
		}
		//TODO Strip Slashes HTMLspecialchars etc
		$conv_uid = $_GET["conv_uid"];
		$usr_nick = $_GET["nick_name"];
		$usr_msg  = $_GET["msg"];
		cnv_push_msg($conv_uid, $usr_nick, $usr_msg);
	}
	else if(isset($_GET["conv_uid"]) && isset($_GET['ic']))
	{
		$conv_uid = $_GET["conv_uid"];
		$ce =  conv_exist($_GET["conv_uid"]);
		if(!$ce)
		{
			die("<error>Invalid UID</error>");
		}
		cnv_pull_msg($conv_uid);
	}
	else
	{
		die("<error>External Call</error>");
	}
}
*/


if($_SERVER['REQUEST_METHOD'] == "POST") #DEBUG ONLY!
{
	if(isset($_POST["conv_uid"]) && isset($_POST["nick_name"]) && isset($_POST["msg"]))
	{
		//scrie un mesaj in log
		$ce =  conv_exist($_POST["conv_uid"]);
		if(!$ce)
		{
			die("<error>Invalid UID</error>");
		}
		//TODO Strip Slashes HTMLspecialchars etc
		$conv_uid = $_POST["conv_uid"];
		$usr_nick = $_POST["nick_name"];
		$usr_msg  = $_POST["msg"];
		cnv_push_msg($conv_uid, $usr_nick, $usr_msg);
	}
	else if(isset($_POST["conv_uid"]) && isset($_GET['ic']))
	{
		$conv_uid = $_POST["conv_uid"];
		$ce =  conv_exist($conv_uid);
		if(!$ce)
		{
			die("<error>Invalid UID</error>");
		}
		cnv_pull_msg($conv_uid);
	}
	else
	{
		die("<error>External Call</error>");
	}
}
/* PRODUCTION
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	if(isset($_POST["conv_exist"]))
	{
		$ce =  conv_exist($_POST["conv_exist"]);
		echo $ce?"1":"0";
		//var_dump($_POST);
	}
}
*/
/*!
* Scrie in fisierul log.xml din directorul conversatiei un mesaj nou
@param $conv_uid id-ul conversatiei
@param $usr_nick numele expeditorului
@param $usr_msg textul mesajului
*/
function cnv_push_msg($conv_uid, $usr_nick, $usr_msg)
{
	$cpm_file_log_name = "logs/conv_" . $conv_uid . "/log.xml";
	$cpm_xml = simplexml_load_file($cpm_file_log_name);
	echo $cpm_xml->asXML();
	//$cpm_xml->addChild("new_child", "nc_value");
	$cpm_msg = $cpm_xml->addChild("message");
	$cpm_timestamp;
	if(isset($_SERVER['REQUEST_TIME']))
	{
		$cpm_timestamp = $_SERVER['REQUEST_TIME'];
	}
	else
	{
		$cpm_timestamp = time();
	}
	$cpm_msg->addChild("message_body", $usr_msg);
	$cpm_msg->addChild("sender_name", $usr_nick);
	$cpm_msg->addChild("timestamp", $cpm_timestamp);
	$cpm_xml->asXML($cpm_file_log_name);
	//$cu_xml->asXML($cu_file_base . '/config.xml');//Write to file
}
/*!
* Afiseaza continutul fisierului log.xml al conversatiei  cu ID-ul $conv_uid
@param $conv_uid id-ul conversatiei
*/

function cnv_pull_msg($conv_uid)
{
	$cpl_file_log_name = "logs/conv_" . $conv_uid . "/log.xml";
	$cpl_xml = simplexml_load_file($cpl_file_log_name);
	echo $cpl_xml->asXML();
}
?>