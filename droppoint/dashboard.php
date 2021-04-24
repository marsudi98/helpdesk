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

// Reset
$opmain = '';
$count = 0;

// Change for 3.0.3
use JAKWEB\JAKsql;

// Now if we have multi site we have fully automated process
if (JAK_SUPERADMINACCESS && !empty(JAKDB_MAIN_NAME) && JAK_MAIN_LOC && $jakhs['hostactive']) {
	// Database connection to the main site
	$jakdb1 = new JAKsql([
		// required
		'database_type' => JAKDB_MAIN_DBTYPE,
		'database_name' => JAKDB_MAIN_NAME,
		'server' => JAKDB_MAIN_HOST,
		'username' => JAKDB_MAIN_USER,
		'password' => JAKDB_MAIN_PASS,
		'charset' => 'utf8',
		'port' => JAKDB_MAIN_PORT,
		'prefix' => JAKDB_MAIN_PREFIX,
			         
		// [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
		'option' => [PDO::ATTR_CASE => PDO::CASE_NATURAL]
		]);

	// We get the user data from the main table
	$opmain = $jakdb1->get("users", ["id", "signup", "trial", "paidtill", "active"], ["AND" => ["opid" => JAK_USERID, "locationid" => JAK_MAIN_LOC]]);

	// Check if we have some new and unread tickets.
	$count = $jakdb1->count("support_tickets", ["AND" => ["userid" => $opmain["id"], "readtime" => 0]]);

	// We get the settings for the payment
    $sett = array();
    $settings = $jakdb1->select("settings", ["varname", "used_value"]);
    foreach ($settings as $v) {
        $sett[$v["varname"]] = $v["used_value"]; 
    }

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check']) && $_POST['check'] == "paymember") {

		if (isset($_POST['amount']) && ($_POST['amount'] == $jakhs['pricemonth'] || $_POST['amount'] == (3*$jakhs['pricemonth']) || $_POST['amount'] == (6*$jakhs['pricemonth']) || $_POST['amount'] == (12*$jakhs['pricemonth']))) {

			// Get the trial in the correct format $jakwidget['validtill']
			if ($_POST['amount'] == (12*$jakhs['pricemonth'])) {
				if (JAK_VALIDTILL > time()) {
	            	$paidunix = strtotime("+12 month", JAK_VALIDTILL);
	            } else {
	            	$paidunix = strtotime("+12 month");
	            }
	        } elseif ($_POST['amount'] == (6*$jakhs['pricemonth'])) {
	        	if (JAK_VALIDTILL > time()) {
	            	$paidunix = strtotime("+6 month", JAK_VALIDTILL);
	            } else {
	            	$paidunix = strtotime("+6 month");
	            }
	        } elseif ($_POST['amount'] == (3*$jakhs['pricemonth'])) {
	        	if (JAK_VALIDTILL > time()) {
	            	$paidunix = strtotime("+3 month", JAK_VALIDTILL);
	            } else {
	            	$paidunix = strtotime("+3 month");
	            }
	        } else {
	        	if (JAK_VALIDTILL > time()) {
	            	$paidunix = strtotime("+1 month", JAK_VALIDTILL);
	            } else {
	            	$paidunix = strtotime("+1 month");
	            }
	        }

	        // get the nice time
	        $paidtill = date('Y-m-d H:i:s', $paidunix);

			if (isset($_POST['paidhow']) && $_POST['paidhow'] == 'stripe' && isset($_POST['token']) && !empty($_POST['token'])) {

				require_once('payment/stripe/Stripe.php');
					 
				$stripe = array(
					'secret_key'      => $sett["stripesecret"],
					'publishable_key' => $sett["stripepublic"]
				);

				$stripe_amount = $_POST['amount'] * 100;
									 
				\Stripe\Stripe::setApiKey($sett["stripesecret"]);

				try {
					$charge = \Stripe\Charge::create(array(
					"amount" => $stripe_amount, // amount in cents, again
					"currency" => $sett["currency"],
					"source" => $_POST['token'],
					"description" => $jakuser->getVar("email"))
				);
					
				// Now make the stuff paid because we received the money.
				// finally update the main database
                $jakdb1->update("users", [ 
                    "trial" => "1980-05-06 00:00:00",
                    "paidtill" => $paidtill,
                    "active" => 1,
                    "confirm" => 0], ["AND" => ["opid" => JAK_USERID, "locationid" => JAK_MAIN_LOC]]);

                // Update the advanced access table
                $jakdb1->update("advaccess", [ 
                    "lastedit" => $jakdb->raw("NOW()"),
                    "paidtill" => $paidtill,
                    "paythanks" => 1], ["AND" => ["opid" => JAK_USERID, "id" => $opmain["id"]]]);

                // Payment details insert
                $jakdb1->insert("subscriptions", [ 
	                "locationid" => JAK_MAIN_LOC,
	                "userid" => JAK_USERID,
	                "amount" => $_POST['amount'],
	                "paidhow" => "Stripe - Credit Card",
	                "currency" => $sett["currency"],
		            "paidfor" => "HD3 Membership",
	                "paidwhen" => $jakdb->raw("NOW()"),
	                "paidtill" => $paidtill,
	                "success" => 1]);

                // Update the time for the user on the custom installation
            	$jakdb->update("settings", ["used_value" => $paidunix], ["varname" => "validtill"]);

            	// Now let us delete the define cache file
				$cachewidget = APP_PATH.JAK_CACHE_DIRECTORY.'/opcache'.JAK_USERID.'.php';
				if (file_exists($cachewidget)) {
				    unlink($cachewidget);
				}

				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					header('Cache-Control: no-cache');
					die(json_encode(array("status" => 1, "infomsg" => $jkl['g299'], "date" => JAK_base::jakTimesince($paidtill, JAK_DATEFORMAT, JAK_TIMEFORMAT))));
				} else {
					// redirect back to home
					jak_redirect(BASE_URL);
				}
											
				} catch(\Stripe\Error\Card $e) {
					// Error go back to set group
					if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
						header('Cache-Control: no-cache');
						die(json_encode(array("status" => 0, "infomsg" => $jkl["g297"])));
					} else {
						// redirect back to home
						$_SESSION["errormsg"] = $jkl["g297"];
						jak_redirect(BASE_URL);
					}
										
				}

			}

			// Now we go with paypal and verify the payment
			if (isset($_POST['paidhow']) && $_POST['paidhow'] == 'paypal') {
							
				// Include the paypal library
				include_once ('payment/paypal.php');
										
				// Create an instance of the paypal library
				$myPaypal = new Paypal();
										
				// Specify your paypal email
				$myPaypal->addField('business', $sett["paypal"]);
										
				// Specify the currency
				$myPaypal->addField('currency_code', $sett["currency"]);
										
				// Specify the url where paypal will send the user on success/failure
				$myPaypal->addField('return', BASE_URL.JAK_rewrite::jakParseurl('ps', 'success'));
				$myPaypal->addField('cancel_return', BASE_URL.JAK_rewrite::jakParseurl('ps', 'failure'));
										
				// Specify the url where paypal will send the IPN
				$myPaypal->addField('notify_url', BASE_URL.'payment/paypal_ipn.php');
										
				// Specify the product information
				$myPaypal->addField('item_name', JAK_TITLE);
				$myPaypal->addField('amount', $_POST['amount']);
										
				// Specify any custom value
				$myPaypal->addField('custom', base64_encode('cc3/cd3:#:'.JAK_USERID));
										
				// Enable test mode if needed
				// $myPaypal->enableTestMode();
								
				$JAK_GO_PAY = $myPaypal->submitPayment($jkl["g296"]);

				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					header('Cache-Control: no-cache');
					die(json_encode(array("status" => 1, "content" => $JAK_GO_PAY)));
				}

			}

			// Now we go with 2Checkout and verify the payment
		if (isset($_POST['paidhow']) && $_POST['paidhow'] == 'twoco') {

					// Include the 2Checkout library
			include_once ('payment/twoco.php');

					// Create an instance of the 2Checkout library
			$my2CO = new TwoCo();

					// Specify your 2Checkout vendor id
			$my2CO->addField('sid', $sett['twoco']);

					// Specify the order information
			$my2CO->addField('cart_order_id', JAK_TITLE);
			$my2CO->addField('total', $_POST['amount']);

					// Specify the url where 2Checkout will send the IPN
			$my2CO->addField('x_Receipt_Link_URL', BASE_URL.'payment/twoco_ipn.php');
			$my2CO->addField('tco_currency', $sett["currency"]);
			$my2CO->addField('custom', base64_encode('cc3/cd3:#:'.JAK_USERID));

			// Enable test mode if needed
			// $my2CO->enableTestMode();

			$JAK_GO_PAY = $my2CO->submitPayment($jkl["hd256"]);

			if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
				header('Cache-Control: no-cache');
				die(json_encode(array("status" => 1, "content" => $JAK_GO_PAY)));
			}

		}

		}

		// Send email to owner to contact the customer
		if (isset($_POST['access']) && $_POST['access'] == "advaccess") {

			$mail = new PHPMailer(); // defaults to using php "mail()"
			    	
			if (JAK_SMTP_MAIL) {
			    	
			    $mail->IsSMTP(); // telling the class to use SMTP
			    $mail->Host = JAK_SMTPHOST;
			    $mail->SMTPAuth = (JAK_SMTP_AUTH ? true : false); // enable SMTP authentication
			    $mail->SMTPSecure = JAK_SMTP_PREFIX; // sets the prefix to the server
			    $mail->SMTPAutoTLS = false;
			    $mail->SMTPKeepAlive = (JAK_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
			    $mail->Port = JAK_SMTPPORT; // set the SMTP port for the GMAIL server
			    $mail->Username = JAK_SMTPUSERNAME; // SMTP account username
			    $mail->Password = JAK_SMTPPASSWORD; // SMTP account password
			    $mail->SetFrom(JAK_EMAIL);
			    $mail->AddAddress(JAK_EMAIL);
			    $mail->AddReplyTo($jakuser->getVar("email"));
			    		
			} else {
			    	
			    $mail->SetFrom(JAK_EMAIL);
			    $mail->AddAddress(JAK_EMAIL);
			    $mail->AddReplyTo($jakuser->getVar("email"));
			    	
			}
			    	
			$mail->Subject = "Advanced Solution Request";

			$mailadv = "Following client with name: ".$jakuser->getVar("name").", username: ".$jakuser->getVar("username").", userid: ".$jakuser->getVar("id")." and email address: ".$jakuser->getVar("email")." would like to know more about your advanced solution.";
			$mail->MsgHTML($mailadv);
			    	
			if ($mail->Send()) {

				// set session to sent
				$_SESSION["adv_contact"] = true;

				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					header('Cache-Control: no-cache');
					die(json_encode(array("status" => 1, "infomsg" => "Request has been sent, we will contact you within 24 hours.")));
				} else {
					// redirect back to home
					$_SESSION["successmsg"] = "Request has been sent, we will contact you within 24 hours.";
					jak_redirect(BASE_URL);
				}
											
			} else {
				// Error go back to set group
				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					header('Cache-Control: no-cache');
					die(json_encode(array("status" => 0, "infomsg" => $jkl['g116'])));
				} else {
					// redirect back to home
					$_SESSION["infomsg"] = $jkl['g116'];
					jak_redirect(BASE_URL);
				}
										
			}

		}

		// Error go back to set group
		if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
			header('Cache-Control: no-cache');
			die(json_encode(array("status" => 0, "infomsg" => $jkl['g116'])));
		} else {
			// redirect back to home
			$_SESSION["infomsg"] = $jkl['g116'];
			jak_redirect(BASE_URL);
		}

	}

	// Get stat out for the super operator
	$totalop = $jakdb->count("user");
	$totaldep = $jakdb->count("departments");
	$totalwidg = $jakdb->count("chatwidget");

}

// Statistics
$stataccess = false;
$chatmsgStat = $visitorStat = $contactStat = $clientsStat = 0;
if (jak_get_access("statistic", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

	if (jak_get_access("statistic_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

		// Get the stats
		$chatmsgStat = $jakdb->count("transcript");
		$visitorStat = $jakdb->count("buttonstats");

	} else {

		// Get all convid into an array
		$sessids = $jakdb->select("sessions", "id", ["operatorid" => JAK_USERID]);
		// Get all messages from the convids
		$chatmsgStat = $jakdb->count("transcript", ["convid" => $sessids]);
		$visitorStat = $jakdb->count("buttonstats", ["depid" => [$jakuser->getVar("departments")]]);

	}

	$clientsStat = $jakdb->count("clients");

	if (jak_get_access("off_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
		$contactStat = $jakdb->count("contacts");		
	}

	$stataccess = true;
}

$statsupport = false;
$totalTickets = $openTickets = 0;
if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

	// Get the totals
    $totalTickets = $jakdb->count("support_tickets");

    // Get the open tickets
	if (is_numeric($jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
		$openTickets = $jakdb->select("support_tickets", ["id", "subject", "updated"], ["AND" => ["OR" => ["operatorid" => $jakuser->getVar("id"), "depid" => $jakuser->getVar("support_dep")], "status" => [1,2]], "ORDER" => ["updated" => "DESC"], "LIMIT" => 10]);
	} elseif (!((boolean)$jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
		$openTickets = $jakdb->select("support_tickets", ["id", "subject", "updated"], ["AND" => ["OR" => ["operatorid" => $jakuser->getVar("id"), "depid" => [$jakuser->getVar("support_dep")]], "status" => [1,2]], "ORDER" => ["updated" => "DESC"], "LIMIT" => 10]);
	} else {
		$openTickets = $jakdb->select("support_tickets", ["id", "subject", "updated"], ["status" => [1,2], "ORDER" => ["updated" => "DESC"], "LIMIT" => 10]);
	}

	$statsupport = true;

}

$statschat = false;
$openChats = 0;
if (jak_get_access("leads", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

	if (jak_get_access("leads_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

		$openChats = $jakdb->select("sessions", ["id", "name", "initiated"], ["ORDER" => ["id" => "DESC"], "LIMIT" => 10]);

	} else {

		$openChats = $jakdb->select("sessions", ["id", "name", "initiated"], ["AND" => ["operatorid" => $jakuser->getVar("id")], "ORDER" => ["id" => "DESC"], "LIMIT" => 10]);

	}

}

// Get the country list
$ctl = $jakdb->pdo->prepare("SELECT COUNT(id) AS total_country, countrycode, country FROM ".JAKDB_PREFIX."buttonstats WHERE countrycode != '' GROUP BY countrycode ORDER BY total_country DESC LIMIT 8");

$ctl->execute();

$ctlres = $ctl->fetchAll();

// Get the public operator chat, check if we have access
if (($jakhs['hostactive'] == 1 && $jakhs['groupchat'] == 1) || $jakhs['hostactive'] == 0) {
	$gcarray = array();
	$JAK_PUBLICCHAT = $jakdb->select("groupchat", ["id", "title", "opids", "lang"], ["AND" => ["active" => 1]]);
	if (isset($JAK_PUBLICCHAT) && !empty($JAK_PUBLICCHAT)) foreach ($JAK_PUBLICCHAT as $gc) {
		// Let's check if we have access
		if ($gc["opids"] == 0 || in_array(JAK_USERID, explode(",", $gc["opids"]))) {
			$gcarray[] = $gc;
		}
	}
}

// Get the comments to approve
$approve_comments = array();
if (jak_get_access("blog", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
	$approve_comments = $jakdb->select("blogcomments", ["[>]blog" => ["blogid" => "id"]], ["blogcomments.id", "blogcomments.blogid", "blog.title", "blogcomments.message", "blogcomments.time"], ["AND" => ["blogcomments.approve" => 0], "ORDER" => ["blogcomments.time" => "DESC"]]);
}

// Title and Description
$SECTION_TITLE = $jkl['m'];
$SECTION_DESC = "";

// Include the calendar
$css_file_header = BASE_URL.'css/fullcalendar.css';

// Include the javascript file for results
$js_file_footer = 'js_dashboard.php';
// Call the template
$template = 'dashboard.php';

?>