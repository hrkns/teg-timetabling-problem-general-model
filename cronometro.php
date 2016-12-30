<?php
	class Cronometro{
		private $times;

		public function __construct($str = ""){
			$this->times = [];
			$this->construct($str);
		}

		private function construct($str){
			if(gettype($str) == "string"){
				$this->times[$str] = microtime(true);
			}else if(gettype($str) == "array"){
				$val = microtime(true);
				foreach ($str as $key => $value) {
					$this->times[$value] = $val;
				}
			}else if(is_numeric($str)){
				$str = strval($str);
				if(strlen(trim($str)) > 0){
					$this->times[$str] = microtime(true);
				}
			}
		}

		public function start($str = ""){
			$this->construct($str);
		}

		private function getTime($id){
			if(array_key_exists($id, $this->times)){
				return microtime(true) - $this->times[$id];
			}

			return null;
		}

		public function count($str = ""){
			if(gettype($str) == "string"){
				return $this->getTime($str);
			}else if(gettype($str) == "array"){
				$val = [];

				foreach ($str as $key => $value) {
					$val[$value] = $this->count($value);
				}

				return $val;
			}else if(is_numeric($str)){
				$str = strval($str);
				return $this->getTime($str);
			}

			return null;
		}
	}
?>