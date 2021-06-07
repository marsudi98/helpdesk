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
if (!jak_get_access("settings", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$jaktable = 'ticketpriority';
$jaktable1 = 'customfields';
$jaktable2 = 'support_departments';
$jaktable3 = 'clients';
$jaktable4 = 'support_tickets';
$jaktable5 = 'translations';
$jaktable6 = 'ticketoptions';

// Call the language function
$lang_files = jak_get_lang_files(JAK_LANG);

// Reset some vars
$totalChange = 0;
$lastChange = '';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'options':
		# code...
		// Get all responses
		$PRIORITY_ALL = $jakdb->select($jaktable, ["id", "title", "class", "dorder"], ["ORDER" => ["dorder" => "ASC"]]);
		switch ($page2) {
			case 'delete':
				# code...
				// Check if user exists and can be deleted
				if (is_numeric($page3)) {
				        
					// Now check how many languages are installed and do the dirty work
					$result = $jakdb->delete($jaktable6, ["id" => $page3]);
				
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
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 93, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
				    $_SESSION["successmsg"] = $jkl['g14'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
				    
				} else {
				    
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'edit':
				// Check if the option exists
				if (is_numeric($page3) && jak_row_exist($page3,$jaktable6)) {
				
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					    $jkp = $_POST;
						
					    if (empty($jkp['jak_title'])) {
					        $errors['e'] = $jkl['e2'];
					    }
					    
					    if (count($errors) == 0) {

					    	$result = $jakdb->update($jaktable6, ["depid" => $jkp['jak_depid'],
						        "title" => $jkp['jak_title'],
								"icon" => $jkp['jak_icon'],
								"oponly" => $jkp['jak_oponly'],
								"credits" => $jkp['credits'],
								"priorityid" => $jkp['jak_priority'],
								"dorder" => $jkp['jak_order'],
				          		"edited" => $jakdb->raw("NOW()")], ["id" => $page3]);
								 
							if (!$result) {
							    $_SESSION["infomsg"] = $jkl['i'];
					    		jak_redirect($_SESSION['LCRedirect']);
							} else {
								// Now let us delete the define cache file
								$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
								if (file_exists($cachestufffile)) {
									unlink($cachestufffile);
								}

								// Now we store the translations, that will be nasty
								if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) {
									if (isset($jkp['title_'.$lf]) && !empty($jkp['title_'.$lf])) {
										// Now check if the language already exist
										if ($jakdb->has($jaktable5, ["AND" => ["toptionid" => $page3, "lang" => $lf]])) {
											$jakdb->update($jaktable5, ["title" => $jkp['title_'.$lf]], ["AND" => ["toptionid" => $page3, "lang" => $lf]]);
										} else {
											$jakdb->insert($jaktable5, ["lang" => $lf, "toptionid" => $page3, "title" => $jkp['title_'.$lf], "time" => $jakdb->raw("NOW()")]);
										}
									}
									// Delete the entry
									if (isset($jkp['deltrans_'.$lf]) && !empty($jkp['deltrans_'.$lf])) {
										$jakdb->delete($jaktable5, ["AND" => ["toptionid" => $page3, "lang" => $jkp['deltrans_'.$lf]]]);
									}
								}

								// Write the log file each time someone tries to login before
          						JAK_base::jakWhatslog('', JAK_USERID, 0, 91, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
								
							    $_SESSION["successmsg"] = $jkl['g14'];
					    		jak_redirect($_SESSION['LCRedirect']);
							}
					
						// Output the errors
						} else {
							$errors = $errors;
						}
					}
				
					// Title and Description
					$SECTION_TITLE = $jkl["hd220"];
					$SECTION_DESC = "";
						
					// Get all departments
					$JAK_DEPARTMENTS = $jakdb->select($jaktable2, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
					// Get all responses
					$PRIORITY_ALL = $jakdb->select($jaktable, ["id", "title", "class", "dorder"], ["ORDER" => ["dorder" => "ASC"]]);

					// Get translations
					$JAK_PRIO_TRANSLATION = $jakdb->select($jaktable5, ["id", "lang", "title"], ["optionid" => $page3]);
					
					// Get the data
					$JAK_FORM_DATA = jak_get_data($page3, $jaktable6);

					// Call the template
					$template = 'editticketoptions.php';
				
				} else {
				    
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}

			break;
			default:
				if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_priority'])) {
				    $jkp = $_POST;
				    
				    if (empty($jkp['jak_title'])) {
				            $errors['e'] = $jkl['e2'];
				        }
				        
				        if (count($errors) == 0) {

				        	// Get the next order
				          $last = $jakdb->get($jaktable6, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
				          $last = $last + 1;

				        	$jakdb->insert($jaktable6, ["depid" => $jkp['jak_depid'],
					        	"title" => $jkp['jak_title'],
								"icon" => $jkp['jak_icon'],
								"oponly" => $jkp['jak_oponly'],
								"credits" => $jkp['credits'],
								"priorityid" => $jkp['jak_priority'],
								"dorder" => $last,
			          			"edited" => $jakdb->raw("NOW()"),
								"created" => $jakdb->raw("NOW()")]);

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
          						JAK_base::jakWhatslog('', JAK_USERID, 0, 92, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
				    			
				    		    $_SESSION["successmsg"] = $jkl['g14'];
				    			jak_redirect($_SESSION['LCRedirect']);
				    		}
				    
				    // Output the errors
				    } else {
				    
				        $errors = $errors;
				    }  
		   
				}

				// Get all departments
				$JAK_DEPARTMENTS = $jakdb->select($jaktable2, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
				
				// Get all responses
				// $TOPTIONS_ALL = $jakdb->select($jaktable6, ["id", "title", "icon", "priorityid", "dorder"], ["priorityid" => ] ["ORDER" => ["dorder" => "ASC"]]);
				$TOPTIONS_ALL = $jakdb->select($jaktable6, ["[>]ticketpriority" => ["priorityid" => "id"]], ["ticketoptions.id", "ticketoptions.title", "ticketoptions.icon", "ticketoptions.dorder", "ticketpriority.title(ptitle)",], ["ORDER" => ["dorder" => "ASC"]]);	
				// $TOPTIONS_ALL = $jakdb->query("SELECT t1.id AS id, t1.title AS title, t1.icon AS icon, t1.dorder AS dorder, t2.title FROM ".JAKDB_PREFIX.$jaktable6." AS t1 FULL JOIN ".JAKDB_PREFIX."ticketpriority AS t2 ON(t1.priorityid = t2.id) ORDER BY dorder ASC")->fetch();
				
				// echo json_encode($TOPTIONS_ALL);
				// exit;

				// How often we had changes
			    $totalChange = $jakdb->count("whatslog", ["whatsid" => [91,92,93]]);

			    // Last Edit
			    if ($totalChange != 0) {
			      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [91,92,93], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
			    }
				
				// Title and Description
				$SECTION_TITLE = $jkl["hd224"];
				$SECTION_DESC = "";
				 
				// Call the template
				$template = 'ticketoptions.php';
			break;
		}
	
	break;

	case 'form':

		switch ($page2) {
			case 'delete':
				# code...
				// Check if user exists and can be deleted
				if (is_numeric($page3)) {

					// Get the data
					$getdata = jak_get_data($page3, $jaktable1);
				        
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

					// Drop the field from the table
					if ($getdata['fieldlocation'] == 1) {
						$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."clients DROP `".$getdata['val_slug']."`");
					} else {
						$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."support_tickets DROP `".$getdata['val_slug']."`");
					}

					// Delete translations
					$jakdb->delete($jaktable5, ["customfieldid" => $page3]);

					// Write the log file each time someone tries to login before
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 90, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
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
				if (is_numeric($page3) && jak_row_exist($page3,$jaktable1)) {
				
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				    $jkp = $_POST;
				
				    if (empty($jkp['jak_title'])) {
				            $errors['e'] = $jkl['e2'];
				    }

				   	if (isset($jkp['jak_fieldtype']) && ($jkp['jak_fieldtype'] == 2 || $jkp['jak_fieldtype'] == 3)) {
					    if (empty($jkp['jak_field_html'])) {
					        $errors['e2'] = $jkl['hd131'];
					    }
					}
				    
				    if (count($errors) == 0) {

				    	$result = $jakdb->update($jaktable1, ["title" => $jkp['jak_title'],
							"depid" => $jkp['jak_depid'],
							"field_html" => $jkp['jak_field_html'],
							"fieldlocation" => $jkp['jak_fieldloc'],
							"fieldtype" => $jkp['jak_fieldtype'],
							"mandatory" => $jkp['jak_mandatory'],
							"onregister" => $jkp['jak_onregister'],
							"dorder" => $jkp['order']], ["id" => $page3]);
		
						if (!$result) {
						    $_SESSION["infomsg"] = $jkl['i'];
				    		jak_redirect($_SESSION['LCRedirect']);
						} else {

							// Now we create the field in the appropriate table
							if ($jkp['jak_fieldloc'] == 1) {
								$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."support_tickets DROP `".$jkp['jak_slug'] ."`;");
								$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."clients ADD `".$jkp['jak_slug'] ."` TEXT NULL AFTER `language`;");
							} elseif ($jkp['jak_fieldloc'] == 2) {
								$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."clients DROP `".$jkp['jak_slug'] ."`;");
								$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."support_tickets ADD `".$jkp['jak_slug'] ."` TEXT NULL AFTER `content`;");
							}
							
							// Now let us delete the define cache file
							$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
							if (file_exists($cachestufffile)) {
								unlink($cachestufffile);
							}

							// Now we store the translations, that will be nasty
							if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) {
								if (isset($jkp['title_'.$lf]) && !empty($jkp['title_'.$lf])) {
									// Now check if the language already exist
									if ($jakdb->has($jaktable5, ["AND" => ["customfieldid" => $page3, "lang" => $lf]])) {
										$jakdb->update($jaktable5, ["title" => $jkp['title_'.$lf], "description" => $jkp['field_html_'.$lf]], ["AND" => ["customfieldid" => $page3, "lang" => $lf]]);
									} else {
										$jakdb->insert($jaktable5, ["lang" => $lf, "customfieldid" => $page3, "title" => $jkp['title_'.$lf], "description" => $jkp['field_html_'.$lf], "time" => $jakdb->raw("NOW()")]);
									}
								}
								// Delete the entry
								if (isset($jkp['deltrans_'.$lf]) && !empty($jkp['deltrans_'.$lf])) {
									$jakdb->delete($jaktable5, ["AND" => ["customfieldid" => $page3, "lang" => $jkp['deltrans_'.$lf]]]);
								}
							}

							// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 88, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
							
						    $_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect($_SESSION['LCRedirect']);
						}
				
					// Output the errors
					} else {
						$errors = $errors;
					}
				
					}
				
					// Title and Description
					$SECTION_TITLE = $jkl["hd153"];
					$SECTION_DESC = "";

					// Get translations
					$JAK_FIELD_TRANSLATION = $jakdb->select($jaktable5, ["id", "lang", "title", "description"], ["customfieldid" => $page3]);
						
					// Get all departments
					$JAK_DEPARTMENTS = $jakdb->select($jaktable2, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
					
					// Get the data
					$JAK_FORM_DATA = jak_get_data($page3, $jaktable1);

					// Call the template
					$template = 'editcustomfield.php';
				
				} else {
				    
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			default:
				# code...

				if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_customfield'])) {
				    $jkp = $_POST;
				    
				    if (empty($jkp['jak_title'])) {
				            $errors['e'] = $jkl['e2'];
				    }
				        
				    if (empty($jkp['jak_slug']) || !preg_match('/^([a-z-_0-9]||[-_])+$/', $jkp['jak_slug'])) {
				        $errors['e1'] = $jkl['e14'];
				    }

				    // Since MySQL 5.7 we have to rename the slug
				    $newslug = str_replace("-", "_", $jkp['jak_slug']);

				    // Check if the slug is forbidden
				    $tablefield = false;
				    if (!isset($errors['e1']) && $jkp['jak_fieldloc'] == 1) {
					    $tablefield = $jakdb->query("SHOW COLUMNS FROM ".JAKDB_PREFIX.$jaktable3." LIKE '".$newslug."'")->fetchAll();
					    if ($tablefield) $errors['e1'] = $jkl['hd165'];
					} elseif (!isset($errors['e1']) && $jkp['jak_fieldloc'] == 2) {
					    $tablefield = $jakdb->query("SHOW COLUMNS FROM ".JAKDB_PREFIX.$jaktable4." LIKE '".$newslug."'")->fetchAll();
					    if ($tablefield) $errors['e1'] = $jkl['hd165'];
					}

				   	if (isset($jkp['jak_fieldtype']) && ($jkp['jak_fieldtype'] == 2 || $jkp['jak_fieldtype'] == 3)) {
					    if (empty($jkp['jak_field_html'])) {
					        $errors['e2'] = $jkl['hd131'];
					    }
					}
				        
				    if (count($errors) == 0) {

				    	// Get the next order
			        	$last = $jakdb->get($jaktable1, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
			         	$last = $last + 1;

				    	$jakdb->insert($jaktable1, ["title" => $jkp['jak_title'],
						"fieldlocation" => $jkp['jak_fieldloc'],
						"val_slug" => $newslug,
						"depid" => $jkp['jak_depid'],
						"field_html" => $jkp['jak_field_html'],
						"fieldtype" => $jkp['jak_fieldtype'],
						"mandatory" => $jkp['jak_mandatory'],
						"onregister" => $jkp['jak_onregister'],
						"dorder" => $last,
						"active" => 1,
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

					    	// Now we create the field in the appropriate table
					    	if ($jkp['jak_fieldloc'] == 1) {
					    		$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."clients ADD `".$newslug."` TEXT NULL AFTER `language`;");
					    	} elseif ($jkp['jak_fieldloc'] == 2) {
					    		$jakdb->query("ALTER TABLE ".JAKDB_PREFIX."support_tickets ADD `".$newslug."` TEXT NULL AFTER `content`;");
					    	}

					    	// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 89, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					    			
					    	$_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect($_SESSION['LCRedirect']);
				    	}
				    
				    // Output the errors
				    } else {
				    
				        $errors = $errors;
				    }  
		   
				}

				// Get all departments
				$JAK_DEPARTMENTS = $jakdb->select($jaktable2, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
				
				// Get all responses
				$CUSTOMF_ALL = jak_get_page_info($jaktable1);

				// How often we had changes
			    $totalChange = $jakdb->count("whatslog", ["whatsid" => [88,89,90]]);

			    // Last Edit
			    if ($totalChange != 0) {
			      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [88,89,90], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
			    }
				
				// Title and Description
				$SECTION_TITLE = $jkl["hd148"];
				$SECTION_DESC = "";
				
				// Include the javascript file for results
				$js_file_footer = 'js_customfield.php';
				 
				// Call the template
				$template = 'customfield.php';
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

			// Delete translations
			$jakdb->delete($jaktable5, ["priorityid" => $page2]);

			// Write the log file each time someone tries to login before
          	JAK_base::jakWhatslog('', JAK_USERID, 0, 87, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
			
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

			    if (empty($jkp['jak_title'])) {
			        $errors['e'] = $jkl['e2'];
			    }
			    
			    if (count($errors) == 0) {
					
			    	$result = $jakdb->update($jaktable, ["depid" => $jkp['jak_depid'],
				        "title" => $jkp['jak_title'],
						"class" => $jkp['jak_class'],
						"oponly" => $jkp['jak_oponly'],
						"credits" => $jkp['credits'],
						"dorder" => $jkp['jak_order'],
						"duetime" => $jkp['due_time'],
		          		"edited" => $jakdb->raw("NOW()")], ["id" => $page2]); 
						
					if (!$result) {
					    $_SESSION["infomsg"] = $jkl['i'];
			    		jak_redirect($_SESSION['LCRedirect']);
					} else {
						// Now let us delete the define cache file
						$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
						if (file_exists($cachestufffile)) {
							unlink($cachestufffile);
						}

						// Now we store the translations, that will be nasty
						if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) {
							if (isset($jkp['title_'.$lf]) && !empty($jkp['title_'.$lf])) {
								// Now check if the language already exist
								if ($jakdb->has($jaktable5, ["AND" => ["priorityid" => $page2, "lang" => $lf]])) {
									$jakdb->update($jaktable5, ["title" => $jkp['title_'.$lf]], ["AND" => ["priorityid" => $page2, "lang" => $lf]]);
								} else {
									$jakdb->insert($jaktable5, ["lang" => $lf, "priorityid" => $page2, "title" => $jkp['title_'.$lf], "time" => $jakdb->raw("NOW()")]);
								}
							}
							// Delete the entry
							if (isset($jkp['deltrans_'.$lf]) && !empty($jkp['deltrans_'.$lf])) {
								$jakdb->delete($jaktable5, ["AND" => ["priorityid" => $page2, "lang" => $jkp['deltrans_'.$lf]]]);
							}
						}

						// Write the log file each time someone tries to login before
          				JAK_base::jakWhatslog('', JAK_USERID, 0, 85, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
						
					    $_SESSION["successmsg"] = $jkl['g14'];
			    		jak_redirect($_SESSION['LCRedirect']);
					}
			
				// Output the errors
				} else {
					$errors = $errors;
				}
			}
		
			// Title and Description
			$SECTION_TITLE = $jkl["hd152"];
			$SECTION_DESC = "";
				
			// Get all departments
			$JAK_DEPARTMENTS = $jakdb->select($jaktable2, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);

			// Get translations
			$JAK_PRIO_TRANSLATION = $jakdb->select($jaktable5, ["id", "lang", "title"], ["priorityid" => $page2]);
			
			// Get the data
			$JAK_FORM_DATA = jak_get_data($page2, $jaktable);

			// Call the template
			$template = 'editticketpriority.php';
		
		} else {
		    
		   	$_SESSION["errormsg"] = $jkl['i3'];
		    jak_redirect($_SESSION['LCRedirect']);
		}
		
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_priority'])) {
		    $jkp = $_POST;
		    
		    if (empty($jkp['jak_title'])) {
				$errors['e'] = $jkl['e2'];
			}
		        
			if (count($errors) == 0) {

				// Get the next order
				$last = $jakdb->get($jaktable, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
				$last = $last + 1;

				$jakdb->insert($jaktable, ["depid" => $jkp['jak_depid'],
					"title" => $jkp['jak_title'],
					"class" => $jkp['jak_class'],
					"oponly" => $jkp['jak_oponly'],
					"credits" => $jkp['credits'],
					"dorder" => $last,
					"edited" => $jakdb->raw("NOW()"),
					"created" => $jakdb->raw("NOW()")]);

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
					JAK_base::jakWhatslog('', JAK_USERID, 0, 86, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
					$_SESSION["successmsg"] = $jkl['g14'];
					jak_redirect($_SESSION['LCRedirect']);
				}
		
			// Output the errors
			} else {
			
				$errors = $errors;
			}  
		}

		// Get all departments
		$JAK_DEPARTMENTS = $jakdb->select($jaktable2, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
		
		// Get all responses
		$PRIORITY_ALL = $jakdb->select($jaktable, ["id", "title", "class", "dorder"], ["ORDER" => ["dorder" => "ASC"]]);

		// How often we had changes
	    $totalChange = $jakdb->count("whatslog", ["whatsid" => [85,86,87]]);

	    // Last Edit
	    if ($totalChange != 0) {
	      $lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [85,86,87], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
	    }
		
		// Title and Description
		$SECTION_TITLE = $jkl["hd149"];
		$SECTION_DESC = "";
		
		// Include the javascript file for results
		$js_file_footer = 'js_pages.php';
		 
		// Call the template
		$template = 'ticketpriority.php';
}
?>