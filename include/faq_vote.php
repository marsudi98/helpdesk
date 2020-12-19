<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1980 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH                                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2017 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../config.php')) die('ajax/[comment_vote.php] config.php not exist');
require_once '../config.php';

if (!JAK_USERISLOGGED) die(json_encode(array("status" => 0)));

if (is_numeric($_GET['vid'])) {

	if (isset($_GET['vote']) && ($_GET['vote'] == "up" || $_GET['vote'] == "down")) {
		
		if ($_GET['vote'] == "down") {
			$votesql = 'votes - 1';
			$jakdb->update("faq_article", ["votes[-]" => 1], ["id" => $_GET['vid']]);
		} else {
			$jakdb->update("faq_article", ["votes[+]" => 1], ["id" => $_GET['vid']]);
		}
		
		die(json_encode(array("status" => 1)));
		
	}
	
	die(json_encode(array("status" => 0)));

} else {
	die(json_encode(array("status" => 0)));
}
?>