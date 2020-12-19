<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.2                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Get the data per array for page,newsletter with limit
function jak_get_page_info($table,$limit = "") 
{
	global $jakdb;
	if (!empty($limit)) {
    	$datatable = $jakdb->select($table, "*", ["ORDER" => ["id" => "DESC"], "LIMIT" => $limit]);
    } else {
    	$datatable = $jakdb->select($table, "*", ["ORDER" => ["id" => "DESC"]]);
    }
        
    if (!empty($datatable)) return $datatable;
}

// Get the available chat packages
function jak_get_chat_packages() {

	// Get the language folder
	$packdir = '../'.'package/';

	return array_diff(scandir($packdir), array('..', '.', 'index.html', '.DS_Store'));
}

// Get the available front end packages
function jak_get_templates() {

	// Get the language folder
	$packdir = '../'.'template/';

	return array_diff(scandir($packdir), array('..', '.', 'index.html', '.DS_Store'));
}

// Search for lang files in the admin folder, only choose .ini files.
function jak_get_sound_files() {

	$getsound = array();

	global $jakdb;
	// Get the sounds from the installed packages
	$packsound = $jakdb->select("chatwidget", "template", ["GROUP" => "template"]);

    if (isset($packsound) && !empty($packsound)) {

        foreach ($packsound as $v) {

        	$packagef = 'package/'.$v.'/';
			if (file_exists('../'.$packagef.'config.php')) {

				include_once '../'.$packagef.'config.php';

	        	if (isset($jakgraphix["sound"]) && !empty($jakgraphix["sound"])) {

	        		// Get the general sounds
					$dynsound = '../'.$packagef.$jakgraphix["sound"];

					if ($dynhandle = opendir($dynsound)) {
					
					    /* This is the correct way to loop over the directory. */
					    while (false !== ($dynfile = readdir($dynhandle))) {
					    	$dynshowsound = substr($dynfile, strrpos($dynfile, '.'));
						    if ($dynfile != '.' && $dynfile != '..' && $dynshowsound == '.mp3') {
						    
						    	$getsound[] = $packagef.$jakgraphix["sound"].substr($dynfile, 0, -4);
						    
						    }
					    }
					    closedir($dynhandle);
					}
	        	}
	        }
        }
    }
	
	// Get the general sounds
	$soundir = '../sound/';

	if ($handle = opendir($soundir)) {
	
	    /* This is the correct way to loop over the directory. */
	    while (false !== ($file = readdir($handle))) {
		    $showsound = substr($file, strrpos($file, '.'));
		    if ($file != '.' && $file != '..' && $showsound == '.mp3') {
		    
		    	$getsound[] = 'sound/'.substr($file, 0, -4);
		    
		    }
	    }
	    closedir($handle);
		return $getsound;
	    
	}
}

// Get all user out the database limited with the paginator
function jak_get_user_all($table, $userid, $supero) {

	global $jakdb;
	if ($userid && $supero) {
		$datausr = $jakdb->select($table, "*", ["OR" => ["id" => $userid, "id[!]" => $supero]]);
	} elseif ($userid) {
		$datausr = $jakdb->select($table, "*", ["id" => $userid]);
	} elseif ($supero) {
		$datausr = $jakdb->select($table, "*", ["id[!]" => $supero]);
	} else {
		$datausr = $jakdb->select($table, "*");
	}
	
    return $datausr;
}

// Check if user exist and it is possible to delete ## (config.php)
function jak_user_exist_deletable($id) {
	$useridarray = explode(',', JAK_SUPERADMIN);
	// check if userid is protected in the config.php
	if (in_array($id, $useridarray)) {
	    return false;
	} else {
		global $jakdb;
	    if ($jakdb->has("user", ["id" => $id])) return true;
	}
	return false;
}

function secondsToTime($seconds,$time) {
	$singletime = explode(",", $time);
	if (is_numeric($seconds)) {
    	$dtF = new DateTime("@0");
    	$dtT = new DateTime("@$seconds");
    	return $dtF->diff($dtT)->format('%a '.$singletime[0].', %h '.$singletime[1].', %i '.$singletime[2].' '.$singletime[4].' %s '.$singletime[3]);
    } else {
    	return '0 '.$singletime[0].', 0 '.$singletime[1].', 0 '.$singletime[2].' '.$singletime[4].' 0 '.$singletime[3];
    }
}

// Update Operator CC
function updateOperatorCC($opidcc, $ticketid) {

	global $jakdb;

	if (empty($opidcc)) {

		// Delete all entries if we have none
    	$jakdb->delete("support_tickets_cc", ["ticketid" => $ticketid]);
    } else {

        // Get all operators in cc
        $currentcc = $jakdb->select("support_tickets_cc", "operatorid", ["ticketid" => $ticketid]);

        // We check the difference
        $opccremove = array_diff($currentcc, $opidcc);
        $opccadd = array_diff($opidcc, $currentcc);

        // We run the foreach to remove
        if (!empty($opccremove)) foreach ($opccremove as $or) {
            $jakdb->delete("support_tickets_cc", ["AND" => ["ticketid" => $ticketid, "operatorid" => $or]]);
        }

        if (!empty($opccadd)) foreach ($opccadd as $oa) {
        	$jakdb->insert("support_tickets_cc", ["ticketid" => $ticketid, "operatorid" => $oa, "created" => $jakdb->raw("NOW()")]);
        }

    }

    return true;

}
?>