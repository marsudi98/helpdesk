<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH                                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2016 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

class JAK_client {
	
	private $data;
	private $lsvar = 0;
	
	public function __construct($row) {
		$this->data = $row;
	}
	
	function getVar($lsvar) {
		
		// Setting up an alias, so we don't have to write $this->data every time:
		$d = $this->data;
		
		return $d[$lsvar];
	}
}
?>