<?php

class Form {

public $method 	= "get";
public $submit 	= "submit";
public $rules 		= array();
public $msgs 		= array();
public $errors 	= array();
public$ruleset = array('in','required', 'match', 'alpha', 'numeric', 'alphanum', 'max', 'min', 'exact', 'email');

public function method($method = "get"){
	$this->method = strtolower($method);
}

public function msgs($name, $msgs){
	$name = str_replace(' ','_',$name);
	$this->msgs[$name] = $msgs;
}

public function getMsg($name = null){

	if($name == null) {
		if( isset( $this->errors[0] ) ) {
			return $this->errors[0];
		}
	} else if( isset( $this->msgs[$name] ) ) {
		return $this->msgs[$name];
	}
}

public function data($name, $index = null){

	if($this->method == "get"){
		return get($name, $index);
	}
	
	if($this->method == "post"){
		return post($name, $index);
	}
}

public function rule($name, $rules=array()){
	$name = str_replace(' ','_',$name);
	
	foreach($rules as $rule => $value){
		if(!in_array($rule,$this->ruleset)){
			unset($rules[$rule]);
		}
	}
	
	$this->rules[$name] = $rules;
}

public function error($name = null){

	if(!$this->isSubmit($this->submit)){
		return false;
	}

	if(!@empty($this->errors[$name])){
		return $this->errors[$name];
	}
	
	if(!@empty($this->errors[0])){
		return $this->errors[0];
	}
}

public function isSubmit($submit,$value = null){
	
	$this->submit = $submit;
	$submit = $this->data($submit);

	if( $value != null ) {
	
		return ($submit == $value);
		
	} else {
	
		return !empty($submit);
		
	}
}

public function apply_rule($rule, $input, $value) {
	
	if( is_array( $input ) ) {
	
		$result = array();
		
		foreach( $input as $index => $input_val ) {

			$result[] = $this->apply_rule( $rule, $input_val, $value );
		
		}

		return total_boolean( $result );
	
	} else {
	
		return $this->$rule( $input, $value );
	
	}

}

public function validate($submit,$value){

	$error_count = 0;
	
	if(!$this->isSubmit($submit,$value)){
		return false;
	}
	foreach($this->rules as $name => $rules){
	
		foreach($rules as $rule => $value){
		
			$frule = "__$rule";
			
			$input = $this->data( $name );
			
			$return = $this->apply_rule($frule, $input, $value);
			
			if($return === false){
			
				$error_count++;
				$_name = str_replace('_',' ',$name);
				
				if( isset( $this->msgs[$name][$rule] ) ) {
				
					$errormsg = str_replace(	'@name',	$_name, $this->msgs[$name][$rule]);
					
					$this->errors[$_name] = $errormsg;
					$this->errors[] = $errormsg;
				
				}
			}
		}
	}
	
	return (empty($this->errors) && ($error_count < 1));
}

private function __required($name, $value, $index = null){
	// $name = $this->data($name);
	if($value == 'true'){
		return !empty($name);
	}
	return false;
}

private function __match($name, $value, $index = null){
	// $name = $this->data($name);
	$value = $this->data($value);
	return ($value == $name);
}

private function __alpha($name, $value, $index = null){
	// $name = $this->data($name);
	if($value == 'true'){
		preg_match("/^[A-Za-z ]+$/",$name,$m);
		return !empty($m);
	}
	return false;
}

private function __numeric($name, $value, $index = null){
	// $name = $this->data($name);
	if($value == 'true'){
		preg_match("/^[0-9]+$/",$name,$m);
		return !empty($m);
	}
	return false;
}

private function __alphanum($name, $value, $index = null){
	// $name = $this->data($name);
	if($value == 'true'){
		preg_match("/^[A-Za-z0-9-_ ]+$/",$name,$m);
		return !empty($m);
		// return ctype_alnum($name);
	}
	return false;
}

private function __max($name, $value, $index = null){
	// $name = $this->data($name);
	if(strlen($name) <= $value){
		return true;
	}
	return false;
}

private function __in($name, $value, $index = null){
	// $name = $this->data($name);
	
	if( is_array( $value ) ) {
		if( in_array($name, $value) ){
			return true;
		}
	}
	return false;
}

private function __min($name, $value, $index = null){
	// $name = $this->data($name);
	
	if(strlen($name) >= $value){
		return true;
	}
	
	return false;
}

private function __exact($name, $value, $index = null){
	// $name = $this->data($name);
	if(strlen($name) == $value){
		return true;
	}
	return false;
}

private function __email($name, $value, $index = null){
	// $name = $this->data($name);
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