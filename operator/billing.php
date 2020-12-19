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
if (!jak_get_access("billing", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$jaktable = 'subscriptions';
$jaktable1 = 'billing_packages';
$jaktable2 = 'clients';
$jaktable3 = 'departments';
$jaktable4 = 'support_departments';
$jaktable5 = 'faq_categories';

// We reset some vars
$totalChange = 0;
$lastPaid = '';
$busy_package = '-';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'packages':

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
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 54, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
					
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
				
				    if (empty($jkp['title'])) {
				        $errors['e'] = $jkl['e2'];
				    }
				        
				    if (JAK_BILLING_MODE == 1 && empty($jkp['credits']) && !is_numeric($jkp['credits'])) {
				        $errors['e1'] = $jkl['e15'];
				    }

				    if (JAK_BILLING_MODE != 1 && empty($jkp['amount']) && !is_numeric($jkp['amount'])) {
				        $errors['e2'] = $jkl['e15'];
				    }

				    if (empty($jkp['currency']) && strlen(trim($jkp['currency'])) != 3) {
				        $errors['e3'] = $jkl['hd73'];
				    }
				    
				    if (count($errors) == 0) {

				    	if (JAK_BILLING_MODE == 1) {
          					$jkp['paidtill'] = "";
          				} else {
          					$jkp['credits'] = 0;
          				}

          				// Chat departments
					    if (!isset($jkp['jak_depid'])) {
					    	$depa = 0;
					    } else {
					    	$depa = join(',', $jkp['jak_depid']);
					    }

					    // Support Departments
					    if (!isset($jkp['jak_depids'])) {
					    	$depas = 0;
					    } else {
					    	$depas = join(',', $jkp['jak_depids']);
					    }

					    // FAQ Categories
					    if (!isset($jkp['jak_depidf'])) {
					    	$depaf = 0;
					    } else {
					    	$depaf = join(',', $jkp['jak_depidf']);
					    }

				    	$result = $jakdb->update($jaktable1, ["title" => $jkp['title'],
							"content" => $jkp['content'],
							"previmg" => $jkp['previmg'],
							"credits" => $jkp['credits'],
							"paidtill" => $jkp['paidtill'],
							"chat_dep" => $depa,
							"support_dep" => $depas,
							"faq_cat" => $depaf,
							"amount" => $jkp['amount'],
							"currency" => $jkp['currency'],
							"dorder" => $jkp['order']], ["id" => $page3]);
				
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
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 55, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
							
						    $_SESSION["successmsg"] = $jkl['g14'];
				    		jak_redirect($_SESSION['LCRedirect']);
						}
				
					// Output the errors
					} else {
						$errors = $errors;
					}
				
					}
				
					// Title and Description
					$SECTION_TITLE = $jkl["hd76"];
					$SECTION_DESC = "";

					// Get all departments
					$JAK_DEPARTMENTS = $jakdb->select($jaktable3, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
					$JAK_DEP_SUPPORT = $jakdb->select($jaktable4, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
					$JAK_CAT_FAQ = $jakdb->select($jaktable5, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
					
					$JAK_FORM_DATA = jak_get_data($page3, $jaktable1);
					$template = 'editbilling.php';
				
				} else {
				    
				   	$_SESSION["errormsg"] = $jkl['i3'];
				    jak_redirect($_SESSION['LCRedirect']);
				}
			break;
			case 'lock':
				# code...
				// Check if user exists and can be deleted
				if (isset($page3) && is_numeric($page3)) {

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

				if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_billing'])) {
				    $jkp = $_POST;
				    
				    if (empty($jkp['title'])) {
				        $errors['e'] = $jkl['e2'];
				    }
				        
				    if (JAK_BILLING_MODE == 1 && empty($jkp['credits']) && !is_numeric($jkp['credits'])) {
				        $errors['e1'] = $jkl['e15'];
				    }

				    if (JAK_BILLING_MODE != 1 && empty($jkp['amount']) && !is_numeric($jkp['amount'])) {
				        $errors['e2'] = $jkl['e15'];
				    }

				    if (empty($jkp['currency']) && strlen(trim($jkp['currency'])) != 3) {
				        $errors['e3'] = $jkl['hd73'];
				    }
				        
				    if (count($errors) == 0) {

				    	// Get the next order
          				$last = $jakdb->get($jaktable, "dorder", ["ORDER" => ["dorder" => "DESC"]]);
          				$last = $last + 1;

          				if (JAK_BILLING_MODE == 1) {
          					$jkp['paidtill'] = "";
          				} else {
          					$jkp['credits'] = 0;
          				}

          				// Chat departments
					    if (!isset($jkp['jak_depid'])) {
					    	$depa = 0;
					    } else {
					    	$depa = join(',', $jkp['jak_depid']);
					    }

					    // Support Departments
					    if (!isset($jkp['jak_depids'])) {
					    	$depas = 0;
					    } else {
					    	$depas = join(',', $jkp['jak_depids']);
					    }

					    // FAQ Categories
					    if (!isset($jkp['jak_depidf'])) {
					    	$depaf = 0;
					    } else {
					    	$depaf = join(',', $jkp['jak_depidf']);
					    }

				    	$jakdb->insert($jaktable1, ["title" => $jkp['title'],
						"content" => $jkp['content'],
						"previmg" => $jkp['previmg'],
						"credits" => $jkp['credits'],
						"paidtill" => $jkp['paidtill'],
						"chat_dep" => $depa,
						"support_dep" => $depas,
						"faq_cat" => $depaf,
						"amount" => $jkp['amount'],
						"currency" => $jkp['currency'],
						"dorder" => $last,
						"active" => 1,
						"time" => $jakdb->raw("NOW()")]);

						$lastid = $jakdb->id();

				    	if (!$lastid) {
				    		$_SESSION["infomsg"] = $jkl['i'];
				    		jak_redirect($_SESSION['LCRedirect']);
				    	} else {

				    		// Write the log file each time someone tries to login before
          					JAK_base::jakWhatslog('', JAK_USERID, 0, 53, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

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
				$JAK_DEP_SUPPORT = $jakdb->select($jaktable4, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
				$JAK_CAT_FAQ = $jakdb->select($jaktable5, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
				
				// Get all responses
				$BILLING_ALL = jak_get_page_info($jaktable1);

				// How often we had changes
			    $totalChange = $jakdb->count("whatslog", ["whatsid" => [54,55,56]]);

			    // Last Edit
			    if ($totalChange != 0) {
			    	$lastChange = $jakdb->get("whatslog", "time", ["whatsid" => [54,55,56], "ORDER" => ["time" => "DESC"], "LIMIT" => 1]);
			    }

			    // Get the busiest operator
				$busy_package = $jakdb->query("SELECT COUNT(t1.package) AS mostPa, t2.title FROM ".JAKDB_PREFIX."subscriptions AS t1 LEFT JOIN ".JAKDB_PREFIX."billing_packages AS t2 ON(t1.package = t2.id) GROUP BY t1.package ORDER BY mostPa DESC LIMIT 1")->fetch();
					
				// Title and Description
				$SECTION_TITLE = $jkl["hd60"];
				$SECTION_DESC = "";
					
				// Include the javascript file for results
				$js_file_footer = 'js_pages.php';
					 
				// Call the template
				$template = 'billing.php';
			break;
		}

	break;
	default:
		
		// Let's go on with the script
	    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	        $jkp = $_POST;
	        
	        if (isset($jkp['action']) && $jkp['action'] == "delete") {

	          if (!JAK_USERID || !JAK_SUPERADMINACCESS) jak_redirect(BASE_URL);
	        
	          if (isset($jkp['jak_delete_subscriptions'])) {
	            
	            $delartic = $jkp['jak_delete_subscriptions'];
	      
	              for ($i = 0; $i < count($delartic); $i++) {
	                  $delart = $delartic[$i];
	                  $uidacc = explode(":#:", $delart);

	                  // The last 30 days
	                  $last30d = date("Y-m-d H:i:s", strtotime("-30 days"));

	                  // Now we need to check if that has been paid and it is younger than 30 days
	                  $subs = $jakdb->get($jaktable, ["clientid", "package"], ["AND" => ["success" => 1, "paidwhen[>]" => $last30d]]);

	                  if ($subs) {

	                  	// Get the package
	                  	$subs = $jakdb->get($jaktable1, ["credits", "paidtill"], ["id" => $subs['package']]);

	                  	// Remove the credits from the client
	                  	$client = $jakdb->get($jaktable2, ["credits", "paid_until"], ["id" => $subs['clientid']]);

	                  	// Start calculating.
	                  	// Credits first
	                  	$newcredits = $client['credits'] - $subs['credits'];
	                  	if ($newcredits > 0) {
	                  		$jakdb->update($jaktable2, ["credits" => $newcredits], ["id" => $subs['clientid']]);
	                  	} else {
	                  		$jakdb->update($jaktable2, ["credits" => 0], ["id" => $subs['clientid']]);
	                  	}
	                  	// Paid until
	                  	$datenow = date("Y-m-d");
	                  	$datepackage = date("Y-m-d", strtotime($subs["paidtill"]));
	                  	$newdate = $client["paid_until"] - $datepackage;
	                  	if ($client["paid_until"] != "1980-05-05-06" && $newdate > $datenow) {
	                  		$jakdb->update($jaktable2, ["paid_until" => $newdate], ["id" => $subs['clientid']]);
	                  	} else {
	                  		$jakdb->update($jaktable2, ["paid_until" => "1980-05-06"], ["id" => $subs['clientid']]);
	                  	}

	                  }

	                // At least we delete the entry
	                $jakdb->delete($jaktable, ["id" => $uidacc[0]]);

	                // Write the log file each time someone tries to login before
          			JAK_base::jakWhatslog('', JAK_USERID, 0, 56, $uidacc[0], (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
	                  
	              }
	              
	              $_SESSION["successmsg"] = $jkl['g14'];
	              jak_redirect($_SESSION['LCRedirect']);
	          }
	      
	          $_SESSION["errormsg"] = $jkl['i3'];
	          jak_redirect($_SESSION['LCRedirect']);
	        
	        }  
	    }

		// FAQ's
		$totalAll = $totalAllI = 0;

    	// Get the totals
    	$totalAll = $jakdb->count($jaktable);

    	// Get the income
    	if ($totalAll > 0) $totalAllI = $jakdb->sum($jaktable, "amount", ["GROUP" => "currency"]);

    	// Get the busiest operator
		$busy_package = $jakdb->query("SELECT COUNT(t1.package) AS mostPa, t2.title FROM ".JAKDB_PREFIX."subscriptions AS t1 LEFT JOIN ".JAKDB_PREFIX."billing_packages AS t2 ON(t1.package = t2.id) GROUP BY t1.package ORDER BY mostPa DESC LIMIT 1")->fetch();

	    // Last Edit
	    if ($totalAll != 0) {
	      $lastPaid = $jakdb->get($jaktable, "paidwhen", ["success" => 1, "ORDER" => ["paidwhen" => "DESC"], "LIMIT" => 1]);
	    }
		
		// Title and Description
		$SECTION_TITLE = $jkl["hd57"];
		$SECTION_DESC = "";
		
		// Include the javascript file for results
		$js_file_footer = 'js_subscriptions.php';
		 
		// Call the template
		$template = 'subscriptions.php';
}
?>