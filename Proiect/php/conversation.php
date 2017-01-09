<?php
/** @file conversation.php 
@brief Gestioneaza crearea de noi conversatii

*/

//var_dump(conv_update_config("80bf99c", "parola", time()));
//var_dump(conv_exist("80bf99c"));
?>
<?php

/*!
 * @brief genereaza un ID nou pentru conversatie
 * ID-ul este aleator
 * @return un sir de caractere reprezentand noul ID(caractere hexazecimale)
*/
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

/*!
* @brief Verifica daca conversatia cu ID-ul $uid exista
* @param $uid ID-ul conversatiei
*/
function conv_exist($uid)
{
	$dirExist = is_dir("logs/conv_" . $uid);
	return $dirExist;
}
/*!
* @brief Creeaza o noua conversatie cu parametrii dati 
* @param $admin_pass parola administratorului
*/
function conv_create($admin_pass, $conv_title ,$adminName)
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

	$cc_file_base = "logs/conv_" . $cc_uid;
	mkdir($cc_file_base);
	//TODO Creare fisier config si fisier log.txt
	$timestamp = 0;
	if(isset($_SERVER['REQUEST_TIME']))
	{
		$timestamp = $_SERVER['REQUEST_TIME'];
	}
	else {
		$timestamp = time();
	}
	$admin_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
	//Verificare cu password_verify('parola_text', $hash);
	$cc_endl = "\r\n";
	$cc_file_conf = fopen($cc_file_base . "/config.xml", "w");
	$cc_xml_conf = '<?xml version="1.0" encoding="UTF-8"?>' . $cc_endl;
	$cc_xml_conf = $cc_xml_conf . '<root>' . $cc_endl;
	$cc_xml_conf = $cc_xml_conf . '<creation_date>' . $timestamp . '</creation_date>' . $cc_endl;
	$cc_xml_conf = $cc_xml_conf . '<admin_pass>' . $admin_pass . '</admin_pass>' . $cc_endl;
	$cc_xml_conf = $cc_xml_conf . '<admin_name>' . $adminName . '</admin_name>' . $cc_endl;
	$cc_xml_conf = $cc_xml_conf . '<title>' . $conv_title . '</title>' . $cc_endl;
	$cc_xml_conf = $cc_xml_conf . '</root>' . $cc_endl;

	fwrite($cc_file_conf, $cc_xml_conf);
	fclose($cc_file_conf);

	$cc_file_log = fopen($cc_file_base . "/log.xml", "w");
	$cc_xml_log = '<?xml version="1.0" encoding="UTF-8"?>' . $cc_endl;
	$cc_xml_log .= "<conversation></conversation>" . $cc_endl;
	fwrite($cc_file_log, $cc_xml_log);
	fclose($cc_file_log);
	return $cc_uid;
}


/*     Nu e nevoie de asta!
* @brief updateaza fisierul de configurare al conversatiei 
* Fisierul config.xml din directorul conversatie este updatat 
* astfel incat sa contina noile valori de configuratie date ca 
* parametri
* @param $uid ID-ul conversatiei
* @param $admin_pass parola administratorului
* @param $timestamp data de creare a conversatiei
function conv_update_config($uid, $admin_pass, $timestamp)
{
	$cu_file_base = "logs/conv_" . $uid;
	$admin_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
	$cu_endl = "\r\n";
	$cu_xml = simplexml_load_file($cu_file_base . '/config.xml');
	//var_dump($cu_xml);
	//$cu_xml->{'creation_date'};
	$cu_xml->creation_date[0] = $timestamp;
	$cu_xml->admin_pass[0] = $admin_pass;
	//echo $cu_xml->creation_date[0];
	//var_dump($cu_xml);
	//echo "<br>-----------";
	//$cu_xml->addChild("new_child", "nc_value");//Adauga nodul new_child cu valoarea nc_value
	$cu_xml->asXML($cu_file_base . '/config.xml');

}
*/
function get_admin_name($conv_uid)
{
	$cu_xml = simplexml_load_file("log/conv_$conv_uid ". '/config.xml');
	return ($cu_xml->admin_name[0]);
}
?>

