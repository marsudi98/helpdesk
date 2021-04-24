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
$jaktable1 = 'support_tickets_cc';
$jaktable2 = 'support_tickets';
$jaktable3 = 'support_departments';
$jaktable4 = 'ticket_answers';
$jaktable5 = 'clients';
$jaktable6 = 'user';
$jaktable7 = 'ticketpriority';
$jaktable8 = 'ticketoptions';
$jaktable9 = 'customfields_data';

// Reset some vars
$getTotal = 0;
$jkp = "";
$errors = array();

// The footer url for similar articles
$similarlink = JAK_SUPPORT_URL;
$similarshort = 't';
$similartitle = $jkl['hd123'];

// Get the user agent
$valid_agent = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);

// New ticket
if ($page1 == "n") {

  // Overwrite session
  $_SESSION["depinfo"] = '0';

  if (JAK_CLIENTID || JAK_TICKET_GUEST_WEB) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;

        if (isset($jkp['start-fresh']) && !empty($jkp['start-fresh'])) {
          unset($_SESSION["userinfo"]);
          unset($_SESSION["depinfo"]);
          $_SESSION["successmsg"] = $jkl['s'];
          jak_redirect($_SESSION['LCRedirect']);
        }

        if (isset($jkp['jak_depid']) && is_numeric($jkp['jak_depid']) && $jkp['jak_depid'] != 0) {
          $_SESSION["depinfo"] = $jkp['jak_depid'];
          $_SESSION["successmsg"] = $jkl['s'];
          // jak_redirect($_SESSION['LCRedirect']);
        }

        // We store the ticket
        if (isset($jkp['action']) && $jkp['action'] == "send_ticket") {

          if (empty($jkp['subject'])) {
            $errors['e'] = $jkl['hd105'].'<br>';
          }
            
          if (empty($jkp['content'])) { 
            $errors['e1'] = $jkl['e2'].'<br>';
          }

          if (!JAK_CLIENTID) {

            if (empty($jkp['name'])) { 
              $errors['e3'] = $jkl['e'].'<br>';
            }

            if (empty($jkp['email']) || !filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) { 
              $errors['e4'] = $jkl['e1'].'<br>';
            }

            if (jak_field_not_exist(strtolower($jkp['email']), $jaktable5, "email")) {
              $errors['e4'] = $jkl['hd35'].'<br>';
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

          }

          // And we check the custom fields
          $formfields = $jakdb->select('customfields', ["title", "val_slug"], ["AND" => ["fieldlocation" => 2, "mandatory" => 1]]);
          if (isset($formfields) && !empty($formfields)) {
            foreach ($formfields as $v) {
              if (!isset($jkp[$v["val_slug"]]) || empty($jkp[$v["val_slug"]])) {
                $errors[$v["val_slug"]] = sprintf($jkl['hd31'], $v["title"]).'<br>';
              }
            }
          }

          if (JAK_CLIENTID && JAK_BILLING_MODE == 1) {
            $priocredit = $optcredit = 0;
            if (isset($jkp["jak_priority"]) && is_numeric($jkp["jak_priority"])) {
              $priocredit = $jakdb->get($jaktable7, "credits", ["id" => $jkp["jak_priority"]]);
            }

            if (isset($jkp["jak_toption"]) && is_numeric($jkp["jak_toption"])) {
              $optcredit = $jakdb->get($jaktable8, "credits", ["id" => $jkp["jak_toption"]]);
            }

            $totalc = $jkp["depcredit"] + $priocredit + $optcredit;

            if ($jakclient->getVar("credits") < $totalc) {
              $errors['e2'] = sprintf($jkl['hd110'], $totalc, $jakclient->getVar("credits")).'<br>';
            }
          }
          
          if (count($errors) == 0) {

            // Filter the subject
            $subjectf = filter_var($jkp['subject'], FILTER_SANITIZE_STRING);
            $awb = filter_var($jkp['awb'], FILTER_SANITIZE_STRING);
            $jak_depid = filter_var($jkp['jak_depid'], FILTER_SANITIZE_STRING);
            $droppoint = filter_var($jkp['droppoint'], FILTER_SANITIZE_STRING);

            // Filter the content
            $contentf = jak_clean_safe_userpost($jkp['content']);

            if (JAK_CLIENTID) {
              $cname = $jakclient->getVar("name");
              $cemail = $jakclient->getVar("email");
              $cprivate = $jkp['jak_private'];
            } else {
              $cname = filter_var($jkp['name'], FILTER_SANITIZE_STRING);
              $cemail = $jkp['email'];
              $cprivate = 0;
            }

            // We need to check if there is no option set.
            $jak_priority = $jak_toption = 0;
            if (isset($jkp['jak_priority']) && is_numeric($jkp['jak_priority'])) $jak_priority = $jkp['jak_priority'];
            if (isset($jkp['jak_toption']) && is_numeric($jkp['jak_toption'])) $jak_toption = $jkp['jak_toption'];

            // We have the due date and we will need to make it right for mysql
            $duedatesql = date("Y-m-d", strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day'));

            // We need the time once
            $ticketcreated = time();
            
            // Create the ticket
            $result = $jakdb->insert($jaktable2, ["depid" => $jak_depid,
              "awb"       => $awb,
              "subject"   => $subjectf,
              "content"   => $contentf,
              "clientid"  => JAK_CLIENTID,
              "name" => $cname,
              "email" => $cemail,
              "referrer" => filter_var($referrer, FILTER_SANITIZE_STRING),
              "private" => $cprivate,
              "priorityid" => $jak_priority,
              "toptionid" => $jak_toption,
              "status" => 1,
              "ip" => $ipa,
              "updated" => $ticketcreated,
              "initiated" => $ticketcreated,
              "duedate" => $duedatesql]);

            if (!$result) {
              $_SESSION["infomsg"] = $jkl['i'];
              jak_redirect($_SESSION['LCRedirect']);
            } else {

              // Get the ID from the ticket
              $lastid = $jakdb->id();

              // And we complete the custom fields
              $formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 2]);
              if (isset($formfields) && !empty($formfields)) {
                foreach ($formfields as $v) {
                  if (isset($jkp[$v]) && is_array($jkp[$v])) {
                    $joinval = join(',', $jkp[$v]);
                    $jakdb->update($jaktable2, [$v => filter_var($joinval, FILTER_SANITIZE_STRING)], ["id" => $lastid]);
                  } else {
                    $jakdb->update($jaktable2, [$v => filter_var($jkp[$v], FILTER_SANITIZE_STRING)], ["id" => $lastid]);
                  }
                }
              }

              // Write the log file each time someone tries to login before
              JAK_base::jakWhatslog('', 0, JAK_CLIENTID, 8, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $cemail, $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

              // Now we have a guest that write a ticket
              if (!JAK_CLIENTID && isset($jkp['createaccount']) && $jkp['createaccount'] == 1 || !JAK_CLIENTID && JAK_TICKET_ACCOUNT) {

                // create new password
                $password = jak_password_creator();
                $passcrypt = hash_hmac('sha256', $password, DB_PASS_HASH);

                $jakdb->insert($jaktable5, ["chat_dep" => JAK_STANDARD_CHAT_DEP,
                  "support_dep" => JAK_STANDARD_SUPPORT_DEP,
                  "faq_cat" => JAK_STANDARD_FAQ_CAT,
                  "name" => $cname,
                  "email" => $cemail,
                  "password" => $passcrypt,
                  "canupload" => 1,
                  "access" => 1,
                  "time" => $jakdb->raw("NOW()")]);

                $clientid = $jakdb->id();
                  
                if (!$clientid) {
                  $_SESSION["errormsg"] = $jkl['not'];
                  jak_redirect($_SESSION['LCRedirect']);
                } else {

                  // Write the log file each time someone tries to login before
                  JAK_base::jakWhatslog('', 0, $clientid, 12, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $cemail, $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                  // +1 for the support request
                  $jakdb->update($jaktable5, ["supportrequests[+]" => 1], ["id" => $clientid]);

                  // Update the ticket to the correct client
                  $jakdb->update($jaktable2, ["clientid" => $clientid, "private" => $jkp['jak_private']], ["id" => $lastid]);

                  $newuserpath = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.$clientid;
                      
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
                        $jakdb->update($jaktable2, [$v => $joinval], ["id" => $clientid]);
                      } else {
                        $jakdb->update($jaktable2, [$v => $jkp[$v]], ["id" => $clientid]);
                      }
                    }
                  }

                  // Get the email template
                  $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

                  // Change fake vars into real ones.
                  if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
                    if ($v["msgtype"] == 14 && $v["lang"] == JAK_LANG) {
                      $phold = array('{url}', '{title}', '{cname}', '{cemail}', '{cpassword}', '{email}');
                      $replace   = array(BASE_URL, JAK_TITLE, $cname, $cemail, $password, JAK_EMAIL);
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
                  $mail->addAddress($cemail);
                  $mail->Subject = JAK_TITLE.' - '.$jkl['hd33'];
                  $mail->MsgHTML($body);

                  if ($mail->Send()) $_SESSION["infomsg"] = $jkl['hd32'];

                  $_SESSION["infomsg"] = $jkl["hd34"];
                }

              } else {

                // Set the client ticket request +1
                $jakdb->update($jaktable5, ["supportrequests[+]" => 1], ["id" => JAK_CLIENTID]);

                // We run on a credit based system?
                if (JAK_BILLING_MODE == 1 && $totalc != 0) {
                  // We need to get the credits
                  $jakdb->update($jaktable5, ["credits[-]" => $totalc], ["id" => JAK_CLIENTID]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => JAK_CLIENTID, "credits" => $totalc, "created" => $jakdb->raw("NOW()")]);
                }

              }

              // We will need to inform the operator if set so
              if (JAK_TICKET_INFORM_R) {

                // Get the email template
                $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

                // Operator URL
                $ticketurl = JAK_rewrite::jakParseurl('operator', 'support', 'read', $lastid);
                
                // Change fake vars into real ones.
                $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
                $cssUrl   = array(sprintf($jkl['hd94'], $ticketurl), BASE_URL, JAK_TITLE);
                $nlcontent = str_replace($cssAtt, $cssUrl, $nlhtml);
                
                $bodya = str_ireplace("[\]", "", $nlcontent);

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

                // We need to send it to the department as well
                if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
                
                  if ($v["id"] == $_SESSION["depinfo"]) {
                    if ($v["email"]) $maila->AddCC($v["email"]);
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
                $maila->addAddress(JAK_EMAIL);
                $maila->Subject = JAK_TITLE.' - '.$subjectf;
                $maila->MsgHTML($bodya);

                // We sent silently
                $maila->Send();

              }

              // we send push notifications
              $resultu = $jakdb->select("user", ["id", "pusho_tok", "pusho_key", "push_notifications"], ["AND" => ["support_dep" => [0, $_SESSION["depinfo"]], "access" => 1]]);
            
              if (isset($resultu) && !empty($resultu)) foreach ($resultu as $rowu) {

                if ($rowu["push_notifications"] && ((JAK_NATIVE_APP_TOKEN && JAK_NATIVE_APP_KEY) || ($rowu["pusho_tok"] && $rowu["pusho_key"]))) {

                  jak_send_notifications($rowu["id"], 0, JAK_TITLE, $jkl['hd47'], false, $rowu["push_notifications"], false, "", $rowu["pusho_tok"], $rowu["pusho_key"], false);

                }
              }

              $_SESSION["successmsg"] = $jkl['hd96'];
              // Go to the tickets
              jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $lastid, JAK_rewrite::jakCleanurl($subjectf)));

            }
            
          } else {
            $errors = $errors;
          }

        }
    }

    // Only if we have a valid department we call the rest
    if (isset($_SESSION["depinfo"]) && is_numeric($_SESSION["depinfo"])) {

      if (JAK_CLIENTID) {
        // We run on a credit based system?
        if (JAK_BILLING_MODE == 1) {
          // We need to get the credits
          if ($jakclient->getVar("credits") < 2) {
            $_SESSION["errormsg"] = sprintf($jkl["hd103"], $jakclient->getVar("credits"), JAK_rewrite::jakParseurl(JAK_CLIENT_URL));
            jak_redirect(JAK_rewrite::jakParseurl(JAK_CLIENT_URL));
          }

        // We run the membership based system
        } elseif (JAK_BILLING_MODE == 2 && strtotime($client_save["paid_until"]) < time()) {
          $_SESSION["errormsg"] = sprintf($jkl['hd104'], JAK_rewrite::jakParseurl(JAK_CLIENT_URL));
          jak_redirect(JAK_rewrite::jakParseurl(JAK_CLIENT_URL));
        }
      }

      // Let us collect the department details.
      $DEP_CREDIT = 0;
      if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
        if ($v["id"] == $_SESSION["depinfo"]) {
          $DEP_TITLE = $v["title"];
          $DEP_CREDIT = $v["credits"];
        }         
      }

      // Get all priorities
      $PRIORITY_ALL = $jakdb->select($jaktable7, "*", ["depid" => [0, $_SESSION["depinfo"]]]);
      // Get all options
      $TOPTIONS_ALL = $jakdb->select($jaktable8, "*", ["depid" => [0, $_SESSION["depinfo"]]]);

      // Get the custom fields if any
      $custom_fields = jak_get_custom_fields_modern($jkp, 2, $_SESSION["depinfo"], $jakclient->getVar("language"), false, false, false, false, $errors);

      // finally get the predefined message if any.
      $JAK_PRE_CONTENT = '';
      $JAK_PRE_CONTENT = $jakdb->get($jaktable3, "pre_content", ["id" => $_SESSION["depinfo"]]);

    }



    // if (!isset($_SESSION["depinfo"])) {
      // Get the correct departments
      $DEPARTMENTS_ALL = array();
      if (JAK_CLIENTID) {
        if (isset($HD_SUPPORT_DEPARTMENTS) && !empty($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $d) {
          if ($jakclient->getVar("support_dep") == 0) {
            $DEPARTMENTS_ALL[] = $d;
          } elseif ($jakclient->getVar("support_dep") != 0 && in_array($d["id"], explode(",", $jakclient->getVar("support_dep")))) {
            $DEPARTMENTS_ALL[] = $d;
          }
        }
      } else {
        if (isset($HD_SUPPORT_DEPARTMENTS) && !empty($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $d) {
          if ($d["guesta"] == 1) {
            $DEPARTMENTS_ALL[] = $d;
          }
        }
      }
    // }

    $limitreached = false;
    if (JAK_TICKET_LIMIT != 0) {
      $totaltickets = $jakdb->count($jaktable2, "id", ["AND" => ["status" => 2, "clientid" => JAK_CLIENTID]]);
      if ($totaltickets >= JAK_TICKET_LIMIT) {
        $limitreached = true;
      }
    }

    // Include the javascript file for results
    $js_file_footer = 'js_newticket.php';

    // Load the template
    include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/newticket.php';

  } else {
    jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL));
  }

// Get the ticket details
} elseif ($page1 == "t" && isset($page2) && is_numeric($page2) && jak_row_exist($page2, $jaktable2)) {

  $ticketwrite = $uploadactive = false;

  // Clients can edit the own post for 15 minutes
  $mino = date('Y-m-d H:i:s', (time() - 15 * 60));

  // Get the data
  $JAK_FORM_DATA = $jakdb->get($jaktable2, ["[>]".$jaktable3 => ["depid" => "id"], "[>]".$jaktable5 => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.subject(title)", "support_tickets.content", "support_tickets.clientid", "support_tickets.ip", "support_tickets.private", "support_tickets.status", "support_tickets.attachments", "support_tickets.initiated", "support_tickets.ended", "support_tickets.updated", "support_tickets.priorityid", "support_tickets.toptionid", "support_departments.title(department)", "clients.name", "clients.email", "clients.picture", "clients.credits(clientcredits)", "clients.paid_until"], ["support_tickets.id" => $page2]);

  // Get the title
  $titlearray = explode(" ", $JAK_FORM_DATA["title"], 5);
  $titlearray = array_filter($titlearray,function($v){ return strlen($v) > 2; });

  // Check permissions depend on the login status
  if (JAK_CLIENTID) {

    // The ticket is private and we are not the owner
    if ($JAK_FORM_DATA["private"] && $JAK_FORM_DATA["clientid"] != JAK_CLIENTID) jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL));

    if ($jakclient->getVar("support_dep") == 0) {

      // Similar
      $similarart = $jakdb->select($jaktable2, ["id", "subject(title)", "content", "updated(time)"], ["OR #sim" => ["AND #public" => ["id[!]" => $page2, "content[~]" => $titlearray, "private" => 0], "AND #private" => ["id[!]" => $page2, "content[~]" => $titlearray, "private" => 1, "clientid" => JAK_CLIENTID]], "LIMIT" => 3]);

      // Page Navigation
      $nextp = $jakdb->get($jaktable2, ["id", "subject"], ["OR #sim" => ["AND #public" => ["id[>]" => $page2, "private" => 0], "AND #private" => ["id[>]" => $page2, "private" => 1, "clientid" => JAK_CLIENTID]], "ORDER" => ["id" => "ASC"]]);
      $prevp = $jakdb->get($jaktable2, ["id", "subject"], ["OR #sim" => ["AND #public" => ["id[<]" => $page2, "private" => 0], "AND #private" => ["id[<]" => $page2, "private" => 1, "clientid" => JAK_CLIENTID]], "ORDER" => ["id" => "DESC"]]);

    } else {

      if (!$jakdb->has($jaktable3, ["OR" => ["guesta" => 1, "id" => [$jakclient->getVar("support_dep")]]])) jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL));

        // Similar
        $similarart = $jakdb->select($jaktable2, ["id", "subject(title)", "content", "updated(time)"], ["OR #sim" => ["AND #public" => ["id[!]" => $page2, "depid" => [$jakclient->getVar("support_dep")], "content[~]" => $titlearray, "private" => 0], "AND #private" => ["id[!]" => $page2, "depid" => [$jakclient->getVar("support_dep")], "content[~]" => $titlearray, "private" => 1, "clientid" => JAK_CLIENTID]], "LIMIT" => 3]);

        // Page Navigation
        $nextp = $jakdb->get($jaktable2, ["id", "subject"], ["OR #sim" => ["AND #public" => ["id[>]" => $page2, "depid" => [$jakclient->getVar("support_dep")], "private" => 0], "AND #private" => ["id[>]" => $page2, "depid" => [$jakclient->getVar("support_dep")], "private" => 1, "clientid" => JAK_CLIENTID]], "ORDER" => ["id" => "ASC"]]);
        $prevp = $jakdb->get($jaktable2, ["id", "subject"], ["OR #sim" => ["AND #public" => ["id[<]" => $page2, "depid" => [$jakclient->getVar("support_dep")], "private" => 0], "AND #private" => ["id[<]" => $page2, "depid" => [$jakclient->getVar("support_dep")], "private" => 1, "clientid" => JAK_CLIENTID]], "ORDER" => ["id" => "DESC"]]);
    }

    // Check if we are the owner
    if (JAK_CLIENTID == $JAK_FORM_DATA["clientid"]) $ticketwrite = true;

    // Check if we can upload
    if (JAK_CLIENTID == $JAK_FORM_DATA["clientid"] && (JAK_TICKET_ATTACH != 0 && $JAK_FORM_DATA["attachments"] < JAK_TICKET_ATTACH && $jakclient->getVar("canupload")) || (JAK_TICKET_ATTACH == 0 && $jakclient->getVar("canupload"))) $uploadactive = true;

    // Ticket is still open
    $ticketopen = true;
    if ($JAK_FORM_DATA["status"] == 3 && JAK_TICKET_REOPEN != 0) {
        // Clients can edit the own post for 15 minutes
        $tclosed = date('Y-m-d H:i:s', strtotime("-".JAK_TICKET_REOPEN." days"));
        if ($JAK_FORM_DATA["ended"] > strtotime($tclosed)) $ticketopen = $uploadactive = false;
    }
    if ($JAK_FORM_DATA["status"] == 4) $ticketopen = $uploadactive = false;

  } elseif (JAK_USERID) {

    if ($jakuser->getVar("support_dep") == 0 || JAK_SUPERADMINACCESS) {

      // Similar
      $similarart = $jakdb->select($jaktable2, ["id", "subject(title)", "content", "updated(time)"], ["AND" => ["id[!]" => $page2, "content[~]" => $titlearray], "LIMIT" => 3]);

      // Page Navigation
      $nextp = $jakdb->get($jaktable2, ["id", "subject"], ["id[>]" => $page2, "ORDER" => ["id" => "ASC"]]);
      $prevp = $jakdb->get($jaktable2, ["id", "subject"], ["id[<]" => $page2, "ORDER" => ["id" => "DESC"]]);

    } else {

      if (!$jakdb->has($jaktable3, ["OR" => ["guesta" => 1, "id" => [$jakuser->getVar("support_dep")]]])) jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL));

      // Similar
      $similarart = $jakdb->select($jaktable2, ["id", "subject(title)", "content", "updated(time)"], ["AND" => ["id[!]" => $page2, "depid" => [$jakuser->getVar("support_dep")], "content[~]" => $titlearray], "LIMIT" => 3]);

      // Page Navigation
      $nextp = $jakdb->get($jaktable2, ["id", "subject"], ["AND" => ["id[>]" => $page2, "depid" => [$jakuser->getVar("support_dep")]], "ORDER" => ["id" => "ASC"]]);
      $prevp = $jakdb->get($jaktable2, ["id", "subject"], ["AND" => ["id[<]" => $page2, "depid" => [$jakuser->getVar("support_dep")]], "ORDER" => ["id" => "DESC"]]);
    }

    // Check if we are the owner
    if (JAK_SUPERADMINACCESS || JAK_USERID == $JAK_FORM_DATA["operatorid"]) $ticketwrite = true;

    // Check if we can upload files
    if (JAK_SUPERADMINACCESS || $jakuser->getVar("files")) $uploadactive = true;

  } else {

    // The ticket is private and we are not the owner
    if ($JAK_FORM_DATA["private"]) jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL));

    // Similar
    $similarart = $jakdb->select($jaktable2, ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject"], ["AND" => ["id[!]" => $page2, "support_tickets.private" => 0, "support_departments.guesta" => 1, "support_tickets.content[~]" => $titlearray], "LIMIT" => 5]);

    // Page Navigation
    $nextp = $jakdb->get("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject"], ["AND" => ["support_departments.guesta" => 1, "support_tickets.id[>]" => $page2, "support_tickets.private" => 0], "ORDER" => ["support_tickets.id" => "ASC"]]);
    $prevp = $jakdb->get("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject"], ["AND" => ["support_departments.guesta" => 1, "support_tickets.id[<]" => $page2, "support_tickets.private" => 0], "ORDER" => ["support_tickets.id" => "DESC"]]);
  }

  // Now do the dirty work with the post vars
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $jkp = $_POST;

      // We store the answer
      if (isset($jkp['action']) && $jkp['action'] == "send_answer") {

        // We edit a post
        if (isset($jkp['editpost']) && !empty($jkp['editpost']) && is_numeric($jkp['editpost'])) {

          if (JAK_USERID || $jakdb->has($jaktable4, ["AND" => ["id" => $jkp['editpost'], "clientid" => JAK_CLIENTID, "sent[>]" => $mino]])) {

            // Filter the content
            $contentf = jak_clean_safe_userpost($jkp['content']);

            $result = $jakdb->update($jaktable4, ["content" => $contentf], ["id" => $jkp['editpost']]);

            if (!$result) {
              $_SESSION["infomsg"] = $jkl['not'];
              jak_redirect($_SESSION['LCRedirect']);
            } else {
              $_SESSION["successmsg"] = $jkl['s'];
              jak_redirect($_SESSION['LCRedirect']);
            }

          } else {
            $_SESSION["infomsg"] = $jkl['hd95'];
            jak_redirect($_SESSION['LCRedirect']);
          }

        }

        // We change the status
        if (isset($jkp['changestatus']) && !empty($jkp['changestatus']) && $jkp['jak_status'] != $JAK_FORM_DATA["status"]) {

          if (JAK_USERID || $jakdb->has($jaktable2, ["AND" => ["id" => $page2, "clientid" => JAK_CLIENTID]])) {

            // Filter the content
            $contentf = jak_clean_safe_userpost($jkp['content']);

            $result = $jakdb->update($jaktable2, ["status" => $jkp['jak_status'], "updated" => time()], ["id" => $page2]);

            if (!$result) {
              $_SESSION["infomsg"] = $jkl['not'];
              jak_redirect($_SESSION['LCRedirect']);
            } else {

              // Ticket is closed set an ending time
              if ($jkp['jak_status'] == 3) {
                $jakdb->update($jaktable2, ["ended" => time()], ["id" => $page2]);

                // Send email to customers if set so.
                if (JAK_TICKET_CLOSE_R == 1) {

                  // Dashboard URL
                  $ticketurl = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2, JAK_rewrite::jakCleanurl($JAK_FORM_DATA["title"]));

                  // Let's check if we have an imap
                  $answeremail = $ticktext = '';
                  $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $JAK_FORM_DATA_CUSTOM["depid"]]);
                  if ($check_imap) $answeremail = $check_imap;

                  // Get the ticket answer template
                  if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
              
                    if ($v["msgtype"] == 23 && $v["lang"] == JAK_LANG) {

                      $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
                      $replace   = array(BASE_URL, JAK_TITLE, $JAK_FORM_DATA['email'], $JAK_FORM_DATA['name'], $JAK_FORM_DATA['clientcredits'], $JAK_FORM_DATA['paid_until'], '#'.$delart, $JAK_FORM_DATA_CUSTOM['subject'], $ticketurl, $answeremail);
                      $ticktext = str_replace($phold, $replace, $v["message"]);
                      break;
                          
                    }
                        
                  }

                  // Get the email template
                  if (!empty($ticktext)) {
                      $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
                      
                      // Change fake vars into real ones.
                      $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
                      $cssUrl   = array($ticktext, BASE_URL, JAK_TITLE);
                      $nlcontent = str_replace($cssAtt, $cssUrl, $nlhtml);
                      
                      $body = str_ireplace("[\]", "", $nlcontent);

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
                      $mail->Subject = JAK_TITLE.' - '.sprintf($jkl['hd101'], $JAK_FORM_DATA['title']);
                      $mail->MsgHTML($body);

                      // Send email to customer
                      $mail->Send();
                  }

                } // end sending closed message
              }

              $_SESSION["successmsg"] = $jkl['s'];
              jak_redirect($_SESSION['LCRedirect']);
            }

          } else {
            $_SESSION["infomsg"] = $jkl['hd100'];
            jak_redirect($_SESSION['LCRedirect']);
          }

        }

        if (empty($jkp['content'])) { 
          $errors['e'] = $jkl['e2'];
        }

        if (count($errors) == 0) {

          // Filter the content
          $contentf = jak_clean_safe_userpost($jkp['content']);

          if (JAK_USERID) {

            $result = $jakdb->insert($jaktable4, ["ticketid" => $page2,
            "operatorid" => JAK_USERID,
            "content" => $contentf,
            "lastedit" => $jakdb->raw("NOW()"),
            "sent" => $jakdb->raw("NOW()")]);

            // Get the ID from the ticket
            $lastid = $jakdb->id();

            // Write the log file each time someone tries to login before
            JAK_base::jakWhatslog('', JAK_USERID, 0, 32, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

          } elseif (JAK_CLIENTID) {

            $result = $jakdb->insert($jaktable4, ["ticketid" => $page2,
            "clientid" => JAK_CLIENTID,
            "content" => $contentf,
            "lastedit" => $jakdb->raw("NOW()"),
            "sent" => $jakdb->raw("NOW()")]);

            // Get the ID from the ticket
            $lastid = $jakdb->id();

            // Write the log file each time someone tries to login before
            JAK_base::jakWhatslog('', 0, JAK_CLIENTID, 32, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakclient->getVar("email"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

          }
      
          if (!$result) {
              $_SESSION["infomsg"] = $jkl['not'];
              jak_redirect($_SESSION['LCRedirect']);
          } else {

            // We have the due date and we will need to make it right for mysql
            $duedatesql = date("Y-m-d", strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day'));

            $jakdb->update($jaktable2, ["updated" => time(), "status" => 1, "ended" => 0, "duedate" => $duedatesql], ["id" => $page2]);

              // And we complete the custom fields
              $formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 2]);
              if (isset($formfields) && !empty($formfields)) {
                foreach ($formfields as $v) {
                  if (isset($jkp[$v]) && is_array($jkp[$v])) {
                    $joinval = join(',', $jkp[$v]);
                    $jakdb->update($jaktable2, [$v => filter_var($joinval, FILTER_SANITIZE_STRING)], ["id" => $page2]);
                  } else {
                    $jakdb->update($jaktable2, [$v => filter_var($jkp[$v], FILTER_SANITIZE_STRING)], ["id" => $page2]);
                  }
                }
              }

              // Finally we inform the customer about the answer
              if (JAK_USERID) {

                // Dashboard URL
                $ticketurl = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2);

                // Let's check if we have an imap
                $answeremail = $ticktext = '';
                $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $JAK_FORM_DATA_CUSTOM["depid"]]);
                if ($check_imap) $answeremail = $check_imap;

                // Get the ticket answer template
                if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
        
                  if ($v["msgtype"] == 21 && $v["lang"] == JAK_LANG) {

                    $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}', '{ticketcontent}');
                    $replace   = array(BASE_URL, JAK_TITLE, $JAK_FORM_DATA['email'], $JAK_FORM_DATA['name'], $JAK_FORM_DATA['clientcredits'], $JAK_FORM_DATA['paid_until'], '#'.$page2, $JAK_FORM_DATA['title'], $ticketurl, $answeremail, replace_urls_emails($contentf, BASE_URL, JAK_FILES_DIRECTORY));
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
                  $mail->Subject = JAK_TITLE.' - RE:'.$JAK_FORM_DATA['title'];
                  $mail->MsgHTML($body);

                  if ($mail->Send()) $_SESSION["infomsg"] = $jkl['hd32'];

                }

              }

              // We will need to inform the operator if set so
              if (JAK_CLIENTID && JAK_TICKET_INFORM_R) {

                // Get the email template
                $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

                // Operator URL
                $ticketurl = JAK_rewrite::jakParseurl('operator', 'support', 'read', $page2);
                
                // Change fake vars into real ones.
                $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
                $cssUrl   = array(sprintf($jkl['hd93'], $ticketurl), BASE_URL, JAK_TITLE);
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
                
                  if ($v["id"] == $JAK_FORM_DATA["depid"]) {
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
                $mail->AddReplyTo($JAK_FORM_DATA['email']);
                $mail->Subject = JAK_TITLE.' - RE:'.$JAK_FORM_DATA['title'];
                $mail->MsgHTML($body);

                // We sent silently
                $mail->Send();

                // Now we will need to inform the customers that have assigned CC
                $operator_cc_id = $jakdb->select($jaktable1, "operatorid", ["ticketid" => $page2]);
                if (isset($operator_cc_id) && !empty($operator_cc_id)) {

                  // Get the emails
                  $operator_cc_email = $jakdb->select($jaktable6, "email", ["id" => $operator_cc_id]);

                  // The URL
                  $opurlt = sprintf($jkl['hd130'], JAK_rewrite::jakParseurl('operator', 'support', 'read', $page2));

                  $mailcc = new PHPMailer(); // defaults to using php "mail()" or optional SMTP

                  if (JAK_SMTP_MAIL) {
                    $mailcc->IsSMTP(); // telling the class to use SMTP
                    $mailcc->Host = JAK_SMTPHOST;
                    $mailcc->SMTPAuth = (JAK_SMTP_AUTH ? true : false); // enable SMTP authentication
                    $mailcc->SMTPSecure = JAK_SMTP_PREFIX; // sets the prefix to the server
                    $mailcc->SMTPAutoTLS = false;
                    $mailcc->SMTPKeepAlive = (JAK_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
                    $mailcc->Port = JAK_SMTPPORT; // set the SMTP port for the GMAIL server
                    $mailcc->Username = JAK_SMTPUSERNAME; // SMTP account username
                    $mailcc->Password = JAK_SMTPPASSWORD; // SMTP account password
                  }

                  // Finally send the email
                  $mailcc->SetFrom(JAK_EMAIL);
                  if (isset($operator_cc_email) && !empty($operator_cc_email)) foreach ($operator_cc_email as $occ) {
                    $mailcc->addAddress($occ);
                  }
                  $mailcc->Subject = sprintf($jkl['hd131'], $JAK_FORM_DATA['subject']);
                  $mailcc->MsgHTML($opurlt);
                  $mailcc->Send();
                }

              }

              // we send push notifications
              $topid = $jakdb->get($jaktable2, "operatorid", ["id" => $page2]);
              $rowu = $jakdb->get("user", ["id", "pusho_tok", "pusho_key", "push_notifications"], ["AND" => ["id" => $topid, "access" => 1]]);
            
              if (isset($rowu) && !empty($rowu)) {

                if ($rowu["push_notifications"] && ((JAK_NATIVE_APP_TOKEN && JAK_NATIVE_APP_KEY) || ($rowu["pusho_tok"] && $rowu["pusho_key"]))) {

                  jak_send_notifications($rowu["id"], 0, JAK_TITLE, $jkl['hd127'], false, $rowu["push_notifications"], false, "", $rowu["pusho_tok"], $rowu["pusho_key"], false);

                }
              }

              $_SESSION["successmsg"] = $jkl['hd97'];
              jak_redirect($_SESSION['LCRedirect']);
          }

        // Output the errors
        } else {
            $errors = $errors;
        }

      }

      if (isset($jkp['action']) && $jkp['action'] == "file_refresh") {

        $JAK_TICKET_FILES = jak_get_files(APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2);
        
        // Output the header  
        header('Cache-Control: no-cache');
        die(json_encode(array('status' => 1, 'html' => $acajax->get_commentajax($jkl['hd69']))));

      }
      
  }

  // Page Nav
  $JAK_NAV_NEXT = $JAK_NAV_NEXT_TITLE = $JAK_NAV_PREV = $JAK_NAV_PREV_TITLE = "";
  if (isset($nextp) && !empty($nextp)) {
    $JAK_NAV_NEXT = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $nextp['id'], JAK_rewrite::jakCleanurl($nextp['subject']));
    $JAK_NAV_NEXT_TITLE = $nextp['subject'];
  }
  if (isset($prevp) && !empty($prevp)) {
    $JAK_NAV_PREV = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $prevp['id'], JAK_rewrite::jakCleanurl($prevp['subject']));
    $JAK_NAV_PREV_TITLE = $prevp['subject'];
  }

  // Get the Priorities if any
  if ($JAK_FORM_DATA["priorityid"] != 0) {
    $JAK_PRIORITY_DATA = $jakdb->get($jaktable7, ["title", "class"], ["id" => $JAK_FORM_DATA["priorityid"]]);
  }

  // Get the options if any
  if ($JAK_FORM_DATA["toptionid"] != 0) {
    $JAK_OPTION_DATA = $jakdb->get($jaktable8, ["title", "icon"], ["id" => $JAK_FORM_DATA["toptionid"]]);
  }

  // Get the ticket Answers
  if (JAK_USERID) {
    $JAK_ANSWER_DATA = $jakdb->select($jaktable4, ["[>]".$jaktable6 => ["operatorid" => "id"], "[>]".$jaktable5 => ["clientid" => "id"]], ["ticket_answers.id", "ticket_answers.content", "ticket_answers.lastedit", "ticket_answers.sent", "user.id(oid)", "user.name(oname)", "user.picture", "clients.id(cid)", "clients.name(cname)", "clients.picture(cpicture)"], ["ticket_answers.ticketid" => $page2, "ORDER" => ["ticket_answers.sent" => "ASC"]]);
  } else {
    $JAK_ANSWER_DATA = $jakdb->select($jaktable4, ["[>]".$jaktable6 => ["operatorid" => "id"], "[>]".$jaktable5 => ["clientid" => "id"]], ["ticket_answers.id", "ticket_answers.content", "ticket_answers.lastedit", "ticket_answers.sent", "user.id(oid)", "user.name(oname)", "user.picture", "clients.id(cid)", "clients.name(cname)", "clients.picture(cpicture)"], ["AND" => ["ticket_answers.ticketid" => $page2, "ticket_answers.private" => 0], "ORDER" => ["ticket_answers.sent" => "ASC"]]);
  }

  // Get the attachments if any
  if ($JAK_FORM_DATA["attachments"] != 0) {

    // Now we get the path
    $targetPathTA = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2;

    if (is_dir($targetPathTA)) {
      $JAK_TICKET_FILES = jak_get_files_encrypt($targetPathTA);
    } else {
      // We could not find any attachmeents
      $jakdb->update($jaktable2, ["attachments" => 0], ["id" => $page2]);
    }
  }

  // Get the custom fields if any
  if ($ticketwrite) {
      $JAK_CUSTOM_FIELD = $jakdb->get($jaktable2, "*", ["id" => $page2]);
      $langcust = JAK_LANG;
      if (JAK_CLIENTID) $langcust = $jakclient->getVar("language");
      if (JAK_USERID) $langcust = $jakuser->getVar("language");
      $custom_fields = jak_get_custom_fields_modern($JAK_CUSTOM_FIELD, 2, $JAK_FORM_DATA["depid"], $langcust, false, false, false, false, $errors);
  }

  // Include the javascript file for results
  $js_file_footer = 'js_ticket.php';

  // Load the template
  include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/ticket.php';

// We need only the categories
} else {

  // Let's go on with the script
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    if (isset($_POST['action']) && $_POST['action'] == "depid") {
      if (isset($_POST['jak_depid']) && is_numeric($_POST['jak_depid']) && $_POST['jak_depid'] != 0) {
        if (JAK_CLIENTID && ($jakclient->getVar("support_dep") == 0 || in_array($_POST['jak_depid'], explode(",", $jakclient->getVar("support_dep"))))) {
          $_SESSION["sortdepid"] = $_POST['jak_depid'];
          jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'c', $_POST['jak_depid']));
        } elseif (JAK_USERID && ($jakuser->getVar("support_dep") == 0 || in_array($_POST['jak_depid'], explode(",", $jakuser->getVar("support_dep"))))) {
          $_SESSION["sortdepid"] = $_POST['jak_depid'];
          jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'c', $_POST['jak_depid']));
        } else {
          if (isset($HD_SUPPORT_DEPARTMENTS) && !empty($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $d) {
            if ($d["id"] == $_POST['jak_depid'] && $d["guesta"] == 1) {
              $_SESSION["sortdepid"] = $_POST['jak_depid'];
              jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'c', $_POST['jak_depid']));
            }
          }
        }
      }
      
      unset($_SESSION["sortdepid"]);
      jak_redirect(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL));
    }
  }

  // Get the correct departments
  $dep_filter = array();
  if (JAK_CLIENTID) {
    if (isset($HD_SUPPORT_DEPARTMENTS) && !empty($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $d) {
      if ($d["guesta"] == 1 || $jakclient->getVar("support_dep") == 0 || in_array($d["id"], explode(",", $jakclient->getVar("support_dep")))) {
        $dep_filter[] = $d;
      }
    }
  } elseif (JAK_USERID) {
    if (isset($HD_SUPPORT_DEPARTMENTS) && !empty($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $d) {
      if ($jakuser->getVar("support_dep") == 0 || in_array($d["id"], explode(",", $jakuser->getVar("support_dep")))) {
        $dep_filter[] = $d;
      }
    }
  } else {
    if (isset($HD_SUPPORT_DEPARTMENTS) && !empty($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $d) {
      if ($d["guesta"] == 1) {
        $dep_filter[] = $d;
      }
    }
  }

  // Include the javascript file for results
  $js_file_footer = 'js_support.php';

  // Load the template
  include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/support.php';

}
?>