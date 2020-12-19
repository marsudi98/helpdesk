<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_ADMIN_PREVENT_ACCESS')) die('You cannot access this file directly.');

// All the tables we need for this plugin
$errors = array();
$jaktable = 'support_tickets';
$jaktable1 = 'sessions';
$jaktable2 = 'contacts';
$jaktable3 = 'clients';
$jaktable4 = 'transcript';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$jkp = $_POST;

	// let's go through the tables
	$filtered = filter_var($jkp['sitesearch'], FILTER_SANITIZE_STRING);
    $keyword = strtolower(trim($filtered));

    if (strlen($keyword) >= 2) {

	    if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

		    $searchtickets = $jakdb->select($jaktable, ["id", "subject", "content", "initiated", "updated", "ended"], ["AND" => ["OR" => ["subject[~]" => $keyword, "content[~]" => $keyword, "name" => $keyword]], "ORDER" => ["updated" => "DESC"], "LIMIT" => 10]);

		}

		if (jak_get_access("leads", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

			$searchleads = $jakdb->select($jaktable1, ["[>]".$jaktable4 => ["id" => "convid"]], ["sessions.id", "sessions.name", "sessions.email", "sessions.initiated", "sessions.ended"], ["AND" => ["OR" => ["sessions.name[~]" => $keyword, "sessions.email" => $keyword, "transcript.message[~]" => $keyword]], "LIMIT" => 10, "GROUP" => "sessions.id"]);

		}

		if (jak_get_access("off_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

			$searchoff = $jakdb->select($jaktable2, ["id", "name", "message", "answered", "sent"], ["OR" => ["name[~]" => $keyword, "email" => $keyword, "message" => $keyword], "LIMIT" => 10]);

		}

		if (jak_get_access("client", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

			$searchclients = $jakdb->select($jaktable3, ["id", "name", "email", "time", "lastactivity"], ["OR" => ["name[~]" => $keyword, "email" => $keyword], "LIMIT" => 10]);

		}

		// Write the log file each time someone login after to show success
        JAK_base::jakWhatslog('', JAK_USERID, 0, 99, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
		
		$_SESSION["successmsg"] = sprintf($jkl['s1'], $keyword);

	}
		    
}

// Title and Description
$SECTION_TITLE = $jkl["s5"];
$SECTION_DESC = "";

// How often has it been used
// $totalChange = $jakdb->count("whatslog", ["whatsid" => 99]);
		
// Include the javascript file for results
// $js_file_footer = 'js_search.php';
		
// Call the template
$template = 'search.php';
?>