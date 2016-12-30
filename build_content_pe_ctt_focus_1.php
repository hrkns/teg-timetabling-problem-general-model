<?php
	$porcentaje = ($roof_percentage - (($roof_percentage - $floor_percentage) * ($counter / $cant_ciclos)))/100;
	$cant_actividades = (int)floor($cant_actividades * $porcentaje);
	$n = count($Actividades);

	while($n > $cant_actividades){
		$rnd = rand(0, $n-1);
		$Actividades = array_merge(array_slice($Actividades, 0, $rnd), array_slice($Actividades, $rnd + 1));

		foreach ($Individuos as $key => $individuo) {
			$Individuos[$key]["actividades"] = array_merge(array_slice($Individuos[$key]["actividades"], 0, $rnd), array_slice($Individuos[$key]["actividades"], $rnd + 1));
		}

		foreach ($Actividades as $key => $actividad) {
			$Actividades[$key]["precedencias"] = array_merge(array_slice($Actividades[$key]["precedencias"], 0, $rnd), array_slice($Actividades[$key]["precedencias"], $rnd + 1));
		}

		$n--;
	}


	$content = 	"language ESSENCE' 1.0"."\n\n".
				"letting Cant_Caracteristicas be ".$cant_caracteristicas."\n".
				"letting Cant_Actividades be ".$cant_actividades."\n".
				"letting Cant_Eventos be ".$cant_actividades."\n".
				"letting Cant_Lapsos be ".$cant_lapsos."\n".
				"letting Cant_Individuos be ".$cant_individuos."\n".
				"letting Cant_Ubicaciones be ".$cant_ubicaciones."\n".
				"letting Cant_Cols_Actividades be 1 + Cant_Caracteristicas + Cant_Lapsos + Cant_Actividades"."\n".
				"letting Cant_Cols_Lapsos be 3"."\n".
				"letting Cant_Cols_Individuos be Cant_Actividades + 1"."\n".
				"letting Cant_Cols_Ubicaciones be Cant_Caracteristicas + 2"."\n\n".
				"letting Actividades : matrix indexed by [int(1..Cant_Actividades),int(1..Cant_Cols_Actividades)] of int(0..) be"."\n\n";


	$content .= "$1 columna para id de actividad\n";
	$content .= "$".($cant_caracteristicas)." columnas para marcadores booleanos de caracteristicas\n";
	$content .= "$".($cant_lapsos)." columnas para marcadores booleanos de lapsos vetados\n";
	$content .= "$".($cant_actividades)." columnas para valores de precedencia\n\n";


	$str = "[ ";
	foreach ($Actividades as $key => $actividad) {
		$arr = [];
		array_push($arr, $actividad["id"]);
		foreach ($actividad["caracteristicas"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		foreach ($actividad["lapsos_vetados"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		foreach ($actividad["precedencias"] as $k => $flagc) {
			array_push($arr, $flagc);
		}

		$str .= "[".implode(", ", $arr)."],";
	}

	$content .= substr($str, 0, strlen($str) - 1)."]\n\n";
	$content .= "letting Lapsos  : matrix indexed by [int(1..Cant_Lapsos),int(1..Cant_Cols_Lapsos)] of int(0..) be\n\n";
	$content .= "$1 columna para id de lapso\n";
	$content .= "$1 columna para id de timeslot\n";
	$content .= "$1 columna para peso de lapso\n\n";
	$str = "[ ";
	foreach ($Lapsos as $key => $lapso) {
		$arr = [];
		array_push($arr, $lapso["id"]);
		array_push($arr, $lapso["timeslot"]);
		array_push($arr, $lapso["peso"]);
		$str .= "[".implode(", ", $arr)."],";
	}
	$content .= substr($str, 0, strlen($str) - 1)."]\n\n";
	$content .= "letting Individuos  : matrix indexed by [int(1..Cant_Individuos),int(1..Cant_Cols_Individuos)] of int(0..) be\n";
	$content .= "$1 columna para id de individuo\n";
	$content .= "$".($cant_actividades)." columnas para marcadores booleanos de actividades a realizar\n\n";
	$str = "[ ";
	foreach ($Individuos as $key => $individuo) {
		$arr = [];
		array_push($arr, $individuo["id"]);
		foreach ($individuo["actividades"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		$str .= "[".implode(", ", $arr)."],";
	}
	$content .= substr($str, 0, strlen($str) - 1)."]\n\n";
	$content .= "letting Ubicaciones  : matrix indexed by [int(1..Cant_Ubicaciones),int(1..Cant_Cols_Ubicaciones)] of int(0..) be\n";
	$content .= "$1 columna para id de ubicacion\n";
	$content .= "$".($cant_caracteristicas)." columnas para marcadores booleanos de caracteristicas\n\n";
	$content .= "$1 columna para capacidad\n";
	$str = "[ ";
	foreach ($Ubicaciones as $key => $ubicacion) {
		$arr = [];
		array_push($arr, $ubicacion["id"]);
		foreach ($ubicacion["caracteristicas"] as $k => $flagc) {
			array_push($arr, $flagc);
		}
		array_push($arr, $ubicacion["capacidad"]);
		$str .= "[".implode(", ", $arr)."],";
	}
	$content .= substr($str, 0, strlen($str) - 1)."]\n\n";
	$content .= 
			"letting INDEX_INDIVIDUOS be domain int(1..Cant_Individuos)\n".
			"letting INDEX_EVENTOS be domain int(1..Cant_Eventos)\n".
			"letting INDEX_CARACTERISTICAS be domain int(2..Cant_Caracteristicas+1)\n".
			"letting INDEX_LAPSOS_VETADOS be domain int(Cant_Caracteristicas+2..Cant_Lapsos+Cant_Caracteristicas+1)\n".
			"letting INDEX_ACTIVIDADES_A_REALIZAR be domain int(2..Cant_Actividades+1)\n".

			"letting ID be 1\n".
			"letting ACTIVIDAD_DE_EVENTO be 2\n".
			"letting UBICACION_DE_EVENTO be 3\n".
			"letting LAPSO_DE_EVENTO be 4\n".
			"letting TIMESLOT_DE_LAPSO be 2\n".
			"letting TOTAL_POSIBLES_VALORES_EVENTO_MATRIZ be ".(max([$cant_ubicaciones, $cant_lapsos, $cant_actividades]) + 10)."\n\n";;

	$namefile = "eprime/pe-ctt/".$name_dir.".param";
	file_put_contents($namefile, $content);
?>