<?php
	$content = file_get_contents($dircb.$name_file);

	$i = strpos($content, "Courses:");$i += strlen("Courses:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$cant_actividades = intval(substr($content, $i + 1, $length));

	$i = strpos($content, "Rooms:");$i += strlen("Rooms:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$cant_ubicaciones = intval(substr($content, $i + 1, $length));

	$i = strpos($content, "Days:");$i += strlen("Days:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$cant_dias = intval(substr($content, $i + 1, $length));

	$i = strpos($content, "Periods_per_day:");$i += strlen("Periods_per_day:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$cant_timeslots = intval(substr($content, $i + 1, $length));

	$cant_lapsos = $cant_dias * $cant_timeslots;

	$i = strpos($content, "Min_Max_Daily_Lectures:");$i += strlen("Min_Max_Daily_Lectures:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$Min_Max_Daily_Lectures = explode(" ", trim(substr($content, $i + 1, $length)));
	$min_daily_lectures = $Min_Max_Daily_Lectures[0];
	$max_daily_lectures = $Min_Max_Daily_Lectures[1];

	$i = strpos($content, "UnavailabilityConstraints:");$i += strlen("UnavailabilityConstraints:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$UnavailabilityConstraints = intval(substr($content, $i + 1, $length));

	$i = strpos($content, "RoomConstraints:");$i += strlen("RoomConstraints:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$RoomConstraints = intval(substr($content, $i + 1, $length));

	$i = strpos($content, "Curricula:");$i += strlen("Curricula:");$j = $i + 1;$length = 0;while($content[$j++] != "\n"){$length++;}$cant_curriculos = intval(substr($content, $i + 1, $length));

	$curriculos_template = [];
	for($i = 0; $i < $cant_curriculos; $i++){
		array_push($curriculos_template, 0);
	}

	$lapsos_vetados_template = [];
	for($i = 0; $i < $cant_lapsos; $i++){
		array_push($lapsos_vetados_template, 0);
	}

	$ubicaciones_vetadas_template = [];
	for($i = 0; $i < $cant_ubicaciones; $i++){
		array_push($ubicaciones_vetadas_template, 0);
	}
	$actividades_a_vetar_template = [];
	for($i = 0; $i < $cant_actividades; $i++){
		array_push($actividades_a_vetar_template, 0);
	}

	$Actividades = array();
	$CodeActividades = array();
	$profesores = array();
	$cant_profesores = 0;
	$i = strpos($content, "COURSES:")+strlen("COURSES:");
	while($content[$i++] != "\n");
	$j = 0;
	$cant_eventos = 0;

	while($j++ < $cant_actividades){
		$data = "";
		while($content[$i] != "\n")$data.=$content[$i++];
		$i++;
		$data = explode(" ", $data);

		if(!array_key_exists($data[1], $profesores)){
			$profesores[$data[1]] = ++$cant_profesores;
		}

		array_push($Actividades, [
			"id" => $j,
			"curriculos" => $curriculos_template,
			"lapsos_vetados" => $lapsos_vetados_template,
			"ubicaciones_vetadas" => $ubicaciones_vetadas_template,
			"profesor" => $profesores[$data[1]],
			"cant_clases" => $data[2],
			"min_working_days" => $data[3],
			"cant_estudiantes" => $data[4],
			"doble_clase" => $data[5]
		]);
		$CodeActividades[$data[0]] = $j - 1;
		$cant_eventos += intval($data[2]);
	}

	$Ubicaciones = array();
	$CodeUbicaciones = array();
	$i = strpos($content, "ROOMS:")+strlen("ROOMS:");
	while($content[$i++] != "\n");
	$j = 0;
	while($j++ < $cant_ubicaciones){
		$data = "";
		while($content[$i] != "\n")$data.=$content[$i++];
		$i++;
		$data = explode(" ", $data);
		array_push($Ubicaciones, [
			"id" => $j,
			"capacidad" => $data[1],
			"actividades_a_vetar" => $actividades_a_vetar_template,
			"edificio" => $data[2],
		]);
		$CodeUbicaciones[$data[0]] = $j - 1;
	}

	$i = strpos($content, "CURRICULA:")+strlen("CURRICULA:");
	while($content[$i++] != "\n");
	$j = 0;
	while($j++ < $cant_curriculos){
		$data = "";
		while($content[$i] != "\n")$data.=$content[$i++];
		$i++;
		$data = explode(" ", $data);
		$lim = intval($data[1]);
		for($k = 0; $k < $lim; $k++){
			$Actividades[$CodeActividades[$data[$k+2]]]["curriculos"][$j - 1] = 1;
		}
	}

	$Lapsos = array();
	for($a = 1; $a <= $cant_dias; $a++){
		$Lapsos[strval($a)] = array();
		for($b = 1; $b <= $cant_timeslots; $b++){
			$Lapsos[strval($a)][strval($b)] = array(
				"id" => ($a - 1)*$cant_timeslots+$b,
				"dia" => $a,
				"timeslot" => $b,
				"actividades_a_vetar" => $actividades_a_vetar_template
			);
		}
	}

	$i = strpos($content, "UNAVAILABILITY_CONSTRAINTS:")+strlen("UNAVAILABILITY_CONSTRAINTS:");
	while($content[$i++] != "\n");
	$j = 0;
	while($j++ < $UnavailabilityConstraints){
		$data = "";
		while($content[$i] != "\n")$data.=$content[$i++];
		$i++;
		$data = explode(" ", $data);
		$Actividades[$CodeActividades[$data[0]]]["lapsos_vetados"][intval($data[1]) * $cant_timeslots + intval($data[2])] = 1;
		$Lapsos[strval(intval($data[1])+1)][strval(intval($data[2])+1)]["actividades_a_vetar"][$CodeActividades[$data[0]]] = 1;
	}

	$i = strpos($content, "ROOM_CONSTRAINTS:")+strlen("ROOM_CONSTRAINTS:");
	while($content[$i++] != "\n");
	$j = 0;
	while($j++ < $RoomConstraints){
		$data = "";
		while($content[$i] != "\n")$data.=$content[$i++];
		$i++;
		$data = explode(" ", $data);
		foreach ($data as $key => $value) {
			$data[$key] = trim($value);
		}
		$Actividades[$CodeActividades[$data[0]]]["ubicaciones_vetadas"][$CodeUbicaciones[$data[1]]] = 1;
		$Ubicaciones[$CodeUbicaciones[$data[1]]]["actividades_a_vetar"][$CodeActividades[$data[0]]] = 1;
	}
?>