<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.3.4                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2018 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('config.php')) die('rest_api config.php not exist');
require_once 'config.php';

$userid = $loginhash = "";
$newdata = array();
if (isset($_REQUEST['userid']) && !empty($_REQUEST['userid']) && is_numeric($_REQUEST['userid'])) $userid = $_REQUEST['userid'];
if (isset($_REQUEST['loginhash']) && !empty($_REQUEST['loginhash'])) $loginhash = $_REQUEST['loginhash'];

if (!empty($userid) && !empty($loginhash)) {

	// Let's check if we are logged in
	$usr = $jakuserlogin->jakCheckrestlogged($userid, $loginhash);

	if ($usr) {

		// Select the fields
		$jakuser = new JAK_user($usr);
		// Only the SuperAdmin in the config file see everything
		if ($jakuser->jakSuperadminaccess($userid)) {
			define('JAK_SUPERADMINACCESS', true);
		} else {
			define('JAK_SUPERADMINACCESS', false);
		}

		// Ok, we have check for some data, pull it
	    if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

	    	// Filter the right result or show all
	    	if (is_numeric($jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
				$newdata = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_departments.title(department)", "support_tickets.name", "support_tickets.email", "support_tickets.private", "support_tickets.status", "support_tickets.updated"], ["OR" => ["support_tickets.operatorid" => $jakuser->getVar("id"), "support_tickets.depid" => $jakuser->getVar("support_dep")], "ORDER" => ["support_tickets.updated" => "DESC"]]);
			} elseif (!((boolean)$jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
				$newdata = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_departments.title(department)", "support_tickets.name", "support_tickets.email", "support_tickets.private", "support_tickets.status", "support_tickets.updated"], ["OR" => ["support_tickets.operatorid" => $jakuser->getVar("id"), "support_tickets.depid" => [$jakuser->getVar("support_dep")]], "ORDER" => ["support_tickets.updated" => "DESC"]]);
			} else {
				$newdata = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_departments.title(department)", "support_tickets.name", "support_tickets.email", "support_tickets.private", "support_tickets.status", "support_tickets.updated"], ["ORDER" => ["support_tickets.updated" => "DESC"]]);
			}
		}

		if (isset($newdata) && !empty($newdata)) {
			die(json_encode(array('status' => true, 'tickets' => $newdata)));
		} else {
			die(json_encode(array('status' => false, 'errorcode' => 9)));
		}

	} else {
		die(json_encode(array('status' => false, 'errorcode' => 1)));
	}
}

die(json_encode(array('status' => false, 'errorcode' => 7)));
?>