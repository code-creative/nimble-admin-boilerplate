<?php 

class logger 
{

	function __construct($nb) {
		$this->nb = $nb;
	}
	
	function log($message, $tag = "system")
	{
		//system_log
		$insert = array("message" => $message, "tag" => $tag, "log_date" => $this->nb->now);
		$this->nb->db->insert("system_log", $insert);	
	}
	
}