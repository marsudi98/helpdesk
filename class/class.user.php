<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

class JAK_user
{
	private $data;
	private $lsvar = 0;
	private $username = '';
	
	public function __construct($row) {
		$this->data = $row;
	}
	
	function jakSuperadminaccess($lsvar) {
		$useridarray = explode(',', JAK_SUPERADMIN);
		// check if userid exist in db.php
		if (in_array($lsvar, $useridarray)) {
			return true;
		} else {
			return false;
		}
	
	}
	
	function getVar($lsvar) {
		
		// Setting up an alias, so we don't have to write $this->data every time:
		$d = $this->data;
		
		if (isset($d[$lsvar])) {
			return $d[$lsvar];
		} else {
			return false;
		}
		
	}
}
?>