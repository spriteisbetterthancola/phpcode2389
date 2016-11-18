<?php
session_start();
if($_SERVER["REQUEST_METHOD"] == "GET");
{
	if(isset($_GET['text']))
	{
		echo "Scriere";
	    $text = $_GET['text'];
	    if(isset($_GET['name']))
	    {
	    	$_SESSION['name'] = $_GET['name'];
	    }
	    else
	    {
	    	$_SESSION['name'] = 'Anonim';
	    }
	    $fp = fopen("logs/log.txt", 'a');
	    fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>\n");
	    fclose($fp);
	}
	//if(isset($_SESSION['text']))
	else
	{
		echo "Afisare";
		$fp = fopen("logs/log.txt", 'r');
		while (!feof($fp)) {
			$text = fread($fp, 100);
			echo $text;
		}
		fclose($fp);
	}
}
//}
?>
