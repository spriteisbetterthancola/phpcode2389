<pre>
<?php require_once "qr_error_correction.php"; ?>
<?php
//Generator QR
if($_SERVER["REQUEST_METHOD"] == "GET")
{
	$lines = 3;
	$colums = 3;
	$M = array_fill(0, $lines, array_fill(0, $colums, 1));
	$M[1][1] = 0;
	//echo ascii_print($M);
	//$var = ascii_print($M);
	//echo qr_log(0);
	if(isset($_GET["text"]))
	{
		generate_qr($_GET["text"]);
	}
	///* DEBUG STUFFS
	/*

	$p7 = array(qr_pow(0), 1);//1
	$p7 = multiply_polynoms($p7, array(qr_pow(1), 1));//2
	$p7 = multiply_polynoms($p7, array(qr_pow(2), 1));//3
	$p7 = multiply_polynoms($p7, array(qr_pow(3), 1));//4
	print_polin($p7);
	a1x4 + a15x3 + a54x2 + a120x1 + a64
	$p7 = multiply_polynoms($p7, array(qr_pow(4), 1));//5
	$p7 = multiply_polynoms($p7, array(qr_pow(5), 1));//6
	$p7 = multiply_polynoms($p7, array(qr_pow(6), 1));//7
	$p7 = multiply_polynoms($p7, array(qr_pow(7), 1));//8
	$p7 = multiply_polynoms($p7, array(qr_pow(8), 1));//9
	$alpha = qr_alpha_pol($p7);
	print_polin($alpha);
	//print_polin(qr_alpha_pol(multiply_polynoms($p7, array(0,1))));
	
	//*/
}
?>
<?php

function generate_qr($text)
{
	//1 - Analiza datelor
		//Se stie ca vom crea un QR pentru un url deci datel vor fi alfanumerice
		//URL-ul va fi de forma http://ADRESA_SITE/pagina?nr=XXXXXX
		//Aceasta adresa are pana in 50 de caractere
			//Vom alege Vesiunea 4 cu Error Corection Quality
	$qr_version = 4;
	$qr_mode = "alphanumeric";
	$qr_error_correction_level = "H";
	$qr_caracter_capacity_table = array(1 => array('L' => array('numeric' => 41, 'alphanumeric' => 25, 'byte' => 17),
												   'M' => array('numeric' => 34, 'alphanumeric' => 20, 'byte' => 14),
												   'Q' => array('numeric' => 27, 'alphanumeric' => 16, 'byte' => 11),
												   'H' => array('numeric' => 17, 'alphanumeric' => 10, 'byte' =>  7)),

										2 => array ('L' => array('numeric' => 77, 'alphanumeric' => 47, 'byte' => 32),
													'M' => array('numeric' => 63, 'alphanumeric' => 38, 'byte' => 26),
													'Q' => array('numeric' => 48, 'alphanumeric' => 29, 'byte' => 20),
													'H' => array('numeric' => 34, 'alphanumeric' => 20, 'byte' => 14)),

										3 => array ('L' => array('numeric' =>127, 'alphanumeric' => 77, 'byte' => 53),
													'M' => array('numeric' =>101, 'alphanumeric' => 61, 'byte' => 42),
													'Q' => array('numeric' => 77, 'alphanumeric' => 47, 'byte' => 32),
													'H' => array('numeric' => 58, 'alphanumeric' => 35, 'byte' => 24)),

										4 => array ('L' => array('numeric' =>187, 'alphanumeric' =>114, 'byte' => 78),
													'M' => array('numeric' =>149, 'alphanumeric' => 90, 'byte' => 62),
													'Q' => array('numeric' =>111, 'alphanumeric' => 67, 'byte' => 46),
													'H' => array('numeric' => 82, 'alphanumeric' => 50, 'byte' => 34))
										);

	//2 - Codarea datelor (encoding)
	$qr_mode_indicator = "0010";//indicator binar pt modul alfanumeric
	
	//Lungimea trebuie sa fie pe 9 biti
	$data_len = decbin(strlen($text));
	while(strlen($data_len) < 9)
	{
		$data_len = "0" . $data_len;
	}
	
	$text = strtoupper($text);
	$encoded_data = alfa_encode($text);
	$bits_required = 8 * 36;//36 - nr maxim de cuvinte de cod pentru Cod 4-H

	$encoded_data = $qr_mode_indicator . $data_len . $encoded_data;
	$terminator = "";
	for($i =0; $i < 4 && strlen($encoded_data . $terminator); $i++)
	{
		$terminator .= "0";
	}
	$encoded_data = $encoded_data . $terminator;
	//Facem $encoded_data sa aiba dimensiunea un multiplu de 8
	while (strlen($encoded_data) % 8 != 0) {
		$encoded_data = $encoded_data . "0";
	}

	if(strlen($encoded_data) < $bits_required)
	{
		$pad_grups = intdiv_1(($bits_required - strlen($encoded_data)), 8);
		//var_dump($pad_grups);
		$pad_strings = array(0 => "11101100", 1 => "00010001");
		for($i = 0; $i < $pad_grups; $i++)
		{
			$encoded_data = $encoded_data . $pad_strings[$i % 2];
		}
	}
	
	//var_dump($encoded_data);
	
	//3 - Calculare cod detectie de erori CRC
		//4-H desparte codul in o grupa cu 4 blocuri a cate 9 cuvinte de cod fiecare
		//Un cuvant de cod are 8 biti din mesajul codat
	$qr_code_blocks = qr_split_encoded_data($encoded_data, $qr_version, $qr_error_correction_level);
		//var_dump($qr_code_blocks);//afisare blocuri
	$qr_ec_blocks = qr_gen_ec_blocks($qr_code_blocks, $qr_version, $qr_error_correction_level);
}

function intdiv_1($a, $b){
    return ($a - $a % $b) / $b;
}

function to_binary_string($value)//Nu e folosita!
{
	return decbin($value);
}

function alfa_encode($text)
{
	$qr_alphanum_table = array(
			"0" =>  0, "1" =>  1, "2" =>  2, "3" =>  3, "4" =>  4, "5" =>  5, "6" =>  6, "7" =>  7, "8" => 8, 
			"9" =>  9, "A" => 10, "B" => 11, "C" => 12, "D" => 13, "E" => 14, "F" => 15, "G" => 16, "H" => 17, 
			"I" => 18, "J" => 19, "K" => 20, "L" => 21, "M" => 22, "N" => 23, "O" => 24, "P" => 25, "Q" => 26, 
			"R" => 27, "S" => 28, "T" => 29, "U" => 30, "V" => 31, "W" => 32, "X" => 33, "Y" => 34, "Z" => 35, 
			" " => 36, "$" => 37, "%" => 38, "*" => 39, "+" => 40, "-" => 41, "." => 42, "/" => 43, ":" => 44);
	$output = "";
	for ($i=0, $tl = strlen($text); $i < $tl; $i = $i + 2) { 
		$block = $qr_alphanum_table[$text[$i]];
		if ($i+1 < $tl)
		{
			$block = $block * 45 + $qr_alphanum_table[$text[$i+1]];
			$block = decbin($block);
			while (strlen($block) < 11)
			{
				$block = "0" . $block;
			}
		}
		else {
			$block = decbin($block);
			while (strlen($block) < 6)
			{
				$block = "0" . $block;
			}
		}
		$output = $output . $block;
	}
	return $output;
}

function ascii_print($matrix)
{
	$output = "<pre>";
	for ($i=0, $lines = sizeof($matrix); $i<$lines; $i++)	{
		for ($j=0, $colums = sizeof($matrix[$i]); $j<$colums; $j++) {
			if ($matrix[$i][$j]==1) {
				$output .= "#";
			}
			else 
			{
				$output .= " ";
			}
			if($j<$lines-1) {
				$output .= " ";
			}
		}
		$output .= "\n";
	}
	$output .= "\n</pre>";
	return $output;
}


?>

</pre>