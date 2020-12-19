<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('config.php')) die('rest_api config.php not exist');
require_once 'config.php';

$userid = $loginhash = $ticketid = $operatorid = $depid = $message = $status = $tstatus = $priority = $option = $notes = $private = $toldstatus = $oldopid = $olddepid = $oldpriority = $oldoption = "";
$errors = array();
$sendform = $answersaved = false;
if (isset($_REQUEST['userid']) && !empty($_REQUEST['userid']) && is_numeric($_REQUEST['userid'])) $userid = $_REQUEST['userid'];
if (isset($_REQUEST['loginhash']) && !empty($_REQUEST['loginhash'])) $loginhash = $_REQUEST['loginhash'];
if (isset($_REQUEST['ticketid']) && !empty($_REQUEST['ticketid'])) $ticketid = $_REQUEST['ticketid'];
if (isset($_REQUEST['operatorid']) && !empty($_REQUEST['operatorid'])) $operatorid = $_REQUEST['operatorid'];
if (isset($_REQUEST['depid']) && !empty($_REQUEST['depid'])) $depid = $_REQUEST['depid'];
if (isset($_REQUEST['message']) && !empty($_REQUEST['message'])) $message = $_REQUEST['message'];
if (isset($_REQUEST['status']) && !empty($_REQUEST['status'])) $status = $_REQUEST['status'];
if (isset($_REQUEST['tstatus']) && !empty($_REQUEST['tstatus'])) $tstatus = $_REQUEST['tstatus'];
if (isset($_REQUEST['priority']) && !empty($_REQUEST['priority'])) $priority = $_REQUEST['priority'];
if (isset($_REQUEST['option']) && !empty($_REQUEST['option'])) $option = $_REQUEST['option'];
if (isset($_REQUEST['notes']) && !empty($_REQUEST['notes'])) $notes = $_REQUEST['notes'];
if (isset($_REQUEST['private']) && !empty($_REQUEST['private'])) $private = $_REQUEST['private'];
if (isset($_REQUEST['toldstatus']) && !empty($_REQUEST['toldstatus'])) $toldstatus = $_REQUEST['toldstatus'];
if (isset($_REQUEST['oldopid']) && !empty($_REQUEST['oldopid'])) $oldopid = $_REQUEST['oldopid'];
if (isset($_REQUEST['olddepid']) && !empty($_REQUEST['olddepid'])) $olddepid = $_REQUEST['olddepid'];
if (isset($_REQUEST['oldpriority']) && !empty($_REQUEST['oldpriority'])) $oldpriority = $_REQUEST['oldpriority'];
if (isset($_REQUEST['oldoption']) && !empty($_REQUEST['oldoption'])) $oldoption = $_REQUEST['oldoption'];

if (!empty($userid) && !empty($loginhash)) {

	// Let's check if we are logged in
	$usr = $jakuserlogin->jakCheckrestlogged($userid, $loginhash);

	// User is logged in
	if ($usr) {

		// Select the user fields
		$jakuser = new JAK_user($usr);

		$USER_LANGUAGE = strtolower($jakuser->getVar("language"));

		// Import the language file
		if ($USER_LANGUAGE && file_exists(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$USER_LANGUAGE.'.php')) {
			include_once APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$USER_LANGUAGE.'.php';
			$lang = $USER_LANGUAGE;
		} else {
			include_once(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.JAK_LANG.'.php');
			$lang = JAK_LANG;
		}

		if (!empty($ticketid) && is_numeric($ticketid)) {

			// Update the ticket
	        $jakdb->update("support_tickets", ["depid" => $depid, "operatorid" => $operatorid, "priorityid" => $priority, "toptionid" => $option, "private" => $private], ["id" => $ticketid]);

			// Now check if we have an answer
			if (!empty($message)) {

				// Filter the content
          		$contentf = jak_clean_safe_userpost($message);

		        $jakdb->insert("ticket_answers", ["ticketid" => $ticketid,
		        	"operatorid" => $userid,
		            "content" => $contentf,
		            "lastedit" => $jakdb->raw("NOW()"),
		            "sent" => $jakdb->raw("NOW()")]);

		        // Get the ID from the ticket
              	$lastid = $jakdb->id();

		        // Write the log file
              	JAK_base::jakWhatslog('', $userid, 0, 32, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

		        // Finally we inform the customer about the answer
	            $JAK_FORM_DATA = $jakdb->get("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.operatorid", "support_tickets.subject", "support_tickets.content", "support_tickets.clientid", "support_tickets.ip", "support_tickets.referrer", "support_tickets.notes", "support_tickets.private", "support_tickets.status", "support_tickets.attachments", "support_tickets.initiated", "support_tickets.ended", "support_tickets.updated", "support_tickets.priorityid", "support_tickets.toptionid", "support_departments.title", "clients.name", "clients.email", "clients.support_dep", "clients.credits", "clients.paid_until"], ["support_tickets.id" => $ticketid]);

	            // Calculate the update time
              	$responsetime = time() - $JAK_FORM_DATA["updated"];

              	// Let's check if that is is the first answer and it is not a private note
              	$firstcontact = 0;
                if ($JAK_FORM_DATA["initiated"] == $JAK_FORM_DATA["updated"]) $firstcontact = 1;

                // Insert response time
                insertResponsetime($userid, $ticketid, $responsetime, $firstcontact);

                // Update the ticket
	            $jakdb->update("support_tickets", ["status" => 2, "updated" => time()], ["id" => $ticketid]);

	            // Rest Api URL
	            $ticketurl = str_replace('rest/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $ticketid, JAK_rewrite::jakCleanurl($JAK_FORM_DATA["subject"])));

	            // Let's check if we have an imap
	            $answeremail = $ticktext = '';
	            $check_imap = $jakdb->get("php_imap", "emailanswer", ["depid" => $JAK_FORM_DATA["depid"]]);
	            if ($check_imap) {
	              $answeremail = $check_imap;
	              $subjectl = JAK_TITLE.' - [#'.$ticketid.'] - RE:'.$JAK_FORM_DATA['subject'];
	            } else {
	              $subjectl = JAK_TITLE.' - RE:'.$JAK_FORM_DATA['subject'];
	            }

	            // Get the ticket answer template
	            if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
	    
	              if ($v["msgtype"] == 21 && $v["lang"] == JAK_LANG) {

	                $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}', '{ticketcontent}');
	                $replace   = array(BASE_URL, JAK_TITLE, $JAK_FORM_DATA['email'], $JAK_FORM_DATA['name'], $JAK_FORM_DATA['credits'], $JAK_FORM_DATA['paid_until'], '#'.$ticketid, $JAK_FORM_DATA['subject'], $ticketurl, $answeremail, replace_urls_emails($contentf, BASE_URL, JAK_FILES_DIRECTORY));
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
	              $cssUrl   = array($ticktext, BASE_URL, JAK_TITLE);
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
	              	$mail->addAddress($JAK_FORM_DATA['email']);
	              	if (!empty($answeremail)) $mail->AddReplyTo($answeremail);
	              	$mail->Subject = $subjectl;
	              	$mail->MsgHTML($body);
					$mail->Send();

	            }

	            $answersaved = true;
		        $sendform = true;

		    }

		    // Check if we have a change in the notes
		    if (!empty($notes)) {

		    	$savenotes = filter_var($notes, FILTER_SANITIZE_STRING);
	            // Update the ticket
	            $jakdb->update("support_tickets", ["notes" => $savenotes], ["id" => $ticketid]);

	            $sendform = true;

		    }

	        if (JAK_BILLING_MODE == 1 && $JAK_FORM_DATA["clientid"] != 0) {

	        	// Check if we have a change in the departmant
	            if ($depid != $olddepid) {
	                if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
	                  if ($v["id"] == $olddepid) {
	                    $oldcredits = $v["credits"];
	                  }      
	                }
	                if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
	                  if ($v["id"] == $depid) {
	                    $newcredits = $v["credits"];
	                  }       
	                }

	                if ($newcredits > $oldcredits) {
	                  $newc = $newcredits - $oldcredits;
	                  $jakdb->update("clients", ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
	                  // Credit system control
	                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => $userid, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
	                } elseif ($newcredits < $oldcredits) {
	                  $newc = $oldcredits - $newcredits;
	                  $jakdb->update("clients", ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
	                  // Credit system control
	                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => $userid, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
	                }

	                $sendform = true;
	            }
        	}

            // Check if we have a change in the priority
            if ($priority != $oldpriority) {
                $oldcredits = $jakdb->get("ticketpriority", "credits", ["id" => $oldpriority]);
                $newcredits = $jakdb->get("ticketpriority", "credits", ["id" => $priority]);

                if ($newcredits > $oldcredits) {
                  $newc = $newcredits - $oldcredits;
                  $jakdb->update("clients", ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => $userid, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
                } elseif ($newcredits < $oldcredits) {
                  $newc = $oldcredits - $newcredits;
                  $jakdb->update("clients", ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => $userid, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                }

                $sendform = true;
            }

            // Check if we have a change in the option
            if ($option != $oldoption) {
                $oldcredits = $jakdb->get("ticketoptions", "credits", ["id" => $oldoption]);
                $newcredits = $jakdb->get("ticketoptions", "credits", ["id" => $option]);

                if ($newcredits > $oldcredits) {
                  $newc = $newcredits - $oldcredits;
                  $jakdb->update("clients", ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => $userid, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
                } elseif ($newcredits < $oldcredits) {
                  $newc = $oldcredits - $newcredits;
                  $jakdb->update("clients", ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => $userid, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                }

                $sendform = true;
            }

            // ticket status update
            if ($tstatus != $toldstatus) {

            	// Has it ended forever
            	$tended = 0;
            	if ($tstatus == 3 || $tstatus == 4) $tended = time();

            	// Now let's update the status
    			$jakdb->update("support_tickets", ["status" => $tstatus, "ended" => $tended], ["id" => $ticketid]);

    			$sendform = true;

            }

            // And we complete the custom fields
            $formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 2]);
            if (isset($formfields) && !empty($formfields)) {
              foreach ($formfields as $v) {
                if (isset($_REQUEST[$v]) && is_array($_REQUEST[$v]) && !empty($_REQUEST[$v])) {
                  	$joinval = join(',', $_REQUEST[$v]);
                  	$jakdb->update("support_tickets", [$v => filter_var($joinval, FILTER_SANITIZE_STRING)], ["id" => $ticketid]);
                } elseif (isset($_REQUEST[$v]) && !empty($_REQUEST[$v])) {
                	$jakdb->update("support_tickets", [$v => filter_var($_REQUEST[$v], FILTER_SANITIZE_STRING)], ["id" => $ticketid]);
                } elseif (isset($v)) {
                	$jakdb->update("support_tickets", [$v => ""], ["id" => $ticketid]);
                }
              }

              $sendform = true;
            }

            // We have a change in the operator, let's inform the new operator.
            if ($operatorid != $oldopid && $operatorid != $userid) {

              // The new operator
              $new_operator_ticket = $jakdb->get("user", ["username", "email"], ["id" => $operatorid]);

              // The URL
              $opurlt = sprintf($jkl['hd249'], JAK_rewrite::jakParseurl('support', 'read', $ticketid));

              $maila = new PHPMailer(); // defaults to using php "mail()" or optional SMTP

              if (JAK_SMTP_MAIL) {
                $maila->IsSMTP(); // telling the class to use SMTP
                $maila->Host = JAK_SMTPHOST;
                $maila->SMTPAuth = (JAK_SMTP_AUTH ? true : false); // enable SMTP authentication
                $maila->SMTPSecure = JAK_SMTP_PREFIX; // sets the prefix to the server
                $maila->SMTPAutoTLS = false;
                $maila->SMTPKeepAlive = (JAK_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
                $maila->Port = JAK_SMTPPORT; // set the SMTP port for the GMAIL server
                $maila->Username = JAK_SMTPUSERNAME; // SMTP account username
                $maila->Password = JAK_SMTPPASSWORD; // SMTP account password
              }

              // Finally send the email
              $maila->SetFrom(JAK_EMAIL);
              $maila->addAddress($new_operator_ticket['email']);
              $maila->Subject = JAK_TITLE.' - '.$jkl['hd177'].' / '.$JAK_FORM_DATA['subject'];
              $maila->MsgHTML($opurlt);
              $maila->Send();

              $sendform = true;
            }

            // Ok we do have everything, let's role...
			if ($answersaved || $sendform) {

	  			// Form has been sent, let's send the success status
				die(json_encode(array('status' => true, 'answered' => $answersaved, 'sendform' => $sendform)));

			} else {
				die(json_encode(array('status' => false, 'errors' => $errors)));
			}
			die(json_encode(array('status' => false, 'errorcode' => 7, 'errorcode' => false)));
		} else {
			die(json_encode(array('status' => false, 'errorcode' => 9, 'errorcode' => false)));
		}
		die(json_encode(array('status' => false, 'errorcode' => 7, 'errorcode' => false)));
	} else {
		die(json_encode(array('status' => false, 'errorcode' => 1, 'errorcode' => false)));
	}
}

die(json_encode(array('status' => false, 'errorcode' => 7, 'errorcode' => false)));
?>