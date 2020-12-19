<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.1                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_PREVENT_ACCESS')) die('You cannot access this file directly.');

// All the tables we need for this plugin
$jaktable = 'clients';
$jaktable1 = 'support_tickets';
$jaktable2 = 'ticket_answers';
$jakfield = 'email';
$errors = array();

// We need to check if the call is coming thru get
if ($_SERVER["REQUEST_METHOD"] == 'GET' && $page1) {

	// Get the api and sid key
	$api_key = hash_hmac('md5', FULL_SITE_DOMAIN.JAK_O_NUMBER, DB_PASS_HASH);
	$api_key1 = hash_hmac('md5', JAK_O_NUMBER.FULL_SITE_DOMAIN, DB_PASS_HASH);
	
	// Rebuild the decryption because of PHP 7.2
	$query = "";
	$c = base64_decode(strtr($page1, '._-', '+/='));
	$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
	$iv = substr($c, 0, $ivlen);
	$hmac = substr($c, $ivlen, $sha2len=32);
	$ciphertext_raw = substr($c, $ivlen+$sha2len);
	$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $api_key, $options=OPENSSL_RAW_DATA, $iv);
	$calcmac = hash_hmac('sha256', $ciphertext_raw, $api_key, $as_binary=true);
	if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
	{
	    $query = $original_plaintext;
	}
	
	parse_str($query, $jkp);

	// Set the project id
	$pid = $jkp['pid'] ? $jkp['pid'] : JAK_STANDARD_SUPPORT_DEP;
	
	if ($jkp['sid'] = $api_key1) {
	
		switch ($jkp['action']) {
		
			case 'new':

				$row = $jakdb->get($jaktable, ["id", "chat_dep", "support_dep", "faq_cat", "paid_until"], ["email" => $jkp['email']]);

				if ($row) {

					// Ok we update the credits
					if (JAK_BILLING_MODE == 1 && isset($jkp["credits"]) && !empty($jkp["credits"]) && $jkp["credits"] != 0) {

						$jakdb->update($jaktable, ["credits[+]" => $jkp["credits"]], ["id" => $row["id"]]);

					} 

					// We update the membership access
					if (JAK_BILLING_MODE == 2 && isset($jkp["valid"]) && !empty($jkp["valid"])) {
						// Get the new date
						if (strtotime($row["paid_until"]) > time()) {
			            	$paidunix = strtotime($jkp["valid"], strtotime($row["paid_until"]));
			            } else {
			            	$paidunix = strtotime($jkp["valid"]);
			            }
						$paidtill = date('Y-m-d', $paidunix);
						$jakdb->update($jaktable, ["paid_until" => $paidtill], ["id" => $row["id"]]);
					}
					
					// Update the password
					if (isset($jkp["pass"]) && !empty($jkp["pass"])) {
						$jakdb->update($jaktable, ["password" => $jkp['pass']], ["id" => $row["id"]]);
					}

					// Update the chat departments if set so
					if (isset($jkp["chatdep"]) && !empty($jkp["chatdep"])) {
						$jakdb->update($jaktable, ["chat_dep" => $jkp['chatdep']], ["id" => $row["id"]]);
					}

					// Update the support departments if set so
					if (isset($jkp["supportdep"]) && !empty($jkp["supportdep"])) {
						$jakdb->update($jaktable, ["support_dep" => $jkp['supportdep']], ["id" => $row["id"]]);
					}

					// Update the faq categories if set so
					if (isset($jkp["faqcat"]) && !empty($jkp["faqcat"])) {
						$jakdb->update($jaktable, ["faq_cat" => $jkp['faqcat']], ["id" => $row["id"]]);
					}

					// Finally we update the rest
					$jakdb->update($jaktable, ["name" => $jkp['name'], "email" => $jkp['email']], ["id" => $row["id"]]);
						
				} else {
				
					if (empty($jkp['name'])) {
					    $errors['e'] = $jkl['e'];
					}
					
					if (JAK_EMAIL_BLOCK) {
						$blockede = explode(',', JAK_EMAIL_BLOCK);
						if (in_array($jkp['email'], $blockede) || in_array(strrchr($jkp['email'], "@"), $blockede)) {
							$errors['e1'] = $jkl['e10'];
						}
					}
					
					if ($jkp['email'] == '' || !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) {
					    $errors['e2'] = $jkl['e1'];
					}
					
					if (jak_field_not_exist(strtolower($jkp['email']),$jaktable,$jakfield)) {
					    $errors['e3'] = $jkl['hd35'];
					}
					
					if (count($errors) == 0) {

						// We have no errors we insert the user

						// create new password
						if ($jkp["pass"]) {
							$password = $jkl['hd58'];
							$passcrypt = $jkp["pass"];
						} else {
			    			$password = jak_password_creator();
			    			$passcrypt = hash_hmac('sha256', $password, DB_PASS_HASH);
			    		}

						$result = $jakdb->insert($jaktable, [ 
							"chat_dep" => ($jkp["chatdep"] ? $jkp["chatdep"] : JAK_STANDARD_CHAT_DEP),
							"support_dep" => ($jkp["supportdep"] ? $jkp["supportdep"] : JAK_STANDARD_SUPPORT_DEP),
							"faq_cat" => ($jkp["faqcat"] ? $jkp["faqcat"] : JAK_STANDARD_FAQ_CAT),
							"name" => filter_var($jkp["name"], FILTER_SANITIZE_STRING),
							"email" => filter_var($jkp["email"], FILTER_SANITIZE_EMAIL),
							"password" => $passcrypt,
							"credits" => ($jkp["credits"] ? $jkp["credits"] : 0),
							"paid_until" => ($jkp["valid"] ? $jkp["valid"] : "1980-05-06"),
							"canupload" => 1,
							"access" => 1,
							"time" => $jakdb->raw("NOW()")]);

						$uid = $jakdb->id();
					
						if (!$result) {

							// We will need to inform the operator if set so
						    if (JAK_TICKET_INFORM_R) {

						        // Get the email template
						        $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
						                
						        // Change fake vars into real ones.
						        $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
						        $cssUrl   = array("There has been an error when creating following user: ".$jkp['email'], BASE_URL, JAK_TITLE);
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

						        // We need to send it to the department as well
						        if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
						                
						            if ($v["id"] == $accounts["depid"]) {
						                if ($v["email"]) $mail->AddCC($v["email"]);
						            }
						                  
						        }

						        // CC? Yes it does, send it to following address
						        if (!empty(JAK_EMAILCC)) {
						            $emailarray = explode(',', JAK_EMAILCC);
						                  
						            if (is_array($emailarray)) foreach($emailarray as $ea) { 
						                $mail->AddCC(trim($ea));
						            } 
						                  
						        }

						        // Finally send the email
						        $mail->SetFrom(JAK_EMAIL);
						        $mail->AddReplyTo($semail);
						        $mail->Subject = JAK_TITLE.' - API Error';
						        $mail->MsgHTML($body);

						        // We sent silently
						        $mail->Send();

						    }

						} else {

							$newuserpath = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.$uid;
							
							if (!is_dir($newuserpath)) {
								mkdir($newuserpath, 0755);
								copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $newuserpath."/index.html");
							}

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

							$mail->Send();
							
						}
					}	
				}
					
				exit();
			
			break;
			
			case 'update':

				// Collect the information from the old email address
				$row = $jakdb->get($jaktable, ["id", "chat_dep", "support_dep", "faq_cat", "paid_until"], ["email" => $jkp['oldemail']]);

				if ($row) {

					// Ok we update the credits
					if (JAK_BILLING_MODE == 1 && isset($jkp["credits"]) && !empty($jkp["credits"]) && $jkp["credits"] != 0) {

						$jakdb->update($jaktable, ["credits[+]" => $jkp["credits"]], ["id" => $row["id"]]);

					} 

					// We update the membership access
					if (JAK_BILLING_MODE == 2 && isset($jkp["valid"]) && !empty($jkp["valid"])) {
						// Get the new date
						if (strtotime($row["paid_until"]) > time()) {
			            	$paidunix = strtotime($jkp["valid"], strtotime($row["paid_until"]));
			            } else {
			            	$paidunix = strtotime($jkp["valid"]);
			            }
						$paidtill = date('Y-m-d', $paidunix);
						$jakdb->update($jaktable, ["paid_until" => $paidtill], ["id" => $row["id"]]);

					}
					
					// Update the password
					if (isset($jkp["pass"]) && !empty($jkp["pass"])) {
						$jakdb->update($jaktable, ["password" => $jkp['pass']], ["id" => $row["id"]]);
					}

					// Update the chat departments if set so
					if (isset($jkp["chatdep"]) && !empty($jkp["chatdep"])) {
						$jakdb->update($jaktable, ["chat_dep" => $jkp['chatdep']], ["id" => $row["id"]]);
					}

					// Update the support departments if set so
					if (isset($jkp["supportdep"]) && !empty($jkp["supportdep"])) {
						$jakdb->update($jaktable, ["support_dep" => $jkp['supportdep']], ["id" => $row["id"]]);
					}

					// Update the faq categories if set so
					if (isset($jkp["faqcat"]) && !empty($jkp["faqcat"])) {
						$jakdb->update($jaktable, ["faq_cat" => $jkp['faqcat']], ["id" => $row["id"]]);
					}

					// Finally we update the rest
					$jakdb->update($jaktable, ["name" => $jkp['name'], "email" => $jkp['email']], ["id" => $row["id"]]);
						
				}
				
				exit();
			
			break;
			
			case 'delete':

				$row = $jakdb->get($jaktable, ["id", "chat_dep", "support_dep", "faq_cat", "paid_until"], ["email" => $jkp['email']]);
			
				if ($row) {
					$jakdb->delete($jaktable, ["id" => $row["id"]]);

					// Delete Avatar and folder
					$targetPath = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.$row["id"].'/';
					$removedouble =  str_replace("//","/",$targetPath);
					foreach(glob($removedouble.'*.*') as $jak_unlink) {
								
						@unlink($jak_unlink);
								
						@unlink($targetPath);
								
					}

					// Find tickets from this user and set to 0
					$jakdb->update($jaktable1, ["clientid" => 0], ["clientid" => $row["id"]]);
					$jakdb->update($jaktable2, ["clientid" => 0], ["clientid" => $row["id"]]);

				}
				
				exit();
			
			break;
			
			default:
			
				// We could print an error here
				
				exit();
			
		}
	
	
	} else {
		// Wrong api key we can print an error here
	}


} else {
	// Some access to the api but it failed
}
?>