<?php 

class CC_Error_Handler {

	public function __construct(){
	
		error_reporting(E_ALL | E_STRICT);
		
		$handler = array($this, 'cc_error_handler');
		set_error_handler($handler);
		
	}

	public function cc_error_handler($errno, $errstr, $errfile, $errline, $errcontext){
	
		if( defined('DEBUG_BACKTRACE_IGNORE_ARGS') ) {
			$backtrace = array_reverse(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
		} else {
			$backtrace = array_reverse(debug_backtrace());
		}
		// array_pop($backtrace);
		
		echo "<pre class='CrowdCode_Error'>";

		$i = 0;
		foreach($backtrace as $trace){
			
			$file = '';
			if(isset($trace['file']) && isset($trace['line'])){
				$file = " <strong>[$trace[file]:$trace[line]]</strong>";
			}
			
			$class = '';
			if(isset($trace['class']) && $trace['class'] != __CLASS__){
				$class = " $trace[class]->";
			}
			
			$args = '';
			if(isset($trace['args'])){
			
				foreach($trace['args'] as $arg){
				$args .= print_r($arg, true) . ", ";
				}
				
				$args = trim($args, ", ");
				$args = str_replace("\n", "", $args);
				$args = str_replace("  ", "", $args);
			}
			
			$function = '';
			if(isset($trace['function']) && $trace['function'] != __FUNCTION__){
				$function = " $trace[function]($args)";
			}
			
			echo "<div>#". $i++ . $file . $class . $function . "</div>\r\n";
		}
		
		echo "<div>Error Number: $errno</div>";
		
		echo "<div>Error Message: $errstr</div>";
		
		echo "</pre>";
		
		die;
	}
}

?>