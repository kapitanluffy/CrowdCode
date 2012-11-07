<?php 

class CC_Session {

	public $data = array();

	function __construct(){
		
		$session_path = TMP;
		
		session_save_path( $session_path );
		
		session_start();
		
		$this->data =& $_SESSION;
		
	}

	function variable($name, $value = null){
		
		if( is_array( $value ) ) {
		
			if( isset( $_SESSION[$name] )  === false ) {
			
				$_SESSION[$name] = array();
			
			}
			
			$_SESSION[$name] = array_merge( $_SESSION[$name], $value);
		
		} 
		
		else if( isset( $_SESSION[$name] ) === false ) {
		
			$_SESSION[$name] = $value;
			
		}
		
		if( isset($_SESSION[$name]) === true && $value == null ) {
		
			return $_SESSION[$name];
		
		}

	}
	
	function gate($name, $url, $value = true){

		$variable = $this->variable( $name );
		
		# if expected variable is equal to specified boolean value then redirect
		if( is_bool($value) && !empty( $variable ) === $value ){

			get_instance()->url->redirect( $url );
			exit;

		}

		# if expected variable is in the specified set of values then redirect
		else if( is_array($value) && in_array($variable, $value) ){

			get_instance()->url->redirect( $url );
			exit;

		}

		# if expected variable is not equal to specified non-boolean value then redirect
		else if( !is_bool($value) && $variable != $value ){

			get_instance()->url->redirect( $url );
			exit;

		}

	}
	
	function destroy($name = null, $value = null){
	
		if( $name == null ) {
		
			session_destroy();
		
		} else if( $value != null) {

			$key = array_keys($_SESSION[$name], $value);
			unset( $_SESSION[$name][$key[0]] );
		
		} else {
		
			unset( $_SESSION[$name] );
		
		}
	}

}

?>