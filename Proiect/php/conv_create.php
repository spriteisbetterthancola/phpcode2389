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
	//TODO de modificat IP-ul 
	$host_name = "192.168.43.249";//In productie  $_SERVER["SERVER_ADDR"] returneaza adresa sub forma www.host.com
?>

<body>
	<h1>Conversatia a fost creata cu succes!</h1>
	<h2>ID-ul conversatie este <b><?php echo $conv_uid ?></b></h2>
	Cod conversatie:
	<br/>
	<img src="gen_qr.php?text=http://<?php echo $host_name ?>/index.php?id=<?php echo $conv_uid ?>">
	<br/>
	<button >
	<h3>Imagine disponibila la alte dimensiuni:</h3>
	<?php
	for($i=1; $i<80; $i=$i+3)
	{
		echo '<a href="gen_qr.php?text=http://' . $host_name . '/index.php?id=' . $conv_uid . '&module_size=' . $i . '">';
		echo 33*$i ;
		echo "x" ;
		echo 33*$i; 
		echo "</a><br/>";
	}
	?>
	<!-- <a href="gen_qr.php?text=http://<?php echo $_SERVER["HTTP_HOST"] ?>/index.php?id=<?php echo $conv_uid ?>&module_size=1">33x33</a><br/> -->
</body>