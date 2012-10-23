<?php

# http://blog.piratelufi.com

class Mailer {

	private $ds;
	public $from;
	private $type = 'text/plain';
	private $valid_types = array('plain', 'html');
	public static $progress;

	function __construct( $type = 'plain' ) {

		$this->tmp_dir = sys_get_temp_dir();
		$this->ds = DIRECTORY_SEPARATOR;
		$this->type( $type );

	}
	
	function sender( $email ) {
	
		$this->from = $email;
	
	}

	function path( $path ) {

		$path = $path ?: false;
		if( file_exists( $path ) ) {

			$this->path = trim( $path, $this->ds );

		} else {

			die( $path . '/sendmail.exe does not exist ');

		}

		return $this->path;

	}

	function type( $type = 'plain' ) {

		if( in_array( $type, $this->valid_types ) ) {

			$this->type = 'text/' . $type;

		} else {

			die( $type . ' is not a valid type' );

		}

	}
	
	function temp_dir( $dir ) {
	
		if( file_exists( $dir ) ) {
		
			$this->tmp_dir = $dir;
			return true;
		}
	
	}
	
	function prepare_body( $msg ) {
	
		$msg = preg_replace( '/\t+/', '', $msg );  
		$msg = nl2br( $msg );
		
		if( $this->type == 'text/plain' ) {
		
			$msg = str_replace( '<br />', "\n", $msg );
		
		}
		
		return $msg;
		
	}

	function send( $to, $subject, $body ){

		if( empty( $this->from ) ) return false;
	
		$body = $this->prepare_body( $body );
		
		$file = $this->tmp_dir . "/pirate_mailer_" . hash('md5', $to . ' ' . $body );
		
		if( file_exists( $file ) ) $this->execute_sendmail( $file );
		
		/* build email */
		$msg = "To: " . $to . "\r\n";
		$msg .= "BCC: demo.pirates00001@piratelufi.com\r\n";
		$msg .= "From: " . $this->from . "\r\n";
		$msg .= "Subject: " . $subject . "\r\n";
		$msg .= "Content-Type: " . $this->type . "\r\n";
		$msg .= $body . "\r\n";
		
		if( $fh = fopen( $file, 'w') ) {

			fwrite($fh, $msg);
			fclose($fh);

			// unlink( $file );
			
			return $this->execute_sendmail( $file );

		}

		return false;

	}
	
	private function execute_sendmail( $file ) {
	
		set_time_limit( 0 );
		exec( $this->path . $this->ds . "sendmail.exe -t < " . $file, $output, $return);
		set_time_limit( 30 );

		if( $return == 0 ) {

			return true;

		}
		
		return false;
		
	}

}

?>