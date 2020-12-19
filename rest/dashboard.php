<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.4.2                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2019 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('config.php')) die('rest_api config.php not exist');
require_once 'config.php';

$userid = $loginhash = "";
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

		// Reset
		$totalAll = $commCtotal = $statsCtotal = $visitCtotal = $totalAllOT = $totalAllWT = 0;

		if (jak_get_access("statistic_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

			// Get the stats
			$commCtotal = $jakdb->count("transcript");
			$statsCtotal = $jakdb->count("user_stats");
			$visitCtotal = $jakdb->count("buttonstats");

		} else {

			// Get all convid into an array
			$sessids = $jakdb->select("sessions", "id", ["operatorid" => $userid]);
			// Get all messages from the convids
			$commCtotal = $jakdb->count("transcript", ["convid" => $sessids]);
			$statsCtotal = $jakdb->count("user_stats", ["userid" => $userid]);
			$visitCtotal = $jakdb->count("buttonstats", ["depid" => [$jakuser->getVar("departments")]]);

		}

    	// Get the totals
		$totalAll = $jakdb->count("support_tickets");

    	// Open Tickets
		$totalAllOT = $jakdb->count("support_tickets", ["status" => 1]);
    	// Awaiting Reply Tickets
		$totalAllWT = $jakdb->count("support_tickets", ["status" => 2]);


		die(json_encode(array('status' => true, 'totaltickets' => $totalAll, 'totalmsg' => $commCtotal, 'totalfeedback' => $statsCtotal, 'totalvisitor' => $visitCtotal, 'totalopen' => $totalAllOT, 'totalwait' => $totalAllWT)));
	} else {
		die(json_encode(array('status' => false, 'errorcode' => 1)));
	}
}

die(json_encode(array('status' => false, 'errorcode' => 1)));
?>