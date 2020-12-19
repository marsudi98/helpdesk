<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../../config.php')) die('ajax/[oprequests.php] config.php not exist');
require_once '../../config.php';

if(!isset($_SESSION['jak_lcp_idhash'])) die("Nothing to see here");

// Import the user or standard language file
if (isset($_SESSION['jak_lcp_lang']) && file_exists(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$_SESSION['jak_lcp_lang'].'.php')) {
    include_once(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$_SESSION['jak_lcp_lang'].'.php');
} else {
    include_once(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.JAK_LANG.'.php');
}

$switchc = '';
if (isset($_POST['cal-action']) && !empty($_POST['cal-action'])) $switchc = $_POST['cal-action'];

// Back to dashboard
$backtodash = str_replace('ajax/', '', BASE_URL);

switch ($switchc) {
	case 'cal-new':
		# code

		if (isset($_POST['cal-title']) && !empty($_POST['cal-title'])) {

			$startdate = date('Y-m-d H:i:s ', strtotime($_POST['cal-start']));
			$enddate = date('Y-m-d H:i:s ', strtotime($_POST['cal-end']));

			$result = $jakdb->insert("events", ["title" => $_POST['cal-title'], "content" => $_POST['cal-content'], "color" => $_POST['cal-color'], "start" => $startdate, "end" => $enddate, "lastedit" => $jakdb->raw("NOW()"), "created" => $jakdb->raw("NOW()")]);

			if ($result) {
				$_SESSION["successmsg"] = $jkl['g14'];
				jak_redirect($backtodash);
			}

		}
		$_SESSION["errormsg"] = $jkl['i4'];
		jak_redirect($backtodash);
	break;
	case 'cal-edit':
		# code...

		if (isset($_POST['cal-id']) && is_numeric($_POST['cal-id']) && $jakdb->has("events", ["id" => $_POST['cal-id']]) && isset($_POST['cal-title']) && !empty($_POST['cal-title'])) {

			if (isset($_POST['cal-delete']) && $_POST['cal-delete'] == 1) {
				$jakdb->delete("events", ["id" => $_POST['cal-id']]);
			} else {

				$startdate = date('Y-m-d H:i:s ', strtotime($_POST['cal-start']));
				$enddate = date('Y-m-d H:i:s ', strtotime($_POST['cal-end']));

				if ($jakdb->update("events", ["title" => $_POST['cal-title'], "content" => $_POST['cal-content'], "color" => $_POST['cal-color'], "start" => $startdate, "end" => $enddate, "lastedit" => $jakdb->raw("NOW()")], ["id" => $_POST['cal-id']])) {
				}

			}
			$_SESSION["successmsg"] = $jkl['g14'];
			jak_redirect($backtodash);
		}
		$_SESSION["errormsg"] = $jkl['i'];
		jak_redirect($backtodash);
	break;
	case 'cal-date':
		# code...

		if (isset($_POST['cal-id']) && is_numeric($_POST['cal-id']) && $jakdb->has("events", ["id" => $_POST['cal-id']])) {

			// Datefromat
			$startdate = date('Y-m-d H:i:s ', strtotime($_POST['cal-start']));
			$enddate = date('Y-m-d H:i:s ', strtotime($_POST['cal-end']));

			if ($jakdb->update("events", ["start" => $startdate, "end" => $enddate], ["id" => $_POST['cal-id']])) {

				die(json_encode(array('status' => 1)));
			}
		}

		die(json_encode(array('status' => 0)));
	break;
	default:
		# code...
		die(json_encode(array('status' => 0)));
}
?>