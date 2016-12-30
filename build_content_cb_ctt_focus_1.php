<?php
	$porcentaje = ($roof_percentage - (($roof_percentage - $floor_percentage) * ($counter / $cant_ciclos)))/100;//10 / 100;
	$cant_actividades = (int)floor($cant_actividades * $porcentaje)+1;
	$n = count($Actividades);

	while($n > $cant_actividades){
		$rnd = rand(0, $n-1);
		$Actividades = array_merge(array_slice($Actividades, 0, $rnd), array_slice($Actividades, $rnd + 1));

		foreach ($Lapsos as $key => $dia) {
			foreach ($dia as $k2 => $lapso) {
				$Lapsos[$key][$k2]["actividades_a_vetar"] = array_merge(array_slice($Lapsos[$key][$k2]["actividades_a_vetar"], 0, $rnd), array_slice($Lapsos[$key][$k2]["actividades_a_vetar"], $rnd + 1));
			}
		}

		foreach ($Ubicaciones as $key => $ubicacion) {
			$Ubicaciones[$key]["actividades_a_vetar"] = array_merge(array_slice($Ubicaciones[$key]["actividades_a_vetar"], 0, $rnd), array_slice($Ubicaciones[$key]["actividades_a_vetar"], $rnd + 1));
		}

		$n--;
	}

	$cant_eventos = 0;
	for($i = 0; $i < $cant_actividades; $i++){
		$Actividades[$i]["cant_clases"] = (int)floor($Actividades[$i]["cant_clases"]*$porcentaje)+1;
		$cant_eventos += $Actividades[$i]["cant_clases"];
	}

	$content = 	"language ESSENCE' 1.0"."\n\n".
				"letting Cant_Actividades be ".$cant_actividades."\n".
				"letting Cant_Lapsos be ".$cant_lapsos."\n".
				"letting Cant_Eventos be ".$cant_eventos."\n".
				"letting Cant_Ubicaciones be ".$cant_ubicaciones."\n".
				"letting Cant_Curriculos be ".$cant_curriculos."\n".
				"letting Base_Lapsos_Vetados be Cant_Curriculos + 3"."\n".
				"letting Base_Ubicaciones_Vetadas be Cant_Curriculos + 3 + Cant_Lapsos"."\n".
				"letting Cant_Cols_Actividades be 6 + Cant_Curriculos + Cant_Ubicaciones + Cant_Lapsos"."\n".
				"letting Cant_Cols_Lapsos be 3 + Cant_Actividades"."\n".
				"letting Cant_Cols_Ubicaciones be 3 + Cant_Actividades"."\n\n".
				"letting Actividades : matrix indexed by [int(1..Cant_Actividades),int(1..Cant_Cols_Actividades)] of int(0..) be"."\n\n";

	//build activities
	$content .= "$1 columna para id de actividad\n";
	$content .= "$".($cant_curriculos)." columnas para marcadores booleanos de curriculos\n";
	$content .= "$1 columna para marcador booleano de doble actividad\n";
	$content .= "$1 columna para cantidad de estudiantes\n";
	$content .= "$".($cant_lapsos)." columnas para marcadores booleanos de lapsos vetados\n";
	$content .= "$".($cant_ubicaciones)." columnas para marcadores booleanos de ubicaciones vetadas\n";
	$content .= "$1 columna para id de profesor\n";
	$content .= "$1 columna para cantidad minima de dias\n";
	$content .= "$1 columna para cantidad de clases\n\n";

	$str = "[ ";
	foreach ($Actividades as $key => $actividad) {
		$arr = [];
		array_push($arr, $actividad["id"]);
		foreach ($actividad["curriculos"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		array_push($arr, $actividad["doble_clase"]);
		array_push($arr, $actividad["cant_estudiantes"]);
		
		foreach ($actividad["lapsos_vetados"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		
		foreach ($actividad["ubicaciones_vetadas"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		array_push($arr, $actividad["profesor"]);
		array_push($arr, $actividad["min_working_days"]);
		array_push($arr, $actividad["cant_clases"]);

		$str .= "[".implode(", ", $arr)."],";
	}
	$content .= substr($str, 0, strlen($str) - 1)."]\n\n";
	$content .= "letting Lapsos : matrix indexed by [int(1..Cant_Lapsos),int(1..Cant_Cols_Lapsos)] of int(0..) be\n\n";


	$content .= "$1 columna para id de lapso\n";
	$content .= "$1 columna para id de dia\n";
	$content .= "$1 columna para id de timeslot\n";
	$content .= "$".count($cant_actividades)." columnas para marcadores booleanos de actividades vetadas\n\n";


	$str = "[ ";
	foreach ($Lapsos as $key => $dia) {
		foreach ($dia as $k2 => $lapso) {
			$arr = [];
			array_push($arr, $lapso["id"]);
			array_push($arr, $lapso["dia"]);
			array_push($arr, $lapso["timeslot"]);
			foreach ($lapso["actividades_a_vetar"] as $k => $flagc) {
				array_push($arr, $flagc);
			}
			$str .= "[".implode(", ", $arr)."],";
		}
	}
	$content .= substr($str, 0, strlen($str) - 1)."]\n\n";
	$content .= "letting Ubicaciones  : matrix indexed by [int(1..Cant_Ubicaciones),int(1..Cant_Cols_Ubicaciones)] of int(0..) be\n\n";



	$content .= "$1 columna para id de ubicacion\n";
	$content .= "$1 columna para capacidad\n";
	$content .= "$".($cant_actividades)." columnas para marcadores booleanos de actividades vetadas\n";
	$content .= "$1 columna para id de edificio\n\n";



	$str = "[ ";
	foreach ($Ubicaciones as $key => $ubicacion) {
		$arr = [];
		array_push($arr, $ubicacion["id"]);
		array_push($arr, $ubicacion["capacidad"]);
		foreach ($ubicacion["actividades_a_vetar"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		array_push($arr, $ubicacion["edificio"]);
		$str .= "[".implode(", ", $arr)."],";
	}

	$content .= substr($str, 0, strlen($str) - 1)."]\n\n".
				"letting ID be 1\n".
				"letting INDEX_CURRICULOS be domain int(2..Cant_Curriculos+1)\n".
				"letting INDEX_EVENTOS be domain int(1..Cant_Eventos)\n".
				"letting INDEX_ACTIVIDADES be domain int(1..Cant_Actividades)\n".
				"letting ACTIVIDAD_DE_EVENTO be 2\n".
				"letting CAPACIDAD_DE_UBICACION be 2\n".
				"letting UBICACION_DE_EVENTO be 3\n".
				"letting LAPSO_DE_EVENTO be 4\n".
				"letting PROFESOR_DE_ACTIVIDAD be Cant_Curriculos + Cant_Lapsos + Cant_Ubicaciones + 4\n".
				"letting DIA_DE_LAPSO be 2\n".
				"letting MULTIPLES_CLASES be Cant_Curriculos + 1\n".
				"letting CANT_ESTUDIANTES_DE_ACTIVIDAD be 3 + Cant_Curriculos\n".
				"letting EDIFICIO_DE_UBICACION be 3 + Cant_Actividades\n".
				"letting TOTAL_POSIBLES_VALORES_EVENTO_MATRIZ be ".(max([$cant_eventos, $cant_ubicaciones, $cant_lapsos, $cant_actividades]) + 10)."\n\n";
	$data = $content;
	$namefile = "eprime/cb-ctt/".$name_dir.".param";
	file_put_contents($namefile, $content);
?>