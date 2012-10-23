<?php 

class Template {

	private $blocks = array();

	private $patterns = array(
		# {@if bool}
		"#{@if ([\'\[\]a-zA-Z0-9_-]+)}#si",
		"#{@if ([\'\[\]a-zA-Z0-9_-]+)=([\'\[\]a-zA-Z0-9_-]+)}#si",
		# {@loop list}
		"#{@loop ([\w]+)}#si",
		# {@list.[INDEX]}
		"#{@([\w]+)\.\[INDEX\]}#si",
		# {&list/index}
		"#{@([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)(?:|(\[[\'\[\]a-zA-Z0-9_-]+\]+))}#si",
		# {&list}
		"#{@([\w]+)}#si",
		# endif;
		"#{!if}#si",
		# endforeach;
		"#{!loop}#si",
		# all other variables
		"#{[!@]([\w]+)}#si", 
	);
	
	private $replace = array(
		'<?php if( isset( $$1 ) && !empty( $$1 ) ): ?>',
		'<?php if( isset( $$1 ) && !empty( $$1 ) && ($$1==$$2) ): ?>',
		'<?php if( isset( $$1 ) && !empty( $$1 ) ): foreach( $$1 as $$1_index => $$1_row ): ?>',
		'<?php echo $$1_index; ?>',
		'<?php echo isset($$1_row[\'$2\']$3) ? $$1_row[\'$2\']$3 : \'\' ; ?>',
		'<?php echo $$1_row; ?>',
		'<?php endif; ?>',
		'<?php endforeach; endif; ?>',
		'<?php if( defined( \'$1\' ) ): echo $1; elseif( isset( $$1) ): echo $$1; else: echo \'{@$1}\'; endif; ?>',
	);
	
	public function remove_undefined( $removeUndefined = false ) {
		
		if( $removeUndefined ) {
			
			$this->replace[count($this->replace)] = '<?php if( defined( \'$1\' ) ): echo $1; elseif( isset( $$1) ): echo $$1; else: echo \'\'; endif; ?>';
			
		}
		
	}

	public function read( $file ) {
		
		$file = VIEWS . $file . '.php';
		
		if( file_exists( $file ) ) {
		
			$this->file = file_get_contents( $file );
			$this->file = str_replace("\t", "", $this->file);
		
		}
		
		foreach( $this->blocks as $n => $v ) {

			$this->file = str_replace( "{@block_$n}", $v, $this->file);
			
		}

	}
	
	public function clear_block( $name ) {
	
		if( !@empty( $name ) ) {
		
			unset( $this->blocks[$name] );
			
		}
	}
	
	public function block( $name, $file = null ) {
	
		if( $file == null ) {
		
			$file = $name;
		
		}
	
		$this->read($file);
		
		if( !@empty( $this->file ) ) {
		
			$this->blocks[$name] = $this->file;
		
		}
	}

	public function parse() {
	
		$CC =& get_instance();
	
		
		foreach( $CC->data as $n => $v ) {
		
			$$n = $v;
			
		}

		$this->file = preg_replace($this->patterns, $this->replace, $this->file);

		ob_start();

		eval("?> $this->file");
		
		$this->file = ob_get_clean();
		
	}
	
	function display( $file = null ) {
		$CC =& get_instance();
		
		if( $file == null ) {
	
			$file = 'index';
		
		}
		
		$this->read( $file );
		
		
		if( load_class( 'cache' ) ) {
		
			if( $cache = $CC->cache->get( $this->file, $CC->data ) ) {
				$this->file = $cache;
			
			} else {
				$this->parse( $CC->data );
				
				$CC->cache->create( $this->file ); 
			
			}
		
		} else {
			$this->parse( $CC->data );
		
		}
	echo $this->file;
	
	exit();
	
	}

}


?>