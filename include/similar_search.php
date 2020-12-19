<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1980 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../config.php')) die('ajax/[similar_search.php] config.php not exist');
require_once '../config.php';

// Database tables
$jaktable = 'support_tickets';
$jaktable1 = 'faq_article';
$jaktable2 = 'faq_categories';

// Reset vars
$searchmsg = $similar_articles = '';
$searchtickets = array();
$searchfaq = array();

if (isset($_GET['s']) && !empty($_GET['s'])) {

	// Sanitise the search string
	$searchmsg = html_entity_decode($_GET['s']);
	$searchmsg = strip_tags($searchmsg);
	$searchmsg = filter_var($searchmsg, FILTER_SANITIZE_STRING);
	$searchmsg = trim($searchmsg);

	if (isset($searchmsg) && !empty($searchmsg)) {

		// Let's dig through the database
		if (JAK_USERISLOGGED && JAK_CLIENTID != 0) {

		    $searchtickets = $jakdb->select($jaktable, ["id", "subject", "content", "initiated", "updated", "ended"], ["AND" => ["OR" => ["subject[~]" => $searchmsg, "content[~]" => $searchmsg]], "private" => 0, "clientid[!]" => JAK_CLIENTID, "ORDER" => ["updated" => "DESC"], "LIMIT" => 10]);

		    $searchfaq = $jakdb->select($jaktable1, ["id", "title", "content", "lang"], ["AND" => ["OR" => ["title[~]" => $searchmsg, "content[~]" => $searchmsg]], "active" => 1, "ORDER" => ["dorder" => "DESC"], "LIMIT" => 10]);

		} else {

			$searchtickets = $jakdb->select($jaktable, ["id", "subject", "content", "initiated", "updated", "ended"], ["AND" => ["OR" => ["subject[~]" => $searchmsg, "content[~]" => $searchmsg]], "private" => 0, "ORDER" => ["updated" => "DESC"], "LIMIT" => 10]);

			$searchfaq = $jakdb->select($jaktable1, ["[>]faq_categories" => ["catid" => "id"]], ["id", "title", "content", "lang"], ["AND" => ["OR" => ["title[~]" => $searchmsg, "content[~]" => $searchmsg]], "active" => 1, "guesta" => 1, "ORDER" => ["dorder" => "DESC"], "LIMIT" => 10]);

		}

		if (isset($searchtickets) && !empty($searchtickets) || isset($searchfaq) && !empty($searchfaq)) {

			$similar_articles .= '<div class="list-group">';

			if (!empty($searchtickets)) foreach ($searchtickets as $t) {
				# code...
				$similar_articles .= '<a href="'.str_replace('include/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $t["id"], JAK_rewrite::jakCleanurl($t["subject"]))).'" target="_blank" class="list-group-item list-group-item-light"><div class="d-flex w-100 justify-content-between"><h5 class="mb-1 mt-0">'.$t["subject"].'</h5><small>'.JAK_base::jakTimesince($t["updated"], JAK_DATEFORMAT, JAK_TIMEFORMAT).'</small></div><p class="mb-1">'.jak_cut_text($t["content"], 100, "...").'</p></a>';
			}

			if (!empty($searchfaq)) foreach ($searchfaq as $f) {
				# code...
				$similar_articles .= '<a href="'.str_replace('include/', '', JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $f["id"], JAK_rewrite::jakCleanurl($f["title"]))).'" target="_blank" class="list-group-item list-group-item-dark"><div class="d-flex w-100 justify-content-between"><h5 class="mb-1 mt-0">'.$f["title"].'</h5><small>'.strtoupper($f["lang"]).'</small></div><p class="mb-1">'.jak_cut_text($f["content"], 100, "...").'</p></a>';
			}

			$similar_articles .= '</div>';
			
		}

		if (!empty($similar_articles)) {
		
			die(json_encode(array("status" => 1, "articles" => $similar_articles)));
		} else {
			die(json_encode(array("status" => 0)));
		}
		
	}
	
	die(json_encode(array("status" => 0)));

} else {
	die(json_encode(array("status" => 0)));
}
?>