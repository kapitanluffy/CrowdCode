<?php 

class CC_Upload {

	public $multiple = false;
	public $ruleset = array('required','success','extension','error');
	public $rules = array();
	public $success_msg = array();
	private $data = null;
	
	function __construct() {
	
		$this->upload_dir = 'uploads' . DS;
		
	}
	
	function folder( $dir ) {
	
		$this->upload_dir .= $dir . DS ;
		
		if( ! file_exists( ASSETS . $this->upload_dir ) ) {
		
			trigger_error(ASSETS . $this->upload_dir . ' does not exist');
			mkdir( ASSETS . $this->upload_dir, 777 );
			
		}
	}
	
	function data( $name ) {
		if( isset( $this->data[$name] ) ) {
			return $this->data[$name];
		}
	}
	
	function files( $name, $index = 'name' ) {
		if( $this->multiple !== false ) {
		
			return isset( $_FILES[$this->multiple][$index][$name] ) ? $_FILES[$this->multiple][$index][$name] : false;
			
		}
		
		if( isset( $_FILES[$name][$index] ) && !empty( $_FILES[$name][$index] ) ) {
			return $_FILES[$name][$index];
		} else {
			return false;
		}
	}
	
	function multiple( $upload ) {
		$this->multiple = $upload;
		return $this;
	}
	
	public function msg($name, $msgs){
	
		$name = str_replace(' ','_',$name);
		$this->msgs[$name] = $msgs;
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
	
	function __extension($name, $value) {
		$ext = trim( $this->get_extension( $this->files($name) ), '.');

		if( ! in_array( $ext, $value) ) {
			return false;
		}
		
		return true;
	}
	
	private function __required($name, $value){

		if($value == 'true'){
			$is_file = $this->files($name);
			return !empty( $is_file );
		}
		return false;
	}
	
	public function is_file_uploaded($name){
	
		return $this->files($name);

	}
	
	function is_uploaded( $name ) {

		if( $this->is_required( $name ) ) {
		
			return is_array( $this->data );
			
		} else if( empty( $this->errors ) ) {
		
			return true;
		
		} else {
		
			return false;
		
		}

	}
	
	public function validate($upload){
		
		# create success rule for a single upload
		if( $this->multiple === false ) {
		
			if( ! isset( $this->rules[$upload]['success'] ) ) {
			
				$this->rules[$upload]['success'] = true;
				
			}
			
		}
		
		# start checking rules for each uploaded file
		foreach($this->rules as $name => $rules){

			# create a success rule for multiple upload
			if( $this->multiple != false ) {
			
				if( ! isset( $this->rules[$name]['success'] ) ) {
				
					$this->rules[$name]['success'] = true;
					
				}
				
			}
		
			# get uploaded photo's filename
			$file_name = $this->files($name);
			
				# check rules of each uploaded photo
				foreach($rules as $rule => $value){
					
					$return = true;

					if( $rule != 'success' ) {

						# if upload is required or a file is uploaded
						if( $this->is_file_uploaded($name) || $this->is_required( $name ) ) {
						
							# hence, run the rule
							$frule = "__$rule";
							$return = $this->$frule($name, $value);
							
						}
					
					}
					
					# if the rule returns false
					if($return === false){
						
						# get the corresponding error message for that rule
						if( isset( $this->msgs[$name][$rule] ) ) {
							$errormsg = str_replace( '@name',	 $file_name, $this->msgs[$name][$rule]);
							$this->errors[$name] = $errormsg;
							$this->errors[] = $errormsg;

						}
					}
				}
				
				# if there are no errors
				if( empty( $this->errors ) ) {
					
					# run the success rule
					$this->__success( $name, $this->rules[$name]['success'] );
					
				}

		}
		
		return $this->is_uploaded( $upload );
	}
	
	function get_extension( $filename ) {

		return strrchr($filename, '.');
	}
	
	function get_filename( $filename ) {

		return basename($filename, $this->get_extension( $filename ) );
	}
	
	function __create_file_hash( $file, $algo = 'md5' ) {

		return hash_file( $algo, $file );
	}
	
	function is_required( $name ) {
	
		if( isset( $this->rules[$name]['required'] ) ) {
		
			return isset( $this->rules[$name] );
			
		}
		
		return false;
	
	}
	
	function __success( $name, $value = null ) {
	
		# return false if there are errors
		if( ! empty( $this->errors ) ) {
			return false;
		}
	
		# return false if a file is not uploaded
		if( ! $this->is_file_uploaded( $name ) ){
			return false;
		}
		$ext 	= $this->get_extension( $this->files($name) );
		$file 		= $this->get_filename( $this->files($name) );
		$tmp 	= $this->files($name, 'tmp_name');
		
		if( $value != null && ! is_bool( $value ) ) {
		
			$value = str_replace('@hash', $this->__create_file_hash( $tmp ), $value);
			$value = str_replace('@name', $file, $value);			
			$file = $value;
			
		}

			die(ASSETS . $this->upload_dir);
		if( ! move_uploaded_file( $tmp, ASSETS . $this->upload_dir . $file . $ext ) ) {
		
			$errormsg = 'Error moving to upload directory '. $file ;
			$this->errors['moving'] = $errormsg;
			$this->errors[] 			 = $errormsg;
			return false;
			
		} else {
		
			$dir = str_replace(DS, '/', $this->upload_dir);
			$this->data[$name]['path'] 	= $dir . $file . $ext;
			$this->data[$name]['name'] 	= $file . $ext;
			
			if( isset( $this->msgs[$name]['success'] ) ) {
				$this->data[$name]['msg'] = $this->msgs[$name]['success'];
				$this->success_msg[$name] = $this->msgs[$name]['success'];
				$this->success_msg[] = $this->msgs[$name]['success'];
			}
			
		}

		return true;

	}
	
	public function get_msg($name = null){
	
		if($name == null) {
		
			if( isset( $this->errors[0] ) ) {
			
				return $this->errors[0];
				
			} else {
			
				if( isset( $this->success_msg[0] ) ) {
					return $this->success_msg[0];
				}
			}
			
		} else {
			
			if( isset( $this->msgs[$name] ) ) {
		
				return $this->msgs[$name];
			
			} else {
			
				return $this->success_msg[$name];

			}
			
		}
		
	}

}

?>