<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_ADMIN_PREVENT_ACCESS')) die('You cannot access this file directly.');

// Check if the user has access to this file
if (!jak_get_access("blog", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$jaktable = 'blog';
$jaktable1 = 'blogcomments';

// We reset some vars
$totalChange = 0;
$lastChange = '';

switch ($page1) {
  case 'new':
    # code...
    
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;
    
        if (empty($jkp['title'])) {
            $errors['e'] = $jkl['e2'];
        }
        
        if (empty($jkp['content'])) { 
          $errors['e1'] = $jkl['e1'];
        }
        
        if (count($errors) == 0) {

          // Get the next order
          $last = $jakdb->get($jaktable, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
          $last = $last + 1;

          $result = $jakdb->insert($jaktable, ["lang" => $jkp['jak_lang'],
            "opid" => JAK_USERID,
            "title" => $jkp['title'],
            "content" => jak_clean_safe_userpost($_REQUEST['content']),
            "previmg" => $jkp['previmg'],
            "showdate" => $jkp['showdate'],
            "comments" => $jkp['comments'],
            "socialbutton" => $jkp['socialbutton'],
            "membersonly" => $jkp['membersonly'],
            "dorder" => $last,
            "active" => 1,
            "time" => $jakdb->raw("NOW()")]);

          $lastid = $jakdb->id();
    
        if (!$lastid) {
          $_SESSION["infomsg"] = $jkl['i'];
          jak_redirect($_SESSION['LCRedirect']);
        } else {

          // Write the log file each time someone tries to login before
          JAK_base::jakWhatslog('', JAK_USERID, 0, 27, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

          $_SESSION["successmsg"] = $jkl['g14'];
          jak_redirect(JAK_rewrite::jakParseurl('blog', 'edit', $lastid));
        }
    
      // Output the errors
      } else {
      
          $errors = $errors;
      }
    
    }

    // Title and Description
    $SECTION_TITLE = $jkl["hd30"];
    $SECTION_DESC = "";

    // Call the language function
    $lang_files = jak_get_lang_files();

    // Include the javascript file for results
    $js_file_footer = 'js_editor.php';

    // Load the template
    $template = 'newblog.php';

  break;
  case 'edit':
    // Check if the user exists
    if (is_numeric($page2) && jak_row_exist($page2,$jaktable)) {
    
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;
    
        if (empty($jkp['title'])) {
            $errors['e'] = $jkl['e2'];
        }
        
        if (empty($jkp['content'])) { 
          $errors['e1'] = $jkl['e1'];
        }
        
        if (count($errors) == 0) {

          $result = $jakdb->update($jaktable, ["lang" => $jkp['jak_lang'],
            "opid" => JAK_USERID,
            "title" => $jkp['title'],
            "content" => jak_clean_safe_userpost($_REQUEST['content']),
            "previmg" => $jkp['previmg'],
            "showdate" => $jkp['showdate'],
            "comments" => $jkp['comments'],
            "socialbutton" => $jkp['socialbutton'],
            "membersonly" => $jkp['membersonly'],
            "dorder" => $jkp['order'],
            "hits" => $jkp['hits']], ["id" => $page2]);
    
        if (!$result) {
            $_SESSION["infomsg"] = $jkl['i'];
            jak_redirect($_SESSION['LCRedirect']);
        } else {

            // Update Time
            if (isset($jkp['updatetime']) && $jkp['updatetime'] == 1) {
              $jakdb->update($jaktable, ["time" => $jakdb->raw("NOW()")], ["id" => $page2]);
            }

            // Delete Comments
            if (isset($jkp['delcom']) && $jkp['delcom'] == 1) {
              $jakdb->delete($jaktable1, ["blogid" => $page2]);
            }

            // Write the log file each time someone tries to login before
            JAK_base::jakWhatslog('', JAK_USERID, 0, 36, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect($_SESSION['LCRedirect']);
        }
    
      // Output the errors
      } else {
      
          $errors = $errors;
      }
    
    }
      // Title and Description
      $SECTION_TITLE = $jkl["hd31"];
      $SECTION_DESC = "";
      
      // Call the language function
      $lang_files = jak_get_lang_files();

      // Get the data
      $JAK_FORM_DATA = jak_get_data($page2, $jaktable);

      // Include the javascript file for results
      $js_file_footer = 'js_editor.php';

      // Load the template
      $template = 'editblog.php';
    
    } else {
        $_SESSION["errormsg"] = $jkl['i3'];
        jak_redirect(JAK_rewrite::jakParseurl('blog'));
    }
  break;
  case 'comment':
    if ($page2 == "status") {
      if (is_numeric($page3) && $jakdb->has($jaktable1, ["id" => $page3])) {
        $jakdb->update($jaktable1, ["approve" => 1], ["id" => $page3]);
        $_SESSION["successmsg"] = $jkl['g14'];
        jak_redirect(BASE_URL);
      }
    } elseif ($page2 == "delete") {
      if (is_numeric($page3) && $jakdb->has($jaktable1, ["id" => $page3])) {
        $jakdb->delete($jaktable1, ["id" => $page3]);
        $_SESSION["successmsg"] = $jkl['g14'];
        jak_redirect(BASE_URL);
      }
    } else {
      $_SESSION["errormsg"] = $jkl['i3'];
      jak_redirect(BASE_URL);
    }
  break;
	default:

    // Let's go on with the script
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;
        
        if (isset($jkp['action']) && $jkp['action'] == "delete") {

          if (!JAK_USERID || !JAK_SUPERADMINACCESS) jak_redirect(BASE_URL);
        
          if (isset($jkp['jak_delete_blogs'])) {
            
            $delartic = $jkp['jak_delete_blogs'];
      
              for ($i = 0; $i < count($delartic); $i++) {
                  $delart = $delartic[$i];
                  $uidacc = explode(":#:", $delart);

                  $jakdb->delete($jaktable, ["id" => $uidacc[0]]);

                  // Write the log file each time someone tries to login before
                  JAK_base::jakWhatslog('', JAK_USERID, 0, 28, $uidacc[0], (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
                
              }
              
              $_SESSION["successmsg"] = $jkl['g14'];
              jak_redirect($_SESSION['LCRedirect']);
          }
      
          $_SESSION["errormsg"] = $jkl['i3'];
          jak_redirect($_SESSION['LCRedirect']);
        
        }

        if (isset($jkp['action']) && $jkp['action'] == "status") {

          if (isset($jkp['jak_delete_blogs'])) {
            
            $delartic = $jkp['jak_delete_blogs'];
      
            for ($i = 0; $i < count($delartic); $i++) {
              $statusu = $delartic[$i];
              $uidacc = explode(":#:", $statusu);

              // Update row
              if ($uidacc[1] == 1) {
                $jakdb->update($jaktable, ["active" => 0], ["id" => $uidacc[0]]);
              } else {
                $jakdb->update($jaktable, ["active" => 1], ["id" => $uidacc[0]]);
              }
                
            }
              
            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect($_SESSION['LCRedirect']);
        }
      
      
        $_SESSION["successmsg"] = $jkl['g14'];
        jak_redirect($_SESSION['LCRedirect']);

      }    
    }
		
		// Leads
		$totalAll = $totalAllC = 0;

    // Get the totals
    $totalAll = $jakdb->count($jaktable);

    // Get the total comments
    $totalAllC = $jakdb->count($jaktable1);

    // How often we had changes
    $totalChange = $jakdb->count("whatslog", ["whatsid" => [27,28,36]]);

    // Last Edit
    if ($totalChange != 0) {
      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [27,28,36], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
    }
		
		// Title and Description
		$SECTION_TITLE = $jkl["hd13"];
		$SECTION_DESC = "";
		
		// Include the javascript file for results
		$js_file_footer = 'js_blog.php';
		
		// Call the template
		$template = 'blog.php';
}
?>