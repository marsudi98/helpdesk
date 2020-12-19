<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.4                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2018 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('config.php')) die('rest_api config.php not exist');
require_once 'config.php';

$userid = $loginhash = $ticketid = $attach = "";
if (isset($_REQUEST['userid']) && !empty($_REQUEST['userid']) && is_numeric($_REQUEST['userid'])) $userid = $_REQUEST['userid'];
if (isset($_REQUEST['loginhash']) && !empty($_REQUEST['loginhash'])) $loginhash = $_REQUEST['loginhash'];
if (isset($_REQUEST['ticketid']) && !empty($_REQUEST['ticketid'])) $ticketid = $_REQUEST['ticketid'];
if (isset($_REQUEST['attach']) && !empty($_REQUEST['attach'])) $attach = $_REQUEST['attach'];

if (!empty($userid) && !empty($loginhash)) {

	// Let's check if we are logged in
	$usr = $jakuserlogin->jakCheckrestlogged($userid, $loginhash);

	if ($usr) {

		if (empty($ticketid) || empty($attach)) die(json_encode(array('status' => false, 'errorcode' => 2)));

		$imgdata = base64_decode($attach);
		$im = imagecreatefromstring($imgdata); 
		if ($im !== false) {

			// if you need the image mime type
			$f = finfo_open();
			$mime = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
		    	
		    if (($mime == "image/jpeg") || ($mime == "image/pjpeg") || ($mime == "image/png") || ($mime == "image/gif")) {

				// first get the target path
				$targetPathd = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$ticketid.'/';
				$targetPath =  str_replace("//","/",$targetPathd);
				// Create the target path
				if (!is_dir($targetPath)) {
					mkdir($targetPath, 0755);
					copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $targetPath . "/index.html");
					    	
				}

				// Get the correct ending.
				if ($mime == "image/jpeg" || $mime == "image/pjpeg") {
					$imend = ".jpg";
				} elseif ($mime == "image/gif") {
					$imend = ".gif";
				} else {
					$imend = ".png";
				}

				$ufile = 'opfiletm_'.round(microtime(true)) . '.' . end($imend);

				// The path to upload
			    $targetFile =  str_replace('//', '/', $targetPath).$ufile;
			    // The path to show
			    $targetShow =  str_replace('//', '/', BASE_URL.JAK_FILES_DIRECTORY.'/support/'.$ticketid.'/').$ufile;

				// Save as the correct ending
				if ($ufile == ".jpg") {
					imagejpeg($im, $targetFile);
				} elseif ($ufile == ".gif") {
					imagegif($im, $targetFile);
				} else {
					imagepng($im, $targetFile);
				}

				imagedestroy($im);

				// Update counter on ticket
	    		$jakdb->update("support_tickets", ["attachments[+]" => 1], ["id" => $ticketid]);

				// Output the ticket
				die(json_encode(array('status' => true, 'newattach' => $targetShow, 'filename' => $ufile)));

			} else {
				die(json_encode(array('status' => false, 'errorcode' => 2)));
			}

		} else {
			die(json_encode(array('status' => false, 'errorcode' => 9)));
		}

	} else {
		die(json_encode(array('status' => false, 'errorcode' => 1)));
	}
}

die(json_encode(array('status' => false, 'errorcode' => 7)));
?>