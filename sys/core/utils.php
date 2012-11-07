<?php

function &tpl_data($var = null){
	
	static $data;

	if($data == null){
		$data =& get_instance()->data;
	}

	if($var != null){

		return $data->$var;

	}

	return $data;

}

function tpl_compare($var1,$var2 = true,$condition = '=='){

	$var1 =& tpl_data($var1);
	$var2 =& tpl_data($var2);

	switch($condition){
		case '==': 
			return ($var1 == $var2);
		break;
		case '!=': 
			return ($var1 != $var2);
		break;
		case '>=': 
			return ($var1 >= $var2);
		break;
		case '<=': 
			return ($var1 <= $var2);
		break;
		case '>': 
			return ($var1 > $var2);
		break;
		case '<': 
			return ($var1 < $var2);
		break;
		default:
			if( !empty($var1) ) { return true; } else { return false; }
		break;
	}

}

class TPL_Utils {

	public static $loop_status;
	public static $data;

	static function &data($var = null){
		
		if(self::$data == null){
			self::$data =& get_instance()->data;
		}

		$data =& self::$data;

		if( $var != null && isset($data->$var) ){

			return $data->$var;
		
		}

		return $data;

	}

	static function say($var){
		
		$data =& self::data();

		if( isset($data->$var) ) {
			
			if( self::$loop_status == true ) {
				
				$var_value = $var . '_value';

				return $data->$var_value; 
			
			} else {

				return $data->$var; 
				
			}
		
		} else { 
			return null;
		}

	}

	static function loop($var){

		$value =& self::data($var);

		if( self::$loop_status == false && $value != null ){
			self::$loop_status = true;
			return 'foreach($'.$var.' as $'.$var.'_index => $'.$var.'_value):';
		}

	}

	static function end_loop(){

		if(self::$loop_status == true){
			self::$loop_status = false;
			return "endforeach;";
		}

	}

}

/**
load_class()
@description 
	loads classes from the applib and syslib
@string class
	name of the class to be loaded
@string alias
	alias of the class to be used
@return
	returns the instance of the loaded class
*/

function &load_class( $class = null, $alias = null ){
	
	static $loaded_objects = array();
	
	$class = strtolower( $class );
	
	$alias = strtolower( $alias );
	
	$loaded_classes = loaded_classes();

	# if alias is empty, set the alias to current class
	if( $alias == null ) {
	
		$alias = $class;
	
	}
	
	# if class is false, return false
	if( $class == false ) {
	
		return $class;
	
	}
	
	# check if class is instantiated
	else if( in_array( $class, $loaded_classes ) ) {

		return $loaded_objects[ $class ];
	
	}
	
	# check if class exists in app library
	else if( file_exists( APPLIB . $class . '.php' ) ){
	
		include APPLIB . $class . '.php';
	
		# instantiate class
		$loaded_objects[ $alias ] = new $class();
	
	}
	
	# check if class exists in sys library
	else if( file_exists( SYSLIB . $class . '.php' ) ){

		include SYSLIB . $class . '.php';
	
		$cc_class = 'CC_' . $class;

		# instantiate class
		$loaded_objects[ $alias ] = new $cc_class();
	
	}
	
	else {
	
		die( $class . ' class not found' );
	
	}
	
	# register loaded class
	loaded_classes( $alias );
	
	return $loaded_objects[ $alias ];

}

/**
loaded_classes()
@description
	stores all the names and aliases of the classes loaded by load_class() function.
	these names are retrieved later to be used as a reference for classes.
@string $class 
	name / alias of the class loaded, default null
@return
	returns an array of the names of the loaded classes
*/

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

function &get($name = null){

	if($name != null){
		return $_GET[$name];
	}

	return $_GET;

}

function &post($name = null){

	if($name != null){
		return $_POST[$name];
	}

	return $_POST;

}

function array_index_anatomy($string){
	
	$name_exp = '/([0-9a-zA-Z _-]+)(\[[\[\]0-9a-zA-Z _-]+\]|)/';

	$index_exp = '/\[([0-9a-zA-Z _-]+)\]/';

	$result = array(
		'name'=>null,
		'index'=>array()
	);

	preg_match($name_exp, $string, $name);

	$result['name'] = $name[1];

	if(isset($name[2])){
		
		preg_match($index_exp, $name[2], $index);
		
		if(isset($index[1])){

			$result['index'][] = $index[1];

		}
	}

	return $result;

}

function &array_index_reference(&$array, $index){

	$result = null;

	if(empty($index)){
		return $array;
	}

	foreach($index as $i){
		// echo 'x';var_dump(isset($array[$i]));

		if(!isset($result)){

			if(!isset($array[$i])){
				$array[$i] = array();
			}

			$result =& $array[$i];

		}	else {

			if(!isset($result[$i])){
				$result[$i] = array();
			}

			$result =& $result[$i];

		}

	}

	return $result;

}

function &array_get_reference_value(&$array, $index){

	$anatomy = array_index_anatomy($index);

	return array_index_reference($array[$anatomy['name']], $anatomy['index']);

}

function array_total_boolean( $array_of_bool ) {

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