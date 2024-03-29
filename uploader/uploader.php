<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../config.php')) die('[uploader.php] config.php not found');
require_once '../config.php';

if (!isset($_SESSION['jrc_userid'])) die("Nothing to see here");

// Import the language file
if ($BT_LANGUAGE && file_exists(APP_PATH.'lang/'.strtolower($BT_LANGUAGE).'.php')) {
    include_once(APP_PATH.'lang/'.strtolower($BT_LANGUAGE).'.php');
} else {
    include_once(APP_PATH.'lang/'.JAK_LANG.'.php');
}

// The new file upload stuff
if (!empty($_FILES['uploadpp']['name']) && is_numeric($_REQUEST['convID'])) {
	
	$filename = $_FILES['uploadpp']['name']; // original filename
	$ls_xtension = pathinfo($_FILES['uploadpp']['name']);
	
	// Check if the extension is valid
	$allowedf = explode(',', JAK_ALLOWED_FILES);
	if (in_array(".".$ls_xtension['extension'], $allowedf)) {
	
	// Get the maximum upload or set to 2
	$postmax = (ini_get('post_max_size') ? filter_var(ini_get('post_max_size'), FILTER_SANITIZE_NUMBER_INT) : "2");
	
	if ($_FILES['uploadpp']['size'] <= ($postmax * 1000000)) {
	
		// first get the target path
		$targetPathd = APP_PATH.JAK_FILES_DIRECTORY.'/user/';
		$targetPath =  str_replace("//", "/", $targetPathd);
	
	
	    $tempFile = $_FILES['uploadpp']['tmp_name'];
	    $name_space = explode(".", $_FILES["uploadpp"]["name"]);
		$ufile = 'usrfile_'.round(microtime(true)) . '.' . end($name_space);
	    	    
	    $targetFile =  str_replace('//','/',$targetPath).$ufile;
	    $origPath = '/user/';
	    $message = $origPath.$ufile;
	    	
	    // Move file     
	    if (move_uploaded_file($tempFile, $targetFile)) {

	    	if (file_exists($targetFile)) {

			    $jakdb->insert("transcript", [ 
					"name" => $_SESSION['jrc_name'],
					"message" => $message,
					"user" => $_SESSION['jrc_userid'],
					"convid" => $_REQUEST['convID'],
					"class" => "download",
					"time" => $jakdb->raw("NOW()")]);

			    $jakdb->update("checkstatus", ["newo" => 1, "typec" => 0], ["convid" => $_SESSION['convid']]);
			 }

		}
	                
	} else {
		$msg = $jkl['e9'];
	}
	            
	} else {
	    $msg = $jkl['e13'];
	}

switch ($_FILES['uploadpp']['error'])
{
     case 0:
     //$msg = "No Error"; // comment this out if you don't want a message to appear on success.
     break;
     case 1:
     $msg = "The file is bigger than this PHP installation allows";
     break;
     case 2:
     $msg = "The file is bigger than this form allows";
     break;
     case 3:
     $msg = "Only part of the file was uploaded";
     break;
     case 4:
     $msg = "No file was uploaded";
     break;
     case 6:
     $msg = "Missing a temporary folder";
     break;
     case 7:
     $msg = "Failed to write file to disk";
     break;
     case 8:
     $msg = "File upload stopped by extension";
     break;
     default:
     $msg = "unknown error ".$_FILES['uploadpp']['error'];
     break;
}

if (isset($msg) && !empty($msg)) {
    $stringData = $msg;
} else { 
	$stringData = $jkl['s']; // This is required for onComplete to fire on Mac OSX
}
echo $stringData;
}
?>