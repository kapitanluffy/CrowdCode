<?php

class CC_mySQL implements Database {
	
	private $db;
	private $pass;
	private $user;
	private $server;
	private $link;
	private $resource;
	public $storage = array();

	function startup($args){

		$this->connect($args['db'], $args['pass'], $args['user'], $args['server']);

	}
	
	function connect($db,$pass,$user,$server){
		
		$this->db 			= $db;
		$this->pass			= $pass;
		$this->user 		= $user;
		$this->server 	= $server;
		$this->link 		= @mysql_connect($server,$user,$pass);
		
		if($this->link === false)
			trigger_error('Error establishing database connection.');
		
		if( !empty( $db ) )
			$this->db($db);
			
	}
	
	function db($db) {
		$this->db = $db;
		
		if($db_ok = mysql_select_db($this->db,$this->link) === false)
			trigger_error($this->db . ' does not exist.');
		
		return $db_ok;
	}

	function __do_query($sql) {

		$this->resource = mysql_unbuffered_query($sql,$this->link);
		
		return $this->resource;	
	}
	
	function query($string = null, $name = 'CC_QUERY'){
	
		if(!empty($string)){
			$this->storage[$name]['query'] = $string;
		}

		return $this;
	}
	
	function go( $name = 'CC_QUERY', $unbuff = false ) {
	
		if( $unbuff === true ) {
			return $this->__do_query($this->storage[$name]['query']);
		}
		
		else {
			return mysql_query( $this->storage[$name]['query'], $this->link );
		}
	
	}
	
	public function escape($string){
		return mysql_real_escape_string($string, $this->link);
	}

	function fetch($name = null, $identifier = null, $search = null){
	
		if( $name == null ) {
			$name = 'CC_QUERY';
		}
		
		$resource = $this->__do_query($this->storage[$name]['query']);
		
		if( is_bool($resource) ){
			return $resource;
		}
		
		$result = array();
		$num = 1;
		
		while($row = mysql_fetch_assoc($resource)){

			$index = 'row'.$num;
			
			if($identifier != null){

				if(!empty($row[$identifier])){
				
					if($search == $row[$identifier]){
						mysql_free_result($resource);
						$this->storage[$name]['result'] = $row;
						return $row;
					}
					
					$index = $row[$identifier];
				}
			}

			$result[$index] = $row;
			$num++;
		
		}
		
		$this->storage[$name]['result'] = $result;
		
		mysql_free_result($resource);
		return $result;
		
	}
	
	function fetch_raw($name, $identifier = null, $search = null){
		$resource = $this->__do_query($this->storage[$name]['query']);
		
		if( is_bool($resource) ){
			return $resource;
		}
		
		$result = array();
		$num = 1;
		
		while($row = mysql_fetch_array($resource, MYSQL_NUM)){
			$index = 'row'.$num;
			$result[$index] = $row;
			$num++;
		}
		
		$this->storage[$name]['result'] = $result;
		return $result;
	}
	
	function result($name = null){
		
		if($name == null){
			// reset($this->storage);
			// $name = key($this->storage);
			$name = 'CC_QUERY';
			return $this->__do_query($this->storage[$name]['query']);
		}
		
		if(isset($this->storage[$name]['result'])){
			return $this->storage[$name]['result'];
		}

	}
	
	function get( $result, $column, $row = '1' ){
		
		return $result['row'.$row][$column];
		
	}
	
	function load_query_file( $filename, $variables = array() ) {

		extract($variables);
		
		include QUERY . $filename . '.php';
		
		${$filename} = trim( ${$filename} );
		
		return ${$filename};
		
	}
}

?>