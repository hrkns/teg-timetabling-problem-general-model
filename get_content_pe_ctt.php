<?php
	$content = file_get_contents($dirpe.$name_file);
	$content = explode("\n", $content);
	$parms = explode(" ", $content[0]);
	$content = array_slice($content, 1);
	unset($content[count($content) - 1]);
	$cant_actividades = intval($parms[0]);
	$cant_ubicaciones = intval($parms[1]);
	$cant_caracteristicas = intval($parms[2]);
	$cant_individuos = intval($parms[3]);
	$Ubicaciones = array();

	for($i = 0; $i < $cant_ubicaciones; $i++){
		array_push($Ubicaciones, array(
			"id" => $i + 1,
			"capacidad" => $content[$i]
		));
	}

	$content = array_slice($content, $cant_ubicaciones);
	$Individuos = array();

	for($i = 0; $i < $cant_individuos; $i++){
		array_push($Individuos, array(
			"id" => $i + 1
		));
	}

	$Actividades = array();

	for($i = 0; $i < $cant_actividades; $i++){
		array_push($Actividades, array(
			"id" => $i + 1
		));
	}

	$k = 0;

	for($i = 0; $i < $cant_individuos; $i++){
		$Individuos[$i]["actividades"] = [];
		for($j = 0; $j < $cant_actividades; $j++){
			array_push($Individuos[$i]["actividades"], intval($content[$k++]));
		}
	}

	$content = array_slice($content, $k);
	$k = 0;

	for($i = 0; $i < $cant_ubicaciones; $i++){
		$Ubicaciones[$i]["caracteristicas"] = [];
		for($j = 0; $j < $cant_caracteristicas; $j++){
			array_push($Ubicaciones[$i]["caracteristicas"], intval($content[$k++]));
		}
	}

	$content = array_slice($content, $k);
	$k = 0;

	for($i = 0; $i < $cant_actividades; $i++){
		$Actividades[$i]["caracteristicas"] = [];
		for($j = 0; $j < $cant_caracteristicas; $j++){
			array_push($Actividades[$i]["caracteristicas"], intval($content[$k++]));
		}
	}

	$content = array_slice($content, $k);
	$cant = count($content);
	$Lapsos = array();
	$cant_lapsos = 0;

	if($cant >= $cant_actividades * $cant_actividades){
		$sobra = $cant - $cant_actividades * $cant_actividades;
		$precedencias = array_slice($content, $sobra);
		$k = 0;

		for($i = 0; $i < $cant_actividades; $i++){
			$Actividades[$i]["precedencias"] = [];
			for($j = 0; $j < $cant_actividades; $j++){
				array_push($Actividades[$i]["precedencias"], intval($precedencias[$k++]));
			}
		}

		/*
		if($sobra % $cant_actividades == 0){
			$cant_lapsos = $sobra / $cant_actividades;

			for($i = 0; $i < $cant_lapsos; $i++){
				array_push($Lapsos, [
					"id" => $i + 1,
					"timeslot" => $i + 1,
					"peso" => $i + 1,
				]);
			}

			$k = 0;
			for($i = 0; $i < $cant_actividades; $i++){
				$Actividades[$i]["lapsos_vetados"] = [];
				for($j = 0; $j < $cant_lapsos; $j++){
					array_push($Actividades[$i]["lapsos_vetados"], intval($content[$k++]));
				}
			}
		}*/

		while($sobra % $cant_actividades != 0){
			$sobra++;
		}

		$cant_lapsos = $sobra / $cant_actividades;

		for($i = 0; $i < $cant_lapsos; $i++){
			array_push($Lapsos, [
				"id" => $i + 1,
				"timeslot" => $i + 1,
				"peso" => $i + 1,
			]);
		}

		$tmp = count($content);
		$lim = $cant_actividades * $cant_lapsos;

		while($tmp++ < $lim){
			array_push($content, 0);
		}

		$k = 0;

		for($i = 0; $i < $cant_actividades; $i++){
			$Actividades[$i]["lapsos_vetados"] = [];
			for($j = 0; $j < $cant_lapsos; $j++){
				array_push($Actividades[$i]["lapsos_vetados"], intval($content[$k++]));
			}
		}
	}else{
		for($i = 0; $i < $cant_actividades; $i++){
			$Actividades[$i]["precedencias"] = array();
			$Actividades[$i]["lapsos_vetados"] = array();
			$cant_lapsos = 1;//$cant_actividades;

			for($j = 0; $j < $cant_actividades; $j++){
				array_push($Actividades[$i]["precedencias"], 0);
			}

			for($j = 0; $j < $cant_lapsos; $j++){
				array_push($Actividades[$i]["lapsos_vetados"], 0);
			}
		}

			for($k = 0; $k < $cant_lapsos; $k++){
				array_push($Lapsos, [
					"id" => $k + 1,
					"timeslot" => $k + 1,
					"peso" => $k + 1,
				]);
			}
	}
?>