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
if (!jak_get_access("departments", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$jaktable = 'departments';
$jaktable1 = 'support_departments';
$jaktable2 = 'faq_categories';
$jaktable3 = 'translations';

// Reset some vars
$newdep = true;
$totalChange = 0;
$busy_department = '-';
$lastChange = '';

// Call the language function
$lang_files = jak_get_lang_files(JAK_LANG);

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'support':

		switch ($page2) {
			case 'delete':
				// Remove department
				if (is_numeric($page3) && $page3 != 1) {
				        
					// Now check how many languages are installed and do the dirty work
					$result = $jakdb->delete($jaktable1, ["id" => $page3]);
				
					if ($result->rowCount() != 1) {
				    	$_SESSION["infomsg"] = $jkl['i'];
				    	jak_redirect($_SESSION['LCRedirect']);
					} else {
					
						// Now let us delete the define cache file
						$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
						if (file_exists($cachestufffile)) {
							unlink($cachestufffile);
						}

						// Delete translations
						$jakdb->delete($jaktable3, ["support_dep" => $page3]);

						// Write the log file each time someone tries to login before
          				JAK_base::jakWhatslog('', JAK_USERID, 0, 66, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
				    	$_SESSION["successmsg"] = $jkl['g14'];
				    	jak_redirect($_SESSION['LCRedirect']);
					}
				    
				} else {
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'edit':
				// Check if department exists
				if (is_numeric($page3) && jak_row_exist($page3,$jaktable1)) {
				
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				    $jkp = $_POST;
				
				    if (empty($jkp['title'])) {
				        $errors['e'] = $jkl['e2'];
				    }
				    
				    if ($jkp['email'] != '' && !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) { 
				    	$errors['e1'] = $jkl['e3'];
				    }
				    
				    if (count($errors) == 0) {

				    	// clean the post
				    	$pre_content = '';
				    	if (isset($_REQUEST["predefined_content"]) && !empty($_REQUEST["predefined_content"])) $pre_content = jak_clean_safe_userpost($_REQUEST['predefined_content']);

				    	$result = $jakdb->update($jaktable1, ["title" => $jkp['title'],
							"description" => $jkp['description'],
							"pre_content" => $pre_content,
							"faq_url" => $jkp['faq'],
							"credits" => $jkp['credits'],
							"email" => $jkp['email'],
							"guesta" => $jkp['jak_guesta'],
							"time" => $jakdb->raw("NOW()")], ["id" => $page3]);
				
						if (!$result) {
						    $_SESSION["infomsg"] = $jkl['i'];
				    		jak_redirect($_SESSION['LCRedirect']);
						} else {

							// Now we store the translations, that will be nasty
							if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) {
								if (isset($jkp['title_'.$lf]) && !empty($jkp['title_'.$lf])) {
									// Now check if the language already exist
									if ($jakdb->has($jaktable3, ["AND" => ["support_dep" => $page3, "lang" => $lf]])) {
										$jakdb->update($jaktable3, ["title" => $jkp['title_'.$lf], "description" => $jkp['description_'.$lf], "faq_url" => $jkp['faqurl_'.$lf]], ["AND" => ["support_dep" => $page3, "lang" => $lf]]);
									} else {
										$jakdb->insert($jaktable3, ["lang" => $lf, "support_dep" => $page3, "title" => $jkp['title_'.$lf], "description" => $jkp['description_'.$lf], "faq_url" => $jkp['faqurl_'.$lf], "time" => $jakdb->raw("NOW()")]);
									}
								}
								// Delete the entry
								if (isset($jkp['deltrans_'.$lf]) && !empty($jkp['deltrans_'.$lf])) {
									$jakdb->delete($jaktable3, ["AND" => ["support_dep" => $page3, "lang" => $jkp['deltrans_'.$lf]]]);
								}
							}
							
							// Now let us delete the define cache file
							$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
							if (file_exists($cachestufffile)) {
								unlink($cachestufffile);
							}

							// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 64, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
							
						    $_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect(JAK_rewrite::jakParseurl('departments', 'support'));
						}
				
				// Output the errors
				} else {
				
				    $errors = $errors;
				}
				
				}
					// Title and Description
					$SECTION_TITLE = $jkl["m17"];
					$SECTION_DESC = "";

					// Get translations
					$JAK_DEP_TRANSLATION = $jakdb->select($jaktable3, ["id", "lang", "title", "description", "faq_url"], ["support_dep" => $page3]);
				
					$JAK_FORM_DATA = jak_get_data($page3, $jaktable1);

					// Include the javascript file for results
					$js_file_footer = 'js_department.php';

					// Get the template file
					$template = 'editdepartment.php';
				
				} else {
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'lock':
				// Set department status
				if (is_numeric($page3) && $page3 != 1) {

					// Check what we have to do
					$datausrac = $jakdb->get($jaktable1, "active", ["id" => $page3]);
					// update the table
					if ($datausrac) {
						$result = $jakdb->update($jaktable1, ["active" => 0], ["id" => $page3]);
					} else {
						$result = $jakdb->update($jaktable1, ["active" => 1], ["id" => $page3]);
					}
					
					if (!$result) {
						$_SESSION["infomsg"] = $jkl['i'];
				    	jak_redirect($_SESSION['LCRedirect']);
					} else {
						
						// Now let us delete the define cache file
						$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
						if (file_exists($cachestufffile)) {
							unlink($cachestufffile);
						}
						
					    $_SESSION["successmsg"] = $jkl['g14'];
				    	jak_redirect($_SESSION['LCRedirect']);
					}
				
				} else {
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			default:
				# code...
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				    $jkp = $_POST;

				    // Hosting is active we need to count the total operators
					if ($jakhs['hostactive']) {
						$totaldep = $jakdb->count($jaktable1);

						if ($totaldep >= $jakhs['departments']) {
							$_SESSION["errormsg"] = $jkl['i6'];
				    		jak_redirect($_SESSION['LCRedirect']);
						}
					}
				    
				    if (isset($_POST['insert_department'])) {
				    
				    if (empty($jkp['title'])) {
				    	$errors['e'] = $jkl['e2'];
				    }
				    
				    if ($jkp['email'] != '' && !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) { 
				    	$errors['e1'] = $jkl['e3'];
				    }
				        
				    if (count($errors) == 0) {

				    	// Get the next order
				    	$last = $jakdb->get($jaktable1, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
				    	$last = $last + 1;

				    	$jakdb->insert($jaktable1, ["title" => $jkp['title'],
							"description" => $jkp['description'],
							"email" => $jkp['email'],
							"faq_url" => $jkp['faq'],
							"credits" => $jkp['credits'],
							"guesta" => $jkp['jak_guesta'],
							"dorder" => $last,
							"time" => $jakdb->raw("NOW()")]);

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
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 65, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
				    			
				    		$_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect($_SESSION['LCRedirect']);
				    	}
				    
				    // Output the errors
				    } else {
				    
				        $errors = $errors;
				    }
				 }
				    
				 if (isset($jkp['corder']) && isset($jkp['real_dep_id'])) {
				     
				 	$dorders = $jkp['corder'];
				    $depid = $jkp['real_dep_id'];
				    $dep = array_combine($depid, $dorders);
				    $updatesql = '';       
				   	
				   	foreach ($dep as $key => $order) {
				    	$result = $jakdb->update($jaktable1, ["dorder" => $order], ["id" => $key]);
				    }
				             
				    if (!$result) {
				 		$_SESSION["infomsg"] = $jkl['i'];
				    	jak_redirect($_SESSION['LCRedirect']);
				 	} else {
				 	
				 		// Now let us delete the define cache file
				 		$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
				 		if (file_exists($cachestufffile)) {
				 			unlink($cachestufffile);
				 		}
				 	
				     	$_SESSION["successmsg"] = $jkl['g14'];
				    	jak_redirect($_SESSION['LCRedirect']);
				 	}
				 	
				 }
				    
		   
				 }
				
				// Get all departments
				$DEPARTMENTS_ALL = $jakdb->select($jaktable1, "*", ["ORDER" => ["dorder" => "ASC"]]);

				// Get the busiest operator
				$busy_department = $jakdb->query("SELECT COUNT(t1.depid) AS mostDEP, t2.title FROM ".JAKDB_PREFIX."support_tickets AS t1 LEFT JOIN ".JAKDB_PREFIX."support_departments AS t2 ON(t1.depid = t2.id) GROUP BY t1.depid ORDER BY mostDEP DESC LIMIT 1")->fetch();

				// How often we had changes
			    $totalChange = $jakdb->count("whatslog", ["whatsid" => [64,65,66]]);

			    // Last Edit
			    if ($totalChange != 0) {
			      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [64,65,66], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
			    }

				// Hosting is active we need to count the total departments
				if ($jakhs['hostactive']) {
					$totaldep = $jakdb->count($jaktable1);
					if ($totaldep >= $jakhs['departments']) $newdep = false;
				}
				 
				// Title and Description
				$SECTION_TITLE = $jkl["hd3"];
				$SECTION_DESC = "";
				
				// Include the javascript file for results
				$js_file_footer = 'js_pages.php';
				
				// Call the template
				$template = 'departments.php';
			break;
		}

	break;
	case 'faq':

		switch ($page2) {
			case 'delete':
				# code...
				// Check if user exists and can be deleted
				if (is_numeric($page3) && $page3 != 1) {
				        
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

						// Delete translations
						$jakdb->delete($jaktable3, ["faq_cat" => $page3]);

						// Write the log file each time someone tries to login before
          				JAK_base::jakWhatslog('', JAK_USERID, 0, 69, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
				    	$_SESSION["successmsg"] = $jkl['g14'];
				    	jak_redirect($_SESSION['LCRedirect']);
					}
				    
				} else {
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'edit':
				// Check if the department exists
				if (is_numeric($page3) && jak_row_exist($page3,$jaktable2)) {
				
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				    $jkp = $_POST;
				
				    if (empty($jkp['title'])) {
				        $errors['e'] = $jkl['e2'];
				    }
				    
				    if ($jkp['email'] != '' && !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) { 
				    	$errors['e1'] = $jkl['e3'];
				    }
				    
				    if (count($errors) == 0) {

				    	$result = $jakdb->update($jaktable2, ["class" => $jkp["jak_class"],
				    		"title" => $jkp['title'],
							"description" => $jkp['description'],
							"email" => $jkp['email'],
							"guesta" => $jkp['jak_guesta'],
							"time" => $jakdb->raw("NOW()")], ["id" => $page3]);
				
						if (!$result) {
						    $_SESSION["infomsg"] = $jkl['i'];
				    		jak_redirect($_SESSION['LCRedirect']);
						} else {

							// Now we store the translations, that will be nasty
							if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) {
								if (isset($jkp['title_'.$lf]) && !empty($jkp['title_'.$lf])) {
									// Now check if the language already exist
									if ($jakdb->has($jaktable3, ["AND" => ["faq_cat" => $page3, "lang" => $lf]])) {
										$jakdb->update($jaktable3, ["title" => $jkp['title_'.$lf], "description" => $jkp['description_'.$lf]], ["AND" => ["depid" => $page2, "lang" => $lf]]);
									} else {
										$jakdb->insert($jaktable3, ["lang" => $lf, "faq_cat" => $page3, "title" => $jkp['title_'.$lf], "description" => $jkp['description_'.$lf], "time" => $jakdb->raw("NOW()")]);
									}
								}
								// Delete the entry
								if (isset($jkp['deltrans_'.$lf]) && !empty($jkp['deltrans_'.$lf])) {
									$jakdb->delete($jaktable3, ["AND" => ["faq_cat" => $page3, "lang" => $jkp['deltrans_'.$lf]]]);
								}
							}
							
							// Now let us delete the define cache file
							$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
							if (file_exists($cachestufffile)) {
								unlink($cachestufffile);
							}

							// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 67, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
							
						    $_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect(JAK_rewrite::jakParseurl('departments', 'faq'));
						}
				
				// Output the errors
				} else {
				
				    $errors = $errors;
				}
				
				}
					// Title and Description
					$SECTION_TITLE = $jkl["hd19"];
					$SECTION_DESC = "";

					// Get translations
					$JAK_DEP_TRANSLATION = $jakdb->select($jaktable3, ["id", "lang", "title", "description"], ["faq_cat" => $page3]);
				
					$JAK_FORM_DATA = jak_get_data($page3, $jaktable2);
					$template = 'editdepartment.php';
				
				} else {
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'lock':
				// Set department status
				if (is_numeric($page3) && $page3 != 1) {

					// Check what we have to do
					$datausrac = $jakdb->get($jaktable2, "active", ["id" => $page3]);
					// update the table
					if ($datausrac) {
						$result = $jakdb->update($jaktable2, ["active" => 0], ["id" => $page3]);
					} else {
						$result = $jakdb->update($jaktable2, ["active" => 1], ["id" => $page3]);
					}
					
					if (!$result) {
						$_SESSION["infomsg"] = $jkl['i'];
				    	jak_redirect($_SESSION['LCRedirect']);
					} else {
						
						// Now let us delete the define cache file
						$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
						if (file_exists($cachestufffile)) {
							unlink($cachestufffile);
						}
						
					    $_SESSION["successmsg"] = $jkl['g14'];
				    	jak_redirect($_SESSION['LCRedirect']);
					}
				
				} else {
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			default:
				# code...
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				    $jkp = $_POST;

				    // Hosting is active we need to count the total operators
					if ($jakhs['hostactive']) {
						$totaldep = $jakdb->count($jaktable2);

						if ($totaldep >= $jakhs['departments']) {
							$_SESSION["errormsg"] = $jkl['i6'];
				    		jak_redirect($_SESSION['LCRedirect']);
						}
					}
				    
				    if (isset($_POST['insert_department'])) {
				    
				    if (empty($jkp['title'])) {
				    	$errors['e'] = $jkl['e2'];
				    }
				    
				    if ($jkp['email'] != '' && !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) { 
				    	$errors['e1'] = $jkl['e3'];
				    }
				        
				    if (count($errors) == 0) {

				    	// Get the next order
				    	$last = $jakdb->get($jaktable2, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
				    	$last = $last + 1;

				    	$jakdb->insert($jaktable2, ["title" => $jkp['title'],
							"description" => $jkp['description'],
							"email" => $jkp['email'],
							"guesta" => $jkp['jak_guesta'],
							"dorder" => $last,
							"time" => $jakdb->raw("NOW()")]);

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
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 68, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
				    			
				    		$_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect($_SESSION['LCRedirect']);
				    	}
				    
				    // Output the errors
				    } else {
				    
				        $errors = $errors;
				    }
				 }
				    
				 if (isset($jkp['corder']) && isset($jkp['real_dep_id'])) {
				     
				 	$dorders = $jkp['corder'];
				    $depid = $jkp['real_dep_id'];
				    $dep = array_combine($depid, $dorders);
				    $updatesql = '';       
				   	
				   	foreach ($dep as $key => $order) {
				    	$result = $jakdb->update($jaktable2, ["dorder" => $order], ["id" => $key]);
				    }
				             
				    if (!$result) {
				 		$_SESSION["infomsg"] = $jkl['i'];
				    	jak_redirect($_SESSION['LCRedirect']);
				 	} else {
				 	
				 		// Now let us delete the define cache file
				 		$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
				 		if (file_exists($cachestufffile)) {
				 			unlink($cachestufffile);
				 		}
				 	
				     	$_SESSION["successmsg"] = $jkl['g14'];
				    	jak_redirect($_SESSION['LCRedirect']);
				 	}
				 	
				 }
				    
		   
				 }
				
				// Get all departments
				$DEPARTMENTS_ALL = $jakdb->select($jaktable2, "*", ["ORDER" => ["dorder" => "ASC"]]);

				// Get the busiest operator
				$busy_department = $jakdb->query("SELECT COUNT(t1.catid) AS mostDEP, t2.title FROM ".JAKDB_PREFIX."faq_article AS t1 LEFT JOIN ".JAKDB_PREFIX."faq_categories AS t2 ON(t1.catid = t2.id) GROUP BY t1.catid ORDER BY mostDEP DESC LIMIT 1")->fetch();

				// How often we had changes
			    $totalChange = $jakdb->count("whatslog", ["whatsid" => [67,68,69]]);

			    // Last Edit
			    if ($totalChange != 0) {
			      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [67,68,69], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
			    }

				// Hosting is active we need to count the total departments
				if ($jakhs['hostactive']) {
					$totaldep = $jakdb->count($jaktable2);
					if ($totaldep >= $jakhs['departments']) $newdep = false;
				}
				 
				// Title and Description
				$SECTION_TITLE = $jkl["hd4"];
				$SECTION_DESC = "";
				
				// Include the javascript file for results
				$js_file_footer = 'js_pages.php';
				
				// Call the template
				$template = 'departments.php';
			break;
		}

	break;
	case 'delete':
		 
		// Check if user exists and can be deleted
		if (is_numeric($page2) && $page2 != 1) {
		        
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

				// Delete translations
				$jakdb->delete($jaktable3, ["chat_dep" => $page3]);

				// Write the log file each time someone tries to login before
          		JAK_base::jakWhatslog('', JAK_USERID, 0, 63, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
			
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
		    
		    if ($jkp['email'] != '' && !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) { 
		    	$errors['e1'] = $jkl['e3'];
		    }
		    
		    if (count($errors) == 0) {

		    	$result = $jakdb->update($jaktable, ["title" => $jkp['title'],
					"description" => $jkp['description'],
					"email" => $jkp['email'],
					"faq_url" => $jkp['faq'],
					"credits" => $jkp['credits'],
					"guesta" => $jkp['jak_guesta'],
					"time" => $jakdb->raw("NOW()")], ["id" => $page2]);
		
				if (!$result) {
				    $_SESSION["infomsg"] = $jkl['i'];
		    		jak_redirect($_SESSION['LCRedirect']);
				} else {

					// Now we store the translations, that will be nasty
					if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) {
						if (isset($jkp['title_'.$lf]) && !empty($jkp['title_'.$lf])) {
							// Now check if the language already exist
							if ($jakdb->has($jaktable3, ["AND" => ["chat_dep" => $page2, "lang" => $lf]])) {
								$jakdb->update($jaktable3, ["title" => $jkp['title_'.$lf], "description" => $jkp['description_'.$lf], "faq_url" => $jkp['faqurl_'.$lf]], ["AND" => ["chat_dep" => $page2, "lang" => $lf]]);
							} else {
								$jakdb->insert($jaktable3, ["lang" => $lf, "chat_dep" => $page2, "title" => $jkp['title_'.$lf], "description" => $jkp['description_'.$lf], "faq_url" => $jkp['faqurl_'.$lf], "time" => $jakdb->raw("NOW()")]);
							}
						}
						// Delete the entry
						if (isset($jkp['deltrans_'.$lf]) && !empty($jkp['deltrans_'.$lf])) {
							$jakdb->delete($jaktable3, ["AND" => ["chat_dep" => $page2, "lang" => $jkp['deltrans_'.$lf]]]);
						}
					}

					// Now let us delete the define cache file
					$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
					if (file_exists($cachestufffile)) {
						unlink($cachestufffile);
					}

					// Write the log file each time someone tries to login before
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 61, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
				    $_SESSION["successmsg"] = $jkl['g14'];
		    		jak_redirect($_SESSION['LCRedirect']);
				}
		
		// Output the errors
		} else {
		
		    $errors = $errors;
		}
		
		}
			// Title and Description
			$SECTION_TITLE = $jkl["m17"];
			$SECTION_DESC = "";

			// Get translations
			$JAK_DEP_TRANSLATION = $jakdb->select($jaktable3, ["id", "lang", "title", "description", "faq_url"], ["chat_dep" => $page2]);
		
			$JAK_FORM_DATA = jak_get_data($page2, $jaktable);
			$template = 'editdepartment.php';
		
		} else {
		   	$_SESSION["errormsg"] = $jkl['i3'];
		    jak_redirect(JAK_rewrite::jakParseurl('departments'));
		}
		
	break;
	case 'lock':
	
		// Check if user exists and can be deleted
		if (is_numeric($page2) && $page2 != 1) {

			// Check what we have to do
			$datausrac = $jakdb->get($jaktable, "active", ["id" => $page2]);
			// update the table
			if ($datausrac) {
				$result = $jakdb->update($jaktable, ["active" => 0], ["id" => $page2]);
			} else {
				$result = $jakdb->update($jaktable, ["active" => 1], ["id" => $page2]);
			}
			
			if (!$result) {
				$_SESSION["infomsg"] = $jkl['i'];
		    jak_redirect($_SESSION['LCRedirect']);
			} else {
				
				// Now let us delete the define cache file
				$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
				if (file_exists($cachestufffile)) {
					unlink($cachestufffile);
				}
				
			    $_SESSION["successmsg"] = $jkl['g14'];
		    	jak_redirect($_SESSION['LCRedirect']);
			}
		
		} else {
		   	$_SESSION["errormsg"] = $jkl['i3'];
		    jak_redirect($_SESSION['LCRedirect']);
		}
	
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $jkp = $_POST;

		    // Hosting is active we need to count the total operators
			if ($jakhs['hostactive']) {
				$totaldep = $jakdb->count($jaktable);

				if ($totaldep >= $jakhs['departments']) {
					$_SESSION["errormsg"] = $jkl['i6'];
		    		jak_redirect($_SESSION['LCRedirect']);
				}
			}
		    
		    if (isset($_POST['insert_department'])) {
		    
		    if (empty($jkp['title'])) {
		    	$errors['e'] = $jkl['e2'];
		    }
		    
		    if ($jkp['email'] != '' && !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) { 
		    	$errors['e1'] = $jkl['e3'];
		    }
		        
		    if (count($errors) == 0) {

		    	// Get the next order
		    	$last = $jakdb->get($jaktable, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
		    	$last = $last + 1;

		    	$jakdb->insert($jaktable, ["title" => $jkp['title'],
					"description" => $jkp['description'],
					"email" => $jkp['email'],
					"faq_url" => $jkp['faq'],
					"credits" => $jkp['credits'],
					"guesta" => $jkp['jak_guesta'],
					"dorder" => $last,
					"time" => $jakdb->raw("NOW()")]);

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
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 62, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
		    			
		    		$_SESSION["successmsg"] = $jkl['g14'];
		    		jak_redirect($_SESSION['LCRedirect']);
		    	}
		    
		    // Output the errors
		    } else {
		    
		        $errors = $errors;
		    }
		 }
		    
		 if (isset($jkp['corder']) && isset($jkp['real_dep_id'])) {
		     
		 	$dorders = $jkp['corder'];
		    $depid = $jkp['real_dep_id'];
		    $dep = array_combine($depid, $dorders);
		    $updatesql = '';       
		   	
		   	foreach ($dep as $key => $order) {
		    	$result = $jakdb->update($jaktable, ["dorder" => $order], ["id" => $key]);
		    }
		             
		    if (!$result) {
		 		$_SESSION["infomsg"] = $jkl['i'];
		    	jak_redirect($_SESSION['LCRedirect']);
		 	} else {
		 	
		 		// Now let us delete the define cache file
		 		$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
		 		if (file_exists($cachestufffile)) {
		 			unlink($cachestufffile);
		 		}
		 	
		     	$_SESSION["successmsg"] = $jkl['g14'];
		    	jak_redirect($_SESSION['LCRedirect']);
		 	}
		 	
		 }
		    
   
		 }
		
		// Get all departments
		$DEPARTMENTS_ALL = $jakdb->select($jaktable, "*", ["ORDER" => ["dorder" => "ASC"]]);

		// Get the busiest operator
		$busy_department = $jakdb->query("SELECT COUNT(t1.department) AS mostDEP, t2.title FROM ".JAKDB_PREFIX."sessions AS t1 LEFT JOIN ".JAKDB_PREFIX."departments AS t2 ON(t1.department = t2.id) GROUP BY t1.department ORDER BY mostDEP DESC LIMIT 1")->fetch();

		// How often we had changes
	    $totalChange = $jakdb->count("whatslog", ["whatsid" => [61,62,63]]);

	    // Last Edit
	    if ($totalChange != 0) {
	      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [61,62,63], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
	    }

		// Hosting is active we need to count the total departments
		if ($jakhs['hostactive']) {
			$totaldep = $jakdb->count($jaktable);
			if ($totaldep >= $jakhs['departments']) $newdep = false;
		}
		 
		// Title and Description
		$SECTION_TITLE = $jkl["hd2"];
		$SECTION_DESC = "";
		
		// Include the javascript file for results
		$js_file_footer = 'js_pages.php';
		
		// Call the template
		$template = 'departments.php';
}
?>