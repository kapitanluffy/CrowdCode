<?php 

class CC_URL {

	public $baseurl;
	public $fullurl;
	public $queryurl;

	public function __construct(){

		global $queryUrl, $controller, $method, $default_controller, $default_method;
		
		$c = $controller;
		if($controller == $default_controller) $c = '';
		
		$m = $method;
		if($method == $default_method) $m = '';
		
		$querystr = implode('/', $queryUrl);
		
		$this->baseurl		= $this->add_http( trim(BASEURL, '/') ) . '/';

		define('CONTROLURL', $this->add_http( trim($this->baseurl . $c, '/') ) );
		
		define('ACTIONURL', $this->add_http( trim($this->baseurl . $c . '/' . $m, '/') ) );
		
		$this->actionurl	= ACTIONURL  . '/';
		
		$this->fullurl		= $this->add_http( trim($this->actionurl . $querystr ,'/') ) . '/';
		
		$this->queryurl		= $queryUrl;
	}

	public function query($searched_index = false){

		if( $searched_index === false ) {
		
			return $this->queryurl;
		
		}
		
		
		if( preg_match("/^[0-9]+$/",$searched_index) == 1 ) {
			$index = $searched_index;

		} 
		
		else {
		
			$index = array_search( $searched_index, $this->queryurl );
		
		}

		if( $index !== false ) {

				$index += 1;
				
				return isset( $this->queryurl[$index] ) ? $this->queryurl[$index] : false ;
				
		}
		
		return false;
		
	}
	
	public function link($string, $current = true){
		$link = ($current === true) ? $this->fullurl .$string : $this->baseurl .$string ;
		return $this->add_http($link);
	}
	
	public function add_http($url, $secure = false){
		
		$http = 'http://';
		if($secure === true){
			$http = 'https://';
		}
		
		if(strstr($url,$http) == ''){
			$url = $http . $url;
		}
		
		return $url;
	}
	
	public function assets($file){

		return $this->link(ASSETS . $file, false);
	}
	
	public function css($file){
		return $this->assets('css/'.$file.'.css');
	}
	
	public function js($file){
		return $this->assets('js/'.$file.'.js');
	}
	
	public function redirect( $url=' ', $current = false ) {
		
		header('Location: '. $this->link($url, $current).'/' );
		exit;
		
	}
}

?>