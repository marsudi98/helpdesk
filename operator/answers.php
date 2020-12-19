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
if (!jak_get_access("answers", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$jaktable = 'answers';
$jaktable1 = 'departments';
$jaktable2 = 'cms_pages';

$forbidden_slug = array(JAK_OPERATOR_LOC, JAK_CLIENT_URL, JAK_BLOG_URL, JAK_FAQ_URL, JAK_SEARCH_URL, JAK_SUPPORT_URL, 'contact', 'start', 'btn', 'quickstart', 'link', 'stop', 'feedback', 'groupchat', 'api', 'check', '404', 'closechat', 'profile', 'chat', 'forgot-password', 'logout');

// We reset some vars
$totalChange = 0;
$lastChange = '';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'cms':
		switch ($page2) {
			case 'edit':

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				    $jkp = $_POST;
				    
					if (empty($jkp['title'])) {
					    $errors['e'] = $jkl['e'];
					}
					        
					if (empty($jkp['url_slug']) || !preg_match('/^([a-z-_0-9]||[-_])+$/', $jkp['url_slug']) || ($jkp['jak_prepage'] == "0" && in_array($jkp['url_slug'], $forbidden_slug))) {
					    $errors['e1'] = $jkl['hd211'];
					}

					if ($jkp['jak_prepage'] == "0" && empty($jkp['content'])) {
					    $errors['e2'] = $jkl['e1'];
					}
					        
					if (count($errors) == 0) {

						if ($jkp['jak_prepage'] != "0") $jkp['jak_prepage'] = $jkp['url_slug'];

					    $result = $jakdb->update($jaktable2, ["lang" => $jkp['jak_lang'],
							"title" => $jkp['title'],
							"url_slug" => $jkp['url_slug'],
							"content" => jak_clean_safe_userpost($_REQUEST['content']),
							"ogimg" => $jkp['previmg'],
							"meta_keywords" => $jkp['meta_key'],
							"meta_description" => $jkp['meta_desc'],
							"dorder" => $jkp['order'],
							"showheader" => $jkp['jak_header'],
							"ishome" => $jkp['jak_ishome'],
							"prepage" => $jkp['jak_prepage'],
							"custom" => $jkp['jak_custom'],
							"custom2" => $jkp['jak_custom2'],
							"custom3" => $jkp['jak_custom3'],
							"custom4" => $jkp['jak_custom4'],
							"custom5" => $jkp['jak_custom5'],
							"access" => $jkp['jak_membersonly'],
							"showfooter" => $jkp['jak_footer'],
							"hits" => $jkp['hits'],
							"edited" => $jakdb->raw("NOW()"),
							"created" => $jakdb->raw("NOW()")], ["id" => $page3]);
					    
					    if (!$result) {
					    	$_SESSION["infomsg"] = $jkl['i'];
					    	jak_redirect($_SESSION['LCRedirect']);
					    } else {

					    	// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 37, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

					    	$_SESSION["successmsg"] = $jkl['g14'];
					    	jak_redirect($_SESSION['LCRedirect']);
					    }
					    
					// Output the errors
					} else {
					    
					    $errors = $errors;
					}
				}

				// Call the language function
				$lang_files = jak_get_lang_files();

				// Get the page
				$JAK_FORM_DATA = jak_get_data($page3, $jaktable2);
				
				// Title and Description
				$SECTION_TITLE = $jkl["hd129"];
				$SECTION_DESC = "";

				// Get the custom stuff from the selected template
				$styleconfig = APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/config.php';
				if (file_exists($styleconfig)) include_once $styleconfig;

				// Include the javascript file for results
				$js_file_footer = 'js_cms.php';
				 
				// Call the template
				$template = 'editcmspage.php';
			break;
			case 'delete':
				// Check if user exists and can be deleted
				if (is_numeric($page3)) {
				        
					// Now check how many languages are installed and do the dirty work
					$result = $jakdb->delete($jaktable2, ["id" => $page3]);

					// Write the log file each time someone tries to login before
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 30, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
				
				if (!$result) {
				    $_SESSION["infomsg"] = $jkl['i'];
				    jak_redirect($_SESSION['LCRedirect']);
				} else {	
				    $_SESSION["successmsg"] = $jkl['g14'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
				    
				} else {
				    
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'lock':
				// Set CMS status
				if (is_numeric($page3)) {

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

				    if (isset($jkp['insert_page'])) {
				    
					    if (empty($jkp['title'])) {
					        $errors['e'] = $jkl['e'];
					    }
					        
					    if (empty($jkp['url_slug']) || !preg_match('/^([a-z-_0-9]||[-_])+$/', $jkp['url_slug']) || ($jkp['jak_prepage'] == "0" && in_array($jkp['url_slug'], $forbidden_slug))) {
					    $errors['e1'] = $jkl['hd211'];
						}

						if ($jkp['jak_prepage'] == "0" && empty($jkp['content'])) {
						    $errors['e2'] = $jkl['e1'];
						}
					        
					    if (count($errors) == 0) {

					    	if ($jkp['jak_prepage'] != "0") $jkp['jak_prepage'] = $jkp['url_slug'];

					        // Get the next order
					        $last = $jakdb->get($jaktable2, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
					        $last = $last + 1;

					        $jakdb->insert($jaktable2, ["lang" => $jkp['jak_lang'],
								"title" => $jkp['title'],
								"url_slug" => $jkp['url_slug'],
								"prepage" => $jkp['jak_prepage'],
								"content" => jak_clean_safe_userpost($_REQUEST['content']),
								"dorder" => $last,
								"edited" => $jakdb->raw("NOW()"),
								"created" => $jakdb->raw("NOW()")]);

							$lastid = $jakdb->id();
					    
					    	if (!$lastid) {
					    		$_SESSION["infomsg"] = $jkl['i'];
					    		jak_redirect($_SESSION['LCRedirect']);
					    	} else {

					    		// Write the log file each time someone tries to login before
          						JAK_base::jakWhatslog('', JAK_USERID, 0, 29, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

					    		$_SESSION["successmsg"] = $jkl['g14'];
					    		jak_redirect($_SESSION['LCRedirect']);
					    	}
					    
					    // Output the errors
					    } else {
					    
					        $errors = $errors;
					    }
					}
				}

				// Call the language function
				$lang_files = jak_get_lang_files();

				// Get all pages
				$PAGES_ALL = jak_get_page_info($jaktable2);

				// How often we had changes
				$totalChange = $jakdb->count("whatslog", ["whatsid" => [29,30,37]]);

				// Last Edit
				if ($totalChange != 0) {
					$lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [29,30,37], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
				}
				
				// Title and Description
				$SECTION_TITLE = $jkl["hd126"];
				$SECTION_DESC = "";

				// Include the javascript file for results
				$js_file_footer = 'js_cms.php';
				 
				// Call the template
				$template = 'cms_pages.php';

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
          	JAK_base::jakWhatslog('', JAK_USERID, 0, 50, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
			
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
			    
			    if (empty($jkp['answer'])) {
			        $errors['e1'] = $jkl['e1'];
			    }
			    
			    // Let's check if we have a welcome message already in the same language
			    if ($jkp['jak_msgtype'] != 1) {

					$rowa = $jakdb->get($jaktable, ["id", "title"], ["AND" => ["id[!]" => $page2, "department" => $jkp['jak_depid'], "lang" => $jkp['jak_lang'], "msgtype" => $jkp['jak_msgtype']]]);
			        if ($rowa) {
			        	$errors['e2'] = sprintf($jkl['e25'], '<a href="'.JAK_rewrite::jakParseurl('answers', 'edit', $rowa["id"]).'">'.$rowa["title"].'</a>');
			        }
			    }
		    
			    if (count($errors) == 0) {
			
					$result = $jakdb->update($jaktable, ["department" => $jkp['jak_depid'],
					"lang" => $jkp['jak_lang'],
					"title" => $jkp['title'],
					"message" => jak_clean_safe_userpost($_REQUEST['answer']),
					"fireup" => $jkp['jak_fireup'],
					"msgtype" => $jkp['jak_msgtype']], ["id" => $page2]);
			
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
          				JAK_base::jakWhatslog('', JAK_USERID, 0, 51, $page2, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
						
					    $_SESSION["successmsg"] = $jkl['g14'];
		    			jak_redirect($_SESSION['LCRedirect']);
					}
			
				// Output the errors
				} else {
			    	$errors = $errors;
				}
			
			}
		
			// Title and Description
			$SECTION_TITLE = $jkl["m21"];
			$SECTION_DESC = "";
			
			// Get all departments
			$JAK_DEPARTMENTS = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
			
			// Call the settings function
			$lang_files = jak_get_lang_files();
		
			$JAK_FORM_DATA = jak_get_data($page2, $jaktable);

			// Include the javascript file for results
			$js_file_footer = 'js_editanswer.php';
			
			// Get the template
			$template = 'editanswer.php';
		
		} else {
		    
		   	$_SESSION["errormsg"] = $jkl['i3'];
		   	jak_redirect(JAK_rewrite::jakParseurl('answers'));
		}
		
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $jkp = $_POST;

		    if (isset($jkp['insert_answer'])) {
		    
			    if (empty($jkp['title'])) {
			            $errors['e'] = $jkl['e2'];
			        }
			        
			        if (empty($jkp['answer'])) {
			            $errors['e1'] = $jkl['e1'];
			        }
			        
			        // Let's check if we have a welcome message already in the same language
			        if ($jkp['jak_msgtype'] != 1) {

				        $rowa = $jakdb->get($jaktable, ["id", "title"], ["AND" => ["department" => $jkp['jak_depid'], "lang" => $jkp['jak_lang'], "msgtype" => $jkp['jak_msgtype']]]);

				        if ($rowa) {
				        	$errors['e2'] = sprintf($jkl['e25'], '<a href="'.JAK_rewrite::jakParseurl('answers', 'edit', $rowa["id"]).'">'.$rowa["title"].'</a>');
				        }
				    }
			        
			        if (count($errors) == 0) {

			        	$jakdb->insert($jaktable, ["department" => $jkp['jak_depid'],
						"lang" => $jkp['jak_lang'],
						"title" => $jkp['title'],
						"message" => jak_clean_safe_userpost($_REQUEST['answer']),
						"fireup" => $jkp['jak_fireup'],
						"msgtype" => $jkp['jak_msgtype'],
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
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 49, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
			    			
			    		    $_SESSION["successmsg"] = $jkl['g14'];
			    			jak_redirect($_SESSION['LCRedirect']);
			    		}
			    
			    // Output the errors
			    } else {
			    
			        $errors = $errors;
			    }
			}
		    
		    if (isset($jkp['create_language_pack'])) {

		    	if (isset($jkp['jak_lang_pack']) && !empty($jkp['jak_lang_pack']) && $jakdb->has($jaktable, ["lang[!]" => $jkp['jak_lang_pack']])) {

			    	// That will create a complete entry for one lanugage
			    	$jakdb->exec("INSERT INTO ".JAKDB_PREFIX."answers (`id`, `lang`, `title`, `message`, `fireup`, `msgtype`, `created`) VALUES
					(NULL, '".$jkp['jak_lang_pack']."', 'Enters Chat', '%operator% enters the chat.', 15, 2, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Expired', 'This session has expired!', 15, 4, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Ended', '%client% has ended the conversation', 15, 3, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Welcome', 'Welcome %client%, a representative will be with you shortly.', 15, 5, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Leave', 'has left the conversation.', 15, 6, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Start Page', 'Please insert your name to begin, a representative will be with you shortly.', 15, 7, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Contact Page', 'None of our representatives are available right now, although you are welcome to leave a message!', 15, 8, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Feedback Page', 'We would appreciate your feedback to improve our service.', 15, 9, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Quickstart Page', 'Please type a message and hit enter to start the conversation.', 15, 10, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Group Chat Welcome Message', 'Welcome to our weekly support session, sharing experience and feedback.', 0, 11, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Group Chat Offline Message', 'The public chat is offline at this moment, please try again later.', 15, 12, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'Group Chat Full Message', 'The public chat is full, please try again later.', 15, 13, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'WhatsApp Online', 'Please click on an operator below to connect via WhatsApp and get help immediately.', 15, 26, NOW()),
					(NULL, '".$jkp['jak_lang_pack']."', 'WhatsApp Offline', 'We are currently offline however please check below for available operators in WhatsApp, we try to help you as soon as possible.', 15, 27, NOW())");

					$jakdb->exec("INSERT INTO ".JAKDB_PREFIX."answers (`id`, `department`, `lang`, `title`, `message`, `fireup`, `msgtype`, `created`) VALUES (NULL, 0, '".$jkp['jak_lang_pack']."', 'Register Email', '<p>Thank you very much for opening an account with us.<br><br>You have used following email address: {cemail}<br>Please use following password to login into your account: {cpassword}<br><br>You can now login on our site <a href=\"{url}\">{title}</a> and change your information, avatar and or password.</p>', 15, 14, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Welcome Register Web', '<div class=\"container\">
						<div class=\"row\">
						<div class=\"col-lg-12\">
						<h2>Thank You</h2>
						<p>Your account has been registered and an email has been sent to the provided email address with your password and further informations.</p>
						<p>Please also check your spam/junk folder, if you should not receive an email from us within the next 2 hours, please contact us thru following email address: {email}.</p>
						<p>Kind regards<br>{title}</p>
						</div>
						</div>
						</div>', 15, 15, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Forgot Password Email', '<p>Someone (hopefully you!) has submitted a forgotten password request for your account. If you do not wish to change your password, just ignore this email and nothing will happen. However, if you did forget your password and wish to set a new one, visit the following link: {reset}</p>', 15, 16, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'password', '<div class=\"container\">
						<div class=\"row\">
						<div class=\"col-lg-12\">
						<h2>Password Reset Web</h2>
						<p>An email has been sent with a link to reset your password.</p>
						<p>Please also check your spam/junk folder, if you should not receive an email from us within the next 2 hours, please try again or contact us thru following email address: {email}.</p>
						<p>Kind regards<br>{title}</p>
						</div>
						</div>
						</div>', 15, 17, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Logout', '<div class=\"container\">
						<div class=\"row\">
						<div class=\"col-lg-12\">
						<h2>Logout successfully</h2>
						<p>You have been logged out successful.</p>
						<p><a href=\"{url}\">Click here to go back to the home page</a></p>
						</div>
						</div>
						</div>', 15, 18, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Error', '<p>Hi!</p><p>An error occured, you might want to try again. It could be that your ip has been blocked, please contact us if you not aware off that.</p><p><a href=\"{url}\">Click here to go back to the home page</a></p>', 15, 19, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'New Ticket', '<p>Hi!</p>
						<p>This message has been automatically generated in response to the creation of a support ticket regarding: \"{subject}\", a summary of which appears below. There is no need to reply to this message right now. Your ticket has been assigned an ID of {ticket}.</p>
						<p>To do so, you may reply to this message, here: {ticketurl}</p>
						<p>Thank you,<br>{title}</p>', 15, 20, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Ticket Answer', '<p>Hi!</p>
						<p>This message has been automatically generated in response to an answer of a support ticket regarding: \"{subject}\", a summary of which appears below.</p>
						<p>Your ticket has following ID: {ticket}</p>
						<p>You may reply to this message, here: {ticketurl}</p>
						<p>Thank you,<br>{title}</p>', 15, 21, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Ticket Reminder', '<p>Hi {cname},</p>
						<p>This is a reminder of your ticket \"{subject}\".</p>
						<p>The history of your ticket can be found in your dashboard or you can go straight to your ticket with following url: {ticketurl}</p>
						<p>Kind regards<br>{title}</p>', 15, 22, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Ticket closed', '<p>Hi {cname},</p>
						<p>Your ticket \"{subject}\" has been closed.</p>
						<p>The history of your ticket can be found in your dashboard or you can go straight to your ticket with following url: {ticketurl}</p>
						<p>You can reopen the ticket, simply reply to it.</p>
						<p>Kind regards<br>{title}</p>', 15, 23, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Start Chat Client', 'Hi %client%, please start the chat below.', 15, 24, NOW()),
						(NULL, 0, '".$jkp['jak_lang_pack']."', 'Support Rating', '<h3>How would you rate the support you received?</h3><p>Hello {cname},<br>We would love to hear what you think of our customer service. Please take a moment to rate the support you have received by clicking the link below.<br>How would you rate the support you received?</p><p>Please rate our support here: {ticketurl}</p>', 15, 25, NOW())");

					// Now let us delete the define cache file
			    	$cachestufffile = APP_PATH.JAK_CACHE_DIRECTORY.'/stuff.php';
			    	if (file_exists($cachestufffile)) {
			    		unlink($cachestufffile);
			    	}

			    	// Write the log file each time someone tries to login before
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 52, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
			    			
			    	$_SESSION["successmsg"] = $jkl['g14'];
			    	jak_redirect($_SESSION['LCRedirect']);

				} else {

					$_SESSION["infomsg"] = $jkl['i4'];
			    	jak_redirect($_SESSION['LCRedirect']);

				}
		    }
		}
		 
		// Get all departments
		$JAK_DEPARTMENTS = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
		
		// Get all answers
		$ANSWERS_ALL = jak_get_page_info($jaktable);

		// How often we had changes
		$totalChange = $jakdb->count("whatslog", ["whatsid" => [49,50,51,52]]);

		// Last Edit
		if ($totalChange != 0) {
			$lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [49,50,51,52], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
		}
		
		// Call the language function
		$lang_files = jak_get_lang_files();

		// Get only the not used language files
		$only_used_lang = $jakdb->select($jaktable, "lang", ["GROUP" => "lang"]);
		$unique_lang = array_diff($lang_files, $only_used_lang);
		
		// Title and Description
		$SECTION_TITLE = $jkl["m20"];
		$SECTION_DESC = "";
		
		// Include the javascript file for results
		$js_file_footer = 'js_pages.php';
		 
		// Call the template
		$template = 'answers.php';
}
?>