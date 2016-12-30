<?php
	$algorithm_cb = "language ESSENCE' 1.0"."\n\n";

	$algorithm_cb .= "	given Cant_Actividades : int
						given Cant_Lapsos : int
						given Cant_Eventos : int
						given Cant_Ubicaciones : int
						given Cant_Curriculos : int
						given Base_Lapsos_Vetados : int
						given Base_Ubicaciones_Vetadas : int
						given Cant_Cols_Actividades : int
						given Cant_Cols_Lapsos : int
						given Cant_Cols_Ubicaciones : int
						given Actividades : matrix indexed by [int(1..Cant_Actividades),int(1..Cant_Cols_Actividades)] of int(0..)
						given Lapsos : matrix indexed by [int(1..Cant_Lapsos),int(1..Cant_Cols_Lapsos)] of int(0..)
						given Ubicaciones  : matrix indexed by [int(1..Cant_Ubicaciones),int(1..Cant_Cols_Ubicaciones)] of int(0..)

						given ID : int
						given INDEX_CURRICULOS : int
						given INDEX_EVENTOS : int
						given INDEX_ACTIVIDADES : int
						given ACTIVIDAD_DE_EVENTO : int
						given CAPACIDAD_DE_UBICACION : int
						given UBICACION_DE_EVENTO : int
						given LAPSO_DE_EVENTO : int
						given PROFESOR_DE_ACTIVIDAD : int
						given DIA_DE_LAPSO : int
						given MULTIPLES_CLASES : int
						given CANT_ESTUDIANTES_DE_ACTIVIDAD : int
						given EDIFICIO_DE_UBICACION : int
						given TOTAL_POSIBLES_VALORES_EVENTO_MATRIZ : int
					";

	$algorithm_cb .= "\n\nfind Eventos : matrix indexed by [int(1..Cant_Eventos),int(1..6)] of int(1..TOTAL_POSIBLES_VALORES_EVENTO_MATRIZ)\n such that \n\n";

	$ini = 1;
	$restricciones = array(
		'$H6
		forAll x : INDEX_EVENTOS.
			forAll y : INDEX_EVENTOS.
				(x != y)
				->
				!(
					Eventos[x, LAPSO_DE_EVENTO] = Eventos[y, LAPSO_DE_EVENTO]
					/\
					Eventos[x, UBICACION_DE_EVENTO] = Eventos[y, UBICACION_DE_EVENTO]
				)',
		'$H7
		forAll x : INDEX_EVENTOS.
			forAll y : INDEX_EVENTOS.
				(	
					x != y
					/\
					! forAll a : INDEX_CURRICULOS.
					(
						Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], a] != Actividades[Eventos[y, ACTIVIDAD_DE_EVENTO], a]
						\/
						Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], a] = 0
					)
				)
				->
				(
					Eventos[x, LAPSO_DE_EVENTO] != Eventos[y, LAPSO_DE_EVENTO]
				)',
		'$revisar esta
		$H8
		forAll x : INDEX_EVENTOS.
			forAll y : INDEX_EVENTOS.
				(
					x != y
					/\
					Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], PROFESOR_DE_ACTIVIDAD] = Actividades[Eventos[y, ACTIVIDAD_DE_EVENTO], PROFESOR_DE_ACTIVIDAD]
				)
				->
				(
					Eventos[x, LAPSO_DE_EVENTO] != Eventos[y, LAPSO_DE_EVENTO]
				)',
		'$H9
		forAll x : INDEX_EVENTOS.
			forAll y : INDEX_EVENTOS.
				(	
					x != y 
					/\ 
					Eventos[x, ACTIVIDAD_DE_EVENTO] = Eventos[y, ACTIVIDAD_DE_EVENTO]
					/\
					Eventos[x, LAPSO_DE_EVENTO] != Eventos[y, LAPSO_DE_EVENTO]
					/\
					Lapsos[Eventos[x, LAPSO_DE_EVENTO], DIA_DE_LAPSO] = Lapsos[Eventos[y, LAPSO_DE_EVENTO], DIA_DE_LAPSO]
				)
				->
				(
					Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], MULTIPLES_CLASES] = 1
				)',
		'$H10
		forAll x : INDEX_EVENTOS.	
			Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], Base_Lapsos_Vetados + Eventos[x, LAPSO_DE_EVENTO]] = 0',

		'$H11
		forAll x : INDEX_EVENTOS.
			Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], Base_Ubicaciones_Vetadas + Eventos[x, UBICACION_DE_EVENTO]] = 0',

		'$S4
		forAll x : INDEX_EVENTOS.
			Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], CANT_ESTUDIANTES_DE_ACTIVIDAD] <= Ubicaciones[Eventos[x, UBICACION_DE_EVENTO], CAPACIDAD_DE_UBICACION]',
		'$S7
		forAll x : INDEX_ACTIVIDADES.
			forAll y : INDEX_EVENTOS.
				forAll z : INDEX_EVENTOS.
					(
						y != z
						/\
						Eventos[y, ACTIVIDAD_DE_EVENTO] = Eventos[z, ACTIVIDAD_DE_EVENTO]
						/\
						Eventos[y, ACTIVIDAD_DE_EVENTO] = Actividades[x, ID]
					)
					->
					(
						Eventos[y, UBICACION_DE_EVENTO] = Eventos[z, UBICACION_DE_EVENTO]
					)',
	);
	$fin = (int)pow(2, count($restricciones));

	/*
	faltan las restricciones
		S5
		S8
	*/

	while($ini <= $fin){
		$binary = decbin($ini);

		while(strlen($binary) < count($restricciones)){
			$binary = "0".$binary;
		}

		$bck = $binary;
		$tmp = [];
		$n = strlen($binary);
		$i = 0;
		while($i < $n){
			array_push($tmp, $binary[$i++]);
		}

		$binary = $tmp;
		$l = 0;
		$min = [];

		foreach ($binary as $key => $value) {
			if($value == '1'){
				array_push($min, $restricciones[$l]);
			}

			$l++;
		}

		file_put_contents("eprime/cb-ctt/".$bck.".eprime", $algorithm_cb."\n\n".implode(",\n", $min));
		$ini++;
	}
?>