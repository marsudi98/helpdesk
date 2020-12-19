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

if(!JAK_USERISLOGGED) die("Nothing to see here");

// Import the language file
if ($BT_LANGUAGE && file_exists(APP_PATH.'lang/'.strtolower($BT_LANGUAGE).'.php')) {
    include_once(APP_PATH.'lang/'.strtolower($BT_LANGUAGE).'.php');
} else {
    include_once(APP_PATH.'lang/'.JAK_LANG.'.php');
}

if (JAK_CLIENTID == $_REQUEST['userIDC'] || JAK_USERID == $_REQUEST['userIDU']) {

// The new file upload stuff
if (!empty($_FILES['uploadpp']['name']) && is_numeric($_REQUEST["ticketId"])) {
	
	// Ticket ID
	$ticketid = $_REQUEST['ticketId'];

	$filename = $_FILES['uploadpp']['name']; // original filename
	$ls_xtension = pathinfo($_FILES['uploadpp']['name']);
	
	// Check if the extension is valid
	$allowedf = explode(',', JAK_ALLOWED_FILES);
	if (in_array(".".$ls_xtension['extension'], $allowedf)) {

	// if mime type is valid
	$mime_type = jak_mime_content_type($_FILES['uploadpp']['name'], $ls_xtension['extension']);
	if ($mime_type) {

	// Get the maximum upload or set to 2
	$postmax = (ini_get('post_max_size') ? filter_var(ini_get('post_max_size'), FILTER_SANITIZE_NUMBER_INT) : "2");
	
	if ($_FILES['uploadpp']['size'] <= ($postmax * 1000000)) {
	
		// first get the target path
		$targetPathd = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$ticketid.'/';
		$targetPath =  str_replace("//", "/", $targetPathd);

		if (!is_dir($targetPath)) {
			mkdir($targetPath, 0755);
			copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $targetPath."/index.html");
		}
	
	    $tempFile = $_FILES['uploadpp']['tmp_name'];
	    $name_space = explode(".", $_FILES["uploadpp"]["name"]);
		$ufile = 'usrfilet_'.round(microtime(true)) . '.' . end($name_space);
	   	
	   	// The path to upload
	    $targetFile =  str_replace('//','/',$targetPath).$ufile;
	    // The path to show
	    $targetShow =  jak_encrypt_decrypt(str_replace('//', '/', '/'.$ticketid.'/').$ufile.':#:'.$ufile.':#:'.$mime_type);

	    // Check if the file is an image
	    if(@is_array(getimagesize($tempFile))){
		    $isimage = 1;
		} else {
		    $isimage = 0;
		}
	    	
	    // Move file     
	    if (move_uploaded_file($tempFile, $targetFile)) {
			// Update counter on ticket
	    	if (file_exists($targetFile)) $jakdb->update("support_tickets", ["attachments[+]" => 1], ["id" => $ticketid]);
	    }
	                
	} else {
		$msg = $jkl['hd88'];
	}
	            
	} else {
	    $msg = $jkl['hd89'];
	}

	} else {
	    $msg = $jkl['hd89'];
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
	$stringData = '{"status":"'.$jkl['s'].'", "filepath": "'.$targetShow.'", "filename": "'.$ufile.'", "isimage": '.$isimage.'}'; // return json
}
} else {
	$stringData = "error";
}
} else {
	$stringData = "error";
}
echo $stringData;
?>