<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../../config.php')) die('ajax/[config.php] config.php not exist');
require_once '../../config.php';

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_SESSION['jak_lcp_idhash'])) die("Nothing to see here");

// Short-circuit if the client did not give us a date range.
if (!isset($_GET['start']) || !isset($_GET['end'])) {
	die("Please provide a date range.");
}

// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
// Since no timezone will be present, they will parsed as UTC.
$range_start = $_GET['start'];
$range_end = $_GET['end'];

// Get the entries out the database
$eventsrange = $jakdb->select("events", ["id", "content", "color", "start", "end", "title"], ["AND" => ["start[>]" => $range_start, "end[<]" => $range_end]]);
if (isset($eventsrange) && !empty($eventsrange)) foreach ($eventsrange as $row) {
	// font color
	if (in_array($row["color"], array("#000000", "#0071c5", "#FF0000", "#008000"))) {
		$fcolor = "#ffffff";
	} else {
		$fcolor = "#333333";		
	}
	$events[] = array('id' => $row['id'], 'title' => $row["title"], 'content' => $row["content"], 'start' => $row["start"], 'end' => $row["end"], 'color' => $row["color"], 'textColor' => $fcolor);
}

// We load the tickets in this period
if (JAK_CALENDAR_TICKETS && jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
	$ticketrange = $jakdb->select("support_tickets", ["id", "subject", "name", "initiated"], ["AND" => ["initiated[>]" => strtotime($range_start), "ended[<]" => strtotime($range_end)]]);
	if (isset($ticketrange) && !empty($ticketrange)) foreach ($ticketrange as $row) {

		$events[] = array('title' => $row["subject"].' - '.$row["name"], 'url' => str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'read', $row['id'])), 'start' => date("Y-m-d H:i:s", $row["initiated"]), 'end' => date("Y-m-d H:i:s", ($row["initiated"] + 3600)), 'color' => "#d649d8", 'editable' => false);
	}
}

// We load the chats in this period
if (JAK_CALENDAR_CHATS && jak_get_access("leads_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
	$chatrange = $jakdb->select("sessions", ["id", "name", "initiated"], ["AND" => ["initiated[>]" => strtotime($range_start), "ended[<]" => strtotime($range_end)]]);
	if (isset($chatrange) && !empty($chatrange)) foreach ($chatrange as $row) {

		$events[] = array('title' => $row["name"], 'url' => str_replace('ajax/', '', JAK_rewrite::jakParseurl('live', $row['id'])), 'start' => date("Y-m-d H:i:s", $row["initiated"]), 'end' => date("Y-m-d H:i:s", ($row["initiated"] + 3600)), 'color' => "#49abd8", 'editable' => false);
	}
}

// We load the offline messages in this period
if (JAK_CALENDAR_OFFLINE && jak_get_access("off_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
	$offrange = $jakdb->select("contacts", ["name", "email", "sent"], ["AND" => ["sent[>=]" => $range_start, "sent[<=]" => $range_end]]);
	if (isset($offrange) && !empty($offrange)) foreach ($offrange as $row) {

		$enddate = new DateTime($row["sent"]);
		$enddate->modify('+1 hour');

		$events[] = array('title' => $row["name"].' - '.$row["email"], 'url' => str_replace('ajax/', '', JAK_rewrite::jakParseurl('contacts')), 'start' => $row["sent"], 'end' => $enddate->format('Y-m-d H:i:s'), 'color' => "#d88c49", 'editable' => false);
	}
}

// We load the payments in this period
if (JAK_CALENDAR_PURCHASES && jak_get_access("billing", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
	$ticketrange = $jakdb->select("subscriptions", ["[>]clients" => ["clientid" => "id"]], ["subscriptions.clientid", "subscriptions.paidwhen", "clients.name", "clients.email"], ["AND" => ["success" => 1, "subscriptions.paidwhen[>=]" => $range_start, "subscriptions.paidwhen[<=]" => $range_end]]);
	if (isset($ticketrange) && !empty($ticketrange)) foreach ($ticketrange as $row) {

		$enddate = new DateTime($row["paidwhen"]);
		$enddate->modify('+1 hour');

		$events[] = array('title' => $row["name"].' - '.$row["email"], 'url' => str_replace('ajax/', '', JAK_rewrite::jakParseurl('users', 'clients', 'edit', $row['clientid'])), 'start' => $row["paidwhen"], 'end' => $enddate->format('Y-m-d H:i:s'), 'color' => "#71d849", 'editable' => false);
	}
}

die(json_encode($events));
?>