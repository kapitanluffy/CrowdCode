<?php

interface Database {

    public function connect($db,$pass,$user,$server);
	
    public function db($db);
	
    public function query($string);
		
    public function escape($string);
}

?>