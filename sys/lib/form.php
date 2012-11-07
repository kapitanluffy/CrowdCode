<?php

class CC_Form {

public $method 	= "get";
public $submit 	= "submit";
public $rules 		= array();
public $msgs 		= array();
public $errors 	= array();
public $ruleset = array('in','required', 'match', 'alpha', 'numeric', 'alphanum', 'max', 'min', 'exact', 'email');

/**
Set the method used by the form

@access public
@param string default get name of the methode
*/
public function method($method = "get"){

	$this->method = strtolower($method);
}

/**
Set the array of messages for an input

@access public
@param string name of the input
@param array an array of messages respective to each rule
@return void
*/
public function msg($name, $msgs){

	$this->msgs[$name] = $msgs;
}

/**
Retrieve the message/s

@access public
@string string optional name of the input
@return string
*/
public function get_msg($name = null){

	if($name == null) {
		if( isset( $this->errors[0] ) ) {
			return $this->errors[0];
		}
	} else if( isset( $this->msgs[$name] ) ) {
		return $this->msgs[$name];
	}
}

/**
Get reference for inputted data

@access public
@param string optional name of the input
@return mixed
*/
public function &data($name = null){

	if($this->method == "get"){
		$data =& $_GET;
	}
	
	if($this->method == "post"){
		$data =& $_POST;
	}

	return array_get_reference_value($data, $name);

}

public function rule($name, $rules=array()){
	
	foreach($rules as $rule => $value){
		if(!in_array($rule,$this->ruleset)){
			unset($rules[$rule]);
		}
	}
	
	$this->rules[$name] = $rules;
}

public function error($name = null){

	if( ! $this->is_submitted($this->submit)){
		return false;
	}

	if(!@empty($this->errors[$name])){
		return $this->errors[$name];
	}
	
	if(!@empty($this->errors[0])){
		return $this->errors[0];
	}
}

/**
This function checks if the form is submitted

@access public
@param string the trigger for the submission of the form
@param string/boolean an optional value compared to the trigger
@return boolean
*/
public function is_submitted($submit,$value = null){

	$trigger =& $this->data($submit, $value);

	if( is_bool($value) ){

		return (empty($trigger) != $value);

	} else if( $value != null ) {
	
		return ($trigger == $value);
		
	} else {

		return !empty($trigger);

	}
}

/**
Apply the rule to the input

@access public
@param string name of the rule
@param string the value of the input
@param string the value of the rule
@return boolean
*/
public function apply_rule($rule, $input, $value) {
	
	if( is_array( $input ) ) {
	
		$result = array();
		
		foreach( $input as $index => $input_val ) {

			$result[] = $this->apply_rule( $rule, $input_val, $value );

		}

		return array_total_boolean( $result );
	
	} else {
	
			return $this->$rule( $input, $value );
	
	}

}

public function validate($submit, $value = null){

	// set the error count
	$error_count = 0;

	// check if the form is submitted
	if( ! $this->is_submitted($submit,$value)){

		return false;
	
	}

	foreach($this->rules as $name => $rules){
	
		foreach($rules as $rule => $value){
		
			// instantiate variables
			$frule = "__$rule";
			$return = true;
			
			// get reference of the input
			$input =& $this->data( $name );

			// apply rule if the input is not empty or the input is required
			if( !empty($input) || array_key_exists('required',$this->rules[$name])){
				$return = $this->apply_rule($frule, $input, $value);
			}
						
			if($return === false){

				$error_count++;
				
				$error_msg = $this->msgs[$name][$rule];

				if(isset($error_msg)) {
				
					$this->errors[$name] = $error_msg;

					$this->errors[] = $error_msg;
				
				}
			}
		}
	}
	
	$result = (empty($this->errors) && ($error_count < 1));

	if($result === true){
		if( isset( $this->msgs['ON_VALIDATION']['true'] ) ) {
			$this->errors['ON_VALIDATION_TRUE'] = $this->msgs['ON_VALIDATION']['true'];
			$this->errors[] = $this->msgs['ON_VALIDATION']['true'];
		}
	}
	else if($result === false){
		if( isset( $this->msgs['ON_VALIDATION']['false'] ) ) {
			$this->errors['ON_VALIDATION_FALSE'] = $this->msgs['ON_VALIDATION']['false'];
			$this->errors[] = $this->msgs['ON_VALIDATION']['false'];
		}
	}


	return (empty($this->errors) && ($error_count < 1));
}

private function __required($name, $value, $index = null){

	if($value == 'true'){
		return !empty($name);
	}
	return false;
}

private function __match($name, $value, $index = null){

	$value =& $this->data($value);
	return ($value == $name);
}

private function __alpha($name, $value, $index = null){

	if($value == 'true'){
		preg_match("/^[A-Za-z ]+$/",$name,$m);
		return !empty($m);
	}
	return false;
}

private function __numeric($name, $value, $index = null){

	if($value == 'true'){
		preg_match("/^[0-9]+$/",$name,$m);
		return !empty($m);
	}
	return false;
}

private function __alphanum($name, $value, $index = null){

	if($value == 'true'){
		preg_match("/^[A-Za-z0-9-_ ]+$/",$name,$m);
		return !empty($m);
		// return ctype_alnum($name);
	}
	return false;
}

private function __max($name, $value, $index = null){

	if(strlen($name) <= $value){
		return true;
	}
	return false;
}

private function __in($name, $value, $index = null){

	
	if( is_array( $value ) ) {
		if( in_array($name, $value) ){
			return true;
		}
	}
	return false;
}

private function __min($name, $value, $index = null){

	
	if(strlen($name) >= $value){
		return true;
	}
	
	return false;
}

private function __exact($name, $value, $index = null){

	if(strlen($name) == $value){
		return true;
	}
	return false;
}

private function __email($name, $value, $index = null){

	// $email = strrchr($name, '@');
	if($value == 'true'){
		// preg_match("/([-_.a-zA-Z0-9]*@[-a-z0-9]+.[a-z]*)/i", $name, $m);
		preg_match("/([\w-\.]+@(?:[\w]+)\.+[a-zA-Z]+)/i", $name, $m);
		return !empty($m);
	}
	return false;
}

}

?>