<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1980 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.4                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2018 jakweb All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../config.php';

if (!isset($_SESSION['jak_lcp_username'])) die("Nothing to see here");

$formsuc = false;
if (JAK_USERID && jak_get_access("answers", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST) && !empty($_POST)) foreach ($_POST as $key => $val) {
			# code...
			$cid = preg_replace("/[^0-9]/", "", $key);

			// Clean the dsgvo link
            include_once 'htmlawed.php';
	        $htmlconfig = array('safe' => 1, 'elements'=>'h1, h2, h3, h4, em, p, br, img, ul, li, ol, a, strong, pre, code, div', 'deny_attribute'=>'style', 'comment'=> 1, 'cdata' => 1, 'valid_xhtml' => 1, 'make_tag_strict' => 1); 
	        $val = htmLawed($val, $htmlconfig);

			if (strpos($key, 'title') !== false) {
				if ($jakdb->has("translations", ["AND" => ["cmsid" => $cid, "lang" => $BT_LANGUAGE]])) {
					$jakdb->update("translations", ["title" => $val], ["AND" => ["cmsid" => $cid, "lang" => $BT_LANGUAGE]]);
				} else {
					$jakdb->insert("translations", ["title" => $val, "cmsid" => $cid, "lang" => $BT_LANGUAGE]);
				}
			}

			if (strpos($key, 'text') !== false) {
				if ($jakdb->has("translations", ["AND" => ["cmsid" => $cid, "lang" => $BT_LANGUAGE]])) {
					$jakdb->update("translations", ["description" => $val], ["AND" => ["cmsid" => $cid, "lang" => $BT_LANGUAGE]]);
				} else {
					$jakdb->insert("translations", ["description" => $val, "cmsid" => $cid, "lang" => $BT_LANGUAGE]);
				}
			}
			// We have stored something
			$formsuc = true;
		}
	}
}
die(json_encode($formsuc));
?>