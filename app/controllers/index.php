<?php 
class Index extends Cloud {
	
	function __construct(){

		parent::__construct();

		$this->load->model('get_data');

		$this->cache->clear();

	}
	
	function index() {
		
		$this->get_data->set_array_of_data();
		
		$this->get_data->get_fruits();

		$this->tpl->display();

	}
	
	function __destruct() {

		parent::__destruct();

	}
	
}

?>