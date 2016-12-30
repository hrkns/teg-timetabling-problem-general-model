<?php
	require_once "IMPRIMIR.php";
	ini_set("memory_limit", "-1");

	function deleteDir($dirPath) {
		if (substr($dirPath, strlen($dirPath) - 1, 1) != "/") {
			$dirPath .= "/";
		}

		$files =glob($dirPath . "*", GLOB_MARK);

		foreach ($files as $file) {
			if (is_dir($file)) {
				deleteDir($file);
			} else {
				unlink($file);
			}
		}
		@rmdir($dirPath);
	}

	file_put_contents("execution_by_test.php", '<?php $execution_by_test = ' . var_export(array(), true) . ';?>');
	@unlink("specific_case_bck");
	@unlink("output_simulation.txt");
	//deleteDir("eprime/");
	$time_start = microtime(true);
	$dircb = "datasets/cb-ctt/";
	$dirpe = "datasets/pe-ctt/";
	$cb = scandir($dircb);
	$pe = scandir($dirpe);
	unset($cb[0]);
	unset($cb[1]);
	unset($pe[0]);
	unset($pe[1]);

	if(!is_dir("eprime")){
		mkdir("eprime");
	}

	if(!is_dir("eprime/cb-ctt")){
		mkdir("eprime/cb-ctt");
	}

	if(!is_dir("eprime/pe-ctt")){
		mkdir("eprime/pe-ctt");
	}

	if(!is_file("pending_reading.php") || !in_array("restart_read", $argv)){
		$pending_reading = array(
			"cb-ctt" => array(
				"already_read" => array(),
				"cant_min_actividades" => 99999999,
				"cant_max_actividades" => 0,
				"cant_min_eventos" => 99999999,
				"cant_max_eventos" => 0,
				"counter_actividades" => 0,
				"counter_eventos" => 0,
			),
			"pe-ctt" => array(
				"already_read" => array(),
				"cant_min_actividades" => 99999999,
				"cant_max_actividades" => 0,
				"counter_actividades" => 0,
			),
		);
		file_put_contents("pending_reading.php", '<?php $pending_reading = ' . var_export($pending_reading, true) . ';?>');
	}else{
		include("pending_reading.php");
	}

	if(!in_array("jump_reading", $argv) && !in_array("jump_reading_cb", $argv)){
		IMPRIMIR( "Construyendo casos de prueba para el CB-CCT en ruta eprime/cb-ctt/...\n\t");
		$time1 = microtime(true);
		require "algorithm_cb.php";

		extract($pending_reading["cb-ctt"]);

		$cant_ciclos = 0;

		//ordering $cb by size, from minor to major
		$cbfiles = array();
		foreach ($cb as $key => $value) {
			array_push($cbfiles, array(
				"name" => $value,
				"size" => filesize($dircb.$value)
			));
		}

		$cant_ciclos = count($cbfiles);

		for($i = 1; $i < $cant_ciclos; $i++){
			for($j = 0; $j < $cant_ciclos - $i; $j++){
				if($cbfiles[$j]["size"] > $cbfiles[$j + 1]["size"]){
					$tmp = $cbfiles[$j];
					$cbfiles[$j] = $cbfiles[$j + 1];
					$cbfiles[$j + 1] = $tmp;
				}
			}
		}

		$floor_percentage = 20;
		$roof_percentage = 50;
		$counter = 1;

		foreach ($cbfiles as $key => $est) {
			$name_file = $est["name"];
			$name_dir = substr($name_file, 0, strpos($name_file, "."));
	
			if(!in_array($name_dir, $pending_reading["cb-ctt"]["already_read"])){
				require "get_content_cb_ctt.php";
				require "build_content_cb_ctt_focus_1.php";
				IMPRIMIR( $name_dir."(".$cant_eventos.", ".$cant_actividades."), ");

				if($cant_actividades < $cant_min_actividades){
					$cant_min_actividades = $cant_actividades;
				}

				if($cant_actividades > $cant_max_actividades){
					$cant_max_actividades = $cant_actividades;
				}

				if($cant_eventos < $cant_min_eventos){
					$cant_min_eventos = $cant_eventos;
				}

				if($cant_eventos > $cant_max_eventos){
					$cant_max_eventos = $cant_eventos;
				}

				$counter_actividades += $cant_actividades;
				$counter_eventos += $cant_eventos;
				$pending_reading["cb-ctt"]["cant_min_actividades"] 	= $cant_min_actividades;
				$pending_reading["cb-ctt"]["cant_max_actividades"] 	= $cant_max_actividades;
				$pending_reading["cb-ctt"]["cant_min_eventos"] 		= $cant_min_eventos;
				$pending_reading["cb-ctt"]["cant_max_eventos"] 		= $cant_max_eventos;
				$pending_reading["cb-ctt"]["counter_actividades"] 	= $counter_actividades;
				$pending_reading["cb-ctt"]["counter_eventos"] 		= $counter_eventos;
				array_push($pending_reading["cb-ctt"]["already_read"], $name_dir);
				file_put_contents("pending_reading.php", '<?php $pending_reading = ' . var_export($pending_reading, true) . ';?>');
			}

			$counter++;
		}

		extract($pending_reading["cb-ctt"]);
		IMPRIMIR( "\n\n");
		IMPRIMIR( "\nCantidad minima de eventos: ".$cant_min_eventos);
		IMPRIMIR( "\nCantidad maxima de eventos: ".$cant_max_eventos);
		IMPRIMIR( "\nCantidad minima de actividades: ".$cant_min_actividades);
		IMPRIMIR( "\nCantidad maxima de actividades: ".$cant_max_actividades);
		IMPRIMIR( "\nCantidad promedio de eventos: ".($counter_eventos / $cant_ciclos));
		IMPRIMIR( "\nCantidad promedio de actividades: ".($counter_actividades / $cant_ciclos));
		$time_end = microtime(true);$time = $time_end - $time1;
		IMPRIMIR( "\n\nTiempo de construccion: ".$time."\n\n\n");
	}

	if(!in_array("jump_reading", $argv) && !in_array("jump_reading_pe", $argv)){

		IMPRIMIR( "Construyendo casos de prueba para el PE-CCT en ruta eprime/pe-ctt/...\n\t");
		$time1 = microtime(true);

		require "algorithm_pe.php";

		extract($pending_reading["cb-ctt"]);

		$cant_ciclos = 0;

		//ordering $pe by size, from minor to major
		$pefiles = array();
		foreach ($pe as $key => $value) {
			array_push($pefiles, array(
				"name" => $value,
				"size" => filesize($dirpe.$value)
			));
		}

		$cant_ciclos = count($pefiles);

		for($i = 1; $i < $cant_ciclos; $i++){
			for($j = 0; $j < $cant_ciclos - $i; $j++){
				if($pefiles[$j]["size"] > $pefiles[$j + 1]["size"]){
					$tmp = $pefiles[$j];
					$pefiles[$j] = $pefiles[$j + 1];
					$pefiles[$j + 1] = $tmp;
				}
			}
		}

		$floor_percentage = 10;
		$roof_percentage = 40;
		$counter = 1;

		foreach ($pefiles as $key => $est) {
			$name_file = $est["name"];
			$name_dir = substr($name_file, 0, strpos($name_file, "."));
			if(!in_array($name_dir, $pending_reading["pe-ctt"]["already_read"])){
				require "get_content_pe_ctt.php";
				require "build_content_pe_ctt_focus_1.php";
				IMPRIMIR( $name_dir."(".$cant_actividades."), ");

				if($cant_actividades < $cant_min_actividades){
					$cant_min_actividades = $cant_actividades;
				}

				if($cant_actividades > $cant_max_actividades){
					$cant_max_actividades = $cant_actividades;
				}

				$counter_actividades += $cant_actividades;

				$pending_reading["pe-ctt"]["cant_min_actividades"] 	= $cant_min_actividades;
				$pending_reading["pe-ctt"]["cant_max_actividades"] 	= $cant_max_actividades;
				$pending_reading["pe-ctt"]["counter_actividades"] 	= $counter_actividades;
				array_push($pending_reading["pe-ctt"]["already_read"], $name_dir);
				file_put_contents("pending_reading.php", '<?php $pending_reading = ' . var_export($pending_reading, true) . ';?>');
			}

			$counter++;
		}

		extract($pending_reading["pe-ctt"]);
		IMPRIMIR( "\n\n");
		IMPRIMIR( "\nCantidad minima de actividades: ".$cant_min_actividades);
		IMPRIMIR( "\nCantidad maxima de actividades: ".$cant_max_actividades);
		IMPRIMIR( "\nCantidad promedio de actividades: ".($counter_actividades / $cant_ciclos));
		$time_end = microtime(true);$time = $time_end - $time1;
		IMPRIMIR( "\n\nTiempo de construccion: ".$time."\n\n\n");
	}

	$time_end = microtime(true);$time = $time_end - $time_start;
	IMPRIMIR( "Tiempo Total de Ejecucion: {$time}");
?>