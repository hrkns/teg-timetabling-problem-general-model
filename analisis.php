<?php
	require "execution_by_test.php";

	$cantidad_cb_cubiertos = 0;
	$cantidad_pe_cubiertos = 0;
	$cantidad_cb_exitosos = 0;
	$cantidad_pe_exitosos = 0;

	$cb_case_min_time = 99999999;
	$cb_case_max_time = 0;
	$cb_case_avg_time = 0;
	$cb_input_min_time = 99999999;
	$cb_input_max_time = 0;
	$cb_input_avg_time = 0;

	$pe_case_min_time = 99999999;
	$pe_case_max_time = 0;
	$pe_case_avg_time = 0;
	$pe_input_min_time = 99999999;
	$pe_input_max_time = 0;
	$pe_input_avg_time = 0;

	$pe_cases = [];
	$cb_cases = [];
	$pe_inputs = [];
	$cb_inputs = [];

	foreach ($execution_by_test as $case => $results) {
		$case = explode("-", $case);

		if($case[0] == "pe"){
			$cantidad_pe_cubiertos++;

			if(!array_key_exists($case[2], $pe_cases)){
				$pe_cases[$case[2]] = [
					"success"=>0,
					"total" => 0,
					"min_time_success" => 99999999,
					"max_time_success" => 0,
					"avg_time_success" => 0,
				];
			}

			if(!array_key_exists($case[3], $pe_inputs)){
				$pe_inputs[$case[3]] = [
					"success"=>0,
					"total" => 0,
					"min_time_success" => 99999999,
					"max_time_success" => 0,
					"avg_time_success" => 0,
				];
			}

			$pe_cases[$case[2]]["total"]++;
			$pe_inputs[$case[3]]["total"]++;

			if($results["file"]){
				$cantidad_pe_exitosos++;
				$pe_cases[$case[2]]["success"]++;
				$pe_inputs[$case[3]]["success"]++;

				$pe_cases[$case[2]]["avg_time_success"] = ($pe_cases[$case[2]]["avg_time_success"] * ($pe_cases[$case[2]]["success"] - 1) + $results["total"]) / $pe_cases[$case[2]]["success"];
				$pe_inputs[$case[3]]["avg_time_success"] = ($pe_inputs[$case[3]]["avg_time_success"] * ($pe_inputs[$case[3]]["success"] - 1) + $results["total"]) / $pe_inputs[$case[3]]["success"];

				if($results["total"] < $pe_cases[$case[2]]["min_time_success"]){
					$pe_cases[$case[2]]["min_time_success"] = $results["total"];
				}

				if($results["total"] > $pe_cases[$case[2]]["max_time_success"]){
					$pe_cases[$case[2]]["max_time_success"] = $results["total"];
				}

				if($results["total"] < $pe_inputs[$case[3]]["min_time_success"]){
					$pe_inputs[$case[3]]["min_time_success"] = $results["total"];
				}

				if($results["total"] > $pe_inputs[$case[3]]["max_time_success"]){
					$pe_inputs[$case[3]]["max_time_success"] = $results["total"];
				}
			}
		}else{
			$cantidad_cb_cubiertos++;

			if(!array_key_exists($case[2], $cb_cases)){
				$cb_cases[$case[2]] = [
					"success"=>0,
					"total" => 0,
					"min_time_success" => 99999999,
					"max_time_success" => 0,
					"avg_time_success" => 0,
				];
			}

			if(!array_key_exists($case[3], $cb_inputs)){
				$cb_inputs[$case[3]] = [
					"success"=>0,
					"total" => 0,
					"min_time_success" => 99999999,
					"max_time_success" => 0,
					"avg_time_success" => 0,
				];
			}

			$cb_cases[$case[2]]["total"]++;
			$cb_inputs[$case[3]]["total"]++;

			if($results["file"]){
				$cantidad_cb_exitosos++;
				$cb_cases[$case[2]]["success"]++;
				$cb_inputs[$case[3]]["success"]++;

				$cb_cases[$case[2]]["avg_time_success"] = ($cb_cases[$case[2]]["avg_time_success"] * ($cb_cases[$case[2]]["success"] - 1) + $results["total"]) / $cb_cases[$case[2]]["success"];
				$cb_inputs[$case[3]]["avg_time_success"] = ($cb_inputs[$case[3]]["avg_time_success"] * ($cb_inputs[$case[3]]["success"] - 1) + $results["total"]) / $cb_inputs[$case[3]]["success"];

				if($results["total"] < $cb_cases[$case[2]]["min_time_success"]){
					$cb_cases[$case[2]]["min_time_success"] = $results["total"];
				}

				if($results["total"] > $cb_cases[$case[2]]["max_time_success"]){
					$cb_cases[$case[2]]["max_time_success"] = $results["total"];
				}

				if($results["total"] < $cb_inputs[$case[3]]["min_time_success"]){
					$cb_inputs[$case[3]]["min_time_success"] = $results["total"];
				}

				if($results["total"] > $cb_inputs[$case[3]]["max_time_success"]){
					$cb_inputs[$case[3]]["max_time_success"] = $results["total"];
				}
			}
		}
	}

	echo "\nCantidad de casos para el pe-cct cubiertos: ".$cantidad_pe_cubiertos;
	echo "\nCantidad de casos para el pe-cct exitosos: ".$cantidad_pe_exitosos;
	echo "\nPorcentaje de casos pe-cct exitosos: ".($cantidad_pe_exitosos / $cantidad_pe_cubiertos * 100);
	$n = 0;

	foreach ($pe_cases as $case => $results) {
		$n++;

		if($results["min_time_success"] < $pe_case_min_time){
			$pe_case_min_time = $results["min_time_success"];
		}

		if($results["max_time_success"] > $pe_case_max_time){
			$pe_case_max_time = $results["max_time_success"];
		}

		$pe_case_avg_time = ($pe_case_avg_time * ($n - 1) + $results["avg_time_success"]) / $n;
	}


	echo "\nMenor tiempo de ejecucion para un caso pe-ctt exitoso: ".$pe_case_min_time;
	echo "\nMayor tiempo de ejecucion para un caso pe-ctt exitoso: ".$pe_case_max_time;
	echo "\nTiempo promedio de ejecucion para un caso pe-ctt exitoso: ".$pe_case_avg_time."\n";
	echo "\nAnalisis por casos:";

	foreach ($pe_cases as $case => $results) {
		echo "\n";
		echo "\t---Caso ".$case."---\n";
		echo "\tTotal pruebas: ".$results["total"]."\n";
		echo "\tTotal exito: ".$results["success"]."\n";
		echo "\tPorcentaje de exito: ".($results["success"] / $results["total"] * 100)." %\n";

		if($results["success"]){
			echo "\tMenor tiempo de solucion: ".$results["min_time_success"]." segundos\n";
			echo "\tMayor tiempo de solucion: ".$results["max_time_success"]." segundos\n";
			echo "\tTiempo promedio de solucion: ".$results["avg_time_success"]." segundos\n";
		}
	}

	if(!in_array("no_input", $argv)){
		echo "\nAnalisis por datos de entrada:";
		foreach ($pe_inputs as $input => $results) {
			echo "\n";
			echo "\t***Entrada ".$input."***\n";
			echo "\tTotal pruebas ".$results["total"]."\n";
			echo "\tTotal exito ".$results["success"]."\n";
			echo "\tPorcentaje de exito: ".($results["success"] / $results["total"] * 100)." %\n";

			if($results["success"]){
				echo "\tMenor tiempo de solucion: ".$results["min_time_success"]." segundos\n";
				echo "\tMayor tiempo de solucion: ".$results["max_time_success"]." segundos\n";
				echo "\tTiempo promedio de solucion: ".$results["avg_time_success"]." segundos\n";
			}
		}
	}

	echo "\n\n";
	echo "\nCantidad de casos para el cb-cct cubiertos: ".$cantidad_cb_cubiertos;
	echo "\nCantidad de casos para el cb-cct exitosos: ".$cantidad_cb_exitosos;
	echo "\nPorcentaje de casos cb-cct exitosos: ".($cantidad_cb_exitosos / $cantidad_cb_cubiertos * 100);
	$n = 0;

	foreach ($cb_cases as $case => $results) {
		$n++;

		if($results["min_time_success"] < $cb_case_min_time){
			$cb_case_min_time = $results["min_time_success"];
		}

		if($results["max_time_success"] > $cb_case_max_time){
			$cb_case_max_time = $results["max_time_success"];
		}

		$cb_case_avg_time = ($cb_case_avg_time * ($n - 1) + $results["avg_time_success"]) / $n;
	}

	echo "\nMenor tiempo de ejecucion para un caso cb-ctt exitoso: ".$cb_case_min_time;
	echo "\nMayor tiempo de ejecucion para un caso cb-ctt exitoso: ".$cb_case_max_time;
	echo "\nTiempo promedio de ejecucion para un caso cb-ctt exitoso: ".$cb_case_avg_time."\n";
	echo "\nAnalisis por casos:";

	foreach ($cb_cases as $case => $results) {
		echo "\n";
		echo "\t---Caso ".$case."---\n";
		echo "\tTotal pruebas ".$results["total"]."\n";
		echo "\tTotal exito ".$results["success"]."\n";
		echo "\tPorcentaje de exito: ".($results["success"] / $results["total"] * 100)." %\n";

		if($results["success"]){
			echo "\tMenor tiempo de solucion: ".$results["min_time_success"]." segundos\n";
			echo "\tMayor tiempo de solucion: ".$results["max_time_success"]." segundos\n";
			echo "\tTiempo promedio de solucion: ".$results["avg_time_success"]." segundos\n";
		}
	}

	if(!in_array("no_input", $argv)){
		echo "\nAnalisis por datos de entrada:";
		foreach ($cb_inputs as $input => $results) {
			echo "\n";
			echo "\t***Entrada ".$input."***\n";
			echo "\tTotal pruebas ".$results["total"]."\n";
			echo "\tTotal exito ".$results["success"]."\n";
			echo "\tPorcentaje de exito: ".($results["success"] / $results["total"] * 100)." %\n";

			if($results["success"]){
				echo "\tMenor tiempo de solucion: ".$results["min_time_success"]." segundos\n";
				echo "\tMayor tiempo de solucion: ".$results["max_time_success"]." segundos\n";
				echo "\tTiempo promedio de solucion: ".$results["avg_time_success"]." segundos\n";
			}
		}
	}

	echo "\n";
?>