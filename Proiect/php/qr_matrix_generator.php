<?php
define('QRM_WHITE', 0);
define ('QRM_BLACK', 1);
define('QRM_RESERVED', 2);
define('QRM_DATA', 4);


function qr_matrix_gen_empty($qr_version)
{
	//Generate a qr Matrix

	//The size of a QR code can be calculated with the formula (((V-1)*4)+21), where V is the QR code version.
	$qmg_matrix_size = (($qr_version - 1) * 4) + 21;

	//Vom folosi constantele QRM_* ca flag-uri pe biti https://en.wiktionary.org/wiki/bitflag
	//Practic o valoare din matrice va fi un vector de flag-uri
	//Primul bit - culoarea moduluilui (0-negru, 1 - alb)
	//Al doilea bit - Daca modulul este sau nu rezervat (0 - nerezervat | 1 - rezervat)

	//Spre exemplu o valoare de 3 inseamna un modul rezervat de culoare alba
	//             o valoare de 1 inseamna un modul nerezervat de culoare alba


	$qr_matrix = array_fill(0, $qmg_matrix_size, array_fill(0, $qmg_matrix_size, QRM_WHITE));

	//1 Adauga modelul de cautare (finder pattern)
	/*
	  0 1 2 3 4 5 6
	0 # # # # #	# #	Legenda:
	1 # . . . . . #		# - negru
	2 # . # # # . #		. - alb
	3 # . # # # . #
	4 # . # # # . #
	5 # . . . . . #
	6 # # # # # # #
	*/
	$qmg_module_finder = array_fill(0, 7, array_fill(0, 7, QRM_WHITE));
	
	for($i = 0; $i < 7; $i++) // Margine neagra exterioara
	{
		$qmg_module_finder[$i][0] = $qmg_module_finder[0][$i] = QRM_BLACK | QRM_RESERVED;
		$qmg_module_finder[$i][6] = $qmg_module_finder[6][$i] = QRM_BLACK | QRM_RESERVED;
	}

	for($i = 1; $i < 6; $i++) //Margine alba interioara
	{
		$qmg_module_finder[$i][1] = $qmg_module_finder[1][$i] = QRM_WHITE | QRM_RESERVED;
		$qmg_module_finder[$i][5] = $qmg_module_finder[5][$i] = QRM_WHITE | QRM_RESERVED;
	}

	for($i = 2; $i < 5; $i++) //Patrat interior negru
	{
		$qmg_module_finder[$i][2] = $qmg_module_finder[2][$i] = QRM_BLACK | QRM_RESERVED;
		$qmg_module_finder[$i][4] = $qmg_module_finder[4][$i] = QRM_BLACK | QRM_RESERVED;
	}
	$qmg_module_finder[3][3] = QRM_BLACK | QRM_RESERVED;


	
	//
    //The top-left finder pattern's top left corner is always placed at (0,0).
    //The top-right finder pattern's top LEFT corner is always placed at ([(((V-1)*4)+21) - 7], 0) -> ($qmg_matrix_size - 7, 0)
    //The bottom-left finder pattern's top LEFT corner is always placed at (0,[(((V-1)*4)+21) - 7]) -> 

    
    $qmg_x = array(0 => 0, $qmg_matrix_size - 7,                    0);
    $qmg_y = array(0 => 0,                    0, $qmg_matrix_size - 7);

    for($i = 0, $n = count($qmg_x); $i < $n; $i++)
    {
    	$m = count($qmg_module_finder);
    	for($j = 0; $j < $m; $j++)
    	{
    		for($k = 0; $k < $m; $k++)
    		{
    			$qr_matrix[$qmg_x[$i] + $j][$qmg_y[$i] + $k] = $qmg_module_finder[$j][$k];
    		}
    	}
    }
    //Adauga separatorii - Benzi albe pe langa modelele de cautare | acestea au lungimea 8

    // a. Pe orizontala acestea se gasesc pe randul 7 (doua benzi de lungime 8) [Una incepe de la 0 si una incepe de la $qmg_matrix_size - 8]
    //	  si pe randul $qmg_matrix_size - 8
    // b. Pe verticala doua pe coloana 7 (una de la 0 si una de la $qmg_matrix_size - 8)
    //    si pe coloana $qmg_matrix_size - 8

    for($i = 0; $i<8; $i++)
    {
    	$qr_matrix[7][$i] = QRM_RESERVED | QRM_WHITE;
    	$qr_matrix[7][$qmg_matrix_size - 8 + $i] = QRM_RESERVED | QRM_WHITE;
    	$qr_matrix[$qmg_matrix_size - 8][$i] = QRM_RESERVED | QRM_WHITE;

    	$qr_matrix[$i][7] = QRM_RESERVED | QRM_WHITE;
    	$qr_matrix[$qmg_matrix_size - 8 + $i][7] = QRM_RESERVED | QRM_WHITE;
    	$qr_matrix[$i][$qmg_matrix_size - 8] = QRM_RESERVED | QRM_WHITE;
    }


	//2 Adauga modelul de aliniere (alignment pattern)
	/*
	  0 1 2 3 4 
	0 # # # # #	Legenda:
	1 # . . . #		# - negru
	2 # . # . #		. - alb
	3 # . . . #
	4 # # # # #
	*/
 
    if($qr_version > 1)
    {
    	//http://www.thonky.com/qr-code-tutorial/alignment-pattern-locations
    	//TODO de copiat tabelul de mai sus
    	//Alignment Pattern Locations
  		$qmg_module_align_location = array(
    		2 => array(6, 18),
    		3 => array(6, 22),
    		4 => array(6, 26),
    		5 => array(6, 30)
    	);

    	$qmg_module_align = array_fill(0, 5, array_fill(0, 5, QRM_WHITE));
    	for($i = 0; $i < 5; $i++) // Margine neagra exterioara
		{
			$qmg_module_align[$i][0] = $qmg_module_align[0][$i] = QRM_BLACK | QRM_RESERVED;
			$qmg_module_align[$i][4] = $qmg_module_align[4][$i] = QRM_BLACK | QRM_RESERVED;
		}
		for($i = 1; $i < 4; $i++) // Margine neagra exterioara
		{
			$qmg_module_align[$i][1] = $qmg_module_align[1][$i] = QRM_WHITE | QRM_RESERVED;
			$qmg_module_align[$i][3] = $qmg_module_align[3][$i] = QRM_WHITE | QRM_RESERVED;
		}
		$qmg_module_align[2][2] = QRM_BLACK | QRM_RESERVED;

    	/*
	    $qmg_module_align[0] = array_fill(0, 5, QRM_BLACK | QRM_RESERVED); //Linia 0

	    $qmg_module_align[1][0] = $qmg_module_align[1][4] =  QRM_BLACK | QRM_RESERVED;
	    $qmg_module_align[1][1] = $qmg_module_align[1][2] = $qmg_module_align[1][3] = QRM_WHITE | QRM_RESERVED; //Linia 1

	    $qmg_module_align[2][0] = $qmg_module_align[2][2] = $qmg_module_align[2][4] = QRM_BLACK | QRM_RESERVED;
	    $qmg_module_align[2][0] = $qmg_module_align[2][2] = QRM_WHITE | QRM_RESERVED;//Linia 2

	    $qmg_module_align[3][0] = $qmg_module_align[3][4] =  QRM_BLACK | QRM_RESERVED;
	    $qmg_module_align[3][1] = $qmg_module_align[3][2] = $qmg_module_align[3][3] = QRM_WHITE | QRM_RESERVED; //Linia 3
	    $qmg_module_align[4] = array_fill(0, 5, QRM_BLACK | QRM_RESERVED);//Linia 4
	    */

	    $qmg_ma_count = count($qmg_module_align_location[$qr_version]);
	    $qmg_x = array();
	    $qmg_y = array();
	    for($i = 0; $i < $qmg_ma_count; $i++)
	    {
		    $qmg_ma_i = $qmg_module_align_location[$qr_version][$i];
	    	for($j = 0; $j < $qmg_ma_count; $j++)
		    {
		    	$qmg_ma_j = $qmg_module_align_location[$qr_version][$j];
		    	if(($qr_matrix[$qmg_ma_i][$qmg_ma_j] & QRM_RESERVED) == 0)
		    	{
		    		//Daca nu incercam sa punem modulul intr-o zona protejata
		    		$qmg_x[] = $qmg_ma_i - 2;
		    		$qmg_y[] = $qmg_ma_j - 2;
		    	}
		    }
	    }

	    for($i = 0, $n = count($qmg_x); $i < $n; $i++)
    	{
    		$m = count($qmg_module_align);
    		for($j = 0; $j < $m; $j++)
    		{
    			for($k = 0; $k < $m; $k++)
    			{
    				$qr_matrix[$qmg_x[$i] + $j][$qmg_y[$i] + $k] = $qmg_module_align[$j][$k];
    			}
    		}
   		}
    }//end if $qr_version > 1

    // 3. Adaugam modelul de sincronizare
    for($i = 6; $i < $qmg_matrix_size - 7; $i = $i + 2)
    {
    	$qr_matrix[$i][6] = $qr_matrix[6][$i] = QRM_BLACK | QRM_RESERVED;
    	$qr_matrix[$i + 1][6] = $qr_matrix[6][$i + 1] = QRM_WHITE | QRM_RESERVED;
    }

    // 4. Adaugam modulul negru
    //the dark module is always located at the coordinate ([(4 * V) + 9], 8) where V is the version of the QR code
    $qr_matrix[4 * $qr_version + 9][8] = QRM_BLACK | QRM_RESERVED;

    // 5. Rezervam zona pentru format
    if( $qr_version < 7)
    {
	    $qmg_reserved_value = QRM_RESERVED | QRM_WHITE;//DEBUG ONLY!!! MUST BE WHITE
	    for($i = 0; $i < 8; $i++)
	    {
	    	//a. Rezerva capetele randului 8
	    	if(($qr_matrix[8][$i] & QRM_RESERVED) == 0) {
		    	$qr_matrix[8][$i] = $qmg_reserved_value;
		    }
		    if (($qr_matrix[8][$qmg_matrix_size - 8 + $i] & QRM_RESERVED) == 0) {
		    	$qr_matrix[8][$qmg_matrix_size - 8 + $i] = $qmg_reserved_value;
		    }
		    if (($qr_matrix[$i][8] & QRM_RESERVED) == 0) {
			    $qr_matrix[$i][8] = $qmg_reserved_value;
			}
			if (($qr_matrix[$qmg_matrix_size - 8 + $i][8] & QRM_RESERVED) == 0) {
			    $qr_matrix[$qmg_matrix_size - 8 + $i][8] = $qmg_reserved_value;
			}
	    }
	    $qr_matrix[8][8] = $qmg_reserved_value;
	}
	else
	{
		//TODO
	}
	return $qr_matrix;
}// Gen empty matrix

//$qr_matrix = $qr_matrix_place_data($qr_matrix, $qr_data);
function qr_matrix_place_data(&$qr_matrix, &$qr_data)
{
	$qpd_up = 1;
	$qpd_down = 2;
	$qpd_left = 4;
	$qpd_right = 8;
	$qpd_direction = $qpd_up | $qpd_right;
	
	$qpd_data_i = 0;
	$qpd_matrix_size = count($qr_matrix);
	$qpd_next_x = $qpd_matrix_size - 1;
	$qpd_next_y = $qpd_matrix_size - 1;
	$qpd_data_count = strlen($qr_data);

	//$log = fopen("log.txt", "w"); // DEBUG

	while($qpd_data_i < $qpd_data_count)
	{
		//1. Adauga
		$qpd_value = QRM_DATA;
		if($qr_data[$qpd_data_i] == "1"){
			$qpd_value = $qpd_value | QRM_BLACK;
		}
		else {
			$qpd_value = $qpd_value | QRM_WHITE;
		}
		//$qr_matrix[$qpd_next_y][$qpd_next_x] = $qr_data[$qpd_data_i];
		$qr_matrix[$qpd_next_y][$qpd_next_x] = $qpd_value;

		
		//fwrite($log, "[{$qpd_next_y}][{$qpd_next_x}] = {$qr_data[$qpd_data_i]}\n");
		
		$qpd_next_valid = 0;
		$qpd_data_i++;
		while($qpd_data_i < $qpd_data_count && $qpd_next_valid == 0)
		{
			if(($qpd_direction & $qpd_up) != 0) {
				if(($qpd_direction & $qpd_right) != 0)
				{
					//daca mergem in sus si suntem pe partea dreapta
					$qpd_next_x--;
					$qpd_direction = $qpd_up | $qpd_left;
				}
				else 
				{
					//mergem in sus si suntem in partea stanga
					if(($qpd_next_x == 7) && ($qpd_next_y == 9))
					{
						//Exceptie. Trebuie sa ne deplasam cu 2 module la stanga
						$qpd_next_x = $qpd_next_x - 2;
						$qpd_direction = $qpd_down | $qpd_right;
					}
					else {
						$qpd_next_x++;
						$qpd_next_y--;
						$qpd_direction = $qpd_up | $qpd_right;
						if($qpd_next_y < 0)
						{
							$qpd_next_y = 0;
							$qpd_next_x = $qpd_next_x - 2;
							$qpd_direction = $qpd_down | $qpd_right;
						}
					}
				}
			}
			else // Mergem in jos
			{
				if(($qpd_direction & $qpd_right) != 0) {
					//Daca mergem in jos si suntem in partea dreapta
					$qpd_next_x--;
					$qpd_direction = $qpd_down | $qpd_left;
				}
				else {
					//Daca mergem in jos si suntem in partea stanga
					$qpd_next_x++;
					$qpd_next_y++;
					$qpd_direction = $qpd_down | $qpd_right;
					if($qpd_next_y == $qpd_matrix_size) {
						$qpd_next_y--;
						$qpd_next_x = $qpd_next_x - 2;
						$qpd_direction = $qpd_up | $qpd_right;
					}
				}
			}
			if(($qr_matrix[$qpd_next_x][$qpd_next_y] & QRM_RESERVED) != 0) {
				$qpd_next_valid = 0;//zona rezervata
			}
			else {
				$qpd_next_valid = 1;
			}
		}//End While $qpd_next_vali

	}//End While 2
	//fclose($log);

	//return $qr_matrix;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * *  ----------------------   * * * * * * * * * * *
 * * * * * * * * * * * *  DATA MASKING FUNCTIONS   * * * * * * * * * * *
 * * * * * * * * * * * *  ----------------------   * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

//Functia calculeaza masca optima pentru matricea $qr_matrix,
//		o aplica si returneaza masca aplicata

function qr_matrix_mask_data(&$qr_matrix)
{
	$qr_data_mask = 0;
	/*/ DEBUG
	 echo "Mask: $qr_data_mask<br>";
	 $dbg_matrix = qr_matrix_apply_mask($qr_matrix, 0);
	 var_dump(ascii_print($dbg_matrix));
	 $dbg_score = qr_matrix_penalty_score($dbg_matrix);
	//*///!DEBUG

	$qmm_min_score = qr_matrix_penalty_score(qr_matrix_apply_mask($qr_matrix, $qr_data_mask));
	for($qmm_mask_number = 1; $qmm_mask_number <= 7; $qmm_mask_number++)
	{
		/*/DEBUG
		 echo "<br>---------------------<br>Mask: $qmm_mask_number<br>";
		 $dbg_matrix = qr_matrix_apply_mask($qr_matrix, $qmm_mask_number);
		 var_dump(ascii_print($dbg_matrix));
		 $dbg_score = qr_matrix_penalty_score($dbg_matrix);
		//*///!DEBUG
		$qmm_aux_score = qr_matrix_penalty_score(qr_matrix_apply_mask($qr_matrix, $qmm_mask_number));
		if($qmm_aux_score < $qmm_min_score)
		{
			$qr_data_mask = $qmm_mask_number;
			$qmm_min_score = $qmm_aux_score;
		}
	}
	$qr_matrix = qr_matrix_apply_mask($qr_matrix, $qr_data_mask);
	return $qr_data_mask;
}


function qr_matrix_penalty_score(& $qr_matrix)
{
	$qmp_score = 0;
	$qmp_row = count($qr_matrix);
	$qmp_colums = 0;
	if(isset($qr_matrix[0]))
	{
		$qmp_colums = count($qr_matrix[0]);
	}

	// Rule #1
	// 1. For the first evaluation condition, check each row one-by-one. If there are five consecutive modules of the same color, add 3 to the penalty.
	// If there are more modules of the same color after the first five, add 1 for each additional module of the same color. 
	// Afterward, check each column one-by-one, checking for the same condition. Add the horizontal and vertical total to obtain penalty score #1.
	$qmp_aux_score = 0;
	

	for($i = 0; $i < $qmp_row; $i++)
	{
		$qmp_consec_white_r = 0;
		$qmp_consec_black_r = 0;
		$qmp_consec_white_c = 0;
		$qmp_consec_black_c = 0;	
		for($j = 0; $j < $qmp_colums; $j++)
		{
			// Per row
			if(($qr_matrix[$i][$j] & QRM_BLACK) != 0)//Black module
			{
				$qmp_consec_black_r++;
				$qmp_consec_white_r = 0;
			}
			else // White module
			{
				$qmp_consec_white_r++;
				$qmp_consec_black_r = 0;
			}

			if($qmp_consec_white_r >= 5)
			{
				if($qmp_consec_white_r == 5)
				{
					$qmp_aux_score += 3;
				}
				else
				{
					$qmp_aux_score += 1;
				}
			}

			if($qmp_consec_black_r >= 5)
			{
				if($qmp_consec_black_r == 5)
				{
					$qmp_aux_score += 3;
				}
				else
				{
					$qmp_aux_score += 1;
				}
			}

			//Per column
			if(($qr_matrix[$j][$i] & QRM_BLACK) != 0)//Black module
			{
				//NOTE i si j sunt schimbate!!!
				$qmp_consec_black_c++;
				$qmp_consec_white_c = 0;
			}
			else // White module
			{
				$qmp_consec_white_c++;
				$qmp_consec_black_c = 0;
			}

			if($qmp_consec_white_c >= 5)
			{
				if($qmp_consec_white_c == 5)
				{
					$qmp_aux_score += 3;
				}
				else
				{
					$qmp_aux_score += 1;
				}
			}

			if($qmp_consec_black_c >= 5)
			{
				if($qmp_consec_black_c == 5)
				{
					$qmp_aux_score += 3;
				}
				else
				{
					$qmp_aux_score += 1;
				}
			}
		}//end for j
	}//end for i
	//echo "Score #1: $qmp_aux_score<br>";
	$qmp_score += $qmp_aux_score;
	$qmp_aux_score = 0;
	// Rule #2
	//For second evaluation condition, look for areas of the same color that are at least 2x2 modules or larger.
	for($i = 0; $i < $qmp_row - 1; $i++)
	{
		for($j = 0; $j < $qmp_colums - 1; $j++)
		{
			// Daca tinem cont ca primul bit din valorile matricei reprezinta culoarea modulului
			// Folosim operatia de SI logic pentru a afla daca toate valorile sunt 1 (negru)
			// si operatia de SAU logic pentru a afla daca oate valorile sunt 0 (alb)
			$qmp_block_sum = 1;
			$qmp_block_sum = ($qr_matrix[$i + 1][$j + 1] & QRM_BLACK) & $qmp_block_sum;
			$qmp_block_sum = ($qr_matrix[$i + 1][$j    ] & QRM_BLACK) & $qmp_block_sum;
			$qmp_block_sum = ($qr_matrix[$i    ][$j + 1] & QRM_BLACK) & $qmp_block_sum;
			$qmp_block_sum = ($qr_matrix[$i    ][$j    ] & QRM_BLACK) & $qmp_block_sum;
			if($qmp_block_sum == 1)
			{
				//toate blocurile sunt negre
				$qmp_aux_score += 3;
			}

			$qmp_block_sum = 0;
			$qmp_block_sum = ($qr_matrix[$i + 1][$j + 1] & QRM_BLACK) | $qmp_block_sum;
			$qmp_block_sum = ($qr_matrix[$i + 1][$j    ] & QRM_BLACK) | $qmp_block_sum;
			$qmp_block_sum = ($qr_matrix[$i    ][$j + 1] & QRM_BLACK) | $qmp_block_sum;
			$qmp_block_sum = ($qr_matrix[$i    ][$j    ] & QRM_BLACK) | $qmp_block_sum;
			if($qmp_block_sum == 0)
			{
				//toate blocurile sunt albe
				$qmp_aux_score += 3;
			}
		}//end for j
	}//end for i
	//echo "Score #2: $qmp_aux_score<br>";//DEBUG
	$qmp_score += $qmp_aux_score;
	$qmp_aux_score = 0;

	// Rule #3
	// The third penalty rule looks for patterns of dark-light-dark-dark-dark-light-dark that have four light modules on either side
	// # . # # # . # . . . .
	// . . . . # . # # # . #
	// We use the magic of regex
	$qmp_pattern_1 = "/(10111010000)/";
	$qmp_pattern_2 = "/(00001011101)/";
	for($i = 0; $i < $qmp_row; $i++)
	{
		$qmp_string_row = "";
		$qmp_string_column = "";
		for($j = 0; $j < $qmp_colums; $j++)
		{
			$qmp_string_row .= ($qr_matrix[$i][$j] & QRM_BLACK) ? "1" : "0";
			$qmp_string_column .= ($qr_matrix[$j][$i] & QRM_BLACK) ? "1" : "0";
		}
		/*DEBUG
		$dbg_pma_c = preg_match_all($qmp_pattern_1, $qmp_string_row);
		if($dbg_pma_c != 0)
		{
			echo "Found $dbg_pma_c matches in row $i<br>";
		}
		$dbg_pma_c = preg_match_all($qmp_pattern_2, $qmp_string_row);
		if($dbg_pma_c != 0)
		{
			echo "Found $dbg_pma_c matches 2 in row $i<br>";
		}
		$dbg_pma_c = preg_match_all($qmp_pattern_1, $qmp_string_column);
		if($dbg_pma_c != 0)
		{
			echo "Found $dbg_pma_c matches in col $i<br>";
		}
		$dbg_pma_c = preg_match_all($qmp_pattern_2, $qmp_string_column);
		if($dbg_pma_c != 0)
		{
			echo "Found $dbg_pma_c matches 2 in col $i<br>";
		}
		//*///!DEBUG
		$qmp_aux_score += 40 * preg_match_all($qmp_pattern_1, $qmp_string_row);
		$qmp_aux_score += 40 * preg_match_all($qmp_pattern_2, $qmp_string_row);
		$qmp_aux_score += 40 * preg_match_all($qmp_pattern_1, $qmp_string_column);
		$qmp_aux_score += 40 * preg_match_all($qmp_pattern_2, $qmp_string_column);
	}
	//echo "Score #3: $qmp_aux_score<br>";//DEBUG
	$qmp_score += $qmp_aux_score;
	$qmp_aux_score = 0;

	// Rule #4
	// The final evaluation condition is based on the ratio of light modules to dark modules
	$qmp_count_dark = 0;
	for($i = 0; $i < $qmp_row; $i++)
	{
		for($j = 0; $j < $qmp_colums; $j++)
		{
			$qmp_count_dark += ($qr_matrix[$i][$j] & QRM_BLACK) ? 1 : 0;
		}
	}
	$qmp_count_total = $qmp_row * $qmp_colums; // sau $qmp_row * $qmp_colums
	//echo "Dark $qmp_count_dark | TOTAL = $qmp_count_total<br>";
	$qmp_percentage = ($qmp_count_dark / $qmp_count_total) * 100.0;
	$qmp_percentage = (int) $qmp_percentage;
	$qmp_percentage_prev5 = intdiv_1($qmp_percentage, 5) * 5;
	$qmp_percentage_next5 = $qmp_percentage_prev5 + 5;
	//echo "$qmp_percentage | $qmp_percentage_prev5 | $qmp_percentage_next5<br>";
	$qmp_percentage_prev5 = (int)abs($qmp_percentage_prev5 - 50);
	$qmp_percentage_prev5 = intdiv_1($qmp_percentage_prev5, 5);
	$qmp_percentage_next5 = (int)abs($qmp_percentage_next5 - 50);
	$qmp_percentage_next5 = intdiv_1($qmp_percentage_next5, 5);
	//echo "$qmp_percentage_prev5 | $qmp_percentage_next5<br>";
	$qmp_aux_score = ($qmp_percentage_next5 < $qmp_percentage_prev5) ? $qmp_percentage_next5 * 10 : $qmp_percentage_prev5 * 10;
	$qmp_score += $qmp_aux_score;
	//echo "Score #4: $qmp_aux_score<br>";//DEBUG
	//echo "----------------<br><b>TOTAL $qmp_score</b><br>";
	return $qmp_score;
}

function qr_matrix_flip_bit($bit)
{
	$bit = $bit ^ QRM_BLACK; // This works because XOR
	// ^ | 0 | 1
	// 0 > 0   1
	// 1 > 1   0

	//QRM_BLACK = 1 -> A XOR QRM_BLACK = NOT A
	return $bit;
}

function qr_matrix_apply_mask($qr_matrix, $qr_data_mask)
{
	$qma_row = count($qr_matrix);
	$qma_colums = 0;
	if(isset($qr_matrix[0]))
	{
		$qma_colums = count($qr_matrix[0]);
	}
	for($row = 0; $row < $qma_row; $row++)
	{
		for($column = 0; $column < $qma_colums; $column++)
		{
			$qma_flip = false;
			//Patterns: http://www.thonky.com/qr-code-tutorial/mask-patterns
			switch ($qr_data_mask) {
				case 0:
					if(($row + $column) % 2 == 0)
					{
						//Switch the bit there
						$qma_flip = true;
					}
					break;
				case 1:
					if($row % 2 == 0)
					{
						$qma_flip = true;
					}
					break;
				case 2:
					if($column % 3 == 0)
					{
						$qma_flip = true;
					}
					break;
				case 3:
					if(($row + $column) % 3 == 0)
					{
						$qma_flip = true;
					}
					break;
				case 4:
					if((($row / 2) + ($column / 3)) % 2 == 0)
					{
						$qma_flip = true;
					}
					break;
				case 5:
					if((($row * $column) % 2) + (($row * $column) % 3) == 0)
					{
						$qma_flip = true;
					}
					break;
				case 6:
					if((($row * $column) % 2) + (($row * $column) % 3) % 2 == 0)
					{
						$qma_flip = true;
					}
					break;
				case 7:
					if((($row + $column) % 2) + (($row * $column) % 3) % 2 == 0)
					{
						$qma_flip = true;
					}
					break;
				default:
					# code...
					echo "ERROR! Invalid data mask {$qr_data_mask}!";
					break;
			}//!switch

			if($qma_flip == true && (($qr_matrix[$row][$column] & QRM_DATA) != 0))//TODO Breakpoint here
			{
				$qr_matrix[$row][$column] = qr_matrix_flip_bit($qr_matrix[$row][$column]);
			}
		}//!for j
	}//!for i
	return $qr_matrix;
}

?>