<?php

class CC_Cache {

	public $cache_file;

	function set_hash_type( $hash ) {
	
		$this->hash = $hash;
	
	}
	
	function set_cache_ext( $ext ) {
	
		$this->ext = $ext;
	
	}

	function get( $file, $data ) {
		
		$hash = hash($this->hash, json_encode( $data ) . $file );
		
		$this->cache_file = CACHE . $hash . $this->ext;
		
		if( file_exists( $this->cache_file ) ) {
		
			return file_get_contents( $this->cache_file );
			
		}
		
		return false;
		
	}
	
	function create( $content ){
	

		if( $fh = fopen( $this->cache_file, 'w' ) ) {

			fwrite($fh, $content);

			fclose($fh);

		}
	
	}
	
	function clear(){
		
		$files = CACHE . '*.cache';
		
		foreach( glob($files) as $file ) {
		
			unlink( $file );
		
		}
		
	}

}

?>