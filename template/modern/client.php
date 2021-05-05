<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_PREVENT_ACCESS')) die('No direct access!');

// Get the important database table
$jaktable1 = 'support_tickets';
$jaktable2 = 'clients';
$jaktable3 = 'departments';
$jaktable4 = 'support_departments';
$jaktable5 = 'faq_categories';
$jaktable6 = 'billing_packages';

$jkp = "";
$errors = $errorsp = $errorsreg = array();

// Valid Agent
$valid_agent = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);

// Now do the dirty work with the post vars
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$jkp = $_POST;

    // Rating the ticket
	if (!empty($jkp['action']) && $jkp['action'] == 'support_rating') {

		if (isset($page2) && isset($page3) && is_numeric($page2) && is_numeric($page3) && $jakdb->has($jaktable1, ["AND" => ["id" => $page2, "initiated" => $page3, "reminder" => 2]])) {

			$row = $jakdb->get($jaktable1, ["clientid", "initiated", "ended"], ["id" => $page2]);

			// Sanitzise input
			$name = filter_var($jkp['name'], FILTER_SANITIZE_STRING);
			$email = filter_var($jkp['email'], FILTER_SANITIZE_EMAIL);
			$message = filter_var($jkp['message'], FILTER_SANITIZE_STRING);

			// Calculate support timet
			$total_supporttime = $row['ended'] - $row['initiated'];

			$jakdb->insert("ticket_rating", ["ticketid" => $page2,
				"clientid" => $row["clientid"],
				"vote" => $jkp["fbvote"],
				"name" => $name,
				"email" => $email,
				"comment" => $message,
				"support_time" => $total_supporttime,
				"time" => $jakdb->raw("NOW()")]);

			// Update the table so it cannot be rated again
			$jakdb->update($jaktable1, ["reminder" => 3], ["id" => $page2]);

			// Finally forward to the client area
			$_SESSION["successmsg"] = $jkl['g68'];
			jak_redirect(JAK_rewrite::jakParseurl(JAK_CLIENT_URL));

		} else {
			$_SESSION["infomsg"] = $jkl['not'];
			jak_redirect(JAK_rewrite::jakParseurl(JAK_CLIENT_URL));
		}

	}

    // Login IN
	elseif (!empty($jkp['action']) && $jkp['action'] == 'login') {

		// recaptcha check
		$recaptcha = false;
		// ReCaptcha Verify if key exist.
		if (!empty(JAK_RECAP_CLIENT) && !empty(JAK_RECAP_SERVER)) {
			$rcurl = 'https://www.google.com/recaptcha/api/siteverify';
			$rcdata = array(
				'secret' => JAK_RECAP_SERVER,
				'response' => $_POST["g-recaptcha-response"]
			);
			$rcoptions = array(
				'http' => array (
					'method' => 'POST',
					'content' => http_build_query($rcdata)
				)
			);
			$rccontext  = stream_context_create($rcoptions);
			$rcverify = file_get_contents($rcurl, false, $rccontext);
			$captcha_success = json_decode($rcverify);
			if ($captcha_success->success == false) {
				$errors['recaptcha'] = $jkl['e12'].'<br>';
				$recaptcha = true;
				$client_check = false;
			}
		}
		
		if (!$recaptcha) {
			$lcookies = false;
			$email = filter_var($jkp['email'], FILTER_SANITIZE_EMAIL);
			$userpass = $jkp['password'];
			if (isset($jkp['lcookies'])) $lcookies = $jkp['lcookies'];

		    // Security fix
			$valid_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

		    // Write the log file each time someone tries to login before
			JAK_base::jakWhatslog('', 0, 0, 4, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $email, $_SERVER['REQUEST_URI'], $valid_ip, $valid_agent);

			// Check that everything is OK
			$client_check = $jakclientlogin->jakCheckuserdata($email, $userpass);

		}

		if ($client_check == true) {

	    	// Now login in the user
			$jakclientlogin->jakLogin($email, $userpass, $lcookies);

	        // Write the log file each time someone login after to show success
			JAK_base::jakWhatslog('', 0, $client_check, 5, 0, '', $email, '', $valid_ip, '');

	        // Unset the recover message
			if (isset($_SESSION['password_recover'])) unset($_SESSION['password_recover']);

			if (isset($_SESSION['LCRedirect'])) {
				jak_redirect($_SESSION['LCRedirect']);
			} else {
				jak_redirect(BASE_URL);
			}

		} else {
			if (isset($errors['recaptcha'])) {
				$errorsl = $errors;
			} else {
				$ErrLogin = $jkl['l'];
			}


		}
	}

	// Forgot password
	elseif (!empty($jkp['action']) && $jkp['action'] == 'forgot-password') {

		if ($jkp['lsE'] == '' || !filter_var($jkp['lsE'], FILTER_VALIDATE_EMAIL)) {
			$errors['e'] = $jkl['e1'];
		}

	 	// ReCaptcha Verify if key exist.
		if (!empty(JAK_RECAP_CLIENT) && !empty(JAK_RECAP_SERVER)) {
			$rcurl = 'https://www.google.com/recaptcha/api/siteverify';
			$rcdata = array(
				'secret' => JAK_RECAP_SERVER,
				'response' => $_POST["g-recaptcha-response"]
			);
			$rcoptions = array(
				'http' => array (
					'method' => 'POST',
					'content' => http_build_query($rcdata)
				)
			);
			$rccontext  = stream_context_create($rcoptions);
			$rcverify = file_get_contents($rcurl, false, $rccontext);
			$captcha_success = json_decode($rcverify);
			if ($captcha_success->success == false) {
				$errors['recaptcha'] = $jkl['e12'].'<br>';
			}
		}

	 	// transform user email
		$femail = filter_var($_POST['lsE'], FILTER_SANITIZE_EMAIL);
		$fwhen = time();

	 	// Check if this user exist
		$client_check = $jakclientlogin->jakForgotpassword($femail, $fwhen);

		if (!$client_check) {
			$errors['e'] = $jkl['e1'];
		}

		if (count($errors) == 0) {
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

	         } else {
	         	$mail->SetFrom(JAK_EMAIL, JAK_TITLE);
	         }

	        // Get user details
	         $oname = $jakdb->get("user", ["id", "name"], ["AND" => ["email" => $femail, "access" => 1]]);

	         $mail->AddAddress($femail);

	         $mail->Subject = JAK_TITLE.' - '.$jkl['hd21'];
	         $body = sprintf($jkl['hd28'], $oname["name"], '<a href="'.JAK_rewrite::jakParseurl('forgot-password', $fwhen).'">'.JAK_rewrite::jakParseurl('forgot-password', $fwhen).'</a>', JAK_TITLE);

	         $mail->MsgHTML($body);
	         $mail->AltBody = strip_tags($body);

	         if ($mail->Send()) {

	         	// Write the log file each time someone login after to show success
        		JAK_base::jakWhatslog('', 0, $oname["id"], 9, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $femail, $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

	         	$_SESSION["infomsg"] = $jkl["hd29"];
	         	jak_redirect($_SESSION['LCRedirect']);
	         }

	     } else {
	     	$errorfp = $errors;
	     }
	 }

	// Register
	 elseif (!empty($jkp['action']) && $jkp['action'] == 'register') {

	 	if (empty($jkp["name"])) {
	 		$errors['e'] = $jkl['e'].'<br>';
	 	}

	 	if (empty($jkp["email"]) || !filter_var($jkp["email"], FILTER_VALIDATE_EMAIL)) {
	 		$errors['e1'] = $jkl['e1'].'<br>';
	 	}

	 	if (jak_field_not_exist(strtolower($jkp['email']), $jaktable2, "email")) {
	 		$errors['e1'] = $jkl['hd35'].'<br>';
	 	}

		// ReCaptcha Verify if key exist.
	 	if (!empty(JAK_RECAP_CLIENT) && !empty(JAK_RECAP_SERVER)) {
	 		$rcurl = 'https://www.google.com/recaptcha/api/siteverify';
	 		$rcdata = array(
	 			'secret' => JAK_RECAP_SERVER,
	 			'response' => $_POST["g-recaptcha-response"]
	 		);
	 		$rcoptions = array(
	 			'http' => array (
	 				'method' => 'POST',
	 				'content' => http_build_query($rcdata)
	 			)
	 		);
	 		$rccontext  = stream_context_create($rcoptions);
	 		$rcverify = file_get_contents($rcurl, false, $rccontext);
	 		$captcha_success = json_decode($rcverify);
	 		if ($captcha_success->success == false) {
	 			$errors['recaptcha'] = $jkl['e12'].'<br>';
	 		}
	 	}

		// And we check the custom fields
	 	$formfields = $jakdb->select('customfields', ["title", "val_slug"], ["AND" => ["fieldlocation" => 1, "mandatory" => 1]]);
	 	if (isset($formfields) && !empty($formfields)) {
	 		foreach ($formfields as $v) {
	 			if (!isset($jkp[$v["val_slug"]]) || empty($jkp[$v["val_slug"]])) {
	 				$errors[$v["val_slug"]] = sprintf($jkl['hd31'], $v["title"]).'<br>';
	 			}
	 		}
	 	}

	 	if (count($errors) == 0) {

			// create new password
	 		$password = jak_password_creator();
	 		$passcrypt = hash_hmac('sha256', $password, DB_PASS_HASH);

	 		$jakdb->insert($jaktable2, ["chat_dep" => JAK_STANDARD_CHAT_DEP,
	 			"support_dep" => JAK_STANDARD_SUPPORT_DEP,
	 			"faq_cat" => JAK_STANDARD_FAQ_CAT,
	 			"name" => filter_var($jkp["name"], FILTER_SANITIZE_STRING),
	 			"email" => $jkp['email'],
	 			"password" => $passcrypt,
	 			"canupload" => 1,
	 			"access" => 1,
	 			"time" => $jakdb->raw("NOW()")]);

	 		$lastid = $jakdb->id();

	 		if (!$lastid) {
	 			$_SESSION["errormsg"] = $jkl['not'];
	 			jak_redirect($_SESSION['LCRedirect']);
	 		} else {

	 			$newuserpath = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.$lastid;

	 			if (!is_dir($newuserpath)) {
	 				mkdir($newuserpath, 0755);
	 				copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $newuserpath."/index.html");
	 			}

				// And we complete the custom fields
	 			$formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 1]);
	 			if (isset($formfields) && !empty($formfields)) {
	 				foreach ($formfields as $v) {
	 					if (isset($jkp[$v]) && is_array($jkp[$v])) {
	 						$joinval = join(',', $jkp[$v]);

	 						if ($jakdb->has($jaktable9, ["AND" => ["val_slug" => $v, "clientid" => $lastid, "fieldlocation" => 1]])) {
	 							$jakdb->update($jaktable9, ["set_value" => filter_var($joinval, FILTER_SANITIZE_STRING)], ["AND" => ["val_slug" => $v, "clientid" => $lastid, "fieldlocation" => 1]]);
	 						} else {
	 							$jakdb->insert($jaktable9, ["val_slug" => $v, "clientid" => $lastid, "fieldlocation" => 1, "set_value" => filter_var($joinval, FILTER_SANITIZE_STRING)]);
	 						}
	 					} else {
	 						if ($jakdb->has($jaktable9, ["AND" => ["val_slug" => $v, "clientid" => $lastid, "fieldlocation" => 1]])) {
	 							$jakdb->update($jaktable9, ["set_value" => filter_var($jkp[$v], FILTER_SANITIZE_STRING)], ["AND" => ["val_slug" => $v, "clientid" => $lastid, "fieldlocation" => 1]]);
	 						} else {
	 							$jakdb->insert($jaktable9, ["val_slug" => $v, "clientid" => $lastid, "fieldlocation" => 1, "set_value" => filter_var($jkp[$v], FILTER_SANITIZE_STRING)]);
	 						}
	 					}
	 				}
	 			}

				// Get the email template
	 			$nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

				// Change fake vars into real ones.
	 			if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
	 				if ($v["msgtype"] == 14 && $v["lang"] == JAK_LANG) {
	 					$phold = array('{url}', '{title}', '{cname}', '{cemail}', '{cpassword}', '{email}');
	 					$replace   = array(BASE_URL, JAK_TITLE, $jkp['name'], $jkp['email'], $password, JAK_EMAIL);
	 					$regtext = str_replace($phold, $replace, $v["message"]);
	 					break;
	 				}
	 			}

				// Change fake vars into real ones.
	 			$cssAtt = array('{emailcontent}', '{weburl}', '{title}');
	 			$cssUrl   = array($regtext, BASE_URL, JAK_TITLE);
	 			$nlcontent = str_replace($cssAtt, $cssUrl, $nlhtml);

	 			$body = str_ireplace("[\]", "", $nlcontent);

				$mail = new PHPMailer(); // defaults to using php "mail()" or optional SMTP

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
				}

				// Finally send the email
				$mail->SetFrom(JAK_EMAIL);
				$mail->addAddress($jkp['email']);
				$mail->Subject = JAK_TITLE.' - '.$jkl['hd33'];
				$mail->MsgHTML($body);

				if ($mail->Send()) $_SESSION["infomsg"] = $jkl['hd32'];

				$_SESSION["infomsg"] = $jkl["hd34"];
				jak_redirect($_SESSION['LCRedirect']);
			}

		} else {
			$errorsreg = $errors;
		}
	}

	// Save Client
	elseif (!empty($jkp['action']) && $jkp['action'] == 'save_client') {

		$updatepass = false;

		if (empty($jkp["name"])) {
			$errors['e'] = $jkl['e'].'<br>';
		}

		if (empty($jkp["email"]) || !filter_var($jkp["email"], FILTER_VALIDATE_EMAIL)) {
			$errors['e1'] = $jkl['e1'].'<br>';
		}

		if (jak_field_not_exist_id($jkp['email'], JAK_CLIENTID, $jaktable2, "email")) {
			$errors['e1'] = $jkl['hd35'].'<br>';
		}

		// And we check the custom fields
		$formfields = $jakdb->select('customfields', ["title", "val_slug"], ["AND" => ["fieldlocation" => 1, "mandatory" => 1]]);
		if (isset($formfields) && !empty($formfields)) {
			foreach ($formfields as $v) {
				if (isset($jkp[$v["val_slug"]])) {
					if (empty($jkp[$v["val_slug"]])) {
						$errors[$v["val_slug"]] = sprintf($jkl['hd31'], $v["title"]).'<br>';
					}
				}
			}
		}

		if (!empty($jkp['jak_newpassword']) || !empty($jkp['jak_cpassword'])) {    
			if ($jkp['jak_newpassword'] != $jkp['jak_cpassword']) {
				$errors['e2'] = $jkl['hd62'];
			} elseif (strlen($jkp['jak_newpassword']) <= '7') {
				$errors['e3'] = $jkl['hd63'];
			} else {
				$updatepass = true;
			}
		}

		if (isset($jkp['deleteavatar']) && $jkp['deleteavatar'] == 1) {


			// first get the target path
			$targetPathd = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.JAK_CLIENTID.'/';
			$targetPath =  str_replace("//","/",$targetPathd);

			// if old avatars exist delete it
			foreach(glob($targetPath.'*.*') as $jak_unlink){
				unlink($jak_unlink);
			}
		   	// Remove the directory as we do not need it anymore. (+1 for a clean system)
			rmdir($targetPath);

		   	// SQL update
			$jakdb->update($jaktable2, ["picture" => "/standard.jpg"], ["id" => JAK_CLIENTID]);

		}

		if (!empty($_FILES['avatar']['name'])) {

			if ($_FILES['avatar']['name'] != '') {

		    	$filename = $_FILES['avatar']['name']; // original filename
		    	// Fix explode when upload in 1.2
		    	$tmpf = explode(".", $filename);
		    	$jak_xtension = end($tmpf);
		    	
		    	if ($jak_xtension == "jpg" || $jak_xtension == "jpeg" || $jak_xtension == "png" || $jak_xtension == "gif") {

		    		if ($_FILES['avatar']['size'] <= 2000000) {

		    			list($width, $height, $type, $attr) = getimagesize($_FILES['avatar']['tmp_name']);
		    			$mime = image_type_to_mime_type($type);

		    			if (($mime == "image/jpeg") || ($mime == "image/pjpeg") || ($mime == "image/png") || ($mime == "image/gif")) {

		    				// first get the target path
		    				$targetPathd = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.JAK_CLIENTID.'/';
		    				$targetPath =  str_replace("//","/",$targetPathd);

		    				// Create the target path
		    				if (!is_dir($targetPath)) {
		    					mkdir($targetPath, 0755);
		    					copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $targetPath . "/index.html");

		    				}

		    				// if old avatars exist delete it
		    				foreach(glob($targetPath.'*.*') as $jak_unlink){
		    					unlink($jak_unlink);
		    					copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $targetPath . "/index.html");
		    				}

		    				$tempFile = $_FILES['avatar']['tmp_name'];
		    				$origName = substr($_FILES['avatar']['name'], 0, -4);
		    				$name_space = strtolower($_FILES['avatar']['name']);
		    				$middle_name = str_replace(" ", "_", $name_space);
		    				$middle_name = str_replace(".jpeg", ".jpg", $name_space);
		    				$glnrrand = rand(10, 99);
		    				$bigPhoto = str_replace(".", "_" . $glnrrand . ".", $middle_name);
		    				$smallPhoto = str_replace(".", "_t.", $bigPhoto);

		    				$targetFile =  str_replace('//','/',$targetPath) . $bigPhoto;
		    				$origPath = '/clients/'.JAK_CLIENTID.'/';
		    				$dbSmall = $origPath.$smallPhoto;

		    				require_once APP_PATH.'include/functions_thumb.php';
		    				// Move file and create thumb     
		    				move_uploaded_file($tempFile,$targetFile);

		    				create_thumbnail($targetPath, $targetFile, $smallPhoto, JAK_USERAVATWIDTH, JAK_USERAVATHEIGHT, 80);

		    				// SQL update
		    				$jakdb->update($jaktable2, ["picture" => $dbSmall], ["id" => JAK_CLIENTID]);

		    			} else {
		    				$errors['e4'] = $jkl['hd60'].'<br>';
		    			}

		    		} else {
		    			$errors['e4'] = $jkl['hd60'].'<br>';
		    		}

		    	} else {
		    		$errors['e4'] = $jkl['hd60'].'<br>';
		    	}
		    	
		    } else {
		    	$errors['e4'] = $jkl['hd60'].'<br>';
		    }
		    
		}

		if (count($errors) == 0) {

			$result = $jakdb->update($jaktable2, [ 
				"name" => filter_var($jkp["name"], FILTER_SANITIZE_STRING),
				"email" => $jkp['email'],
				"language" => $jkp['jak_lang']], ["id" => JAK_CLIENTID]);

			if (!$result) {
				$_SESSION["errormsg"] = $jkl['not'];
				jak_redirect($_SESSION['LCRedirect']);
			} else {

				// Finally we update the password
				if ($updatepass) $jakdb->update($jaktable2, ["password" => hash_hmac('sha256', $jkp['jak_newpassword'], DB_PASS_HASH)], ["id" => JAK_CLIENTID]);

				// And we complete the custom fields
				$formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 1]);
				if (isset($formfields) && !empty($formfields)) {
					foreach ($formfields as $v) {
						if (isset($jkp[$v]) && is_array($jkp[$v])) {
							$joinval = join(',', $jkp[$v]);
							$jakdb->update($jaktable2, [$v => $joinval], ["id" => JAK_CLIENTID]);
						} else {
							$jakdb->update($jaktable2, [$v => $jkp[$v]], ["id" => JAK_CLIENTID]);
						}
					}
				}

				$_SESSION["successmsg"] = $jkl["hd61"];
				jak_redirect($_SESSION['LCRedirect']);

			}

		} else {

			$_SESSION["errormsg"] = $jkl["e3"];
			$errorsp = $errors;
		}

	}

	// Transfer payments
	elseif (!empty($jkp['action']) && $jkp['action'] == 'payment') {

		if (isset($jkp['paidhow']) && $jkp['paidhow'] == 'stripe' && isset($jkp['token']) && !empty($jkp['token'])) {

			require_once('operator/payment/stripe/Stripe.php');

			$stripe = array(
				'secret_key'      => JAK_STRIPE_SECRET_KEY,
				'publishable_key' => JAK_STRIPE_PUBLISH_KEY
			);

			$stripe_amount = $jkp['amount'] * 100;

			\Stripe\Stripe::setApiKey(JAK_STRIPE_SECRET_KEY);

			try {
				$charge = \Stripe\Charge::create(array(
				"amount" => $stripe_amount, // amount in cents, again
				"currency" => $jkp["currency"],
				"source" => $jkp['token'],
				"description" => $jakclient->getVar("email"))
			);

			// Now make the stuff paid because we received the money.
				$package = $jakdb->get($jaktable6, ["credits", "paidtill", "chat_dep", "support_dep", "faq_cat", "amount"], ["id" => $jkp['package']]);
			// Credit based system
				if (JAK_BILLING_MODE == 1) {

				// Update the credits
					$jakdb->update($jaktable2, ["credits[+]" => $package["credits"]], ["id" => JAK_CLIENTID]);

				// Update the chat departments
					if ($package["chat_dep"] != 0) $jakdb->update($jaktable2, ["chat_dep" => $package["chat_dep"]], ["id" => JAK_CLIENTID]);

				// Update the support departments
					if ($package["support_dep"] != 0) $jakdb->update($jaktable2, ["support_dep" => $package["support_dep"]], ["id" => JAK_CLIENTID]);

				// Update the faq categories
					if ($package["faq_cat"] != 0) $jakdb->update($jaktable2, ["faq_cat" => $package["faq_cat"]], ["id" => JAK_CLIENTID]);

			// Memberschip based system
				} elseif (JAK_BILLING_MODE == 2) {

				// Get the new date
					if (strtotime($jakuser->getVar("paid_until")) > time()) {
						$paidunix = strtotime($package["paidtill"], strtotime($jakuser->getVar("paid_until")));
					} else {
						$paidunix = strtotime($package["paidtill"]);
					}
					$paidtill = date('Y-m-d', $paidunix);

				// Update the credits
					$jakdb->update($jaktable2, ["paid_until" => $paidtill], ["id" => JAK_CLIENTID]);

				// Update the chat departments
					if ($package["chat_dep"] != 0) $jakdb->update($jaktable2, ["chat_dep" => $package["chat_dep"]], ["id" => JAK_CLIENTID]);

				// Update the support departments
					if ($package["support_dep"] != 0) $jakdb->update($jaktable2, ["support_dep" => $package["support_dep"]], ["id" => JAK_CLIENTID]);

				// Update the faq categories
					if ($package["faq_cat"] != 0) $jakdb->update($jaktable2, ["faq_cat" => $package["faq_cat"]], ["id" => JAK_CLIENTID]);

				}

            // Payment details insert
				$jakdb->insert("subscriptions_client", ["clientid" => JAK_CLIENTID,
					"amount" => $jkp['amount'],
					"paidhow" => "Stripe - Credit Card",
					"currency" => $jkp["currency"],
					"package" => $jkp["package"],
					"paidwhen" => $jakdb->raw("NOW()"),
					"success" => 1]);

				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					header('Cache-Control: no-cache');
					die(json_encode(array("status" => 1, "infomsg" => $jkl['hd113'])));
				} else {
				// redirect back to home
					jak_redirect(BASE_URL);
				}

			} catch(\Stripe\Error\Card $e) {
				// Error go back to client page
				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					header('Cache-Control: no-cache');
					die(json_encode(array("status" => 0, "infomsg" => $jkl["hd111"])));
				} else {
					// redirect back to home
					$_SESSION["errormsg"] = $jkl["hd111"];
					jak_redirect(BASE_URL);
				}

			}

		}

		// Now we go with paypal and verify the payment
		if (isset($jkp['paidhow']) && $jkp['paidhow'] == 'paypal') {

			// Include the paypal library
			include_once ('operator/payment/paypal.php');

			// Create an instance of the paypal library
			$myPaypal = new Paypal();

			// Specify your paypal email
			$myPaypal->addField('business', JAK_PAYPAL_EMAIL);

			// Specify the currency
			$myPaypal->addField('currency_code', $jkp["currency"]);

			// Specify the url where paypal will send the user on success/failure
			$myPaypal->addField('return', JAK_rewrite::jakParseurl(JAK_CLIENT_URL, 'pay', 'success'));
			$myPaypal->addField('cancel_return', JAK_rewrite::jakParseurl(JAK_CLIENT_URL, 'pay', 'failure'));

			// Specify the url where paypal will send the IPN
			$myPaypal->addField('notify_url', BASE_URL.'operator/payment/paypal_ipn.php');

			// Specify the product information
			$myPaypal->addField('item_name', $jkp['ptitle']);
			$myPaypal->addField('amount', $jkp['amount']);

			// Specify any custom value
			$myPaypal->addField('custom', base64_encode('package:#:'.JAK_CLIENTID.':#:'.$jkp['package']));

			// Enable test mode if needed
			// $myPaypal->enableTestMode();

			$JAK_GO_PAY = $myPaypal->submitPayment($jkl["hd112"]);

			if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
				header('Cache-Control: no-cache');
				die(json_encode(array("status" => 1, "content" => $JAK_GO_PAY)));
			}

		}

		// Now we go with 2Checkout and verify the payment
		if (isset($_POST['paidhow']) && $_POST['paidhow'] == 'twoco') {

					// Include the 2Checkout library
			include_once ('operator/payment/twoco.php');

					// Create an instance of the 2Checkout library
			$my2CO = new TwoCo();

					// Specify your 2Checkout vendor id
			$my2CO->addField('sid', $sett['twoco']);

					// Specify the order information
			$my2CO->addField('cart_order_id', $jkp['ptitle']);
			$my2CO->addField('total', $jkp['amount']);

					// Specify the url where 2Checkout will send the IPN
			$my2CO->addField('x_Receipt_Link_URL', BASE_URL.'operator/payment/twoco_ipn.php');
			$my2CO->addField('tco_currency', $sett["currency"]);
			$my2CO->addField('custom', base64_encode('package:#:'.JAK_CLIENTID.':#:'.$jkp['package']));

			// Enable test mode if needed
			// $my2CO->enableTestMode();

			$JAK_GO_PAY = $my2CO->submitPayment($jkl["hd128"]);

			if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
				header('Cache-Control: no-cache');
				die(json_encode(array("status" => 1, "content" => $JAK_GO_PAY)));
			}

		}

	}
}

// Rate the ticket
if ($page1 == "rt") {

	if (isset($page2) && isset($page3) && is_numeric($page2) && is_numeric($page3) && $jakdb->has($jaktable1, ["AND" => ["id" => $page2,  "initiated" => $page3, "reminder" => 2]])) {

		$row = $jakdb->get($jaktable1, ["id", "name", "email", "subject"], ["id" => $page2]);

		// Include the javascript file for results
		$js_file_footer = 'js_rating.php';

		// Load the template
		include_once APP_PATH.'template/modern/tplblocks/rateticket.php';

	} else {
		$_SESSION["infomsg"] = $jkl['not'];
		jak_redirect(JAK_rewrite::jakParseurl(JAK_CLIENT_URL));
	}

}

// When client/user is logged in
if (JAK_USERISLOGGED) {

	// We edit some client details
	if ($page1 == "edit") {

		// Get the data
		$JAK_FORM_DATA = jak_get_data(JAK_CLIENTID, $jaktable2);

		// Call the settings function
		$lang_files = jak_get_lang_files(JAK_LANG);

		// Get the custom fields
		$custom_fields = jak_get_custom_fields_modern($JAK_FORM_DATA, 1, false, $BT_LANGUAGE, false, false, false, false, $errorsp);

		// Get the customer name
		$JAK_FORM_DATA["title"] = $JAK_FORM_DATA["name"];

		// Load the template
		include_once APP_PATH.'template/modern/tplblocks/clientedit.php';

	} else {

		// Some Resets
		$getTotal = 0;
		$allcsupport = $last5pay = $allpackages = array();

		// include the class
		include_once(APP_PATH.'class/class.paginator.php');

		// Get the custom fields
		if (JAK_CLIENTID) {

		// Get the total
			$getTotal = $jakdb->count("support_tickets", ["clientid" => JAK_CLIENTID]);

			if ($getTotal != 0) {

		    // Paginator
				$pages = new JAK_Paginator;
				$pages->items_total = $getTotal;
				$pages->mid_range = 10;
				$pages->items_per_page = 5;
				$pages->jak_get_page = $page1;
				$pages->jak_where = JAK_rewrite::jakParseurl(JAK_CLIENT_URL);
				$pages->paginate();
				$JAK_PAGINATE = $pages->display_pages();

		    	// Get the result
				$allcsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.status", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_tickets.initiated", "support_departments.id(depid)", "support_departments.title(titledep)", "clients.name"], ["support_tickets.clientid" => JAK_CLIENTID,
					"ORDER" => ["support_tickets.updated" => "DESC"],
					"LIMIT" => $pages->limit
				]);

			}

			// Get the last 5 payments
			$last5pay = $jakdb->select("subscriptions_client", ["[>]billing_packages" => ["package" => "id"]], ["subscriptions_client.id", "subscriptions_client.amount", "subscriptions_client.currency", "subscriptions_client.paidhow", "subscriptions_client.paidwhen", "subscriptions_client.success", "billing_packages.title"], ["subscriptions_client.clientid" => JAK_CLIENTID,
				"ORDER" => ["subscriptions_client.paidwhen" => "DESC"],
				"LIMIT" => 5
			]);

			// Get all packages
			$allpackages = $jakdb->select("billing_packages", ["id", "title", "content", "previmg", "credits", "paidtill", "chat_dep", "support_dep", "faq_cat", "amount", "currency"], ["active" => 1,
				"ORDER" => ["dorder" => "DESC"]
			]);

			// Include the javascript file for results
			$js_file_footer = 'js_dashboard.php';

			// Load the template
			include_once APP_PATH.'template/modern/tplblocks/dashboard.php';

	} // End clientid

	if (JAK_USERID) {

		if ($jakuser->getVar("support_dep") == 0) {

	    	// Get the total
			$getTotal = $jakdb->count("support_tickets");

			if ($getTotal != 0) {

	      		// Paginator
				$pages = new JAK_Paginator;
				$pages->items_total = $getTotal;
				$pages->mid_range = 10;
				$pages->items_per_page = 10;
				$pages->jak_get_page = $page1;
				$pages->jak_where = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);
				$pages->paginate();
				$JAK_PAGINATE = $pages->display_pages();

	      		// Get the result
				$allcsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.status", "support_tickets.initiated", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_departments.id(depid)", "support_departments.title(titledep)", "clients.name"], [ 
					"ORDER" => ["support_tickets.updated" => "DESC"],
					"LIMIT" => $pages->limit
				]);
			}

		} else { 

	    // Get the total
			$getTotal = $jakdb->count("support_tickets", ["depid" => [$jakuser->getVar("support_dep")]]);

			if ($getTotal != 0) {

	      // Paginator
				$pages = new JAK_Paginator;
				$pages->items_total = $getTotal;
				$pages->mid_range = 10;
				$pages->items_per_page = 10;
				$pages->jak_get_page = $page1;
				$pages->jak_where = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);
				$pages->paginate();
				$JAK_PAGINATE = $pages->display_pages();

	      // Get the result
				$allcsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.status", "support_tickets.initiated", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_departments.id(depid)", "support_departments.title(titledep)", "clients.name"], ["support_tickets.depid" => [$jakuser->getVar("support_dep")],
					"ORDER" => ["support_tickets.updated" => "DESC"],
					"LIMIT" => $pages->limit
				]);
			}

		}

		// Load the template
		include_once APP_PATH.'template/modern/tplblocks/dashboardo.php';

	}

}


// Get the stuff when user is not logged in
} else {
	// Get the custom fields
	$custom_fields = jak_get_custom_fields_modern($jkp, 1, false, $BT_LANGUAGE, false, false, false, true, $errorsreg);

	// Include the javascript file for results
	$js_file_footer = 'js_client.php';

	// Load the template
	include_once APP_PATH.'template/modern/tplblocks/client.php';
}
?>