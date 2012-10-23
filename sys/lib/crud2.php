<?php

class CRUD {

	public $tb;
	public $db;
	
	function __construct(){
		global $db;
		$this->db =& $db;
	}
	
	function tb($string){
		$this->tb = $string;
		return $this;
	}
	
	function options($string){
		$this->db->storage['CRUD']['query'] .= $string;
		return $this;
	}
	
	function fetch(){
		return $this->db->fetch('CRUD');
	}
	
	function select($data = null){
		if($data == ''){
			$data = (array) '*';
		} else {
			$data = func_get_args();
		}
		
		if(is_array($data)){
			$cStr = '';
		
			foreach($data as $column => $value){
				$cStr .= $value . ',';
			}
			
			$cStr = trim($cStr,',');
			
			$query = 'SELECT '.$cStr.' FROM '. $this->tb .' ';
			$this->db->query('CRUD',$query);
			
			return $this;
		}

	}
	
	function insert($data){
		if(is_array($data)){
			$cStr = '';
			$vStr = '';			
		
			foreach($data as $column => $value){
				$cStr .= $column . ',';
				$vStr .= "'" . mysql_real_escape_string($value) . "',";
			}
			
			$cStr = trim($cStr, ',');
			$vStr = trim($vStr, ',');
			
			$vStr = str_replace( 'null', null, $vStr );
			
			$query = 'INSERT INTO '.$this->tb.' ('.$cStr.') VALUES ('.$vStr.') ';
			$this->db->query('CRUD',$query);
			
			return $this;
		}
	}
	
	function update( $data ) {
	
		if(is_array($data)){
			$cStr = '';
			$vStr = '';			
		
			foreach($data as $column => $value){
				$cStr .= $column . ',';
				$vStr .= "'" . mysql_real_escape_string($value) . "',";
			}
			
			$cStr = trim($cStr, ',');
			$vStr = trim($vStr, ',');
			
			$vStr = str_replace( 'null', null, $vStr );
			
			$update_string = '';
			foreach( $vStr as $column => $value ) {
			
				$update_string .= $column .'='. $value .', ';
			
			}
			
			$update_string = trim( $update_string, ', ' );
			
			$query = 'UPDATE '.$this->tb.' SET '. $update_string .' ';
			$this->db->query('CRUD',$query);
			
			return $this;
		}
	}
	
}

?>