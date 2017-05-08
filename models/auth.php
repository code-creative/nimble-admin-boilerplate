<?php 

class auth 
{

	function __construct($nb) {
		$this->nb = $nb;
		if(!$this->nb->session->get("user"))
		{
			header( 'Location: ./' ) ;
		}else{
			$this->nb->logged_in = TRUE;	
		}
	}

}