<?php if(!defined('BASEDIR')) exit('No direct script access allowed');

abstract class CC_Debugger {

	public static $start_time;
	public static $end_time;
	
	public static function start_timer(){
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		self::$start_time = $mtime; 
		
		return self::$start_time;
	}
	
	public static function end_timer(){
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$endtime = $mtime; 
		self::$end_time = ($endtime - self::$start_time);
		
		return self::$end_time;
	}

	public function debug( $data, $verbose = false ){
	
		echo $this->__dump_var($data, $verbose);
		
	}

	public function breakpoint( $data, $verbose = false ) {

		echo $this->__dump_var($data, $verbose);

		die('CC Breakpoint');
		
	}
	
	private function __dump_var( $data, $verbosity = false ){
	
		if( $verbosity ){
			
			$data = var_export($data, true);
			
		} else {
	
			$data = print_r($data, true);
		
		}
		
		return '<pre>' . $data . '</pre>';
		
	}

	public function __toString(){

		return $this->__dump_var($this);
		
	}

}

class Cloud_Core extends CC_Debugger {

	public static $instance = null;
	
	function __construct(){
	
		if( self::$instance == null ) {
		
			CC_Debugger::start_timer();
			
			$this->PHP_VER = phpversion();
		
		}
	
	}

	function __destruct() {
	
		$this->execution_time = CC_Debugger::end_timer();
	
	}
	
}

class CC_Data_Warehouse {

	private $xss_filter = false;
	
	private $sqli_filter = false;

	public function sqli_filter(){
		
		$this->sqli_filter = true;
		
		return $this;
	}
	
	public function xss_filter(){
		
		$this->xss_filter = true;
		
		return $this;
	}
	
	private function __sqli_filter($value){
		
		$CC =& Cloud::get_instance();
		
		$this->sqli_filter = false;
	
		return $CC->db->escape($value);
	}
	
	private function __xss_filter($value){
		
		$this->xss_filter = false;
	
		return htmlspecialchars($value);
	}
	
	public function __set($name, $value){
	
		if( $this->xss_filter ) $value = $this->__xss_filter($value);
		
		if( $this->sqli_filter ) $value = $this->__sqli_filter($value);

		$this->$name = $value;
	}
	
}

class CC_Loader {

	public $control = array();
	
	public $library = array();
	
	public $model = array();

	function register_object( $name, $type ) {
	
		array_push( $this->$type, $name );
	
	}

	function control($controller, $method, $queryurl = null) {
	
		if( class_exists( $controller ) === false ) {
		
			include CONTROLLERS . $controller . '.php';
		
		}
		
		if( $queryurl == null ) {
		
			$queryurl =& Cloud::get_instance()->url->queryurl;
		
		}
		
		$this->register_object( $controller, 'control' );
		
		call_user_func_array( array($controller, $method), $queryurl );
	
	}

	function library($name){
	
		$name = strtolower( $name );
		
		Cloud::get_instance()->$name = load_class($name);

		$this->register_object( $name, 'library' );
		
		return Cloud::get_instance()->$name;
		
	}
	
	function model($name){

		$name = strtolower($name);
		
		include MODELS . 'm.' . $name . '.php';
		
		$class = $name . '_Model';
		
		Cloud::get_instance()->$name = new $class;

		$this->register_object( $name, 'model' );
		
		return Cloud::get_instance()->$name;
	}

}

?>