<?php
	require_once "IMPRIMIR.php";
	require_once "cronometro.php";
	$cronometro = new Cronometro();
	$cronometro->start("total");

	if(in_array("first_cb", $argv)){
		$cases = [
			"cb-ctt" => 8,
			"pe-ctt" => 6,
		];
	}else{
		$cases = [
			"pe-ctt" => 6,
			"cb-ctt" => 8,
		];
	}

	$timelimit = [
		"pe-ctt" => 202000,
		"cb-ctt" => 239000,
	];

	function casos_exitosos(){
		include("execution_by_test.php");
		$counter = 0;
		$n = count($execution_by_test);

		foreach ($execution_by_test as $key => $value) {
			if($value["file"]){
				$counter++;
			}
		}

		IMPRIMIR( "\t\t--Hasta ahora ".$counter." de ".$n." pruebas han arrojado resultados positivos, para un medida de exito del %".(100*$counter/$n)."--\n\n");
	}

	foreach ($cases as $case => $cant_bits) {
		$files = scandir("datasets/".$case."/");
		unset($files[0]);
		unset($files[1]);

		//ordering $cb or $pe by size, from minor to major
		$estfiles = array();
		foreach ($files as $key => $value) {
			array_push($estfiles, array(
				"name" => $value,
				"size" => filesize("datasets/".$case."/".$value)
			));
		}

		$n = count($estfiles);
		for($i = 1; $i < $n; $i++){
			for($j = 0; $j < $n - $i; $j++){
				if($estfiles[$j]["size"] > $estfiles[$j + 1]["size"]){
					$tmp = $estfiles[$j];
					$estfiles[$j] = $estfiles[$j + 1];
					$estfiles[$j + 1] = $tmp;
				}
			}
		}

		IMPRIMIR( "Case ".$case."\n");

		foreach ($estfiles as $key => $file) {
			$file = $file["name"];
			$nameinput = substr($file, 0, strpos($file, "."));
			$route = "eprime/".$case."/";
			$filename = $route.$nameinput.".param";
			$ini = 0;
			$fin = (int)(pow(2, $cant_bits)) - 1;

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
				$cond = gettype($positem) == "boolean";// || $positem > $length - 3 || !is_numeric($content[$positem + 1]) || intval($content[$positem + 1]) < 1 || intval($content[$positem + 1]) > 511 || !is_numeric($content[$positem + 2]) || doubleval($content[$positem + 2]) < 0;

				if($cond){
					$ini = 1;
					$tiempo_total = 0;

					if(gettype($positem) == "boolean"){
						$positem = $length;
						array_push($content, $case."-".$nameinput);
						array_push($content, $ini);
						array_push($content, $tiempo_total);
					}else{
						$content = [$case."-".$nameinput, $ini, $tiempo_total];
					}
				}else{
					$ini = intval($content[$positem + 1]);
					$tiempo_total = doubleval($content[$positem + 2]);
				}
			}

			IMPRIMIR( "\tDataset: ".$nameinput."\n");

			if($ini <= $fin){
				while($ini <= $fin){
					$binary = decbin($ini);

					while(strlen($binary) < $cant_bits){
						$binary = "0".$binary;
					}

					$output_file = $route.$nameinput."-".$binary.".minion";
					$cmd = "savilerow ".$route.$binary.".eprime ".$filename." -out-minion ".$output_file." -timelimit ".$timelimit[$case];

					IMPRIMIR( "\t\t(".$ini.") Executing ".$cmd."\n");
					$cronometro->start();
					exec($cmd);
					$minitotal = $cronometro->count();

					include "execution_by_test.php";
					$execution_by_test[$case."-".$nameinput."-".$binary] = [
						"total" => $minitotal,
						"file" => is_file($output_file)
					];
					file_put_contents("execution_by_test.php", '<?php $execution_by_test = ' . var_export($execution_by_test, true) . ';?>');
					IMPRIMIR( "\t\tEjecutado en ".$minitotal."\n");
					IMPRIMIR( "\t\tTiempo total acumulado: ".($cronometro->count("total")+$tiempo_total)."\n\n");

					$ini++;
					$content[$positem + 1] = $ini;
					$content[$positem + 2] = $cronometro->count("total")+$tiempo_total;
					file_put_contents("specific_case_bck", implode("\n", $content));
					casos_exitosos();
				}
			}
		}
	}


	IMPRIMIR( "\n\n\nEjecución total: ".($cronometro->count("total") + $tiempo_total)."\n\n");
?>