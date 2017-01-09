<?php
require_once("conversation.php");
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	if(isset($_POST["conv_exist"]))
	{
		$ce =  conv_exist($_POST["conv_exist"]);
		echo $ce?"1":"0";
		//var_dump($_POST);
	}
};
?>