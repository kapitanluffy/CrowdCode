<?php

function &load_class( $class = null, $alias = null ){
	
	static $loaded_objects = array();
	
	static $object_aliases = array();
	
	$class = strtolower( $class );
	
	$alias = strtolower( $alias );
	
	# if alias is empty, set the alias to current class
	if( $alias == null ) {
	
		$alias = $class;
	
	}
	
	# if class is false, return false
	if( $class == false ) {
	
		return $class;
	
	}
	
	# check if class is instantiated
	else if( in_array( $class, $object_aliases ) ) {

		return $loaded_objects[ $class ];
	
	}
	
	# check if class exists in app library
	else if( file_exists( APPLIB . $class . '.php' ) ){
	
		include SYSLIB . $class . '.php';
	
	}
	
	# check if class exists in sys library
	else if( file_exists( SYSLIB . $class . '.php' ) ){

		include SYSLIB . $class . '.php';
	
	}
	
	else {
	
		exit( $class . ' class not found' );
	
	}
	
	# instantiate class
	$loaded_objects[ $alias ] = new $class();
	
	# register alias
	$object_aliases[ $class ] = $alias;
	
	# register loaded class
	loaded_classes( $alias );
	
	return $class;

}

function loaded_classes( $class = null ) {

	static $loaded_classes = array();
	
	if( $class != null ) {
	
		$loaded_classes[] = $class;
	
	}
	
	return $loaded_classes;

}

function &get_instance() {

	return Cloud::get_instance();

}

function typecast( &$value, $type ) {

	/* watch?=kyMyY_mNuA0 */

	switch( strtolower( $type ) ):
	
		case 'int': 
			$value = (int) $value;
		break;
		case 'str': 
			$value = (string) $value;
		break;
		case 'bool': 
			$value = (bool) $value;
		break;
		case 'float': 
			$value = (float) $value;
		break;
		case 'array': 
			$value = (array) $value;
		break;
		case 'object': 
			$value = (object) $value;
		break;
		case 'null': 
			$value = (unset) $value;
		break;
	
	endswitch;

}

function rand_str( $length = '6', $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`~!@#$%^&*()_+-={}|[]\:";\'<>,.?/'){

	$charset_len = strlen( $charset );

	$random_string = '';
	
	for ($i = 0; $i < $length; $i++)
    {
        $random_pick = mt_rand(1, $charset_len);

        $random_char = $charset[$random_pick-1];

        $random_string .= $random_char;
    }

	return $random_string;
	
}

function get($name = '__ALL__', $index = null){
	if(isset($_GET[$name])){
	
		if(isset($_GET[$name][$index])){
		
			return html_escape( $_GET[$name][$index] );
			
		}
	
		return html_escape( $_GET[$name] );
		
	}
	else if( $name == '__ALL__' ) {
		
		return array_map('html_escape', $_GET );
		
	}
	
	return false;

}

function post($name = '__ALL__', $index = null){

	if( !empty($_POST[$name]) ){
	
		if(!empty($_POST[$name][$index]) && $index != null){
		
			return html_escape( $_POST[$name][$index] );
			
		}
	
		return html_escape( $_POST[$name] );
		
	}
	
	else if( $name == '__ALL__' ) {
		
		return array_map('html_escape', $_POST);
		
	}
	
	return false;
	
}

function total_boolean( $array_of_bool ) {

	$count = count( $array_of_bool );

	$total = array_sum( $array_of_bool );
	
	return ($total == $count);

}

function html_escape( $data ) {

	if( is_array( $data ) ) {
	
		$result = null;
		
		foreach( $data as $index => $value ) {
		
			$result[$index] = html_escape( $value );
		
		}
		
		return $result;
	
	}
	
	return htmlspecialchars( trim( $data ) );

}

?>