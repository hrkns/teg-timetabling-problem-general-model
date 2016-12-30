<?php
	function IMPRIMIR($str){
		$namefile = "output_simulation.txt";
		if(is_file($namefile)){
			file_put_contents($namefile, file_get_contents($namefile).$str);
		}else{
			file_put_contents($namefile, $str);
		}
		echo $str;
	}
?>