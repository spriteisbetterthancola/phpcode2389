<?php
session_start();
if($_SERVER["REQUEST_METHOD"] == "GET");
{
	if(!isset($_SERVER['text']))
	{
		$fp = fopen("logs/log.txt", 'r');
		while (!feof($fp)) {
			$text = fread($fp, 100);
			echo $text;
		}
		fclose($fp);
	}
	//if(isset($_SESSION['text']))
	else
	{
	    $text = $_GET['text'];
	    $_SESSION['name'] = "Tudor";
	    $fp = fopen("logs/log.txt", 'a');
	    fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
	    fclose($fp);
	}
}
//}
?>
