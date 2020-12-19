<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 1.0                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2018 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_PREVENT_ACCESS')) die('No direct access!');

// Get the important database table
$jaktable2 = 'faq_article';
$jaktable3 = 'faq_categories';

$SearchInput = "";

// Now do the dirty work with the post vars
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['smart_search']) || !empty($page1)) {

    $jkp = $_POST;
    
    if (isset($jkp['smart_search'])) {
	
	    if (empty($page1) && $jkp['smart_search'] == '' || $jkp['smart_search'] == $jkl['hd']) {
	        $errors['e'] = $jkl['hd2'];
	    }
	
	    if (empty($errors['e']) && empty($page1) && strlen($jkp['smart_search']) < '2') {
	        $errors['e1'] = $jkl['hd3'];
	    }
	    
	}

    if (count($errors) > 0) {
        $errors = $errors;
    } else {
    
    	if (!empty($page1)) {
    		$SearchInput = filter_var($page1, FILTER_SANITIZE_STRING);
    	} else {
    		$SearchInput = filter_var($jkp['smart_search'], FILTER_SANITIZE_STRING);
    	}

    	$searchresult = array();

    	// Client Access
    	if (JAK_CLIENTID) {
    		// All access
    		if ($jakclient->getVar("faq_cat") == 0) {
    			$searchresult = $jakdb->select($jaktable2, ["[>]".$jaktable3 => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_article.time", "faq_categories.class", "faq_categories.title(titlecat)"], ["faq_article.active" => 1,
				"MATCH" => [
					"columns" => ["faq_article.content", "faq_article.title"],
					"keyword" => $SearchInput],
				"LIMIT" => 10
				]);
			// Only for certain categories
    		} else {
    			$searchresult = $jakdb->select($jaktable2, ["[>]".$jaktable3 => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_article.time", "faq_categories.class", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.catid" => [$jakclient->getVar("faq_cat")], "faq_article.active" => 1],
				"MATCH" => [
					"columns" => ["faq_article.content", "faq_article.title"],
					"keyword" => $SearchInput],
				"LIMIT" => 10
				]);
    		}
    	// Can see all active articles
    	} elseif (JAK_USERID) {
    		$searchresult = $jakdb->select($jaktable2, ["[>]".$jaktable3 => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_article.time", "faq_categories.class", "faq_categories.title(titlecat)"], ["faq_article.active" => 1,
			"MATCH" => [
				"columns" => ["faq_article.content", "faq_article.title"],
				"keyword" => $SearchInput],
			"LIMIT" => 10
			]);
		// Can see categories for guests
    	} else {
    		$searchresult = $jakdb->select($jaktable2, ["[>]".$jaktable3 => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_article.time", "faq_categories.class", "faq_categories.title(titlecat)"], ["AND" => ["faq_categories.guesta" => 1, "faq_article.active" => 1],
			"MATCH" => [
				"columns" => ["faq_article.content", "faq_article.title"],
				"keyword" => $SearchInput],
				"LIMIT" => 10
			]);
    	}
        	    	
    }
}

// Load the template
include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/search.php';
?>