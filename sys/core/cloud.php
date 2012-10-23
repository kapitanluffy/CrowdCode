<?php 

abstract class Cloud extends Cloud_Core {
	
	//public static $instance;

	public function __construct(){
	
		# Create new instance if not instantiated
		if( parent::$instance == null ) {
		
			# Do the core construct
			parent::__construct();
		
			# Reference instance to self
			parent::$instance =& $this;
			
			# Create Data Warehouse object
			parent::$instance->data = new CC_Data_Warehouse;
			
			# Create Loader object
			parent::$instance->load = new CC_Loader;
			
			# Inherit pre-loaded classes
			foreach( loaded_classes() as $class ){

				$class = strtolower($class);
				
				parent::$instance->$class = load_class( $class );
				
			}

		}
		
		# Instance exists hence a Model is called
		else {
		
			# Recurse instance to model. In this way the model can call methods from its controller
			$this->control = parent::$instance;
		
		}

	}
	
	public static function &get_instance() {
	
		return parent::$instance;
		
	}

	function __get($name) {

		$name = strtolower($name);
		
		if( isset( parent::$instance->$name ) ) {
		
			return parent::$instance->$name;
		
		}
		
		return FALSE;
		
	}
	
	function __destruct() {
	
		parent::__destruct();
		
	}

}

?>