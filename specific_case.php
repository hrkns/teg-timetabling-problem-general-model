<?php
	require_once "cronometro.php";

	if(count($argv) < 3){
		exit("Faltan argumentos");
	}

	$case = $argv[1];
	$nameinput = $argv[2];
	$route = "eprime/".$case."/";
	$filename = $route.$nameinput.".param";

	if(!is_file($filename)){
		exit("No existe caso proporcionado");
	}

	if(!is_file("specific_case_bck")){
		$ini = 1;
		$tiempo_total = 0;
		$content = [$case."-".$nameinput, $ini, $tiempo_total];
		$positem = 0;
	}else{
		$content = file_get_contents("specific_case_bck");
		$content = explode("\n", $content);
		$length = count($content);
		$positem = array_search($case."-".$nameinput, $content);
		$cond = gettype($positem) == "boolean" || $positem > $length - 3 || !is_numeric($content[$positem + 1]) || intval($content[$positem + 1]) < 1 || intval($content[$positem + 1]) > 511 || !is_numeric($content[$positem + 2]) || doubleval($content[$positem + 2]) < 0;

		if($cond){
			$ini = 1;
			$tiempo_total = 0;

			if(gettype($positem) == "boolean"){
				$positem = $length;
				array_push($content, $case."-".$nameinput);
				array_push($content, $ini);
				array_push($content, $tiempo_total);
			}else{
				exit();
				$content = [$case."-".$nameinput, $ini, $tiempo_total];
			}
		}else{
			$ini = intval($content[$positem + 1]);
			$tiempo_total = doubleval($content[$positem + 2]);
		}
	}

	$fin = 511;
	$cronometro = new Cronometro();
	$cronometro->start("total");

	while($ini <= $fin){
		$binary = decbin($ini);

		while(strlen($binary) < 9){
			$binary = "0".$binary;
		}

		$cmd = "savilerow ".$route.$binary.".eprime ".$filename." -out-minion ".$route.$nameinput."-".$binary.".minion";

		echo "\t(".$ini.") Executing ".$cmd."\n";
		$cronometro->start();
		exec($cmd);
		echo "\tEjecutado en ".$cronometro->count()."\n";
		echo "\tTiempo total acumulado: ".($cronometro->count("total")+$tiempo_total)."\n\n";

		$ini++;
		$content[$positem + 1] = $ini;
		$content[$positem + 2] = $cronometro->count("total")+$tiempo_total;
		file_put_contents("specific_case_bck", implode("\n", $content));
	}

	echo "\n\n\nEjecución total: ".($cronometro->count("total") + $tiempo_total)."\n\n";
?>