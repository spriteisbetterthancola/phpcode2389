<?php 
//TODO 0. Crush that bug
//TODO 1. Finish the code!!!
//TODO 1. Redenumire functii si parametri pentru simularea unui namespace.

// functia intoarce valoarea log2(a) pentru campuri Galois 256
/* Generare tabele log - antilog
 echo "<pre>";
 for($i = 0; $i < 256; $i++)
 {
 	$c = 0;
	$j = 1;
	while($c < $i)
	{
		$j = ($j * 2);
		$c++;
		if($j >= 256)
		{
			$j = $j ^ 285;
		}
	}
	if($j < 10)
	{
		echo "&nbsp;&nbsp;";
	}
	else if($j<100)
	{
		echo "&nbsp;";
	}
	echo $j;
	echo ", ";
	if(($c+1) % 10 == 0)
	{
		echo "<br>";
	}
 }

 $v_pow = array(
	1,   2,   4,   8,  16,  32,  64, 128,  29,  58, 116, 232, 205, 135,  19,  38,  76, 152,  45,  90, 
	180, 117, 234, 201, 143,   3,   6,  12,  24,  48, 96, 192, 157,  39,  78, 156,  37,  74, 148,  53, 
	106, 212, 181, 119, 238, 193, 159,  35,  70, 140,  5,  10,  20,  40,  80, 160,  93, 186, 105, 210, 
	185, 111, 222, 161,  95, 190,  97, 194, 153,  47, 94, 188, 101, 202, 137,  15,  30,  60, 120, 240, 
	253, 231, 211, 187, 107, 214, 177, 127, 254, 225, 223, 163,  91, 182, 113, 226, 217, 175,  67, 134, 
	 17,  34,  68, 136,  13,  26,  52, 104, 208, 189, 103, 206, 129,  31,  62, 124, 248, 237, 199, 147, 
	 59, 118, 236, 197, 151,  51, 102, 204, 133,  23,  46,  92, 184, 109, 218, 169,  79, 158,  33,  66, 
	132,  21,  42,  84, 168,  77, 154,  41,  82, 164,  85, 170,  73, 146,  57, 114, 228, 213, 183, 115, 
	230, 209, 191,  99, 198, 145,  63, 126, 252, 229, 215, 179, 123, 246, 241, 255, 227, 219, 171,  75, 
	150,  49,  98, 196, 149,  55, 110, 220, 165,  87, 174,  65, 130,  25,  50, 100, 200, 141,   7,  14, 
	 28,  56, 112, 224, 221, 167,  83, 166,  81, 162,  89, 178, 121, 242, 249, 239, 195, 155,  43,  86, 
	172,  69, 138,   9,  18,  36,  72, 144,  61, 122, 244, 245, 247, 243, 251, 235, 203, 139,  11,  22, 
	 44,  88, 176, 125, 250, 233, 207, 131,  27,  54, 108, 216, 173,  71, 142,   1);
 $v_log = array(1 => 0);
 for($i = 2; $i < 256; $i++)
 {
	$v_log[$i] = array_search($i, $v_pow);
 }
 echo "<br>------<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 //print_r($v_log); 
 for($i = 1; $i < 256; $i++)
 {
	if($i%10 == 0)
	{
		echo "<br>";
	}
	if($v_log[$i] < 10)
	{
		echo "&nbsp;&nbsp;";
	}else if($v_log[$i] < 100)
	{
		echo "&nbsp;";
	}
	echo $v_log[$i];
	echo ", ";
 }
 
 echo "</pre>";
 END OF COMMENT 
*/


function qr_split_encoded_data($encoded_data, $qr_version, $qr_error_correction_level)
{
	//http://www.thonky.com/qr-code-tutorial/error-correction-table

	//cwpb - error corection code Words per block
	// g1b - blocks in grup 1 | wpg1b - words per block in g1
	// g2b - blocks in grup 2 | wpg2b - words per block in g2
	// pp ca folosim EC_level H si versiunea 3 sau 4
	$qr_spec_codewords = array (
		3 => array (
			'H' => array ("cwpb" => 26, 'g1b' => 2, 'wpg1b' => 13, 'g2b' => 0, 'wpg2b' => 0)),
		4 => array (
			'H' => array ("cwpb" => 16, 'g1b' => 4, 'wpg1b' => 9, 'g2b' => 0, 'wpg2b' => 0))
		);
	$qr_data = array (1 => array(), 2 => array());//Data este structurata in 2 grupuri: 1 si 2
	$octeti_per_grup1  = $qr_spec_codewords[$qr_version][$qr_error_correction_level]['wpg1b'];
	$blocuri_per_grup1 = $qr_spec_codewords[$qr_version][$qr_error_correction_level]['g1b'];

	$octeti_per_grup2  = $qr_spec_codewords[$qr_version][$qr_error_correction_level]['wpg2b'];
	$blocuri_per_grup2 = $qr_spec_codewords[$qr_version][$qr_error_correction_level]['g2b'];
	
	//adaugare cuvinte in grupul 1
	$cc = 0;
	for ($i = 0; $i < $blocuri_per_grup1; $i++)
	{
		$qr_data[1][$i+1] = array();//adauga un bloc nou
		for($j = 0; $j < $octeti_per_grup1; $j++)
		{
			$qr_data[1][$i+1][$j+1] = substr($encoded_data, $cc, 8);
			$cc = $cc + 8;
		}
	}

	//adaugare cuvinte in grupul 2

	for ($i = 0; $i < $blocuri_per_grup2; $i++)
	{
		$qr_data[2][$i+1] = array();//adauga un bloc nou
		for($j = 0; $j < $octeti_per_grup2; $j++)
		{
			$qr_data[2][$i+1][$j+1] = substr($encoded_data, $cc, 8);
			$cc = $cc + 8;
		}
	}

	return $qr_data;
}

/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * *  FUNCTII CORECTIE ERORI   * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

function qr_pow($a)
{
	$v_pow = array(
	  1,   2,   4,   8,  16,  32,  64, 128,  29,  58, 116, 232, 205, 135,  19,  38,  76, 152,  45,  90, 
	180, 117, 234, 201, 143,   3,   6,  12,  24,  48,  96, 192, 157,  39,  78, 156,  37,  74, 148,  53, 
	106, 212, 181, 119, 238, 193, 159,  35,  70, 140,   5,  10,  20,  40,  80, 160,  93, 186, 105, 210, 
	185, 111, 222, 161,  95, 190,  97, 194, 153,  47,  94, 188, 101, 202, 137,  15,  30,  60, 120, 240, 
	253, 231, 211, 187, 107, 214, 177, 127, 254, 225, 223, 163,  91, 182, 113, 226, 217, 175,  67, 134, 
	 17,  34,  68, 136,  13,  26,  52, 104, 208, 189, 103, 206, 129,  31,  62, 124, 248, 237, 199, 147, 
	 59, 118, 236, 197, 151,  51, 102, 204, 133,  23,  46,  92, 184, 109, 218, 169,  79, 158,  33,  66, 
	132,  21,  42,  84, 168,  77, 154,  41,  82, 164,  85, 170,  73, 146,  57, 114, 228, 213, 183, 115, 
	230, 209, 191,  99, 198, 145,  63, 126, 252, 229, 215, 179, 123, 246, 241, 255, 227, 219, 171,  75, 
	150,  49,  98, 196, 149,  55, 110, 220, 165,  87, 174,  65, 130,  25,  50, 100, 200, 141,   7,  14, 
	 28,  56, 112, 224, 221, 167,  83, 166,  81, 162,  89, 178, 121, 242, 249, 239, 195, 155,  43,  86, 
	172,  69, 138,   9,  18,  36,  72, 144,  61, 122, 244, 245, 247, 243, 251, 235, 203, 139,  11,  22, 
	 44,  88, 176, 125, 250, 233, 207, 131,  27,  54, 108, 216, 173,  71, 142,   1);
	return $v_pow[$a];
}

function qr_log($a)
{
	$v_log = array(
 1 => 0,   1,  25,   2,  50,  26, 198,   3, 223,  51, 238,  27, 104, 199,  75,   4, 100, 224,  14,  52, 
    141, 239, 129,  28, 193, 105, 248, 200,   8,  76, 113,   5, 138, 101,  47, 225,  36,  15,  33,  53, 
    147, 142, 218, 240,  18, 130,  69,  29, 181, 194, 125, 106,  39, 249, 185, 201, 154,   9, 120,  77, 
    228, 114, 166,   6, 191, 139,  98, 102, 221,  48, 253, 226, 152,  37, 179,  16, 145,  34, 136,  54, 
    208, 148, 206, 143, 150, 219, 189, 241, 210,  19,  92, 131,  56,  70,  64,  30,  66, 182, 163, 195, 
     72, 126, 110, 107,  58,  40,  84, 250, 133, 186,  61, 202,  94, 155, 159,  10,  21, 121,  43, 	78, 
    212, 229, 172, 115, 243, 167,  87,   7, 112, 192, 247, 140, 128,  99,  13, 103,  74, 222, 237,  49, 
    197, 254,  24, 227, 165, 153, 119,  38, 184, 180, 124,  17,  68, 146, 217,  35,  32, 137,  46,  55, 
     63, 209,  91, 149, 188, 207, 205, 144, 135, 151, 178, 220, 252, 190,  97, 242,  86, 211, 171,  20, 
     42,  93, 158, 132,  60,  57,  83,  71, 109,  65, 162,  31,  45,  67, 216, 183, 123, 164, 118, 196, 
     23,  73, 236, 127,  12, 111, 246, 108, 161,  59,  82,  41, 157,  85, 170, 251,  96, 134, 177, 187, 
    204,  62,  90, 203,  89,  95, 176, 156, 169, 160,  81,  11, 245,  22, 235, 122, 117,  44, 215,  79, 
    174, 213, 233, 230, 231, 173, 232, 116, 214, 244, 234, 168,  80,  88, 175);
	return $v_log[$a];
}

function  qr_alpha_pol($polin)
{
	$n = count($polin);
	for($i = 0 ; $i<$n; $i++)
	{
		if($polin[$i] != 0)
			$polin[$i] = qr_log($polin[$i]);
		else
			unset($polin[$i]);
	}
	//var_dump($polin);
	//echo("<br>To Log: ");
	return $polin;
}

//afiseaza polinomul dat in notatia alfa
function print_polin($polin, $pp_al_mode = true)
{
	$n = max(array_keys($polin));
	for($i = $n ; $i > 0; $i--)
	{
		if (isset($polin[$i]))
		{
			if($polin[$i] != 0)
			{
				if($pp_al_mode)
				{
					printf("a<sup>%d</sup>x<sup>%d</sup>", $polin[$i], $i);
				}
				else
				{
					echo "{$polin[$i]}x<sup>{$i}</sup>";
				}
			}
			else
				printf("x<sup>%d</sup>", $i);
			if($i > 0 && isset($polin[$i-1]))
			{
				echo " + ";
			}
		}
	}

	//afiseaza ultimul termen
	if(isset($polin[0]))
	{
		if($pp_al_mode)
		{
			printf("a<sup>%d</sup>", $polin[$i]);
		}
		else
		{
			echo $polin[$i];
		}
	}
	echo "<br>";
}

//inmulteste polinomul p cu polinomul q
function multiply_polynoms($p, $q)
{
	$grad_p = count($p) - 1;
	$grad_q = count($q) - 1;
	$grad_r = $grad_p + $grad_q;
	$r = array_fill(0, $grad_r + 1, 0);
	//print_polin(qr_alpha_pol($p));
	//echo " * ";
	//print_polin(qr_alpha_pol($q));
	//echo " = ";
	for($i = 0; $i < $grad_p + 1; $i++)
	{
		for($j = 0; $j < $grad_q + 1; $j++)
		{
			if($p[$i] != 0 && $q[$j] != 0)
			{
				$v = qr_log($p[$i]) + qr_log($q[$j]);
				if ($v > 255)
				{
					$v = $v % 255;
				}
				//printf("qr_log(%d) = %d | qr_log(%d) = %d<br>", $p[$i], qr_log($p[$i]) ,$q[$j], qr_log($q[$j]));
				//printf("qr_pow(%d) = %d<br>", $v, qr_pow($v));
				$v = qr_pow($v);
			}
			else
			{
				$v = 0;
			}
			//*/
			//Adunarea se efectueaza cu un XOR
			$r[$i+$j] = $v ^ $r[$i+$j];
			//printf("r[%d+%d] += %d <br>", $i,$j, $v);
		}
	}
	//print_polin(qr_alpha_pol($r));
	//echo "<br>";
	return $r;
}



//$m - Polinom mesaj
//$g - Polinom de generare
function divide_poly_step($m, $g)
{
	$dps_r = array();// $r = $p * $q[n]
	//Impartim p la q astfel:
		//Inmultim $g cu coeficientul dominant al lui $m
		//BUG GRESIT! InMultim $g cu TERMENUL dominant al lui $m
	$dps_grad_m = count($m) - 1;
	$dps_grad_g = count($g) - 1;
	//BUG trebuie sa inmultim cu a^m*x^n nu doar cu a^m!!!
	//ATENTIE LA GRADUL LUI $g. Acesta trebuie sa scada!
	$dps_lead_term = poly_gen_x_pow_n($dps_grad_m - $dps_grad_g);
	$g = multiply_polynoms($g, $dps_lead_term);
	$g = multiply_polynoms($g, array(0 => $m[$dps_grad_m]));
	//echo "<br>M: ";	print_polin($m, false);	echo "<br>G: ";	print_polin($g, false); //DEBUG
	// Facem XOR intre coeficientii lui $m si $g si punem rezultatul in $r
	for($i = 0; $i < $dps_grad_m; $i++)
	{
		$dps_r[$i] = $g[$i] ^ $m[$i];
	}
	unset($dps_r[$dps_grad_m]);//stergem primul coeficient deoarece este 0
	//echo "<br>R: ";	print_polin($dps_r, false); // DEBUG
	return $dps_r;
}



//$qr_ec_blocks = qr_gen_ec_blocks($qr_code_blocks, $qr_version, $qr_error_correction_level);

//Functia genereaza polinomul mesajului
function gen_poly_msg($gpm_poly_string)
{
	$gpm_poly_dec = array();
	$n = count($gpm_poly_string);
	//Cuvintele de cod incep de la 1 pana la n inclusiv
	//Cuvantul #1 va fi coeficientul lui x^(n-1) deci va fi pe pozitia n-1 in vectorul care incepede la 0
	//Cuvantul #i va fi coeficientul lui x^(n-i) deci va fi pe pozitia n-i a vectorului
	for ($i = 1; $i <= $n; $i++)
		$gpm_poly_dec[$n - $i] = bindec($gpm_poly_string[$i]);
	return $gpm_poly_dec;
}



function poly_gen_x_pow_n($n)
{
	//genereaza polinomul x^n
	$r = array_fill(0, $n, 0);
	$r[$n] = 1;
	return $r;
}

function qr_gen_ec_blocks($qr_code_blocks, $qr_version, $qr_error_correction_level)
{
	$qr_ec_blocks = array();
	$qr_ec_codewords = array (
		3 => array (
			'H' => 22),
		4 => array (
			'H' => 16)
		);
	$ec_per_block = $qr_ec_codewords[$qr_version][$qr_error_correction_level];

	// 1. Creeaza polinom generator
	$poly_gen = array(qr_pow(0), 1);//Grad 1
	for($i = 1; $i < $ec_per_block; $i++)
	{
		$poly_gen = multiply_polynoms($poly_gen, array(qr_pow($i), 1));
	}
	//Poly_Gen are gradul ec_per_block

	//var_dump($cw_per_block);
	//print_polin($poly_gen); // Afisare polinom de generare


	$nr_blocuri = array( 1 => count($qr_code_blocks[1]), 2 => count($qr_code_blocks[2]));
	$ec_data = array(1 => array(), 2 => array());//Codurile de corectie vor fi impartite tot in 2 grupe
	//alocare memorie pentru EC - Nu sunt 100% sigura ca este necesar
	//$ec_data[1] = array_fill(1, $nr_blocuri[1], array());
	//$ec_data[2] = array_fill(1, $nr_blocuri[2], array());
	//var_dump($nr_blocuri);
	//var_dump($qr_code_blocks);
	$qr_code_dec = array(1 => array_fill(1, 4, array()), 2 => array_fill(1, 4, array()));
	
	//$grad_poly_mesaj = count($qr_code_blocks[$b][$i]) - 1;
	//$poly_gen = multiply_polynoms($poly_gen, poly_gen_x_pow_n($grad_poly_mesaj));
	

	for($b = 1; $b <= 2; $b++)//pt fiecare din cele 2 grupe
	{
		for ($i = 1; $i <= $nr_blocuri[$b]; $i++)//pt fiecare bloc in parte
		{
			//1. Generare polinom mesaj
			$poly_mesaj = gen_poly_msg($qr_code_blocks[$b][$i]);
			//DEBUG
			$qr_code_dec[$b][$i] = $poly_mesaj;
			//!DEBUG

			//2. Inmultim mesajul cu x^$ec_per_block. Pentru asta generam polinomul x^ec_per_block
			$poly_mesaj = multiply_polynoms($poly_mesaj, poly_gen_x_pow_n($ec_per_block));
			//Divide mesajul cu polinomul de generare de $cuvinte_pe_bloc ori
			$cuvinte_pe_bloc = count($qr_code_blocks[$b][$i]);
			//print_polin($poly_mesaj);
			// echo "<br>b = {$b} | i = {$i} | cpb = {$cuvinte_pe_bloc}"; // DEBUG
			for($j=0; $j<$cuvinte_pe_bloc; $j++)
			{
				$poly_mesaj = divide_poly_step($poly_mesaj, $poly_gen);
			}
			$qr_ec_blocks[$b][$i] = $poly_mesaj;
		}
	}
	/*
	var_dump($qr_code_blocks[1][1]);
	echo "Data:<br>";
	//print_polin(qr_alpha_pol($qr_code_dec[1][1]));
	print_polin($qr_code_blocks[1][1]);
	print_polin($qr_code_dec[1][1]);

	echo "Poly_Gen<br/>";
	print_polin($poly_gen);

	echo "EC_CODE: <BR>";
	print_polin($qr_ec_blocks[1][1], false);
	*/
	return $qr_ec_blocks;
}

/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * SFARSIT FUNCTII CORECTIE ERORI  * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

function qr_interleave_across($qr_interleave_blocks)
{
	$qia_r = array();
	$qia_blocks = count($qr_interleave_blocks[1]);
	if(isset($qr_interleave_blocks[2]))
	{
		$qia_blocks = $qia_blocks + count($qr_interleave_blocks[2]);
	}
	
	$qia_data = array();
	for($qia_i = 1; $qia_i <= count($qr_interleave_blocks[1]); $qia_i++)//Grupul 1
	{
		$qia_data[$qia_i] = $qr_interleave_blocks[1][$qia_i];
	}
	
	for($qia_j = 1; $qia_j <= count($qr_interleave_blocks[2]); $qia_j++)//Grupul 2
	{
		$qia_data[$qia_i] = $qr_interleave_blocks[2][$qia_j];
		$qia_i++;
	}
	unset($qr_interleave_blocks);

	$qia_has_data = true;
	$qia_c = 0;
	$qia_j = 1;
	//BUG - Pierzi 4 blocuri de EC WTF
	while($qia_has_data == true)
	{
		$qia_has_data = false;
		for($qia_i = 1; $qia_i <= $qia_blocks; $qia_i++)
		{
			if(isset($qia_data[$qia_i][$qia_j]))
			{
				$qia_r[$qia_c++] = $qia_data[$qia_i][$qia_j];
				$qia_has_data = true;
			}
		}
		$qia_j++;
	}
	var_dump($qia_r);
	return $qia_r;
}
function qr_interleave_data($qr_code_blocks, $qr_ec_blocks, $qr_version)
{
	if(isset($qr_ec_blocks[2]) == FALSE)
	{
		$qr_ec_blocks[2] = array();
	}
	$qid_data = array();
	$qid_data[1] = qr_interleave_across($qr_code_blocks);
	$qid_data[2] = qr_interleave_across($qr_ec_blocks);
	$qid_res_string = "";
	
	$qid_n = count($qid_data[1]);
	for($j = 0; $j < $qid_n; $j++)
	{
		$qid_res_string = $qid_res_string . $qid_data[1][$j];
	}
	
	//avem 1 set de date de convertit
	//qr_data[2]
	$qid_n = count($qid_data[2]);
	for($j = 0; $j < $qid_n; $j++)
	{
		$qid_aux = decbin($qid_data[2][$j]);
		$qid_len = strlen($qid_aux);
		while($qid_len < 7)
		{
			$qid_aux = "0" . $qid_aux;
			$qid_len = strlen($qid_aux);
		}
		$qid_res_string = $qid_res_string . $qid_aux;
	}

	//Remainder bits
	$qid_rem_bits = array (
		1	=> 0, 2		=> 7, 3		=> 7, 4		=> 7, 5		=> 7, 6		=> 7, 7		=> 0, 8		=> 0, 9		=> 0, 10	=> 0, 
		11	=> 0, 12	=> 0, 13	=> 0, 14	=> 3, 15	=> 3, 16	=> 3, 17	=> 3, 18	=> 3, 19	=> 3, 20	=> 3, 
		21	=> 4, 22	=> 4, 23	=> 4, 24	=> 4, 25	=> 4, 26	=> 4, 27	=> 4, 28	=> 3, 29	=> 3, 30	=> 3, 
		31	=> 3, 32	=> 3, 33	=> 3, 34	=> 3, 35	=> 0, 36	=> 0, 37	=> 0, 38	=> 0, 39	=> 0, 40	=> 0);
	for($i = 0; $i < $qid_rem_bits[$qr_version]; $i++)
	{
		$qid_res_string = $qid_res_string . "0";
	}
	return $qid_res_string;
}
?>