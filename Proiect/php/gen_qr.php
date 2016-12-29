<pre><!-- <= FOR DEBUG -->
<?php require_once "qr_error_correction.php"; ?>
<?php require_once "qr_matrix_generator.php"; ?>
<?php
//Generator QR

/* Comenteaza linia asta pentru a vedea una din masti [0-7]
$dbg_mask_nr = 0;
$dbg_matrix = qr_matrix_gen_empty(4);
$dbg_data_white = "0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
$dbg_data_dark = "1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111";

while(strlen($dbg_data_white) < 807)
{
	$dbg_data_white = $dbg_data_white . "0";
}

while(strlen($dbg_data_dark) < 807)
{
	$dbg_data_dark = $dbg_data_dark . "1";
}

qr_matrix_place_data($dbg_matrix, $dbg_data_white);
var_dump(ascii_print($dbg_matrix));
var_dump(ascii_print(qr_matrix_apply_mask($dbg_matrix, $dbg_mask_nr)));
//*///!DEBUG




if($_SERVER["REQUEST_METHOD"] == "GET")
{
	if(isset($_GET["text"]))
	{
		$script_time = microtime(true);
		generate_qr($_GET["text"]);
		$script_time = microtime(true) - $script_time;
		echo "Execution time: {$script_time}[s]<br>";
	}
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
	unset($encoded_data);
		//var_dump($qr_code_blocks);//afisare blocuri
	$qr_ec_blocks = qr_gen_ec_blocks($qr_code_blocks, $qr_version, $qr_error_correction_level);
	//var_dump($qr_ec_blocks);
	// 4 - Interclasare date
	$qr_data = qr_interleave_data($qr_code_blocks, $qr_ec_blocks, $qr_version);
	//var_dump($qr_data);
	//Free memory
	unset($qr_code_blocks);
	unset($qr_ec_blocks);

	// 5 - Plasare in matrice
	
	$qr_matrix = qr_matrix_gen_empty($qr_version);
	qr_matrix_place_data($qr_matrix, $qr_data);
	$qr_data_mask = qr_matrix_mask_data($qr_matrix);

	// 6 - Adaugare informatii format
	//function qr_format_apply(& $qr_matrix, $qr_error_correction_level, $qr_data_mask)
	qr_format_apply($qr_matrix, $qr_error_correction_level, $qr_data_mask);
	var_dump(ascii_print($qr_matrix));
	$qr_matrix = qr_matrix_add_quiet_zone($qr_matrix);
	var_dump(ascii_print2($qr_matrix));
	qr_write_image($qr_matrix, "img.png");
	$dbg_size = count($qr_matrix) * 4;
	echo "<br><img src=img.png><br>";
	//function qr_write_image(& $qr_matrix, $img_name, $img_module_size = 4)
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
	for ($i=0, $lines = count($matrix); $i<$lines; $i++)	{
		for ($j=0, $colums = count($matrix[$i]); $j<$colums; $j++) {
			/*
			if(($matrix[$i][$j] & QRM_RESERVED) != 0)
				if ($matrix[$i][$j] % 2 == 1) {
					//$output .= "%";
					$output .= "";
				}
				else 
				{
					//$output .= "`";
					$output .= " ";
				}
			else
			{
				if ($matrix[$i][$j] % 2 == 1) {
					$output .= "#";
				}
				else 
				{
					$output .= ".";
				}
			}
			if($j<$lines-1) {
				$output .= " ";
			}
			//*/
			$output .= ($matrix[$i][$j] % 2) . " ";
		}
		$output .= "\n";
	}
	$output .= "\n</pre>";
	return $output;
}

function ascii_print2($qr_matrix)
{
	$output = "";
	$ap_code = array(0 => " ", 1 => "▄", 2 => "▀", 3 => "█");
	$qr_matrix_size = count($qr_matrix);
	for($i = 0 ; $i < $qr_matrix_size - 1; $i = $i + 2)
	{
		for($j = 0; $j < $qr_matrix_size; $j++)
		{
			$ap_val = 2 * ($qr_matrix[$i][$j] % 2) + ($qr_matrix[$i + 1][$j] % 2);
			$output = $output . $ap_code[$ap_val];
		}
		$output = $output . "<br>";
	}

	for($j = 0; $j < $qr_matrix_size; $j++)
	{
		$ap_val = 2 * ($qr_matrix[$qr_matrix_size - 1][$j] % 2);
		$output = $output . $ap_code[$ap_val];
	}
	return $output;
}

function qr_write_image(& $qr_matrix, $img_name, $img_module_size = 8)
{
	$qr_matrix_size = count($qr_matrix);
	echo "Img size: $qr_matrix_size * $img_module_size<br>";
	$qwi_img = imagecreatetruecolor($qr_matrix_size * $img_module_size, $qr_matrix_size * $img_module_size);
	$qwi_white = imagecolorallocate( $qwi_img, 255, 255, 255 );//White #FFF
	$qwi_black = imagecolorallocate( $qwi_img, 000, 000, 000 );//Black #000
	for($i = 0; $i < $qr_matrix_size; $i++)
	{
		for($j = 0; $j < $qr_matrix_size; $j++)
		{
			//$qwi_color = ($qr_matrix % 2 == 0) ? $qwi_white : $qwi_black;
			$qwi_color = ($qr_matrix[$i][$j] % 2 == 0) ? $qwi_white : $qwi_black;
			imagefilledrectangle($qwi_img, $j * $img_module_size, $i * $img_module_size, $j * $img_module_size + $img_module_size, $i * $img_module_size + $img_module_size, $qwi_color);
		}
	}
	$img_file = fopen($img_name, "w");
	imagepng($qwi_img, $img_file);
	//fclose($img_file);
}

?>

</pre>