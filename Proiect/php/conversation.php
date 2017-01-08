<?php

var_dump(conv_create());

function conv_gen_uid()
{
	$uid = substr(hash("sha512", rand()), 50, 6);
	$hexVal = array(0 => 'c', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'd', 'e', 'f');
	$aux = $uid . $hexVal[0];
		//if(array_search($aux, $outputs)) {//If value already exist
	for($i = 1; conv_exist($aux) == true && $i < 15; $i++)
	{
		$aux = $uid . $hexVal[$i];
	}

	if(conv_exist($aux))
	{
		return "";
	}
	
	return $aux;
}

function conv_exist($uid)
{
	$dirExist = is_dir("logs/conv_" . $uid);
	return $dirExist;
}

function conv_create($admin_pass)
{
	$cc_uid = conv_gen_uid();
	if($cc_uid == "")
	{
		$cc_uid = conv_gen_uid();
		if($cc_uid == "")
		{
			echo "Conversatia nu a putut fi creata!<br>Va rugam contactati suportul tehnic!";
			return;
		}
	}
	mkdir("logs/conv_" . $cc_uid);
	//TODO Creare fisier config si fisier log.txt
	if(isset($_SERVER['REQUEST_TIME']))
	{
		$timestamp = $_SERVER['REQUEST_TIME'];
	}
	$timestamp = time();
	
}
?>