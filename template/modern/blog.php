<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2019 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_PREVENT_ACCESS')) die('No direct access!');

// FAQ is turned off go back to home
if (!JAK_BLOG_A) jak_redirect(BASE_URL);

// Include the comment class file
require_once APP_PATH.'class/class.comment.php';

// Get the important database table
$jaktable2 = 'blog';
$jaktable3 = 'blogcomments';

// The footer url for similar articles
$similarlink = JAK_BLOG_URL;
$similarshort = 'a';
$similartitle = $jkl['hd124'];

// Get the private blogs as well.
$membersonly = 0;
if (JAK_USERID || JAK_CLIENTID) $membersonly = 1;
$JAK_COMMENT_FORM = $BLOGADMIN = false;
$CHECK_USR_SESSION = session_id();

if (JAK_USERID && jak_get_access("blog", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) $BLOGADMIN = true;

// Delete the comment
if (isset($page1) && isset($page2) && is_numeric($page2) && $page1 == "del" && $BLOGADMIN) {

	if ($jakdb->has($jaktable3, ["id" => $page2])) {

		$jakdb->delete($jaktable3, ["id" => $page2]);

		$_SESSION["successmsg"] = $jkl['s'];
		jak_redirect($_SESSION['LCRedirect']);
	} else {
		$_SESSION["errrmsg"] = $jkl['not'];
		jak_redirect(JAK_rewrite::jakParseurl(JAK_BLOG_URL));
	}

}

// Now do the dirty work with the post vars
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_comment']) && !empty($page1)) {
    $jkp = $_POST;

    $arr = array();
			
	$validates = JAK_comment::validate_form($arr, "2000", $jkl['e2'], $jkl['hd73']);
			
	if ($validates) {
	/* Everything is OK, insert to database: */
				
		$cleanuserpostB = htmlspecialchars_decode(jak_clean_safe_userpost($arr['bmessage']));
				
		// is this an answer of another comment
		$quotemsg = 0;
		if (isset($arr['comanswerid']) && $arr['comanswerid'] > 0) $quotemsg = $arr['comanswerid'];

		if (isset($arr['editpostid']) && $arr['editpostid'] > 0 && $BLOGADMIN) {

			$jakdb->update($jaktable3, ["message" => $cleanuserpostB], ["id" => $arr['editpostid']]);
	
			// Output the header	
			header('Cache-Control: no-cache');
			die(json_encode(array('status' => 2, 'id' => $arr['editpostid'], 'html' => $cleanuserpostB)));
		}
				
		// the new session check for displaying messages to user even if not approved
		$sqlset = 0;
		$blogapprove = 1;
		if (!JAK_BLOGPOSTAPPROVE) {
			$sqlset = session_id();
			if (!JAK_USERID) {
				$blogapprove = 0;
			}
		}
				
		if (JAK_USERISLOGGED) {

			if (JAK_USERID) {
				$jakdb->insert($jaktable3, ["blogid" => $page2, "commentid" => $quotemsg, "operatorid" => JAK_USERID, "message" => $cleanuserpostB, "approve" => $blogapprove, "time" => $jakdb->raw("NOW()"), "session" => $sqlset]);
			} else {
				$jakdb->insert($jaktable3, ["blogid" => $page2, "commentid" => $quotemsg, "clientid" => JAK_CLIENTID, "message" => $cleanuserpostB, "approve" => $blogapprove, "time" => $jakdb->raw("NOW()"), "session" => $sqlset]);
			}
					
			$arr['id'] = $jakdb->id();
				
		}
				
		$arr['created'] = JAK_Base::jakTimesince(time(), JAK_DATEFORMAT, JAK_TIMEFORMAT);
		
		// Get the last comment		
		$acajax = new JAK_comment($arr['id'], "id", JAK_BLOG_URL, JAK_DATEFORMAT, JAK_TIMEFORMAT, $BLOGADMIN);
		
		// Output the header	
		header('Cache-Control: no-cache');
		die(json_encode(array('status' => 1, 'html' => $acajax->get_commentajax_modern($jkl['hd69']))));
			
	} else {
		/* Outputtng the error messages */
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			
			header('Cache-Control: no-cache');
			die('{"status":0, "errors":'.json_encode($arr).'}');
				
		} else {
			
			$errors = $arr;
		}
	}


}

// We edit some client details
if ($page1 == "a" && isset($page2) && is_numeric($page2) && jak_row_exist($page2, $jaktable2)) {

	// Get the data
	$JAK_FORM_DATA = jak_get_data_front($page2, $jaktable2);

	if ($JAK_FORM_DATA["membersonly"] == 1 && !JAK_USERISLOGGED) jak_redirect(JAK_rewrite::jakParseurl(JAK_BLOG_URL));

	// Load Comments if we have to
	if ($JAK_FORM_DATA["comments"]) {
		$ac = new JAK_comment($page2, "blogid", JAK_BLOG_URL, JAK_DATEFORMAT, JAK_TIMEFORMAT, $BLOGADMIN);

		$comments_naked = $ac->get_comments();
			
		// Get the header navigation
		$JAK_COMMENTS = array(
			'comm' => array(),
			'subcomm' => array()
		);
		// Builds the array lists with data from the menu table
		if (isset($comments_naked)) foreach ($comments_naked as $comm) {
			// Creates entry into items array with current menu item id ie. $menu['items'][1]
			$JAK_COMMENTS['comm'][$comm['id']] = $comm;
			// Creates entry into parents array. Parents array contains a list of all items with children
			$JAK_COMMENTS['subcomm'][$comm['commentid']][] = $comm['id'];
		}

		$JAK_COMMENTS_TOTAL = $ac->get_total();

	}

	// Get the last comments
	if ($membersonly) {
		$jak_comments = $jakdb->select($jaktable3, ["[>]".$jaktable2 => ["blogid" => "id"]], ["blog.id", "blog.title", "blogcomments.message", "blogcomments.time"], ["AND" => ["blogcomments.approve" => 1, "blog.lang" => $BT_LANGUAGE, "blog.active" => 1], "ORDER" => ["blogcomments.time" => "DESC"], "LIMIT" => 5]);
	} else {
		$jak_comments = $jakdb->select($jaktable3, ["[>]".$jaktable2 => ["blogid" => "id"]], ["blog.id", "blog.title", "blogcomments.message", "blogcomments.time"], ["AND" => ["blogcomments.approve" => 1, "blog.membersonly" => 0, "blog.lang" => $BT_LANGUAGE, "blog.active" => 1], "ORDER" => ["blogcomments.time" => "DESC"], "LIMIT" => 5]);
	}

	// Page Nav
	$JAK_NAV_NEXT = $JAK_NAV_NEXT_TITLE = $JAK_NAV_PREV = $JAK_NAV_PREV_TITLE = "";
	$nextp = jak_next_page($page2, $membersonly, $BT_LANGUAGE);
	if ($nextp) {
		$JAK_NAV_NEXT = JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $nextp['id'], JAK_rewrite::jakCleanurl($nextp['title']));
		$JAK_NAV_NEXT_TITLE = $nextp['title'];
	}
		
	$prevp = jak_previous_page($page2, $membersonly, $BT_LANGUAGE);
	if ($prevp) {
		$JAK_NAV_PREV = JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $prevp['id'], JAK_rewrite::jakCleanurl($prevp['title']));
		$JAK_NAV_PREV_TITLE = $prevp['title'];
	}

	// Get the title for the similar
  	$titlearray = explode(" ", $JAK_FORM_DATA["title"], 5);
  	$titlearray = array_filter($titlearray,function($v){ return strlen($v) > 2; });

	// Similar
    $similarart = $jakdb->select($jaktable2, ["id", "previmg", "title", "content", "time"], ["AND" => ["id[!]" => $page2, "title[~]" => $titlearray], "LIMIT" => 3]);

	// Finally get the operator details
	$JAK_OP_DETAILS = $jakdb->get("user", ["username", "name", "picture", "aboutme"], ["id" => $JAK_FORM_DATA["opid"]]);

	// Get the custom stuff from the selected template
	$styleconfig = APP_PATH.'template/modern/config.php';
	if (file_exists($styleconfig)) include_once $styleconfig;

	// Include the javascript file for results
	$js_file_footer = 'js_blogart.php';

	// Load the template
	include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/blogart.php';

} else {

	// include the class
	include_once(APP_PATH.'class/class.paginator.php');

	// Reset some vars
	$getTotal = 0;

	// Get the total
	if ($membersonly) {
		$getTotal = $jakdb->count("blog");
	} else {
		$getTotal = $jakdb->count("blog", ["membersonly" => 0]);
	}

	if ($getTotal != 0) {
	      
	  // Paginator
	  $pages = new JAK_Paginator;
	  $pages->items_total = $getTotal;
	  $pages->mid_range = JAK_BLOG_PAGINATION;
	  $pages->items_per_page = JAK_BLOG_PAGE;
	  $pages->jak_get_page = $page1;
	  $pages->jak_where = JAK_rewrite::jakParseurl(JAK_BLOG_URL);
	  $pages->paginate();
	  $JAK_PAGINATE = $pages->display_pages();

	  // Get the result
	  if ($membersonly) {
	  	$jak_blogs = $jakdb->select($jaktable2, ["id", "title", "content", "previmg", "time"], ["AND" => ["lang" => $BT_LANGUAGE, "active" => 1], "ORDER" => ["dorder" => "DESC"], "LIMIT" => $pages->limit]);
	  } else {
	  	$jak_blogs = $jakdb->select($jaktable2, ["id", "title", "content", "previmg", "time"], ["AND" => ["membersonly" => 0, "lang" => $BT_LANGUAGE, "active" => 1], "ORDER" => ["dorder" => "DESC"], "LIMIT" => $pages->limit]);
	  }

	}

	// Get the last comments
	if ($membersonly) {
		$jak_comments = $jakdb->select($jaktable3, ["[>]".$jaktable2 => ["blogid" => "id"]], ["blog.id", "blog.title", "blogcomments.message", "blogcomments.time"], ["AND" => ["blogcomments.approve" => 1, "blog.lang" => $BT_LANGUAGE, "blog.active" => 1], "ORDER" => ["blogcomments.time" => "DESC"], "LIMIT" => 5]);
	} else {
		$jak_comments = $jakdb->select($jaktable3, ["[>]".$jaktable2 => ["blogid" => "id"]], ["blog.id", "blog.title", "blogcomments.message", "blogcomments.time"], ["AND" => ["blogcomments.approve" => 1, "blog.membersonly" => 0, "blog.lang" => $BT_LANGUAGE, "blog.active" => 1], "ORDER" => ["blogcomments.time" => "DESC"], "LIMIT" => 5]);
	}

	// Load the template
	include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/blog.php';
}
?>