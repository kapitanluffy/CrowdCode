<?php 

class Gear {

	function __construct(){
		global $class_library;
		$class = get_called_class();
		$class = strtolower(substr($class,0,-5));
		$this->basedir = BASEDIR . $class_library.'\gears\\'.$class.'\\';
	}

}

class Sample_Gear extends Gear {

	function __construct(){
		parent::__construct();
	}

}


?>