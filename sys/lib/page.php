<?php

class Page {

	public $current = 1;
	public $next = 2;
	public $prev = 0;
	public $max;
	public $ident = 'page';
	public $total;
	
	function __construct(){
	
		$CC =& get_instance();
		$this->queryurl =& $CC->url->queryurl;
		$this->db =& $CC->db;
	
	}
	
	function set_max( $max ) {
		
		$this->max = $max;
	}
	
	function query( $sql ) {
		
		$start = ( $this->current() - 1 ) * $this->max;
		
		if( $this->total == '' ) {
		
			$resource = $this->db->query( $sql )->go();
			
			$total = mysql_num_rows( $resource );

			$this->total = ceil($total / $this->max);
		}
		
		$sqlimit = $sql . ' LIMIT ' . $start . ', ' . $this->max . ' '; 
		
		$result = $this->db->query( $sqlimit )->fetch();
		
		if( $result == false ) {
			
			$sqlimit = $sql . ' LIMIT 0, ' . $this->max . ' '; 
			
			$result = $this->db->query( $sqlimit )->fetch();

		}
		
		return $result;
	
	}
	
	function current() {
	
		if( in_array( $this->ident, $this->queryurl ) ) {
	
			$page_index = array_keys($this->queryurl, $this->ident);
			
			if( isset( $this->queryurl[($page_index[0] + 1)] ) ) {
			
				$this->current = $this->queryurl[($page_index[0] + 1)];
			
			}

		}
		
		return $this->current;
	
	}
	
	function prev() {
		
		$this->prev = $this->current() - 1;

		return ( $this->prev <= 0 || $this->prev > $this->total ) ? null : $this->prev;
	
	}
	
	function next() {
		
		$this->next = $this->current() + 1;

		return ( $this->next > $this->total ) ? null : $this->next;
	
	}
	

}

?>