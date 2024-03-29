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
if (!jak_get_access("responses", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$jaktable = 'responses';
$jaktable1 = 'departments';
$jaktable2 = 'support_responses';
$jaktable3 = 'support_departments';

// Reset some vars
$totalChange = 0;
$lastChange = '';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'support':

		switch ($page2) {
			case 'delete':
				# code...
				// Check if user exists and can be deleted
				if (is_numeric($page3)) {
				        
					// Now check how many languages are installed and do the dirty work
					$result = $jakdb->delete($jaktable2, ["id" => $page3]);
				
				if ($result->rowCount() != 1) {
				    $_SESSION["infomsg"] = $jkl['i'];
				    jak_redirect($_SESSION['LCRedirect']);
				} else {
					
					// Now let us delete the define cache file
					$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
					if (file_exists($cachestufffile)) {
						unlink($cachestufffile);
					}

					// Write the log file each time someone tries to login before
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 75, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
				    $_SESSION["successmsg"] = $jkl['g14'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
				    
				} else {
				    
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'edit':
				# code...
				// Check if the user exists
				if (is_numeric($page3) && jak_row_exist($page3,$jaktable2)) {
				
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				    $jkp = $_POST;
				
				    if (empty($jkp['title'])) {
				        $errors['e'] = $jkl['e2'];
				    }
				    
				    if (empty($jkp['response'])) {
				        $errors['e1'] = $jkl['e1'];
				    }
				    
				    if (count($errors) == 0) {

				    	// clean the post
				    	$responsef = jak_clean_safe_userpost($_REQUEST['response']);

				    	$result = $jakdb->update($jaktable2, ["title" => $jkp['title'],
							"depid" => $jkp['jak_depid'],
							"message" => $responsef], ["id" => $page3]);
				
						if (!$result) {
						    $_SESSION["infomsg"] = $jkl['i'];
				    		jak_redirect($_SESSION['LCRedirect']);
						} else {
							
							// Now let us delete the define cache file
							$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
							if (file_exists($cachestufffile)) {
								unlink($cachestufffile);
							}

							// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 73, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
							
						    $_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect($_SESSION['LCRedirect']);
						}
				
					// Output the errors
					} else {
						$errors = $errors;
					}
				
					}
				
					// Title and Description
					$SECTION_TITLE = $jkl["m16"];
					$SECTION_DESC = "";
						
					// Get all departments
					$JAK_DEPARTMENTS = $jakdb->select($jaktable3, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
					
					// Get the data
					$JAK_FORM_DATA = jak_get_data($page3, $jaktable2);

					// Include the javascript file for results
      				$js_file_footer = 'js_editor.php';

      				// Call the template
					$template = 'editresponse.php';
				
				} else {
				    
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect(JAK_rewrite::jakParseurl('response', 'support'));
				}
			break;
			default:
				# code...

				if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_response'])) {
				    $jkp = $_POST;
				    
				    if (empty($jkp['title'])) {
				            $errors['e'] = $jkl['e2'];
				    }
				        
				    if (empty($jkp['response'])) {
				        $errors['e1'] = $jkl['e1'];
				    }
				        
				    if (count($errors) == 0) {

				    	// clean the post
				    	$responsef = jak_clean_safe_userpost($_REQUEST['response']);

				    	$jakdb->insert($jaktable2, ["title" => $jkp['title'],
						"depid" => $jkp['jak_depid'],
						"message" => $responsef]);

						$lastid = $jakdb->id();

				    	if (!$lastid) {
				    		$_SESSION["infomsg"] = $jkl['i'];
				    		jak_redirect($_SESSION['LCRedirect']);
				    	} else {
				    			
					    	// Now let us delete the define cache file
					    	$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
					    	if (file_exists($cachestufffile)) {
					    		unlink($cachestufffile);
					    	}

				    		// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 74, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
				    			
				    		$_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect($_SESSION['LCRedirect']);
				    	}
				    
				    // Output the errors
				    } else {
				    
				        $errors = $errors;
				    }  
		   
				}

				// Get all departments
				$JAK_DEPARTMENTS = $jakdb->select($jaktable3, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
				
				// Get all responses
				$RESPONSES_ALL = jak_get_page_info($jaktable2);

				// How often we had changes
			    $totalChange = $jakdb->count("whatslog", ["whatsid" => [73,74,75]]);

			    // Last Edit
			    if ($totalChange != 0) {
			      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [73,74,75], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
			    }
				
				// Title and Description
				$SECTION_TITLE = $jkl["hd8"];
				$SECTION_DESC = "";
				
				// Include the javascript file for results
				$js_file_footer = 'js_pages.php';
				 
				// Call the template
				$template = 'response.php';
			break;
		}

	break;
	case 'delete':
		 
		// Check if user exists and can be deleted
		if (is_numeric($page2)) {
		        
			// Now check how many languages are installed and do the dirty work
			$result = $jakdb->delete($jaktable, ["id" => $page2]);
		
		if ($result->rowCount() != 1) {
		    $_SESSION["infomsg"] = $jkl['i'];
		    jak_redirect($_SESSION['LCRedirect']);
		} else {
			
			// Now let us delete the define cache file
			$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
			if (file_exists($cachestufffile)) {
				unlink($cachestufffile);
			}

			// Write the log file each time someone tries to login before
          	JAK_base::jakWhatslog('', JAK_USERID, 0, 72, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
			
		    $_SESSION["successmsg"] = $jkl['g14'];
		    jak_redirect($_SESSION['LCRedirect']);
		}
		    
		} else {
		    
		   	$_SESSION["errormsg"] = $jkl['i3'];
		    jak_redirect($_SESSION['LCRedirect']);
		}
		
	break;
	case 'edit':
	
		// Check if the user exists
		if (is_numeric($page2) && jak_row_exist($page2,$jaktable)) {
		
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $jkp = $_POST;
		
		    if (empty($jkp['title'])) {
		        $errors['e'] = $jkl['e2'];
		    }
		    
		    if (empty($jkp['response'])) {
		        $errors['e1'] = $jkl['e1'];
		    }
		    
		    if (count($errors) == 0) {

		    	$result = $jakdb->update($jaktable, ["title" => $jkp['title'],
					"department" => $jkp['jak_depid'],
					"message" => $jkp['response']], ["id" => $page2]);
		
				if (!$result) {
				    $_SESSION["infomsg"] = $jkl['i'];
		    		jak_redirect($_SESSION['LCRedirect']);
				} else {
					
					// Now let us delete the define cache file
					$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
					if (file_exists($cachestufffile)) {
						unlink($cachestufffile);
					}

					// Write the log file each time someone tries to login before
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 70, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
				    $_SESSION["successmsg"] = $jkl['g14'];
		    		jak_redirect($_SESSION['LCRedirect']);
				}
		
			// Output the errors
			} else {
				$errors = $errors;
			}
		
			}
		
			// Title and Description
			$SECTION_TITLE = $jkl["m16"];
			$SECTION_DESC = "";
				
			// Get all departments
			$JAK_DEPARTMENTS = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
			
			// Ge the data
			$JAK_FORM_DATA = jak_get_data($page2, $jaktable);

      		// Call the template
			$template = 'editresponse.php';
		
		} else {
		    
		   	$_SESSION["errormsg"] = $jkl['i3'];
		    jak_redirect(JAK_rewrite::jakParseurl('response'));
		}
		
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_response'])) {
		    $jkp = $_POST;
		    
		    if (empty($jkp['title'])) {
		            $errors['e'] = $jkl['e2'];
		        }
		        
		        if (empty($jkp['response'])) {
		            $errors['e1'] = $jkl['e1'];
		        }
		        
		        if (count($errors) == 0) {

		        	$jakdb->insert($jaktable, ["title" => $jkp['title'],
					"department" => $jkp['jak_depid'],
					"message" => $jkp['response']]);

					$lastid = $jakdb->id();

		    		if (!$lastid) {
		    		    $_SESSION["infomsg"] = $jkl['i'];
		    			jak_redirect($_SESSION['LCRedirect']);
		    		} else {
		    			
		    			// Now let us delete the define cache file
		    			$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
		    			if (file_exists($cachestufffile)) {
		    				unlink($cachestufffile);
		    			}

		    			// Write the log file each time someone tries to login before
          				JAK_base::jakWhatslog('', JAK_USERID, 0, 71, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
		    			
		    		    $_SESSION["successmsg"] = $jkl['g14'];
		    			jak_redirect($_SESSION['LCRedirect']);
		    		}
		    
		    // Output the errors
		    } else {
		    
		        $errors = $errors;
		    }  
   
		}
		 
		// Get all department
		$JAK_DEPARTMENTS = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
		
		// Get all responses
		$RESPONSES_ALL = jak_get_page_info($jaktable);

		// How often we had changes
	    $totalChange = $jakdb->count("whatslog", ["whatsid" => [70,71,72]]);

	    // Last Edit
	    if ($totalChange != 0) {
	      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [70,71,72], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
	    }
		
		// Title and Description
		$SECTION_TITLE = $jkl["hd7"];
		$SECTION_DESC = "";
		
		// Include the javascript file for results
		$js_file_footer = 'js_pages.php';
		 
		// Call the template
		$template = 'response.php';
}
?>