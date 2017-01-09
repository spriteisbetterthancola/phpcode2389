<!DOCTYPE html>
<?php
require_once("conversation.php");
$conv_uid = "";
if(isset($_POST["pswd"]) && isset($_POST["conversationName"]) && isset($_POST["adminName"]))
	{
		$pswd = $_POST["pswd"];
		$convName = $_POST["conversationName"];
		$adminName = $_POST["adminName"];
		$conv_uid = conv_create($pswd, $convName, $adminName);
	}
?>

<body>
	<h1>Conversatia a fost creata cu succes!</h1>
	<h2>ID-ul conversatie este <b><?php echo $conv_uid ?></b></h2>
	Cod conversatie:
	<br/>
	<img src="gen_qr.php?text=http://<?php echo $_SERVER["HTTP_HOST"] ?>/index.php?id=<?php echo $conv_uid ?>">
	<br/>
	<h3>Imagine disponibila la alte dimensiuni:</h3>
	<?php
	for($i=1; $i<80; $i=$i+3)
	{
		$host = $_SERVER["HTTP_HOST"];
		echo '<a href="gen_qr.php?text=http://' . $host . '/index.php?id=' . $conv_uid . '&module_size=' . $i . '">';
		echo 33*$i ;
		echo "x" ;
		echo 33*$i; 
		echo "</a><br/>";
	}
	?>
	<!-- <a href="gen_qr.php?text=http://<?php echo $_SERVER["HTTP_HOST"] ?>/index.php?id=<?php echo $conv_uid ?>&module_size=1">33x33</a><br/> -->
</body>