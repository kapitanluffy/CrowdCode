<?php

class CC_Query {

	public function __construct( $string ) {
	
		$this->query = $string;
	
	}
	
	public function execute() {
	
		return mysql_query( $this->query );
	
	}
	
	public function results() {
	
		$resource = $this->execute( $this->query );
	
		$results = array();
	
		while( $row = mysql_fetch_assoc( $resource ) ) {
		
			$results[] = $row;
		
		}
		
		return $results;
	
	}

}

class CC_Table extends CRUD {

	public function __construct( $name ) {
		
		parent::__construct();
		
		$this->name = $name;
		
	}
	
	public function build_columns() {
	
		if( is_array( $columns ) ) {
	
	}
	
	public function select( $columns = "*" ) {
	
		$columns = func_get_args();

		
			$string = '';
		
			foreach( $columns as $column ) {
				
				$string .= "$column,";
				
			}
			
			$columns = trim( $string, "," );
		
		}
		
		// $query_name = str_replace(",",
		$query_name = 'select_'. $this->name;
		
		$query = new Query("SELECT $columns FROM ". $this->name ." ");
		
		return $query->results();
	
	}
	
	public function join( $table, $col_1, $col_2 ) {
	
	
	
	}

}

class CC_CRUD {

	protected $db;
	
	public $tables = array();

	public function __construct() {
	
		$this->db =& get_instance()->db;
	
	}

	public function table( $name ) {
		
		$this->$name = new Table( $name );
		
		return $this->$name;
	}
	
	private function __is_table() {
	
		return ('Table' == get_called_class());
	
	}

}

?>