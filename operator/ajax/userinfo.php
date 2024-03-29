<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.1.2                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2016 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_SESSION['jak_lcp_idhash'])) die("Nothing to see here");

// Import the user or standard language file
if (isset($_SESSION['jak_lcp_lang']) && file_exists(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$_SESSION['jak_lcp_lang'].'.php')) {
    include_once(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$_SESSION['jak_lcp_lang'].'.php');
} else {
    include_once(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.JAK_LANG.'.php');
}

if (!is_numeric($_POST['id'])) die(json_encode(array('status' => 1, "html" => $jkl['g79'])));

$responses = '';

$row = $jakdb->get("sessions", ["name", "operatorname", "department"], ["id" => $_POST['id']]);

if (isset($row) && !empty($row)) {

	if (isset($HD_RESPONSES) && is_array($HD_RESPONSES)) {

		$responses .= '<option value="0">'.$jkl["g7"].'</option>';
		
		// get the responses from the file specific for this client
		foreach($HD_RESPONSES as $r) {
		
			if ($r["department"] == 0 || $r["department"] == $row["department"]) {
		
				$phold = array("%operator%","%client%","%email%");
				$replace   = array($row['operatorname'], $row["name"], JAK_EMAIL);
				$message = str_replace($phold, $replace, $r["message"]);
				
				$responses .= '<option value="'.$message.'">'.$r["title"].'</option>';
				
			}
		
		}
		
	}

	die(json_encode(array('status' => 1, 'name' => $row["name"], 'responses' => $responses)));

}
?>