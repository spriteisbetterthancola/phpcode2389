<?php require_once "qr_error_correction.php"; ?>
<?php require_once "qr_matrix_generator.php"; ?>
<?php
//Generator QR
/** @file gen_qr.php Genereaza un cod QR in format .png
*/
if($_SERVER["REQUEST_METHOD"] == "GET")
{
	if(isset($_GET["text"]))
	{
		$script_time = microtime(true);
		//echo "Data to encode: {$_GET['text']}<br>";//DEBUG
		generate_qr($_GET["text"]);
		$script_time = microtime(true) - $script_time;
		//echo "Execution time: {$script_time}[s]<br>";
	}
}
?>
<?php
/*!
* @brief Genereaza un cod QR care va encoda textul primit ca parametru 
* @param $text textul de reprezentat sub forma de cod QR
*/
function generate_qr($text)
{
	//1 - Analiza datelor
		//Se stie ca vom crea un QR pentru un url deci datel vor fi alfanumerice
		//URL-ul va fi de forma http://ADRESA_SITE/pagina?nr=XXXXXX
		//Aceasta adresa are pana in 50 de caractere
			//Vom alege Vesiunea 4 cu Error Corection Quality
	
	$qr_version = 4;
	//$qr_mode = "alphanumeric";
	$qr_mode = "byte";
	$qr_error_correction_level = "Q";// Production
	
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
	//$qr_mode_indicator = "0010";//indicator binar pt modul alfanumeric
	$qr_mode_indicator = "0100";//indicator binar pentru modul byte
	//UPDATE: Ne trebuie modul byte deoarece adresele trebuie sa contina semnul "?" care NU este prezent in alfanumeric
	
	//Lungimea trebuie sa fie pe 9 biti
	//TODO Eroare daca lumgimea e mai mare decat capaciatea maxima
	$data_len = decbin(strlen($text));
	//while(strlen($data_len) < 9)//Pentru alfanumeric
	while(strlen($data_len) < 8) // Pentru byte
	{
		$data_len = "0" . $data_len;
	}
	
	//$text = strtoupper($text);//Nu e necesar in modul byte
	//$encoded_data = alfa_encode($text);
	$encoded_data = byte_encode($text);
	//TODO De parametrizat $bits_required!
	//$bits_required = 8 * 36;//36 - nr maxim de cuvinte de cod pentru Cod 4-H
	$bits_required = 8 * 48;  //48 - nr maxim de cuvinte de cod pentru Cod 4-Q
	//$bits_required = 8 * 9; // 9 - nr maxim de cuvinte de cod pentru 1-H

	$encoded_data = $qr_mode_indicator . $data_len . $encoded_data;
	$terminator = "";
	for($i = 0; $i < 4 && strlen($encoded_data . $terminator); $i++)
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
	
	//echo "Encoded Data:<br>";
	//var_dump($encoded_data);
	
	//3 - Calculare cod detectie de erori CRC
		//4-H desparte codul in o grupa cu 4 blocuri a cate 9 cuvinte de cod fiecare
		//Un cuvant de cod are 8 biti din mesajul codat
	$qr_code_blocks = qr_split_encoded_data($encoded_data, $qr_version, $qr_error_correction_level);
	$qr_ec_blocks = qr_gen_ec_blocks($qr_code_blocks, $qr_version, $qr_error_correction_level);
	unset($encoded_data);
	/*/DEBUG
	echo "Data blocks:<br>";
	for($b=1; $b<=4; $b++)
	{
		for($i=1; $i<=9; $i++)
		{
			echo bindec($qr_code_blocks[1][$b][$i]) . ",";
		}
		echo "<br>";
	}
	echo "EC blocks:<br>";
	for($b=1; $b<=4; $b++)
	{
		for($i=1; $i<=9; $i++)
		{
			echo $qr_ec_blocks[1][$b][$i] . " ";
		}
		echo "<br>";
	}
	// !DEBUG */

	// 4 - Interclasare date
	$qr_data = qr_interleave_data($qr_code_blocks, $qr_ec_blocks, $qr_version);
	//var_dump($qr_data);s
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
	//var_dump(ascii_print($qr_matrix));
	$qr_matrix = qr_matrix_add_quiet_zone($qr_matrix);
	//var_dump(ascii_print2($qr_matrix));
	//$dbg_size = count($qr_matrix) * 4;
	$img_module_size = 48;
	$img_size = count($qr_matrix) * $img_module_size;
	qr_write_image($qr_matrix, "img.png", $img_module_size);
	//echo '<br><img src="img.png" height="{$img_size}" width="{$img_size}"><br>';//Daca imaginea a fost salvata in fisier
	//function qr_write_image(& $qr_matrix, $img_name, $img_module_size = 4)
}
/*!
* @brief implementeaza operatia de impartire cu rest  intre 2 numere
* @param $a deimpartitul
* @param $b impartitorul
* @return catul impartirii celor 2 numere
*/

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

//https://kb.iu.edu/d/aepu ISO8859-I Char set
function byte_encode($text)
{
	$qr_encode_byte_table = array( " " => "00100000",
		"!" => "00100001", '"' => "00100010", '#' => "00100011", "$" => "00100100", "%" => "00100101", "&" => "00100110", 
		"'" => "00100111", "(" => "00101000", ")" => "00101001", "*" => "00101010", "+" => "00101011", "," => "00101100", 
		"-" => "00101101", "." => "00101110", "/" => "00101111", "0" => "00110000", "1" => "00110001", "2" => "00110010", 
		"3" => "00110011", "4" => "00110100", "5" => "00110101", "6" => "00110110", "7" => "00110111", "8" => "00111000", 
		"9" => "00111001", ":" => "00111010", ";" => "00111011", "<" => "00111100", "=" => "00111101", ">" => "00111110", 
		"?" => "00111111", "@" => "01000000", "A" => "01000001", "B" => "01000010", "C" => "01000011", "D" => "01000100", 
		"E" => "01000101", "F" => "01000110", "G" => "01000111", "H" => "01001000", "I" => "01001001", "J" => "01001010", 
		"K" => "01001011", "L" => "01001100", "M" => "01001101", "N" => "01001110", "O" => "01001111", "P" => "01010000", 
		"Q" => "01010001", "R" => "01010010", "S" => "01010011", "T" => "01010100", "U" => "01010101", "V" => "01010110", 
		"W" => "01010111", "X" => "01011000", "Y" => "01011001", "Z" => "01011010", "[" => "01011011", "\\" => "01011100", 
		"]" => "01011101", "^" => "01011110", "_" => "01011111", "`" => "01100000", "a" => "01100001", "b" => "01100010", 
		"c" => "01100011", "d" => "01100100", "e" => "01100101", "f" => "01100110", "g" => "01100111", "h" => "01101000", 
		"i" => "01101001", "j" => "01101010", "k" => "01101011", "l" => "01101100", "m" => "01101101", "n" => "01101110", 
		"o" => "01101111", "p" => "01110000", "q" => "01110001", "r" => "01110010", "s" => "01110011", "t" => "01110100", 
		"u" => "01110101", "v" => "01110110", "w" => "01110111", "x" => "01111000", "y" => "01111001", "z" => "01111010", 
		"{" => "01111011", "|" => "01111100", "}" => "01111101", "~" => "01111110");
	//Incomplet. Acestea nu sunt toate caracterele dar sunt toate pe care le vom folosi
	$output = "";
	$be_text_size = strlen($text);
	for($i = 0; $i<$be_text_size; $i++)
	{
		$output = $output . $qr_encode_byte_table[$text[$i]];
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
					$output .= "%";
				}
				else 
				{
					$output .= "`";
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
	$output = "<br>";
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
	//echo "Img size: $qr_matrix_size * $img_module_size<br>";
	$qwi_img = imagecreatetruecolor($qr_matrix_size * $img_module_size, $qr_matrix_size * $img_module_size);
	$qwi_white = imagecolorallocate( $qwi_img, 255, 255, 255 );//White #FFF
	$qwi_black = imagecolorallocate( $qwi_img, 000, 000, 000 );//Black #000
	for($i = 0; $i < $qr_matrix_size; $i++)
	{
		for($j = 0; $j < $qr_matrix_size; $j++)
		{
			$qwi_color = (($qr_matrix[$i][$j] % 2) == 0) ? $qwi_white : $qwi_black;
			imagefilledrectangle($qwi_img, $j * $img_module_size, $i * $img_module_size, $j * $img_module_size + $img_module_size, $i * $img_module_size + $img_module_size, $qwi_color);
		}
	}
	/*/Write the img to a file
	$img_file = fopen($img_name, "w");
	imagepng($qwi_img, $img_file);
	// !Write to file */
	header("Content-type: image/png");
	imagepng($qwi_img);
	//TODO Dealocare resurse!!
}
?>