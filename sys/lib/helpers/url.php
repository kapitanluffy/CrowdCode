<?php 

function url($base = false){
	global $cloud;
	
	return ($base === true) ? $cloud->url->siteUrl : $cloud->url->actionUrl;
}

?>