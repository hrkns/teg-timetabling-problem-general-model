<?php
	$algorithm_pe = "language ESSENCE' 1.0"."\n\n";

	$algorithm_pe .= "	given Cant_Caracteristicas : int
						given Cant_Actividades : int
						given Cant_Eventos : int
						given Cant_Lapsos : int
						given Cant_Individuos : int
						given Cant_Ubicaciones : int
						given Cant_Cols_Actividades : int
						given Cant_Cols_Lapsos : int
						given Cant_Cols_Individuos : int
						given Cant_Cols_Ubicaciones : int
						given Actividades : matrix indexed by [int(1..Cant_Actividades),int(1..Cant_Cols_Actividades)] of int(0..)
						given Lapsos : matrix indexed by [int(1..Cant_Lapsos),int(1..Cant_Cols_Lapsos)] of int(0..)
						given Individuos  : matrix indexed by [int(1..Cant_Individuos),int(1..Cant_Cols_Individuos)] of int(0..)
						given Ubicaciones  : matrix indexed by [int(1..Cant_Ubicaciones),int(1..Cant_Cols_Ubicaciones)] of int(0..)

						given INDEX_INDIVIDUOS : int
						given INDEX_EVENTOS : int
						given INDEX_CARACTERISTICAS : int
						given INDEX_LAPSOS_VETADOS : int
						given INDEX_ACTIVIDADES_A_REALIZAR : int

						given ID : int
						given ACTIVIDAD_DE_EVENTO : int
						given UBICACION_DE_EVENTO : int
						given LAPSO_DE_EVENTO : int
						given TIMESLOT_DE_LAPSO : int
						given TOTAL_POSIBLES_VALORES_EVENTO_MATRIZ : int
					";

	$algorithm_pe .= "\n\nfind Eventos : matrix indexed by [int(1..Cant_Eventos),int(1..4)] of int(1..TOTAL_POSIBLES_VALORES_EVENTO_MATRIZ)\n such that \n\n";

	$ini = 1;
	$restricciones = array(
		'	$H1
			forAll x : INDEX_INDIVIDUOS.
				forAll y : INDEX_EVENTOS.
					forAll z : INDEX_EVENTOS.
						(y != z)
						->
						(	
							Eventos[y, LAPSO_DE_EVENTO] != Eventos[z, LAPSO_DE_EVENTO]
							\/
							Individuos[x, 1 + Eventos[y, ACTIVIDAD_DE_EVENTO]] != Individuos[x, 1 + Eventos[z, ACTIVIDAD_DE_EVENTO]]
							\/
							Individuos[x, 1 + Eventos[y, ACTIVIDAD_DE_EVENTO]] = 0
						)
		',
		'	$H2
			forAll x : INDEX_EVENTOS.
				forAll i : INDEX_CARACTERISTICAS.
					Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], i] = Ubicaciones[Eventos[x, UBICACION_DE_EVENTO], i]
					\/
					Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], i] = 0
		',
		'	$H3
			forAll x : INDEX_EVENTOS.
				forAll y : INDEX_EVENTOS.
					(x != y)
					->
					(
						Eventos[x, UBICACION_DE_EVENTO] != Eventos[y, UBICACION_DE_EVENTO]
						\/
						Eventos[x, LAPSO_DE_EVENTO] != Eventos[y, LAPSO_DE_EVENTO]
					)
		',
		'	$H4
			forAll x : INDEX_EVENTOS.
				forAll i : INDEX_LAPSOS_VETADOS.
					Eventos[x, LAPSO_DE_EVENTO] != Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], i]
		',
		'	$H5
			forAll x : INDEX_EVENTOS.
				forAll y : INDEX_EVENTOS.
					(x != y)
					->
					(
						Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], Eventos[y, ACTIVIDAD_DE_EVENTO] + Cant_Lapsos+Cant_Caracteristicas] = 0
						\/
						(
							Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], Eventos[y, ACTIVIDAD_DE_EVENTO] + Cant_Lapsos+Cant_Caracteristicas] = 1
							->
							Lapsos[Eventos[x, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO] > Lapsos[Eventos[y, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO]
						)
						\/
						(
							Actividades[Eventos[x, ACTIVIDAD_DE_EVENTO], Eventos[y, ACTIVIDAD_DE_EVENTO] + Cant_Lapsos+Cant_Caracteristicas] = -1
							->
							Lapsos[Eventos[x, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO] < Lapsos[Eventos[y, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO]
						)
					)
		',
		'	$S2
			forAll x : INDEX_INDIVIDUOS.
				forAll z : INDEX_EVENTOS.
					forAll w : INDEX_EVENTOS.
						(
							z != w 
							/\
							Eventos[z, ACTIVIDAD_DE_EVENTO] != Eventos[w, ACTIVIDAD_DE_EVENTO]
							/\
							Individuos[x, Eventos[z, ACTIVIDAD_DE_EVENTO] + 1] = 1
							/\
							Individuos[x, Eventos[w, ACTIVIDAD_DE_EVENTO] + 1] = 1
						)
						->
						(
							Lapsos[Eventos[z, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO] - Lapsos[Eventos[w, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO] != 1
							/\
							Lapsos[Eventos[w, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO] - Lapsos[Eventos[z, LAPSO_DE_EVENTO], TIMESLOT_DE_LAPSO] != 1
						)
		'
	);
	$fin = (int)pow(2, count($restricciones));

	/*
	faltan las restricciones
		S1
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

		file_put_contents("eprime/pe-ctt/".$bck.".eprime", $algorithm_pe."\n\n".implode(",\n", $min));
		$ini++;
	}
?>