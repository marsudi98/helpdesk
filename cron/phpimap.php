<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

$imap_url = dirname(__file__) . DIRECTORY_SEPARATOR;

if (!file_exists(str_replace("cron/", "", $imap_url.'config.php'))) die('email.php] config.php not exist');
require_once str_replace("cron/", "", $imap_url.'config.php');

// is php imap extension installed
if (!function_exists('imap_open')) die('PHP IMAP extension is not installed');

if (!file_exists(str_replace("cron/", "", $imap_url.'class/class.imap.php'))) die('email.php] class.imap.php not exist');
require_once str_replace("cron/", "", $imap_url.'class/class.imap.php');

// Import the language file
include_once(str_replace("cron/", "", $imap_url.'lang/'.JAK_LANG.'.php'));

// We need the correct url to filter either from web or cron
$sapi_type = php_sapi_name();
if(substr($sapi_type, 0, 3) == 'cli' || empty($_SERVER['REMOTE_ADDR'])) {
    $path_parts = pathinfo($imap_url);
    $url_filter = $imap_url;
    $url_replace = "/".basename($path_parts['dirname'])."/";
} else {
    $url_filter = "/cron/";
    $url_replace = "/";
}

// All the tables we need for this plugin
$jaktable = 'clients';
$jaktable1 = 'support_tickets';
$jaktable2 = 'ticket_answers';
$jaktable3 = 'support_departments';
$jaktable4 = 'php_imap';
$jaktable5 = 'ticketpriority';
$jaktable6 = 'ticketoptions';

// Write the log file each time someone tries to login before
JAK_base::jakWhatslog('System', 0, 0, 39, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), 'Cron Job', $_SERVER['REQUEST_URI'], 0, 'phpimap');

// Get the database and go through the accounts
$resacc = $jakdb->select($jaktable4, "*", ["active" => 1]);
if (isset($resacc) && !empty($resacc)) foreach ($resacc as $accounts) {
	// collect each record into $accounts

	// open connection
	$imap = new Imap($accounts["mailbox"], $accounts["username"], $accounts["password"], $accounts["encryption"]);

	// proceed on success
	if ($imap->isConnected()) {

		// select folder
		$imap->selectFolder($accounts["scanfolder"]);

		// fetch new emails only
		$emails = $imap->getUnreadMessages();

		// Go through each email
		if (isset($emails) && !empty($emails)) foreach ($emails as $v) {

			// Set time for this email
			$timeupdate = time();

			//---------------------- GET EMAIL HEADER INFO -----------------------//

			// Message ID
			$msgID = $v['uid'];

			// Reset some vars
			$toName = '';

			//get the name and email of the sender
			$sname = filter_var($v['fromname'], FILTER_SANITIZE_STRING);
			$semail = filter_var($v['from'], FILTER_SANITIZE_EMAIL);

			//get the name and email of the recipient
			$toEmail = filter_var($v['to'], FILTER_SANITIZE_EMAIL);
			$toName = filter_var($v['to'], FILTER_SANITIZE_STRING);

			//get the subject
			$subject = (!empty($v['subject']) ? utf8_decode(filter_var($v['subject'], FILTER_SANITIZE_STRING)) : $jkl['hd121']);

			// get the body
			$body = $v['body'];

			// Referer
			$referrer = "PHP IMAP";

			// We don't load attachments
			$loadattach = false;

			// New black list check
			if (JAK_EMAIL_BLOCK) {
				$blockede = explode(',', JAK_EMAIL_BLOCK);
				if (in_array($semail, $blockede) || in_array(strrchr($semail, "@"), $blockede)) {
					// We do nothing with this email because it is blocked and just set it to read
					if ($accounts["msgdel"]) {
						$imap->setUnseenMessage($msgID, true);
					} else {
						$imap->deleteMessage($msgID);
					}
					break;
				}
			}

			// Now we check if the mail is coming from a system email and stop the process as well
			$blockedcc = explode(',', JAK_EMAILCC);
			if ($accounts["emailanswer"] == $semail || in_array($semail, $blockedcc)) {
				// We do nothing with this email because it is blocked and just set it to read
				if ($accounts["msgdel"]) {
					$imap->setUnseenMessage($msgID, true);
				} else {
					$imap->deleteMessage($msgID);
				}
				break;
			}

			// Check if body is not empty otherwise answer back so it uses the HelpDesk online form.
			if (isset($body) && !empty($body) && isset($semail) && !empty($semail)) {

				// So far we have no error
				$errorpipe = false;

				//get rid of any quoted text in the email body
				$body_array = explode("\n",$body);

				$cleanmsg = $message = '';
				foreach ($body_array as $key => $value) {
							
					//remove hotmail sig
					if ($value == "_________________________________________________________________"){
						break;
								
					// Remove text underneath
					} elseif (preg_match("/-------------## Do Not Remove ##-------------/",$value,$matches)) {
						break;
								
					//original message quote
					} elseif (preg_match("/^-*(.*)Original Message(.*)-*/i",$value,$matches)) {
						break;
							
					//check for date wrote string
					} elseif (preg_match("/^On(.*)wrote:(.*)/i",$value,$matches)) {
						break;
							
					//check for To Name email section
					} elseif (preg_match("/^On(.*)$toName(.*)/i",$value,$matches)) {
						break;
							
					//check for To Email email section
					} elseif (preg_match("/^(.*)$toEmail(.*)wrote:(.*)/i",$value,$matches)) {
						break;
								
					//check for quoted ">" section
					} elseif (preg_match("/^>(.*)/i",$value,$matches)){
						break;
							
					//check for date wrote string with dashes
					} elseif (preg_match("/^---(.*)On(.*)wrote:(.*)/i",$value,$matches)) {
						break;
									
					//add line to body
					} else {
						$message .= "$value\n";
					}
						        	
				}
						
				// convert the text into a nice format
				$message = trim($message);
				$message = preg_replace("/(\R){2,}/", "$1", $message);

				$message = "<p>".nl2br($message)."</p>";

				// Clean the text
				$cleanmsg = jak_clean_safe_userpost($message);

				// UTF 8 Decode
				$cleanmsg = utf8_decode($cleanmsg);

				// Now we have the header and body information let's do some PHP/MySQL Magic
				if (!empty($cleanmsg)) {

					// First let's find out if the user exists already in our database.
					$row = $jakdb->get($jaktable, ["id", "name", "email", "canupload", "credits", "paid_until"], ["email" => $semail]);
							
					// Now check if we have an existing client
					if (isset($row["id"]) && is_numeric($row["id"])) {

						$semail = $row["email"];
						$sname = $row["name"];
						$clientid = $row['id'];

					} else {

						// Check if we can have a client if not mark the email
						if (JAK_TICKET_GUEST == 0) {
							if ($accounts["msgdel"]) {
								$imap->setUnseenMessage($msgID, true);
							} else {
								$imap->deleteMessage($msgID);
							}
							break;
						}

						// Do we want a new user?
						if (JAK_TICKET_ACCOUNT) {

							// create new password
				    		$password = jak_password_creator();
				    		$passcrypt = hash_hmac('sha256', $password, DB_PASS_HASH);

							$jakdb->insert($jaktable, [ 
								"chat_dep" => JAK_STANDARD_CHAT_DEP,
								"support_dep" => JAK_STANDARD_SUPPORT_DEP,
								"faq_cat" => JAK_STANDARD_FAQ_CAT,
								"name" => $sname,
								"email" => $semail,
								"password" => $passcrypt,
								"canupload" => 1,
								"access" => 1,
								"time" => $jakdb->raw("NOW()")]);

							$uid = $jakdb->id();
								
							// Create a folder
							$newuserpath = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.$uid;
								
							if (!is_dir($newuserpath)) {
								mkdir($newuserpath, 0755);
								copy(APP_PATH.JAK_FILES_DIRECTORY."/clients/index.html", $newuserpath."/index.html");
							}

							// Write the log file each time someone tries to login before
                  			JAK_base::jakWhatslog('', 0, $uid, 12, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $semail, $_SERVER['REQUEST_URI'], 0, 'phpimap');
									
							// Now send the email to the customer
							
							// Get the email template
                			$nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

							// Change fake vars into real ones.
							if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $hda) {
								if ($hda["msgtype"] == 14 && $hda["lang"] == JAK_LANG) {
									$phold = array('{url}', '{title}', '{cname}', '{cemail}', '{cpassword}', '{email}');
									$replace   = array(str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE, $sname, $semail, $password, JAK_EMAIL);
									$regtext = str_replace($phold, $replace, $hda["message"]);
									break;
								}
							}
										
							// Change fake vars into real ones.
						    $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
						    $cssUrl   = array($regtext, str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
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
							$mail->addAddress($semail);
							$mail->Subject = JAK_TITLE.' - '.$jkl['hd33'];
							$mail->MsgHTML($body);

							$mail->Send();

							// Now we know where to put it. $accounts["depid"]
							$clientid = $uid;

						} else {
							$clientid = 0;
						}
					}
										
					// let's check if that is an answer or if we have to create a new ticket
					if (preg_match("/\[\#[0-9]{1,99}\]/", $subject, $ticket)) {
									
						// Get the ticket id
						$ticketid = filter_var($ticket[0], FILTER_SANITIZE_NUMBER_INT);

						$row1 = $jakdb->get($jaktable1, ["id", "subject", "depid", "operatorid", "clientid", "name", "email", "subject"], ["AND" => ["id" => $ticketid, "clientid" => $clientid]]);
									
						if (isset($row1["id"]) && is_numeric($row1["id"])) {

							// We have the due date and we will need to make it right for mysql
      						$duedatesql = date("Y-m-d", strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day'));
											
							$jakdb->update($jaktable1, ["status" => 1,
								"ended" => 0,
								"updated" => $timeupdate,
								"duedate" => $duedatesql], ["id" => $row1['id']]);
										
							$jakdb->insert($jaktable2, ["ticketid" => $row1['id'],
								"clientid" => $row1['clientid'],
								"content" => $cleanmsg,
								"lastedit" => $jakdb->raw("NOW()"),
            					"sent" => $jakdb->raw("NOW()")]);

							// Get the ID from the answer
				            $lastid = $jakdb->id();

				            // Write the log file each time someone tries to login before
				            JAK_base::jakWhatslog('', 0, $row1['clientid'], 32, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $row1['email'], $_SERVER['REQUEST_URI'], 0, 'phpimap');

							// Dashboard URL
				            $ticketurl = str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $row1["id"], JAK_rewrite::jakCleanurl($row1["subject"])));

				            // Let's check if we have an imap
				            $ticktext = '';

				            // Get the ticket answer template
				            if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $hda) {
				    
				              if ($hda["msgtype"] == 21 && $hda["lang"] == JAK_LANG) {

				                $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}', '{ticketcontent}');
				                $replace   = array(str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE, $row['email'], $row['name'], $row['credits'], $row['paid_until'], '#'.$row1["id"], $row1['subject'], $ticketurl, $accounts["emailanswer"], replace_urls_emails($cleanmsg, str_replace($url_filter, $url_replace, BASE_URL), JAK_FILES_DIRECTORY));
				                $ticktext = str_replace($phold, $replace, $hda["message"]);
				                break;
				                
				              }
				              
				            }

				            if (!empty($ticktext)) {
				            
				              $ticktext = '<p style="color:#c1c1c1;">-------------## Do Not Remove ##-------------</p>'.$ticktext;

				              // Get the email template
				              $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
				            
				              // Change fake vars into real ones.
				              $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
				              $cssUrl   = array($ticktext, str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
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

				              // Check if we have an imap

				              // Finally send the email
				              $mail->SetFrom(JAK_EMAIL);
				              $mail->AddReplyTo($accounts["emailanswer"]);
				              $mail->addAddress($row['email']);
				              $mail->Subject = JAK_TITLE.' - [#'.$row1["id"].'] - RE:'.$row1['subject'];
				              $mail->MsgHTML($body);

				              $mail->Send();

				            }

							// We will need to inform the operator if set so
				            if (JAK_TICKET_INFORM_R) {

				            	// Get the email template
				                $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

				                // Operator URL
				                $ticketurl = str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl('operator', 'support', 'read', $page2));
				                
				                // Change fake vars into real ones.
				                $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
				                $cssUrl   = array(sprintf($jkl['hd93'], $ticketurl), str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
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
				                if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $hd) {
				                
				                  if ($hd["id"] == $accounts["depid"]) {
				                    if ($hd["email"]) $mail->AddCC($hd["email"]);
				                  }
				                  
				                }

				                // CC? Yes it does, send it to following address
				                if (!empty(JAK_EMAILCC)) {
				                  $emailarray = explode(',', JAK_EMAILCC);
				                  
				                  if (is_array($emailarray)) foreach($emailarray as $ea) { 
				                    $mail->AddCC(trim($ea));
				                  } 
				                  
				                }

				                // operator is set.
				                $sendopemail = JAK_EMAIL;
				                if ($row1["operatorid"] != 0) {
				                	$sendopemail = $jakdb->get("user", "email", ["id" => $row1["operatorid"]]);	
				                } 

				                // Finally send the email
				                $mail->SetFrom(JAK_EMAIL);
				                $mail->addAddress($sendopemail);
				                $mail->AddReplyTo($semail);
				                $mail->Subject = JAK_TITLE.' - RE:'.$row1['subject'];
				                $mail->MsgHTML($body);

				               	// We sent silently
				               	$mail->Send();

				            }

						}
								
					// will create a new ticket
					} else {

						// Let check if that client has reached the limit to open a new ticket
						if (JAK_TICKET_LIMIT != 0 && JAK_TICKET_ACCOUNT) {
							$totaltickets = $jakdb->count($jaktable1, "id", ["AND" => ["status" => 2, "clientid" => $clientid]]);
							if (JAK_TICKET_LIMIT <= $totaltickets) {

								// inform the client
								// Do we have legal call from a email client.
								if (isset($semail) && !empty($semail)) {
											
									// Ticket error inform the client
									$ticketonline = sprintf($jkl['hd115'], str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl(JAK_CLIENT_URL)));

									// Get the email template
						            $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
						            
						            // Change fake vars into real ones.
						            $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
						            $cssUrl   = array($ticketonline, str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
						            $nlcontent = str_replace($cssAtt, $cssUrl, $nlhtml);

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
						            $mail->addAddress($semail);
						            $mail->Subject = JAK_TITLE.' - '.$jkl['hd116'];
						            $mail->MsgHTML($nlcontent);

								}

								// and we mark the message as read
								if (isset($msgID)) {
									if ($accounts["msgdel"]) {
										$imap->setUnseenMessage($msgID, true);
									} else {
										$imap->deleteMessage($msgID);
									}
								}
								break;
							}
						}

						// Now we know where to put it. $accounts["depid"]

						// We have the due date and we will need to make it right for mysql
      					$duedatesql = date("Y-m-d", strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day'));

				        // Create the ticket
				        $result = $jakdb->insert($jaktable1, ["depid" => $accounts["depid"],
				          	"subject" => $subject,
				          	"content" => $cleanmsg,
				          	"clientid" => $clientid,
				          	"name" => $sname,
				          	"email" => $semail,
				          	"referrer" => "PHP IMAP",
				          	"private" => JAK_TICKET_PRIVATE,
				          	"priorityid" => JAK_STANDARD_TICKET_PRIORITY,
				          	"toptionid" => JAK_STANDARD_TICKET_OPTION,
				          	"status" => 1,
				          	"updated" => $timeupdate,
				          	"initiated" => $timeupdate,
				          	"duedate" => $duedatesql]);
									
						$ticketid = $jakdb->id();
								
						if ($result) {

							// Write the log file each time someone tries to login before
              				JAK_base::jakWhatslog('', 0, $clientid, 8, $ticketid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $semail, $_SERVER['REQUEST_URI'], 0, 'phpimap');
						
							// Set the client ticket request +1
			              	$jakdb->update($jaktable, ["supportrequests[+]" => 1], ["id" => $row["id"]]);

			              	// We run on a credit based system?
			              	if (JAK_BILLING_MODE == 1 && $totalc != 0) {

			              		$priocredit = $optcredit = 0;
					            if (JAK_STANDARD_TICKET_PRIORITY) {
					              $priocredit = $jakdb->get($jaktable5, "credits", ["id" => JAK_STANDARD_TICKET_PRIORITY]);
					            }

					            if (JAK_STANDARD_TICKET_OPTION) {
					              $optcredit = $jakdb->get($jaktable6, "credits", ["id" => JAK_STANDARD_TICKET_OPTION]);
					            }

				              	$depcredit = 0;
					            if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $hd) {
					                if ($hd["id"] == $accounts["depid"]) {
					                  $depcredit = $hd["credits"];
					                }         
					            }

					            $totalc = $depcredit + $priocredit + $optcredit;

			                	// We need to get the credits
			                	$jakdb->update($jaktable, ["credits[-]" => $totalc], ["id" => $clientid]);
			             	}

			             	// Finally we inform the customer about the new ticket

			              // Dashboard URL
			              $ticketurl = str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $ticketid, JAK_rewrite::jakCleanurl($subject)));

			              // Get the ticket answer template
			              if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
			      
			                if ($v["msgtype"] == 20 && $v["lang"] == JAK_LANG) {

			                  $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}', '{ticketcontent}');
			                  $replace   = array(str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE, $semail, $sname, $row["credits"], $row["paid_until"], '#'.$ticketid, $subject, $ticketurl, $accounts["emailanswer"], replace_urls_emails($cleanmsg, str_replace($url_filter, $url_replace, BASE_URL), JAK_FILES_DIRECTORY));
			                  $ticktext = str_replace($phold, $replace, $v["message"]);
			                  break;
			                  
			                }
			                
			              }

			              if (!empty($ticktext)) {
			              
			                $ticktext = '<p style="color:#c1c1c1;">-------------## Do Not Remove ##-------------</p>'.$ticktext;

			                // Get the email template
			                $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
			              
			                // Change fake vars into real ones.
			                $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
			                $cssUrl   = array($ticktext, str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
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
			                $mail->addAddress($semail);
			                $mail->AddReplyTo($accounts["emailanswer"]);
			                $mail->Subject = JAK_TITLE.' - [#'.$ticketid.'] - RE:'.$subject;
			                $mail->MsgHTML($body);

			                $mail->Send();

			              }

			              	// We will need to inform the operator if set so
			              	if (JAK_TICKET_INFORM_R) {

				                // Get the email template
				                $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

				                // Operator URL
				                $ticketurl = str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl('operator', 'support', 'read', $ticketid));
				                
				                // Change fake vars into real ones.
				                $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
				                $cssUrl   = array(sprintf($jkl['hd94'], $ticketurl), str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
				                $nlcontent = str_replace($cssAtt, $cssUrl, $nlhtml);
				                
				                $bodya = str_ireplace("[\]", "", $nlcontent);

				                $maila = new PHPMailer(); // defaults to using php "mail()" or optional SMTP

				                if (JAK_SMTP_MAIL) {
				                  $maila->IsSMTP(); // telling the class to use SMTP
				                  $maila->Host = JAK_SMTPHOST;
				                  $maila->SMTPAuth = (JAK_SMTP_AUTH ? true : false); // enable SMTP authentication
				                  $maila->SMTPSecure = JAK_SMTP_PREFIX; // sets the prefix to the server
				                  $mail->SMTPAutoTLS = false;
				                  $maila->SMTPKeepAlive = (JAK_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
				                  $maila->Port = JAK_SMTPPORT; // set the SMTP port for the GMAIL server
				                  $maila->Username = JAK_SMTPUSERNAME; // SMTP account username
				                  $maila->Password = JAK_SMTPPASSWORD; // SMTP account password
				                }

				                // We need to send it to the department as well
				                if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $hd) {
				                
				                  if ($hd["id"] == $accounts["depid"]) {
				                    if ($hd["email"]) $maila->AddCC($hd["email"]);
				                  }
				                  
				                }

				                // CC? Yes it does, send it to following address
				                if (!empty(JAK_EMAILCC)) {
				                  $emailarray = explode(',', JAK_EMAILCC);
				                  
				                  if (is_array($emailarray)) foreach($emailarray as $ea) { 
				                    $maila->AddCC(trim($ea));
				                  } 
				                  
				                }

				                // Finally send the email
				                $maila->SetFrom(JAK_EMAIL);
				                $maila->AddReplyTo($semail);
				                $maila->addAddress(JAK_EMAIL);
				                $maila->Subject = JAK_TITLE.' - '.$subject;
				                $maila->MsgHTML($body);

				                // We sent silently
				                $maila->Send();

				            }
			            }
					}

					$loadattach = true;
				}	
						
				if ($loadattach) {
								
					//------------------------ ATTACHMENTS ------------------------------------//
					if (isset($msgID) && !empty($msgID)) $att = $imap->getAttachment($msgID);

		       		if (isset($att) && !empty($att)) {

		       			// Get first the general stuff
		       			$rowa = $jakdb->get($jaktable1, ["id", "depid", "attachments"], ["id" => $ticketid]);

						// Can we have more attachments
						if (JAK_TICKET_ATTACH == 0 || (JAK_TICKET_ATTACH != 0 && $rowa['attachments'] <= JAK_TICKET_ATTACH)) {

							// first get the target path
							$targetPathd = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$rowa['id'];
							$targetPath =  str_replace("//", "/", $targetPathd);

					        foreach ($att as $f) {
					        	if ($f["is_attachment"]) {

					        		$filename = ($f["name"] ? $f["name"] : $f["filename"]);
					        		$jak_xtension = pathinfo($filename);
		
									// Check if the extension is valid
									$allowedf = explode(',', JAK_ALLOWED_FILES);
									if (in_array(".".$jak_xtension['extension'], $allowedf)) {

						        		// Make the directory if we don't have one.
						        		if (!is_dir($targetPath)) {
											mkdir($targetPath, 0755);
											copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $targetPath."/index.html");
										}
							            // change filename to something normal
							            $filename = preg_replace('/[^0-9,a-z,\.,_]*/i','',str_replace(' ', '_', $filename));

							            file_put_contents($targetPath.'/'.$filename, $f["attachment"], LOCK_EX);

							            // Update counter on ticket
	    								$jakdb->update($jaktable1, ["attachments[+]" => 1], ["id" => $rowa['id']]);

									}
								}
					        }
				    	}
					}
				}
						
			} else {
				$errorpipe = true;
			}

			// and we mark the message as read
			if (isset($msgID)) {
				if ($accounts["msgdel"]) {
					$imap->setUnseenMessage($msgID, true);
				} else {
					$imap->deleteMessage($msgID);
				}
			}
						
			// Output the error means we send it by email
			if (isset($errorpipe) && $errorpipe == true) {

				// We will need to inform the operator if set so
			    if (JAK_TICKET_INFORM_R) {

			        // Get the email template
			        $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
			                
			        // Change fake vars into real ones.
			        $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
			        $cssUrl   = array("There has been an error to pull in a message from: ".$semail, str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
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
			        $mail->addAddress(JAK_EMAIL);
			        $mail->AddReplyTo($semail);
			        $mail->Subject = JAK_TITLE.' - PHP IMAP ERROR';
			        $mail->MsgHTML($body);

			        // We sent silently
			        $mail->Send();

			    }
							
			}

		} else {
					
			// Do we have legal call from a email client or is there an email to fetch.
			
		}

	// On error inform the admin
	} else {

		// close connection
		$imap->close();

		// Ticket error inform the client
		$imaperror = "Following error occured when trying to connect to the mailbox ".$accounts["mailbox"].", with username ".$accounts["username"].": ".$imap->getError();

		// Get the email template
		$nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
			                
		// Change fake vars into real ones.
		$cssAtt = array('{emailcontent}', '{weburl}', '{title}');
		$cssUrl   = array($imaperror, str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE);
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
		$mail->AddReplyTo(JAK_EMAIL);
		$mail->Subject = JAK_TITLE.' - PHP IMAP ERROR';
		$mail->MsgHTML($body);

		// We sent silently
		$mail->Send();

	}
}
?>