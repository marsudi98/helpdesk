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
if (!jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$jaktable = 'support_tickets';
$jaktable1 = 'support_departments';
$jaktable2 = 'support_responses';
$jaktable3 = 'ticketpriority';
$jaktable4 = 'ticket_answers';
$jaktable5 = 'clients';
$jaktable6 = 'user';
$jaktable7 = 'php_imap';
$jaktable8 = 'ticketoptions';
$jaktable9 = 'support_tickets_cc';
$jaktable10 = 'support_tickets_response';

// Reset some stuff
$jkp = "";

// Explode the time format so it is always available
$duedateformat = explode(":#:", JAK_TICKET_DUEDATE_FORMAT);

switch ($page1) {
  case 'new':
    // Reset some stuff
    $userid = $depid = 0;
    // Overwrite session
    $_SESSION["depinfo"] = 0;

    // $ticket_data = $jakdb->query("SELECT awb, depid, priorityid, toptionid FROM hd_support_tickets")->fetchAll();
    // foreach($ticket_data as $data) {
    //   $exist_awb = $data['awb'];
    //   $exist_sumber = $data['depid'];
    //   $exist_jenis = $data['priorityid'];
    //   $exist_rincian = $data['toptionid'];

    //   echo $exist_awb;
    //   echo '<br>';
    //   echo $exist_sumber;
    //   echo '<br>';
    //   echo $exist_jenis;
    //   echo '<br>';
    //   echo $exist_rincian;
    //   echo '<br>';
    // }
    // exit;
    
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;

        if (isset($jkp['start-fresh']) && !empty($jkp['start-fresh'])) {
          unset($_SESSION["userinfo"]);
          unset($_SESSION["depinfo"]);
          $_SESSION["successmsg"] = $jkl['g14'];
          jak_redirect($_SESSION['LCRedirect']);
        }

        // if (isset($jkp['jak_clients']) && !empty($jkp['jak_clients'])) {
        //   $_SESSION["userinfo"] = $jkp['jak_clients'];
        //   $_SESSION["successmsg"] = $jkl['g14'];
        //   jak_redirect($_SESSION['LCRedirect']);
        // } 
      
        if (isset($jkp['newclient_cb']) && $jkp['newclient_cb'] == 1) {
          if (isset($jkp['jak_namec']) && !empty($jkp['jak_namec'])) {
          // if (isset($jkp['jak_namec']) && !empty($jkp['jak_namec']) && isset($jkp['jak_emailc']) && filter_var($jkp['jak_emailc'], FILTER_VALIDATE_EMAIL)) {

            $email = ($jkp['jak_emailc'] == "" ? "other@user.com" : $jkp['jak_emailc']);

            // create new password
            $password = jak_password_creator();
            $passcrypt = hash_hmac('sha256', $password, DB_PASS_HASH);
      
            $jakdb->insert($jaktable5, [ 
              "chat_dep" => JAK_STANDARD_CHAT_DEP,
              "support_dep" => JAK_STANDARD_SUPPORT_DEP,
              "faq_cat" => JAK_STANDARD_FAQ_CAT,
              "name" => $jkp['jak_namec'],
              "phone" => $jkp['jak_phonec'],
              "email" => $email,
              "password" => $passcrypt,
              "canupload" => 1,
              "access" => 1,
              "time" => $jakdb->raw("NOW()")]);
      
            $cid = $jakdb->id();
                      
            // Create a folder
            $newuserpath = APP_PATH.JAK_FILES_DIRECTORY.'/clients/'.$cid;
                      
            if (!is_dir($newuserpath)) {
              mkdir($newuserpath, 0755);
              copy(APP_PATH.JAK_FILES_DIRECTORY."/clients/index.html", $newuserpath."/index.html");
            }
      
            // Write the log file each time someone tries to login before
            JAK_base::jakWhatslog('', JAK_USERID, $cid, 12, $cid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), filter_var($jkp['jak_emailc'], FILTER_SANITIZE_EMAIL), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
                        
            // Now send the email to the customer if we wish so.
            if (isset($jkp['send_email']) && $jkp['send_email'] == 1) {
                    
              // Get the email template
              $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
      
              // Change fake vars into real ones.
              if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $hda) {
                if ($hda["msgtype"] == 14 && $hda["lang"] == JAK_LANG) {
                  $phold = array('{url}', '{title}', '{cname}', '{cemail}', '{cpassword}', '{email}');
                  $replace   = array(str_replace($url_filter, $url_replace, BASE_URL), JAK_TITLE, $jkp['jak_namec'], $jkp['jak_emailc'], $password, JAK_EMAIL);
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
              $mail->addAddress($jkp['jak_emailc']);
              $mail->Subject = JAK_TITLE.' - '.$jkl['hd33'];
              $mail->MsgHTML($body);
      
              $mail->Send();
      
            }
      
            // $_SESSION["userinfo"] = $cid.':#:'.JAK_STANDARD_SUPPORT_DEP.':#:'.$jkp['jak_namec'];
            $jak_clients = $cid.':#:'.JAK_STANDARD_SUPPORT_DEP.':#:'.$jkp['jak_namec'];
            
            // $_SESSION["successmsg"] = $jkl['g14'];
            // jak_redirect($_SESSION['LCRedirect']);
          }
        }

        if (isset($jkp['newclient_cb'])) {
          if(empty($jkp['jak_namec']) || empty($jkp['jak_phonec'])) {
            $errors['e'] = 'Fill the client';
          }
        } else {
          if (empty($jkp['jak_clients'])) {
            $errors['e'] = $jkl['hd199'];
          }
        }
        
        if (empty($jkp['jak_depid'])) {
          $errors['e'] = $jkl['hd200'];
        } else {
          if (empty($jkp['subject'])) {
            $errors['e'] = $jkl['e2'];
          }
          if (empty($jkp['content'])) { 
            $errors['e1'] = $jkl['e1'];
          }
          if ($jkp['jak_priority'] == '-') { 
            $errors['jp'] = 'Choose complain category';
          }
        }

        if(jak_field_not_exist($jkp['awb'], $jaktable, "awb") && jak_field_not_exist($jkp['jak_depid'], $jaktable, "depid") && jak_field_not_exist($jkp['jak_priority'], $jaktable, "priorityid") && jak_field_not_exist($jkp['jak_toption'], $jaktable, "toptionid")){
          $errors['e_awb'] = "Complaint already exist.";
        }

        if (count($errors) == 0) {
          // Get the selected clientid
          if (isset($jkp['newclient_cb']) && $jkp['newclient_cb'] == 1) {
            $saveclientid = explode(":#:", $jak_clients);
          } else {
            $saveclientid = explode(":#:", $jkp['jak_clients']);
          }

          // Get the client data once again
          $client_save = $jakdb->get($jaktable5, ["name", "phone", "email", "credits", "paid_until"], ["id" => $saveclientid[0]]);

          $complainer_name = ($jkp['complainer_name'] != '' ? $jkp['complainer_name'] : $client_save["name"]);
          $complainer_cp = ($jkp['complainer_cp'] != '' ? $jkp['complainer_cp'] : $client_save["phone"]);

          // Filter the subject
          $subjectf = trim($jkp['subject']);

          // Filter the subject
          $awb = trim($jkp['awb']);

          // Filter the notes
          $notesf = trim($jkp['jak_notes']);

          // Filter the content
          $contentf = jak_clean_safe_userpost($_REQUEST['content']);

          $jp_ex = explode('-', $jkp['jak_priority']);
          $jak_priority = $jp_ex[0];
          $jp_exres = $jp_ex[1];

          // We have the due date and we will need to make it right for mysql
          if (isset($jkp["jak_priority"]) && !empty($jkp["jak_priority"])) {
            $duedatesql = date("Y-m-d", strtotime('+'.$jp_exres.'day'));
          } else {
            $duedatesql = date("Y-m-d", strtotime('+'.$jp_exres.'day'));
          }

          // We need the time once
          $ticketcreated = time();

          // Operator by jenis complaint / kategori
          $op_id = '';
          $jenis_complaint = $jakdb->select($jaktable3, ["id", "title", "op_id"]);
          foreach($jenis_complaint as $jc){
            $op_id = $jc['op_id'];
            $jenis_complaint = $jc['id'];

            if($jak_priority == $jenis_complaint) {
              $op_id      = explode(",", $op_id);
              $rand_keys  = array_rand($op_id);
              $op_id      = intval($op_id[$rand_keys]);

              // Create the ticket
              $result = $jakdb->insert($jaktable, ["depid" => $jkp["jak_depid"],
              "subject" => $subjectf,
              "awb" => $awb,
              "content" => $contentf,
              "operatorid" => $op_id,
              "clientid" => $saveclientid[0],
              "name" => $client_save["name"],
              "phone" => $client_save["phone"],
              "email" => $client_save["email"],
              "referrer" => $jkp['jak_referrer'],
              "notes" => $notesf,
              "private" => $jkp['jak_private'],
              "priorityid" => $jak_priority,
              "toptionid" => $jkp['jak_toption'],
              "status" => $jkp['jak_status'],
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

                // Write the log file each time someone tries to login before
                JAK_base::jakWhatslog('', JAK_USERID, 0, 8, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                if ($jkp['jak_status'] == 3) {
                  $jakdb->update($jaktable, ["ended" => $ticketcreated], ["id" => $lastid]);
                }

                // And we complete the custom fields
                $formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 2]);
                if (isset($formfields) && !empty($formfields)) {
                  foreach ($formfields as $v) {
                    if (isset($jkp[$v]) && is_array($jkp[$v])) {
                      $joinval = join(',', $jkp[$v]);
                      $jakdb->update($jaktable, [$v => filter_var($joinval, FILTER_SANITIZE_STRING)], ["id" => $lastid]);
                    } else {
                      $jakdb->update($jaktable, [$v => filter_var($jkp[$v], FILTER_SANITIZE_STRING)], ["id" => $lastid]);
                    }
                  }
                }

                // Set the client ticket request +1
                $jakdb->update($jaktable5, ["supportrequests[+]" => 1], ["id" => $saveclientid[0]]);

                // We run on a credit based system?
                if (JAK_BILLING_MODE == 1) {
                  $priocredit = $optcredit = 0;
                  if (isset($jkp["jak_priority"]) && is_numeric($jkp["jak_priority"])) {
                    $priocredit = $jakdb->get($jaktable8, "credits", ["id" => $jkp["jak_priority"]]);
                  }

                  if (isset($jkp["jak_toption"]) && is_numeric($jkp["jak_toption"])) {
                    $optcredit = $jakdb->get($jaktable8, "credits", ["id" => $jkp["jak_toption"]]);
                  }

                  // Let us collect the department details.
                  $depcredit = 0;
                  if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
                    if ($v["id"] == $_SESSION["depinfo"]) {
                      $depcredit = $v["credits"];
                    }         
                  }

                  $totalc = $depcredit + $priocredit + $optcredit;

                  // We need to get the credits
                  if ($totalc != 0) {
                    // Take some credits away
                    $jakdb->update($jaktable5, ["credits[-]" => $totalc], ["id" => $saveclientid[0]]);
                    // Credit system control
                    $jakdb->insert("taken_credits", ["clientid" => $saveclientid[0], "operatorid" => JAK_USERID, "credits" => $totalc, "created" => $jakdb->raw("NOW()")]);

                    // Inform the operator that all credits have been used.
                    if (($client_save["credits"] - $totalc) < 0) {
                      $_SESSION["errormsg"] = sprintf($jkl['hd227'], $client_save["name"], $client_save["credits"]);
                    } 
                  }
                // We run the membership based system
                } elseif (JAK_BILLING_MODE == 2 && strtotime($client_save["paid_until"]) < $ticketcreated) {
                  $_SESSION["errormsg"] = sprintf($jkl['hd228'], $client_save["name"], $client_save["paid_until"]);
                }

                // all has been stored let's send the email if whish so
                if (isset($jkp["inform-client"]) && !empty($jkp["inform-client"])) {
                  // Finally we inform the customer about the new ticket

                  // Dashboard URL
                  $ticketurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $lastid, JAK_rewrite::jakCleanurl($subjectf)));

                  // Let's check if we have an imap
                  $answeremail = $ticktext = '';
                  $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $_SESSION["depinfo"]]);
                  if ($check_imap) {
                    $answeremail = $check_imap;
                    $subjectl = JAK_TITLE.' - [#'.$page2.'] - '.$jkl['hd177'].' / '.$subjectf;
                  } else {
                    $subjectl = JAK_TITLE.' - '.$jkl['hd177'].' / '.$subjectf;
                  }

                  // Get the ticket answer template
                  if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
                    if ($v["msgtype"] == 20 && $v["lang"] == JAK_LANG) {
                      $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}', '{ticketcontent}');
                      $replace   = array(BASE_URL_ORIG, JAK_TITLE, $client_save['email'], $client_save['name'], $client_save['credits'], $client_save['paid_until'], '#'.$lastid, $subjectf, $ticketurl, $answeremail, replace_urls_emails($contentf, BASE_URL_ORIG, JAK_FILES_DIRECTORY));
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
                    $cssUrl   = array($ticktext, BASE_URL_ORIG, JAK_TITLE);
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
                    $mail->addAddress($client_save["email"]);
                    $mail->Subject = JAK_TITLE.' - RE:'.$subjectf;
                    $mail->MsgHTML($body);

                    if ($mail->Send()) $_SESSION["infomsg"] = $jkl['hd134'];
                  }
                }

                // We have a third party operator, let's inform the operator.
                if ($jkp['jak_operator'] != JAK_USERID) {
                  // The new operator
                  $new_operator_ticket = $jakdb->get($jaktable6, ["username", "email"], ["id" => $jkp['jak_operator']]);

                  // The URL
                  $opurlt = sprintf($jkl['hd249'], JAK_rewrite::jakParseurl('support', 'read', $lastid));

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
                  $maila->Subject = $subjectl;
                  $maila->MsgHTML($opurlt);
                  $maila->Send();
                }

                // Check if we have a change in operator cc field
                updateOperatorCC($jkp['jak_opidcc'], $page2);

                // Now we will need to inform the customers that have assigned CC
                if (isset($jkp['jak_opidcc']) && !empty($jkp['jak_opidcc'])) {

                  // The new operator
                  $operator_cc_email = $jakdb->select($jaktable6, "email", ["id" => $jkp['jak_opidcc']]);

                  // The URL
                  $opurlt = sprintf($jkl['hd289'], JAK_rewrite::jakParseurl('support', 'read', $page2));

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
                  $mailcc->Subject = sprintf($jkl['hd290'], $JAK_FORM_DATA['subject']);
                  $mailcc->MsgHTML($opurlt);
                  $mailcc->Send();
                }

                unset($_SESSION["userinfo"]);
                unset($_SESSION["depinfo"]);

                $_SESSION["successmsg"] = $jkl['g14'];
                jak_redirect(JAK_rewrite::jakParseurl('support', 'read', $lastid));
              }
            } 
          }
        // Output the errors
        } else {
          $errors = $errors;
        }
      }

      // Title and Description
      $SECTION_TITLE = $jkl["hd193"];
      $SECTION_DESC = "";

      // Get all clients
      $CLIENTS_ALL = $jakdb->select($jaktable5, ["id", "support_dep", "name", "email"], ["access" => 1, "ORDER" => ["name" => "ASC"]]);
        // Get the department for the selected client
        // if (isset($_SESSION["userinfo"]) && !empty($_SESSION["userinfo"])) {
          $getdep = explode(":#:", $_SESSION["userinfo"]);
          if ($getdep[1] == 0) {
            $DEPARTMENTS_ALL = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
          } else {
            $DEPARTMENTS_ALL = $jakdb->select($jaktable1, ["id", "title"], ["id" => [$getdep[1]], "ORDER" => ["dorder" => "ASC"]]);
          }
        // }

        if (!isset($_SESSION["userinfo"]) && isset($_SESSION["depinfo"])) {

          // Now we get all the informations we need
          $getdep = explode(":#:", $_SESSION["userinfo"]);
          $clientid = $getdep[0];

          $JAK_CLIENT_DATA = $jakdb->get($jaktable5, ["id", "name", "email"], ["id" => $clientid]);
          $JAK_DEP_DATA = $jakdb->get($jaktable1, ["id", "title", "email"], ["id" => $_SESSION["depinfo"]]);

          // Get all operators
          $OPERATOR_ALL = $jakdb->select($jaktable6, ["id", "name", "email"], ["OR #andclause" => ["AND #the first condition" => ["id" => [JAK_SUPERADMIN]], "AND #the second condition" => ["permissions[~]" => "support", "support_dep" => [0, $JAK_DEP_DATA["id"]], "access" => 1]], "ORDER" => ["name" => "ASC"]]);

          // Get all priorities
          $categories = $jakuser->getVar('op_category');
          $categories = explode(',', $categories);

          $integer = [0];
          foreach($categories as $cat) {
            array_push($integer, intval($cat));
          }

          if($jakuser->getVar("username") == 'admin' || $jakuser->getVar('op_category') == 0 || $jakuser->getVar('op_category') == '') {
            $PRIORITY_ALL = $jakdb->select($jaktable3, "*", ["depid" => [0, $JAK_DEP_DATA["id"]]]);
          } else {
            // jenis complaint by pic operator category
            // $PRIORITY_ALL = $jakdb->select($jaktable3, "*", ["depid" => [0, $JAK_DEP_DATA["id"]], "id" => $integer]);
            $PRIORITY_ALL = $jakdb->select($jaktable3, "*", ["depid" => [0, $JAK_DEP_DATA["id"]]]);
          }

          // Get all options
          $TOPTIONS_ALL = $jakdb->select($jaktable8, "*", ["depid" => [0, $JAK_DEP_DATA["id"]]]);

          // Get all operators in cc
          $OPERATOR_CC = $jakdb->select($jaktable9, "operatorid", ["ticketid" => $page2]);

          // Get the custom fields if any
          $custom_fields = jak_get_custom_fields(1, 2, $JAK_DEP_DATA["id"], $jakuser->getVar("language"), false, false, false, false);

          // Get the standard support responses
          $JAK_RESPONSE_DATA = "";
          if (isset($HD_RESPONSEST) && is_array($HD_RESPONSEST)) {

            $JAK_RESPONSE_DATA .= '<option value="0">'.$jkl["g7"].'</option>';
            
            // get the responses from the file specific for this client
            foreach($HD_RESPONSEST as $r) {

              if ($r["depid"] == 0 || $r["depid"] == $JAK_DEP_DATA["id"]) {
                
                $JAK_RESPONSE_DATA .= '<option value="'.$r["id"].'">'.$r["title"].'</option>';
                
              }
            }
          }
        }

      // Ok we have an id from the chat
      if ($page2 = "chat" && is_numeric($page3)) {

        // Get the data
        $datasett = $jakdb->get("sessions", ["id", "clientid", "name", "operatorname", "email", "phone"], ["id" => $page3]);

        if (!empty($datasett)) { 

          // Get the client
          $tfromchat = $jakdb->get($jaktable5, ["id", "support_dep", "name", "email"], ["AND" => ["id" => $datasett["clientid"], "access" => 1]]);

          // Get the messages
          $chatmsgs = $jakdb->select("transcript", "*", ["convid" => $datasett["id"], "ORDER" => ["id" => "ASC"]]);
                    
          $mailchat = '<p>'.$jkl["u"].': '.$datasett['name'].'<br>'.$jkl["u1"].': '.$datasett['email'].'<br>'.$jkl["u14"].': '.$datasett['phone'].'</p><ul class="list-unstyled">';
                    
          foreach ($chatmsgs as $row) {
            // collect each record into $_data
            if ($row['class'] == "notice") {
              $mailchat .= '<li class="list-group-item-info">'.$row['name'].' '.$jkl['g66'].': '.$row['message'].'</li>';
            } else if ($row['class'] == "admin") {
              $mailchat .= '<li class="list-group-item-success">'.$row['time'].' - '.$row['name']." ".$jkl['g66'].': '.$row['message'].'</li>';
            } else {
              $mailchat .= '<li class="list-group-item-light">'.$row['name'].' '.$jkl['g66'].': '.$row['message'].'</li>';
            }
          }
                        
          $mailchat .= '</ul>';

          // Write it into the vars if not set already
          if (empty($_POST["jak_client"])) $_POST["jak_client"] = $tfromchat["id"].':#:'.$tfromchat["support_dep"].':#:'.$tfromchat["name"];
          if (empty($_REQUEST["jak_namec"])) $_REQUEST["jak_namec"] = $datasett['name'];
          if (empty($_REQUEST["jak_emailc"])) $_REQUEST["jak_emailc"] = $datasett['email'];
          if (empty($_REQUEST["content"])) $_REQUEST["content"] = jak_clean_safe_userpost($mailchat);

        }

      }

      // Include the CSS file for the header
      $css_file_header = BASE_URL.'css/selectlive.css';

      // Include the javascript file for results
      $js_file_footer = 'js_newticket.php';

      // Load the template
      $template = 'newticket.php';
  break;
  case 'sub-category':
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;
        
        if ($jkp['priorityid'] == '-') {
          $sub_category = '-';

          echo $sub_category;
        } else {
          $jp_ex = explode('-', $jkp['priorityid']);
          $jak_priority = $jp_ex[0];
          $jp_exres = $jp_ex[1];
          
          $sub_category = $jakdb->select("ticketoptions", ["id", "title"], ["priorityid" => $jak_priority]);

          echo json_encode($sub_category);
        }        
      }
    break;
    case 'subread-category':
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;
        
        if ($jkp['priorityid'] == '-') {
          $sub_category = '-';
        } else {
          $jp_ex = explode('-', $jkp['priorityid']);
          $jak_priority = $jp_ex[0];
          $jp_exres = $jp_ex[1];
          
          $sub_category = $jakdb->select("ticketoptions", ["id", "title"], ["priorityid" => $jak_priority]);
        }        
      }
    break;
    case 'split':

      // First we check if we can split the ticket.
      if (is_numeric($page2) && is_numeric($page3) && jak_row_exist($page2,$jaktable) && jak_row_exist($page3,$jaktable4)) {

        // We have a post
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $jkp = $_POST;

          if (empty($jkp['subject'])) {
            $errors['e'] = $jkl['e2'];
          }

          if (empty($jkp['content'])) { 
            $errors['e1'] = $jkl['e1'];
          }

          if (count($errors) == 0) {

            // Get the client data once again
            $client_save = $jakdb->get($jaktable5, ["ID", "email", "credits", "paid_until"], ["id" => $jkp["clientid"]]);

            // Filter the subject
            $subjectf = trim($jkp['subject']);

            // Filter the subject
            $awb = trim($jkp['awb']);

            // Filter the notes
            $notesf = trim($jkp['jak_notes']);

            // Filter the content
            $contentf = jak_clean_safe_userpost($_REQUEST['content']);

            // We have the due date and we will need to make it right for mysql
            if (isset($jkp["jak_duedate"]) && !empty($jkp["jak_duedate"])) {
              // $duedate = DateTime::createFromFormat($duedateformat[0], $jkp["jak_duedate"]);
              // $duedatesql = $duedate->format("Y-m-d");
              $duedate = new DateTime($jkp["jak_duedate"]);
              $duedatesql = $duedate->format("Y-m-d");
            } else {
              $duedatesql = date("Y-m-d", strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day'));
            }

            // We need the time once
            $ticketcreated = time();

            // Create the ticket
            $result = $jakdb->insert($jaktable, ["depid" => $jkp["jak_depid"],
              "subject" => $subjectf,
              "awb" => $awb,
              "content" => $contentf,
              "operatorid" => $jkp['jak_operator'],
              "clientid" => $jkp["clientid"],
              "name" => $client_save["name"],
              "email" => $client_save["email"],
              "referrer" => $jkp['jak_referrer'],
              "notes" => $notesf,
              "private" => $jkp['jak_private'],
              "priorityid" => $jkp['jak_priority'],
              "toptionid" => $jkp['jak_toption'],
              "status" => $jkp['jak_status'],
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

            // Write the log file each time someone tries to login before
            JAK_base::jakWhatslog('', JAK_USERID, 0, 22, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

            // Now we will need to delete the answer, because we create a new ticket
            $jakdb->delete($jaktable4, ["id" => $page3]);

            // We move attachments
            if (isset($jkp["move-files"]) && is_array($jkp["move-files"])) {

              foreach ($jkp["move-files"] as $f) {

                // first get the target path
                $targetPathd = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$lastid.'/';
                $targetPath =  str_replace("//", "/", $targetPathd);

                if (!is_dir($targetPath)) {
                  mkdir($targetPath, 0755);
                  copy(APP_PATH.JAK_FILES_DIRECTORY."/index.html", $targetPath."/index.html");
                }
              
                $oldlocation = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$f;
                $newlocation = $targetPath.'/'.$f;
                    
                // Move file     
                if (rename($oldlocation, $newlocation)) {

                  // Update counter on the new ticket
                  $jakdb->update($jaktable, ["attachments[+]" => 1], ["id" => $lastid]);

                  // Update counter on the old ticket
                  $jakdb->update($jaktable, ["attachments[-]" => 1], ["id" => $page2]);

                }

              }

            }

            // Should the ticket be closed
            if ($jkp['jak_status'] == 3) {
              $jakdb->update($jaktable, ["ended" => $ticketcreated], ["id" => $lastid]);
            }

                // And we complete the custom fields
            $formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 2]);
            if (isset($formfields) && !empty($formfields)) {
              foreach ($formfields as $v) {
                if (isset($jkp[$v]) && is_array($jkp[$v])) {
                  $joinval = join(',', $jkp[$v]);
                  $jakdb->update($jaktable, [$v => filter_var($joinval, FILTER_SANITIZE_STRING)], ["id" => $lastid]);
                } else {
                  $jakdb->update($jaktable, [$v => filter_var($jkp[$v], FILTER_SANITIZE_STRING)], ["id" => $lastid]);
                }
              }
            }

            // Set the client ticket request +1
            $jakdb->update($jaktable5, ["supportrequests[+]" => 1], ["id" => $saveclientid[0]]);

            // We run on a credit based system?
            if (JAK_BILLING_MODE == 1) {
              $priocredit = $optcredit = 0;
              if (isset($jkp["jak_priority"]) && is_numeric($jkp["jak_priority"])) {
                $priocredit = $jakdb->get($jaktable8, "credits", ["id" => $jkp["jak_priority"]]);
              }

              if (isset($jkp["jak_toption"]) && is_numeric($jkp["jak_toption"])) {
                $optcredit = $jakdb->get($jaktable8, "credits", ["id" => $jkp["jak_toption"]]);
              }

              // Let us collect the department details.
              $depcredit = 0;
              if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
                if ($v["id"] == $ticketinfo["depid"]) {
                  $depcredit = $v["credits"];
                }         
              }

              $totalc = $depcredit + $priocredit + $optcredit;

                  // We need to get the credits
              if ($totalc != 0) {
                    // Take some credits away
                $jakdb->update($jaktable5, ["credits[-]" => $totalc], ["id" => $saveclientid[0]]);
                    // Credit system control
                $jakdb->insert("taken_credits", ["clientid" => $saveclientid[0], "operatorid" => JAK_USERID, "credits" => $totalc, "created" => $jakdb->raw("NOW()")]);

                    // Inform the operator that all credits have been used.
                if (($client_save["credits"] - $totalc) < 0) {
                  $_SESSION["errormsg"] = sprintf($jkl['hd227'], $client_save["name"], $client_save["credits"]);
                } 
              }
                // We run the membership based system
            } elseif (JAK_BILLING_MODE == 2 && strtotime($client_save["paid_until"]) < $ticketcreated) {
              $_SESSION["errormsg"] = sprintf($jkl['hd228'], $client_save["name"], $client_save["paid_until"]);
            }

                // all has been stored let's send the email if whish so
            if (isset($jkp["inform-client"]) && !empty($jkp["inform-client"])) {
                  // Finally we inform the customer about the new ticket

                  // Dashboard URL
              $ticketurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $lastid, JAK_rewrite::jakCleanurl($subjectf)));

                  // Let's check if we have an imap
              $answeremail = $ticktext = '';
              $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $ticketinfo["depid"]]);
              if ($check_imap) {
                $answeremail = $check_imap;
                $subjectl = JAK_TITLE.' - [#'.$page2.'] - '.$jkl['hd177'].' / '.$subjectf;
              } else {
                $subjectl = JAK_TITLE.' - '.$jkl['hd177'].' / '.$subjectf;
              }

                  // Get the ticket answer template
              if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {

                if ($v["msgtype"] == 20 && $v["lang"] == JAK_LANG) {

                  $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}', '{ticketcontent}');
                  $replace   = array(BASE_URL_ORIG, JAK_TITLE, $client_save['email'], $client_save['name'], $client_save['credits'], $client_save['paid_until'], '#'.$lastid, $subjectf, $ticketurl, $answeremail, replace_urls_emails($contentf, BASE_URL_ORIG, JAK_FILES_DIRECTORY));
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
                $cssUrl   = array($ticktext, BASE_URL_ORIG, JAK_TITLE);
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
                    $mail->addAddress($client_save["email"]);
                    $mail->Subject = JAK_TITLE.' - RE:'.$subjectf;
                    $mail->MsgHTML($body);

                    if ($mail->Send()) $_SESSION["infomsg"] = $jkl['hd134'];

                  }
                }

                // We have a third party operator, let's inform the operator.
                if ($jkp['jak_operator'] != JAK_USERID) {

                  // The new operator
                  $new_operator_ticket = $jakdb->get($jaktable6, ["username", "email"], ["id" => $jkp['jak_operator']]);

                  // The URL
                  $opurlt = sprintf($jkl['hd249'], JAK_rewrite::jakParseurl('support', 'read', $lastid));

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
                  $maila->Subject = $subjectl;
                  $maila->MsgHTML($opurlt);
                  $maila->Send();
                }

                // Check if we have a change in operator cc field
                updateOperatorCC($jkp['jak_opidcc'], $page2);

                // Now we will need to inform the customers that have assigned CC
                if (isset($jkp['jak_opidcc']) && !empty($jkp['jak_opidcc'])) {

                  // The new operator
                  $operator_cc_email = $jakdb->select($jaktable6, "email", ["id" => $jkp['jak_opidcc']]);

                  // The URL
                  $opurlt = sprintf($jkl['hd289'], JAK_rewrite::jakParseurl('support', 'read', $page2));

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
                  $mailcc->Subject = sprintf($jkl['hd290'], $JAK_FORM_DATA['subject']);
                  $mailcc->MsgHTML($opurlt);
                  $mailcc->Send();
                }

                $_SESSION["successmsg"] = $jkl['g14'];
                jak_redirect(JAK_rewrite::jakParseurl('support', 'read', $lastid));
              }

            // Output the errors
            } else {
              $errors = $errors;
            }

          }

          // Title and Description
          $SECTION_TITLE = $jkl["hd286"];
          $SECTION_DESC = "";

          // Now let's get the current information out
          $ticketinfo = $jakdb->get($jaktable, ["depid", "clientid", "referrer", "attachments"], ["id" => $page2]);

          // Also get the answer
          $ticketanswer = $jakdb->get($jaktable4, ["ticketid", "content"], ["id" => $page3]);

          $_REQUEST['content'] = $ticketanswer["content"];
          $_REQUEST['jak_referrer'] = $ticketinfo["referrer"];

          if (isset($ticketinfo["clientid"]) && isset($ticketinfo["depid"])) {

            $JAK_CLIENT_DATA = $jakdb->get($jaktable5, ["id", "name", "email", "support_dep"], ["id" => $ticketinfo["clientid"]]);
            $JAK_DEP_DATA = $jakdb->get($jaktable1, ["id", "title", "email"], ["id" => $ticketinfo["depid"]]);

            // Get all operators
            $OPERATOR_ALL = $jakdb->select($jaktable6, ["id", "name", "email"], ["OR #andclause" => ["AND #the first condition" => ["id" => [JAK_SUPERADMIN]], "AND #the second condition" => ["permissions[~]" => "support", "support_dep" => [0, $JAK_DEP_DATA["id"]], "access" => 1]], "ORDER" => ["name" => "ASC"]]);

            // Get all the departments
            if ($JAK_CLIENT_DATA["support_dep"] == 0) {
              $DEPARTMENTS_ALL = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
            } else {
              $DEPARTMENTS_ALL = $jakdb->select($jaktable1, ["id", "title"], ["id" => [$JAK_CLIENT_DATA["support_dep"]], "ORDER" => ["dorder" => "ASC"]]);
            }

            // Get all priorities
            $PRIORITY_ALL = $jakdb->select($jaktable3, "*", ["depid" => [0, $JAK_DEP_DATA["id"]]]);

            // Get all options
            $TOPTIONS_ALL = $jakdb->select($jaktable8, "*", ["depid" => [0, $JAK_DEP_DATA["id"]]]);

            // Get all operators in cc
            $OPERATOR_CC = $jakdb->select($jaktable9, "operatorid", ["ticketid" => $page2]);

            // Get the custom fields if any
            $custom_fields = jak_get_custom_fields($jkp, 2, $JAK_DEP_DATA["id"], $jakuser->getVar("language"), false, false, false, false);

            // Get the attachments if any
            if ($JAK_FORM_DATA["attachments"] != 0) {

              // Now we get the path
              $targetPathTA = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2;

              if (is_dir($targetPathTA)) {
                $JAK_TICKET_FILES = jak_get_files($targetPathTA);
              } else {
                // We could not find any attachmeents
                $jakdb->update($jaktable, ["attachments" => 0], ["id" => $page2]);
              }
            }

            // Get the standard support responses
            $JAK_RESPONSE_DATA = "";
            if (isset($HD_RESPONSEST) && is_array($HD_RESPONSEST)) {

              $JAK_RESPONSE_DATA .= '<option value="0">'.$jkl["g7"].'</option>';
              
              // get the responses from the file specific for this client
              foreach($HD_RESPONSEST as $r) {

                if ($r["depid"] == 0 || $r["depid"] == $JAK_DEP_DATA["id"]) {

                  $phold = array("%operator%","%client%","%email%");
                  $replace   = array($jakuser->getVar("name"), $JAK_CLIENT_DATA["name"], JAK_EMAIL);
                  $message = str_replace($phold, $replace, $r["message"]);
                  
                  $JAK_RESPONSE_DATA .= '<option value="'.base64_encode($message).'">'.$r["title"].'</option>';
                  
                }
              }
            }
          }

        // Include the CSS file for the header
        $css_file_header = BASE_URL.'css/selectlive.css';

        // Include the javascript file for results
        $js_file_footer = 'js_newticket.php';

        // Load the template
        $template = 'splitticket.php';

        } else {

          $_SESSION["errormsg"] = $jkl['i3'];
          jak_redirect($_SESSION['LCRedirect']);

        }

    break;
    case 'read':

      // Check if the user exists
      if (is_numeric($page2) && jak_row_exist($page2,$jaktable)) {

        // Get the ticket data first ~ this is where the data fetched
        $JAK_FORM_DATA = $jakdb->get($jaktable, ["[>]".$jaktable1 => ["depid" => "id"], "[>]".$jaktable5 => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.operatorid", "support_tickets.subject", "support_tickets.content", "support_tickets.clientid", "support_tickets.ip", "support_tickets.referrer", "support_tickets.notes", "support_tickets.private", "support_tickets.status", "support_tickets.attachments", "support_tickets.initiated", "support_tickets.awb", "support_tickets.ended", "support_tickets.updated", "support_tickets.priorityid", "support_tickets.duedate", "support_tickets.toptionid", "support_departments.title", "clients.name", "clients.phone", "clients.email", "clients.picture", "clients.support_dep", "clients.credits", "clients.paid_until"], ["support_tickets.id" => $page2]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $jkp = $_POST;

          // Only store operator, department and option updates
          if (isset($jkp['store-dep']) && $jkp['store-dep'] == 1) {
            $jp_ex = explode('-', $jkp['jak_priority']);
            $jak_priority = $jp_ex[0];
            $jp_exres = $jp_ex[1];

            // Operator by jenis complaint / kategori
            $op_id = '';
            $jenis_complaint = $jakdb->select($jaktable3, ["id", "title", "op_id"]);
            foreach($jenis_complaint as $jc){
              $op_id = $jc['op_id'];
              $jenis_complaint = $jc['id'];

              if($jak_priority == $jenis_complaint) {
                $op_id      = explode(",", $op_id);
                $rand_keys  = array_rand($op_id);
                $op_id      = intval($op_id[$rand_keys]);

                  // We save the new data
                  $jakdb->update($jaktable, ["depid" => $jkp['jak_depid'], "operatorid" => $op_id, "priorityid" => $jak_priority, "toptionid" => $jkp['jak_toption'], "updated" => time()], ["id" => $page2]);
              }
            }
          

            // tambahan untuk due date sesuai priority/complaint category
            $duedate = new DateTime($jkp["created_date"]);
            $duedatesql = $duedate->format("Y-m-d");
            $duedatesql = date("Y-m-d", strtotime($duedatesql.'+'.$jp_exres.'day'));
            // update the ticket
            $jakdb->update($jaktable, ["duedate" => $duedatesql], ["id" => $page2]);
            // end update the ticket
            // end tambahan untuk due date sesuai priority/complaint category
            
            if (JAK_BILLING_MODE == 1 && $JAK_FORM_DATA["clientid"] != 0) {

              // Check if we have a change in the departmant
              if ($jkp['jak_depid'] != $jkp['olddep']) {
                if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
                  if ($v["id"] == $jkp['olddep']) {
                    $oldcredits = $v["credits"];
                  }         
                }
                if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
                  if ($v["id"] == $jkp['jak_depid']) {
                    $newcredits = $v["credits"];
                  }         
                }

                if ($newcredits > $oldcredits) {
                  $newc = $newcredits - $oldcredits;
                  $jakdb->update($jaktable5, ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);

                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);

                } elseif ($newcredits < $oldcredits) {
                  $newc = $oldcredits - $newcredits;
                  $jakdb->update($jaktable5, ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                }
              }

              // Check if we have a change in the priority
              if ($jak_priority != $jkp['oldpriority']) {
                $oldcredits = $jakdb->get($jaktable3, "credits", ["id" => $jkp['oldpriority']]);
                $newcredits = $jakdb->get($jaktable3, "credits", ["id" => $jak_priority]);

                if ($newcredits > $oldcredits) {
                  $newc = $newcredits - $oldcredits;
                  $jakdb->update($jaktable5, ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
                } elseif ($newcredits < $oldcredits) {
                  $newc = $oldcredits - $newcredits;
                  $jakdb->update($jaktable5, ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                }
              }

              // Check if we have a change in the option
              if ($jkp['jak_toption'] != $jkp['oldtoption']) {
                $oldcredits = $jakdb->get($jaktable8, "credits", ["id" => $jkp['oldtoption']]);
                $newcredits = $jakdb->get($jaktable8, "credits", ["id" => $jkp['jak_toption']]);

                if ($newcredits > $oldcredits) {
                  $newc = $newcredits - $oldcredits;
                  $jakdb->update($jaktable5, ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
                } elseif ($newcredits < $oldcredits) {
                  $newc = $oldcredits - $newcredits;
                  $jakdb->update($jaktable5, ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                  $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                }
              }
            } // End Billing

            // We have a change in the operator, let's inform the new operator.
            if ($jkp['jak_opid'] != $jkp['oldopid'] && $jkp['jak_opid'] != JAK_USERID) {

              // The new operator
              $new_operator_ticket = $jakdb->get($jaktable6, ["username", "email"], ["id" => $jkp['jak_opid']]);

              // The URL
              $opurlt = sprintf($jkl['hd249'], JAK_rewrite::jakParseurl('support', 'read', $page2));

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
            }

            // Check if we have a change in operator cc field
            updateOperatorCC($jkp['jak_opidcc'], $page2);

            // We have the due date and we will need to make it right for mysql
            if (isset($jkp["jak_duedate"]) && !empty($jkp["jak_duedate"])) {
              $duedate = new DateTime($jkp["jak_duedate"]);
              $duedatesql = $duedate->format("Y-m-d");
            } else {
              $duedatesql = date("Y-m-d", strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day'));
            }

            // We have the due date and we will need to make it right for mysql
            if (isset($jkp["created_date"]) && !empty($jkp["created_date"])) {
              $cd_date = new DateTime($jkp["created_date"]);
              $cd_date = $cd_date->getTimestamp();

              // tambahan untuk due date sesuai priority/complaint category
              $created_date = new DateTime($jkp["created_date"]);
              $duedatesql = $created_date->format("Y-m-d");
              $duedatesql = date("Y-m-d", strtotime($duedatesql.'+'.$jp_exres.'day'));
              // update the ticket
              $jakdb->update($jaktable, ["duedate" => $duedatesql], ["id" => $page2]);
              // end update the ticket
              // end tambahan untuk due date sesuai priority/complaint category
            }
           
            // Update the private status and due date
            $jakdb->update($jaktable, ["private" => $jkp['jak_private'], "duedate" => $duedatesql, "initiated" => $cd_date], ["id" => $page2]);

            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect($_SESSION['LCRedirect']);
          }

          // Only store notes and go ahead
          if (isset($jkp['store-notes']) && $jkp['store-notes'] == 1) {
            $savenotes = filter_var($jkp['notes'], FILTER_SANITIZE_STRING);
            $jakdb->update($jaktable, ["notes" => $savenotes], ["id" => $page2]);
            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect($_SESSION['LCRedirect']);
          }

        // Only store custom fields and go ahead
        if (isset($jkp['store-fields']) && $jkp['store-fields'] == 1) {
            // And we complete the custom fields
            $formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 2]);
            if (isset($formfields) && !empty($formfields)) {
              foreach ($formfields as $v) {
                // convert nama dp to kode dp
                if ($v == 'dp_bersalah'){
                  $m_dp = $jakdb->query("SELECT * FROM m_dp")->fetchAll();
                  foreach($m_dp as $dp){
                    if($jkp[$v] == $dp['droppoint']) {
                      $jkp[$v] = $dp['id_dp'];
                    }
                  }
                }
                // end convert
                if (isset($jkp[$v]) && is_array($jkp[$v]) && !empty($jkp[$v])) {
                  $joinval = join(',', $jkp[$v]);
                  $jakdb->update($jaktable, [$v => filter_var($joinval, FILTER_SANITIZE_STRING)], ["id" => $page2]);
                } elseif (isset($jkp[$v]) && !is_array($jkp[$v])) {
                  $jakdb->update($jaktable, [$v => filter_var($jkp[$v], FILTER_SANITIZE_STRING)], ["id" => $page2]);
                } else {
                  $jakdb->update($jaktable, [$v => ""], ["id" => $page2]);
                }
              }
            }

            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect($_SESSION['LCRedirect']);
        }

          // We edit some content
          if (isset($jkp['edit-content']) && $jkp['edit-content'] == $page2 && $jakdb->has($jaktable, ["id" => $page2])) {
            $subjectf = $jkp['subject'];
            $contentf = jak_clean_safe_userpost($_REQUEST['content']);
            $jakdb->update($jaktable, ["subject" => $subjectf, "content" => $contentf], ["id" => $page2]);
            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect(JAK_rewrite::jakParseurl('support', 'read', $page2));          
          }

          // We edit some answers
          if (isset($jkp['edit-answer']) && $jkp['edit-answer'] == $page3 && $jakdb->has($jaktable4, ["AND" => ["id" => $page3, "ticketid" => $page2]])) {
            $contentf = jak_clean_safe_userpost($_REQUEST['content']);
            $jakdb->update($jaktable4, ["content" => $contentf], ["id" => $page3]);
            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect(JAK_rewrite::jakParseurl('support', 'read', $page2));          
          }

          if (empty($jkp['content'])) { 
            $errors['e1'] = $jkl['e1'];
          }

          if (count($errors) == 0) {

            // Private note
            $privatenote = 0;
            $tstatus = 2;
            if ($jkp['private-note'] == 1 && $JAK_FORM_DATA["status"] != 2) $privatenote = $tstatus = 1;

            // Filter the content
            $contentf = jak_clean_safe_userpost($_REQUEST['content']);

            $result = $jakdb->insert($jaktable4, ["ticketid" => $page2,
              "operatorid" => JAK_USERID,
              "content" => $contentf,
              "private" => $privatenote,
              "lastedit" => $jakdb->raw("NOW()"),
              "sent" => $jakdb->raw("NOW()")]);

            if (!$result) {
              $_SESSION["infomsg"] = $jkl['i'];
              jak_redirect($_SESSION['LCRedirect']);
            } else {

              // Get the ID from the ticket
              $lastid = $jakdb->id();

              // Write the log file
              JAK_base::jakWhatslog('', JAK_USERID, 0, 32, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

              // clean notes
              $savenotes = filter_var($jkp['notes'], FILTER_SANITIZE_STRING);

              // We have the due date and we will need to make it right for mysql
              if (isset($jkp["jak_duedate"]) && !empty($jkp["jak_duedate"])) {
                $duedate = new DateTime($jkp["jak_duedate"]);
                $duedatesql = $duedate->format("Y-m-d");
              } else {
                $duedatesql = date("Y-m-d", strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day'));
              }

              // Update the ticket
              $jakdb->update($jaktable, ["depid" => $jkp['jak_depid'], "operatorid" => $jkp['jak_opid'], "priorityid" => $jak_priority, "toptionid" => $jkp['jak_toption'], "status" => $tstatus, "updated" => time(), "notes" => $savenotes, "private" => $jkp['jak_private'], "duedate" => $duedatesql], ["id" => $page2]);

              // Calculate the update time
              $responsetime = time() - $JAK_FORM_DATA["updated"];

              // Let's check if that is is the first answer and it is not a private note
              if ($privatenote == 0) {
                $firstcontact = 0;
                if ($JAK_FORM_DATA["initiated"] == $JAK_FORM_DATA["updated"]) $firstcontact = 1;

                // Insert response time
                insertResponsetime(JAK_USERID, $page2, $responsetime, $firstcontact);
              }

              if (JAK_BILLING_MODE == 1 && $JAK_FORM_DATA["clientid"] != 0) {

                // Check if we have a change in the departmant
                if ($jkp['jak_depid'] != $jkp['olddep']) {
                  if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
                    if ($v["id"] == $jkp['olddep']) {
                      $oldcredits = $v["credits"];
                    }         
                  }
                  if (isset($HD_SUPPORT_DEPARTMENTS) && is_array($HD_SUPPORT_DEPARTMENTS)) foreach ($HD_SUPPORT_DEPARTMENTS as $v) {
                    if ($v["id"] == $jkp['jak_depid']) {
                      $newcredits = $v["credits"];
                    }         
                  }

                  if ($newcredits > $oldcredits) {
                    $newc = $newcredits - $oldcredits;
                    $jakdb->update($jaktable5, ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                    // Credit system control
                    $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
                  } elseif ($newcredits < $oldcredits) {
                    $newc = $oldcredits - $newcredits;
                    $jakdb->update($jaktable5, ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                    // Credit system control
                    $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                  }
                }

                // Check if we have a change in the priority
                if ($jak_priority != $jkp['oldpriority']) {
                  $oldcredits = $jakdb->get($jaktable3, "credits", ["id" => $jkp['oldpriority']]);
                  $newcredits = $jakdb->get($jaktable3, "credits", ["id" => $jak_priority]);

                  if ($newcredits > $oldcredits) {
                    $newc = $newcredits - $oldcredits;
                    $jakdb->update($jaktable5, ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                    $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
                  } elseif ($newcredits < $oldcredits) {
                    $newc = $oldcredits - $newcredits;
                    $jakdb->update($jaktable5, ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                    $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                  }
                }

                // Check if we have a change in the option
                if ($jkp['jak_toption'] != $jkp['oldtoption']) {
                  $oldcredits = $jakdb->get($jaktable8, "credits", ["id" => $jkp['oldtoption']]);
                  $newcredits = $jakdb->get($jaktable8, "credits", ["id" => $jkp['jak_toption']]);

                  if ($newcredits > $oldcredits) {
                    $newc = $newcredits - $oldcredits;
                    $jakdb->update($jaktable5, ["credits[-]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                    $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "created" => $jakdb->raw("NOW()")]);
                  } elseif ($newcredits < $oldcredits) {
                    $newc = $oldcredits - $newcredits;
                    $jakdb->update($jaktable5, ["credits[+]" => $newc], ["id" => $JAK_FORM_DATA["clientid"]]);
                  // Credit system control
                    $jakdb->insert("taken_credits", ["clientid" => $JAK_FORM_DATA["clientid"], "operatorid" => JAK_USERID, "credits" => $newc, "taken" => 0, "created" => $jakdb->raw("NOW()")]);
                  }
                }

              }

              // And we complete the custom fields
              $formfields = $jakdb->select('customfields', "val_slug", ["fieldlocation" => 2]);
              if (isset($formfields) && !empty($formfields)) {
                foreach ($formfields as $v) {
                  if (isset($jkp[$v]) && is_array($jkp[$v])) {
                    $joinval = join(',', $jkp[$v]);
                    $jakdb->update($jaktable, [$v => filter_var($joinval, FILTER_SANITIZE_STRING)], ["id" => $page2]);
                  } else {
                    $jakdb->update($jaktable, [$v => filter_var($jkp[$v], FILTER_SANITIZE_STRING)], ["id" => $page2]);
                  }
                }
              }

              // We have a change in the operator, let's inform the new operator.
              if ($jkp['jak_opid'] != $jkp['oldopid'] && $jkp['jak_opid'] != JAK_USERID) {

                // The new operator
                $new_operator_ticket = $jakdb->get($jaktable6, ["username", "email"], ["id" => $jkp['jak_opid']]);

                // The URL
                $opurlt = sprintf($jkl['hd249'], JAK_rewrite::jakParseurl('support', 'read', $page2));

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
              }

              // Check if we have a change in operator cc field
              updateOperatorCC($jkp['jak_opidcc'], $page2);

              // Now we will need to inform the customers that have assigned CC
              if (isset($jkp['jak_opidcc']) && !empty($jkp['jak_opidcc'])) {

                // The new operator
                $operator_cc_email = $jakdb->select($jaktable6, "email", ["id" => $jkp['jak_opidcc']]);

                // The URL
                $opurlt = sprintf($jkl['hd289'], JAK_rewrite::jakParseurl('support', 'read', $page2));

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
                $mailcc->Subject = sprintf($jkl['hd290'], $JAK_FORM_DATA['subject']);
                $mailcc->MsgHTML($opurlt);
                $mailcc->Send();
              }

            // Finally we inform the customer about the answer
            if ($jkp['private-note'] != 1) {

              // Dashboard URL
              $ticketurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2, JAK_rewrite::jakCleanurl($JAK_FORM_DATA["subject"])));

              // Let's check if we have an imap
              $answeremail = $ticktext = '';
              $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $JAK_FORM_DATA["depid"]]);
              if ($check_imap) {
                $answeremail = $check_imap;
                $subjectl = JAK_TITLE.' - [#'.$page2.'] - RE:'.$JAK_FORM_DATA['subject'];
              } else {
                $subjectl = JAK_TITLE.' - RE:'.$JAK_FORM_DATA['subject'];
              }

              // Get the ticket answer template
              if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {

                if ($v["msgtype"] == 21 && $v["lang"] == JAK_LANG) {

                  $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}', '{ticketcontent}');
                  $replace   = array(BASE_URL_ORIG, JAK_TITLE, $JAK_FORM_DATA['email'], $JAK_FORM_DATA['name'], $JAK_FORM_DATA['credits'], $JAK_FORM_DATA['paid_until'], '#'.$page2, $JAK_FORM_DATA['subject'], $ticketurl, $answeremail, replace_urls_emails($contentf, BASE_URL_ORIG, JAK_FILES_DIRECTORY));
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
                $cssUrl   = array($ticktext, BASE_URL_ORIG, JAK_TITLE);
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

                if ($mail->Send()) $_SESSION["infomsg"] = $jkl['hd134'];

              }

          }

          // We are closing the ticket in the same time
          if (isset($jkp['save_close'])) {

            // Now let's update the status and the ended time
            $result = $jakdb->update($jaktable, ["status" => 4, "ended" => time(), "reminder" => 2], ["id" => $page2]);

            // If Rating is on
            if (JAK_TICKET_RATING) {

            // Dashboard URL
            $ticketratingurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_CLIENT_URL, 'rt', $JAK_FORM_DATA["id"], $JAK_FORM_DATA["initiated"]));

              // Let's check if we have an imap
              $answeremail = $ticktextr = '';
              $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $JAK_FORM_DATA["depid"]]);
              if ($check_imap) $answeremail = $check_imap;

              // Get the ticket answer template
              if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {

                if ($v["msgtype"] == 25 && $v["lang"] == JAK_LANG) {

                  $pholdr = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
                  $replacer   = array(str_replace(JAK_OPERATOR_LOC.'/', '', BASE_URL), JAK_TITLE, $JAK_FORM_DATA['email'], $JAK_FORM_DATA['name'], $JAK_FORM_DATA['credits'], $JAK_FORM_DATA['paid_until'], '#'.$JAK_FORM_DATA["id"], $JAK_FORM_DATA['subject'], $ticketratingurl, $answeremail);
                  $ticktextr = str_replace($pholdr, $replacer, $v["message"]);
                  break;

                }

              }

              if (!empty($ticktextr)) {

                // Get the email template
                $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

                  // Change fake vars into real ones.
                $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
                $cssUrl   = array($ticktextr, str_replace(JAK_OPERATOR_LOC.'/', '', BASE_URL), JAK_TITLE);
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
                $mail->Subject = JAK_TITLE.' - '.$jkl['g85'].': '.$JAK_FORM_DATA['subject'];
                $mail->MsgHTML($body);

                $mail->Send();

              }

            }

            // Write the log file each time someone tries to login before
            JAK_base::jakWhatslog('', JAK_USERID, 0, 19, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect(JAK_rewrite::jakParseurl('support'));  

          } else {

            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect($_SESSION['LCRedirect']);

          }
          
        }

        // Output the errors
        } else {

          $errors = $errors;
        }

      }
    
      // Title and Description
      $SECTION_TITLE = $jkl["hd176"].' - '.$JAK_FORM_DATA["subject"];
      $SECTION_DESC = "";

      // Get all operators
      $OPERATOR_ALL = $jakdb->select($jaktable6, ["id", "name", "email"], ["OR #andclause" => ["AND #the first condition" => ["id" => [JAK_SUPERADMIN]], "AND #the second condition" => ["permissions[~]" => "support", "support_dep" => [0, $JAK_FORM_DATA["depid"]], "access" => 1]], "ORDER" => ["name" => "ASC"]]);

      // Get all departments
      if ($JAK_FORM_DATA["support_dep"] == 0) {
        $DEPARTMENTS_ALL = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
      } else {
        $DEPARTMENTS_ALL = $jakdb->select($jaktable1, ["id", "title"], ["id" => [$JAK_FORM_DATA["support_dep"]], "ORDER" => ["dorder" => "ASC"]]);
      }

      // Get all priorities
      $PRIORITY_ALL = $jakdb->select($jaktable3, "*", ["depid" => [0, $JAK_FORM_DATA["depid"]]]);

      // Get all options
      $TOPTIONS_ALL = $jakdb->select($jaktable8, "*", ["depid" => [0, $JAK_FORM_DATA["depid"]], "priorityid" => [0, $JAK_FORM_DATA["priorityid"]]]);

      // Get all operators in cc
      $OPERATOR_CC = $jakdb->select($jaktable9, "operatorid", ["ticketid" => $page2]);

      // We will need the response time
      $total_responses = $jakdb->count($jaktable10, ["ticketid" => $page2]);
      $total_responses_time = $jakdb->sum($jaktable10, "responsetime", ["ticketid" => $page2]);

      // Get the ticket Answers
      $JAK_ANSWER_DATA = $jakdb->select($jaktable4, ["[>]".$jaktable6 => ["operatorid" => "id"], "[>]".$jaktable5 => ["clientid" => "id"]], ["ticket_answers.id", "ticket_answers.content", "ticket_answers.lastedit", "ticket_answers.clientid", "ticket_answers.private", "ticket_answers.sent", "user.id(oid)", "user.name(oname)", "clients.id(cid)", "clients.name(cname)"], ["ticket_answers.ticketid" => $page2, "ORDER" => ["ticket_answers.sent" => "DESC"]]);

      // Get the standard support responses
      $JAK_RESPONSE_DATA = "";
      if (isset($HD_RESPONSEST) && is_array($HD_RESPONSEST)) {

        $JAK_RESPONSE_DATA .= '<option value="0">'.$jkl["g7"].'</option>';

          // get the responses from the file specific for this client
        foreach($HD_RESPONSEST as $r) {

          if ($r["depid"] == 0 || $r["depid"] == $JAK_FORM_DATA["depid"]) {

            $JAK_RESPONSE_DATA .= '<option value="'.$r["id"].'">'.$r["title"].'</option>';

          }
        }
      }

      // Get the custom fields if any
      $JAK_CUSTOM_FIELD_DATA = $jakdb->get($jaktable, "*", ["id" => $page2]);

      $custom_fields = jak_get_custom_fields($JAK_CUSTOM_FIELD_DATA, 2, $JAK_FORM_DATA["depid"], $jakuser->getVar("language"), false, false, false, false);

      // Get the attachments if any
      if ($JAK_FORM_DATA["attachments"] != 0) {

        // Now we get the path
        $targetPathTA = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2;

        if (is_dir($targetPathTA)) {
          $JAK_TICKET_FILES = jak_get_files($targetPathTA);
        } else {
          // We could not find any attachmeents
          $jakdb->update($jaktable, ["attachments" => 0], ["id" => $page2]);
        }
      }

      // We edit some content
      $editcont = 0;
      if (isset($page3) && $page3 == 'edit') {
        $JAK_EDIT_CONTENT = $jakdb->get($jaktable, ["subject", "content"], ["id" => $page2]);
        $editcont = $page2;
      }
      
      // We edit some answer
      $editid = 0;
      if (isset($page3) && is_numeric($page3) && $jakdb->has($jaktable4, ["AND" => ["id" => $page3, "ticketid" => $page2]])) {
        $JAK_EDIT_ANSWER = $jakdb->get($jaktable4, ["content", "private"], ["id" => $page3]);
        $editid = $page3;
      }

      // Check and validate
      $verify_response = $jaklic->verify_license(true);
      if ($verify_response['status'] != true) {
          if (JAK_SUPERADMINACCESS) {
              jak_redirect(JAK_rewrite::jakParseurl('maintenance'));
          } else {
              $_SESSION["errormsg"] = $jkl['e27'];
              jak_redirect(BASE_URL);
          }
      }

      // Include the CSS file for the header
      $css_file_header = BASE_URL_ORIG.'css/dropzone.css';

      // Include the javascript file for results
      $js_file_footer = 'js_ticket.php';

      // Load the template
      $template = 'readticket.php';
      
    } else {
      $_SESSION["errormsg"] = $jkl['i3'];
      jak_redirect($_SESSION['LCRedirect']);
    }

  break;
  case 'delete':

    // Check if the user exists
    if (is_numeric($page2) && is_numeric($page3) && jak_row_exist($page2,$jaktable) && jak_row_exist($page3,$jaktable4)) {

        // Delete the answer
      $jakdb->delete($jaktable4, ["id" => $page3]);

      $_SESSION["successmsg"] = $jkl['g14'];
      jak_redirect(JAK_rewrite::jakParseurl('support', 'read', $page2));

    } else {
      $_SESSION["errormsg"] = $jkl['i3'];
      jak_redirect($_SESSION['LCRedirect']);
    }

  break;
  case 'rating':
    // Check if the file can be deleted
    if (isset($page2) && is_numeric($page2) && $jakdb->has($jaktable, ["id" => $page2])) {

      $rowt = $jakdb->get($jaktable, ["subject"], ["id" => $page2]);
      $row = $jakdb->get("ticket_rating", ["clientid", "name", "email", "vote", "comment", "support_time"], ["ticketid" => $page2]);

        // Call the template
      $template = 'ticketrating.php';

    } else {
      $_SESSION["errormsg"] = $jkl['i3'];
      jak_redirect(JAK_rewrite::jakParseurl('support'));
    }
  break;
  case 'deletef':
    # code...
    // Check if the file can be deleted
    if (is_numeric($page2) && !empty($page3)) {

        // Now let us delete the file
      $filedel = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$page3;
      if (file_exists($filedel)) {
        unlink($filedel);
      }

        // Counter - 1
      $jakdb->update($jaktable, ["attachments[-]" => 1], ["id" => $page2]);

      $_SESSION["successmsg"] = $jkl['g14'];
      jak_redirect($_SESSION['LCRedirect']);

    } else {
      $_SESSION["errormsg"] = $jkl['i3'];
      jak_redirect($_SESSION['LCRedirect']);
    }
  break;
  case 'download_template':
    // download template excel
    ini_set("memory_limit","512M");
    include '../class/PHPExcel.php';
    include '../class/PHPExcel/IOFactory.php';

    $filetype = 'Excel5';
    $filetemplate = $_SERVER['DOCUMENT_ROOT']."/helpdesk/files/standard/template.xls";
    $filename = 'ticketimport_template.xls';
    // Read the file
    $objReader = PHPExcel_IOFactory::createReader($filetype);
    $objPHPExcel = $objReader->load($filetemplate);

    // Modify the file
    $objPHPExcel->setActiveSheetIndex(1);
    $objPHPExcel->getActiveSheet()->setTitle('Notes(!)'); 
    $maxrow = $objPHPExcel->getActiveSheet()->getHighestRow();

    // styling
    foreach(range('A','F') as $columnID) {
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
          ->setAutoSize(true);
    }

    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);  

    $text_center = array(
      'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      )
    );

    $cell_blue = [
      'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '99CCFF')
      ]
    ];

    $thin_border = [
      'borders' => [
        'allborders' => [
          'style' => PHPExcel_Style_Border::BORDER_THIN
        ]
      ],
    ];

    $header_style = [
      'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      ),
      'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '99CCFF')
      ],
      'borders' => [
        'allborders' => [
          'style' => PHPExcel_Style_Border::BORDER_THIN
        ]
      ],
    ];

    $subheader_style = [
      'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      ),
      'borders' => [
        'allborders' => [
          'style' => PHPExcel_Style_Border::BORDER_THIN
        ]
      ],
    ];
    // end styling

    // status
    $count = 3;
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'STATUS')->getStyle('A1')->applyFromArray($header_style);
    $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($thin_border);
    $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', 'id')->getStyle('A2')->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('B2', 'value')->getStyle('B2')->applyFromArray($subheader_style);
    $status = $jakdb->query("
      SELECT id, name FROM hd_ticket_status
    ");
    foreach ($status as $st){     
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$count, $st['id'])->getStyle('A'.$count)->applyFromArray($text_center);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$count, $st['name']);

      $objPHPExcel->getActiveSheet()->getStyle('A'.$count.':B'.$count.'')->applyFromArray($thin_border);
      $count++;
    } 
    // end status
    // sumber complaint
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count+1), 'SUMBER COMPLAINT')->getStyle('A'.($count+1))->applyFromArray($header_style);
    $objPHPExcel->getActiveSheet()->getStyle('A'.($count+1).':B'.($count+1).'')->applyFromArray($thin_border);
    $objPHPExcel->getActiveSheet()->mergeCells('A'.($count+1).':B'.($count+1).'');
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count+2), 'id')->getStyle('A'.($count+2))->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.($count+2), 'value')->getStyle('B'.($count+2))->applyFromArray($subheader_style);
    $count_sc = $count+3;
    $sumber_complaint = $jakdb->query("
      SELECT id, title FROM hd_support_departments
    ");
    foreach ($sumber_complaint as $sc){     
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$count_sc, $sc['id'])->getStyle('A'.$count_sc)->applyFromArray($text_center);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$count_sc, $sc['title']);

      $objPHPExcel->getActiveSheet()->getStyle('A'.$count_sc.':B'.$count_sc.'')->applyFromArray($thin_border);
      $count_sc++;
    } 
    // end sumber complaint
    // jenis complaint
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count_sc+1), 'KATEGORI JENIS COMPLAINT')->getStyle('A'.($count_sc+1))->applyFromArray($header_style);
    $objPHPExcel->getActiveSheet()->getStyle('A'.($count_sc+1).':B'.($count_sc+1).'')->applyFromArray($thin_border);
    $objPHPExcel->getActiveSheet()->mergeCells('A'.($count_sc+1).':B'.($count_sc+1).'');
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count_sc+2), 'id')->getStyle('A'.($count_sc+2))->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.($count_sc+2), 'value')->getStyle('b'.($count_sc+2))->applyFromArray($subheader_style);
    $count_jc = $count_sc+3;
    $jenis_complaint = $jakdb->query("
      SELECT id, title FROM hd_ticketpriority
    ");
    foreach ($jenis_complaint as $jc){     
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$count_jc, $jc['id'])->getStyle('A'.$count_jc)->applyFromArray($text_center);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$count_jc, $jc['title']);

      $objPHPExcel->getActiveSheet()->getStyle('A'.$count_jc.':B'.$count_jc.'')->applyFromArray($thin_border);

      $count_jc++;
    } 
    // end jenis complaint
    // sumber denda
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count_jc+1), 'SUMBER DENDA')->getStyle('A'.($count_jc+1))->applyFromArray($header_style);
    $objPHPExcel->getActiveSheet()->getStyle('A'.($count_jc+1).':B'.($count_jc+1).'')->applyFromArray($thin_border);
    $objPHPExcel->getActiveSheet()->mergeCells('A'.($count_jc+1).':B'.($count_jc+1).'');
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count_jc+2), 'id')->getStyle('A'.($count_jc+2))->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.($count_jc+2), 'value')->getStyle('B'.($count_jc+2))->applyFromArray($subheader_style);
    $count_sd = $count_jc+3;
    $sumber_denda = $jakdb->query("
      SELECT * FROM hd_customfields WHERE id = 2
    ");
    foreach ($sumber_denda as $sd){     
      $sd_value = explode(',', $sd['field_html']);
      foreach($sd_value as $sd){
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$count_sd, 'same as value')->getStyle('A'.$count_sd)->applyFromArray($text_center);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$count_sd, $sd);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$count_sd.':B'.$count_sd.'')->applyFromArray($thin_border);
        $count_sd++;
      }
    } 
    // end sumber denda
    // tipe denda
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count_sd+1), 'TIPE DENDA')->getStyle('A'.($count_sd+1))->applyFromArray($header_style);
    $objPHPExcel->getActiveSheet()->getStyle('A'.($count_sd+1).':B'.($count_sd+1).'')->applyFromArray($thin_border);
    $objPHPExcel->getActiveSheet()->mergeCells('A'.($count_sd+1).':B'.($count_sd+1).'');
    $objPHPExcel->getActiveSheet()->setCellValue('A'.($count_sd+2), 'id')->getStyle('A'.($count_sd+2))->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.($count_sd+2), 'value')->getStyle('B'.($count_sd+2))->applyFromArray($subheader_style);
    $count_td = $count_sd+3;
    $tipe_denda = $jakdb->query("
      SELECT * FROM hd_customfields WHERE id = 3
    ");
    foreach ($tipe_denda as $td){     
      $td_value = explode(',', $td['field_html']);
      foreach($td_value as $tv){
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$count_td, 'same as value')->getStyle('A'.$count_td)->applyFromArray($text_center);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$count_td, $tv);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$count_td.':B'.$count_td.'')->applyFromArray($thin_border);
        $count_td++;
      }
    } 
    // end tipe denda
    // sub-kategori rincian complaint
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'SUB-KATEGORI RINCIAN COMPLAINT')->getStyle('D1')->applyFromArray($header_style);
    $objPHPExcel->getActiveSheet()->getStyle('D1:F1')->applyFromArray($thin_border);
    $objPHPExcel->getActiveSheet()->mergeCells('D1:F1');
    $objPHPExcel->getActiveSheet()->setCellValue('D2', 'id')->getStyle('D2')->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('E2', 'value')->getStyle('E2')->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('F2', 'jenis complaint')->getStyle('F2')->applyFromArray($subheader_style);
    $count_rc = 3;
    $rincian_complaint = $jakdb->query("
      SELECT
        top.id,
        top.title as rincian_complaint,
        tp.id as id_jc,
        tp.title as jenis_complaint
      FROM
        hd_ticketoptions top
      LEFT JOIN hd_ticketpriority tp ON top.priorityid = tp.id
    ");
    foreach ($rincian_complaint as $rc){     
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$count_rc, $rc['id'])->getStyle('D'.$count_rc)->applyFromArray($text_center);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$count_rc, $rc['rincian_complaint']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$count_rc, $rc['id_jc'].' - '.$rc['jenis_complaint']); 

      $objPHPExcel->getActiveSheet()->getStyle('D'.$count_rc.':F'.$count_rc.'')->applyFromArray($thin_border);
      $count_rc++;
    } 
    // end sub-kategori rincian complaint
    // operators
    $objPHPExcel->getActiveSheet()->setCellValue('D'.($count_rc+1), 'OPERATORS')->getStyle('D'.($count_rc+1))->applyFromArray($header_style);
    $objPHPExcel->getActiveSheet()->getStyle('D'.($count_rc+1).':F'.($count_rc+1).'')->applyFromArray($thin_border);
    $objPHPExcel->getActiveSheet()->mergeCells('D'.($count_rc+1).':F'.($count_rc+1).'');
    $objPHPExcel->getActiveSheet()->setCellValue('D'.($count_rc+2), 'id')->getStyle('D'.($count_rc+2))->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.($count_rc+2), 'value')->getStyle('E'.($count_rc+2))->applyFromArray($subheader_style);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.($count_rc+2), 'jenis complaint')->getStyle('F'.($count_rc+2))->applyFromArray($subheader_style);
    $count_op = $count_rc+3;
    $ticket_priority = $jakdb->query("
      SELECT * FROM hd_ticketpriority
    ");
    foreach($ticket_priority as $tp) {
      $opid_explode = explode(",", $tp['op_id']);
      foreach($opid_explode as $op_ex){
        $operators = $jakdb->query("
          SELECT * FROM hd_user WHERE id IN ($op_ex)
        ");
        foreach($operators as $op) {  
          if($op['id'] == $op_ex) {
            // echo json_encode($op['id']);
            // echo json_encode($op['name']);
            // echo json_encode($op['username']);
            // echo json_encode($tp['id']);
            // echo json_encode($tp['title'])."<br>";
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$count_op, $op['id'])->getStyle('D'.$count_op)->applyFromArray($text_center);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$count_op, $op['name']." (".$op['username'].")");
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$count_op, $tp['id'].' - '.$tp['title']); 
      
            $objPHPExcel->getActiveSheet()->getStyle('D'.$count_op.':F'.$count_op.'')->applyFromArray($thin_border);
            $count_op++;
          }
        }
      }
    }
    // end operators
    
    // Write the file
    $path = $_SERVER['DOCUMENT_ROOT']."/helpdesk/files/standard/".$filename;
    $pathurl = BASE_URL_HOME."files/standard/".$filename;

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $filetype);
    $objWriter->save($path);

    echo json_encode($pathurl);
  break;
  case 'status':
    // Check if the ticket exists
  if (is_numeric($page2) && is_numeric($page3) && jak_row_exist($page2,$jaktable)) {

    // Now let's update the status
    $result = $jakdb->update($jaktable, ["status" => $page3, "ended" => 0], ["id" => $page2]);

    // Ticket is closed set an ending time
    if ($page3 == 3 || $page3 == 4) {
      $jakdb->update($jaktable, ["ended" => time()], ["id" => $page2]);

        // Get the ticket data first
      $ticketdata = $jakdb->get($jaktable, ["[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.name", "support_tickets.email", "support_tickets.subject", "support_tickets.initiated", "clients.credits", "clients.paid_until"], ["support_tickets.id" => $page2]);

        // Send email to customers if set so.
      if (JAK_TICKET_CLOSE_R == 1) {

          // Dashboard URL
        $ticketurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2, JAK_rewrite::jakCleanurl($ticketdata["subject"])));

          // Let's check if we have an imap
        $answeremail = $ticktext = '';
        $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $ticketdata["depid"]]);
        if ($check_imap) $answeremail = $check_imap;

          // Get the ticket answer template
        if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {

          if ($v["msgtype"] == 23 && $v["lang"] == JAK_LANG) {

            $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
            $replace   = array(BASE_URL_ORIG, JAK_TITLE, $ticketdata['email'], $ticketdata['name'], $ticketdata['credits'], $ticketdata['paid_until'], '#'.$page3, $ticketdata['subject'], $ticketurl, $answeremail);
            $ticktext = str_replace($phold, $replace, $v["message"]);
            break;

          }

        }

          // Get the email template
        if (!empty($ticktext)) {
          $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

              // Change fake vars into real ones.
          $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
          $cssUrl   = array($ticktext, BASE_URL_ORIG, JAK_TITLE);
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
              $mail->addAddress($ticketdata['email']);
              $mail->Subject = JAK_TITLE.' - '.sprintf($jkl['hd192'], $ticketdata['subject']);
              $mail->MsgHTML($body);

              if ($mail->Send()) $_SESSION["infomsg"] = $jkl['hd134'];
            }

        } // end sending closed message

        // Send rating to customer when ticket is closed forever
        if (JAK_TICKET_RATING && $page3 == 4) {

          // Dashboard URL
          $ticketratingurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_CLIENT_URL, 'rt', $ticketdata["id"], $ticketdata["initiated"]));

          // Let's check if we have an imap
          $answeremail = $ticktextr = '';
          $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $ticketdata["depid"]]);
          if ($check_imap) $answeremail = $check_imap;

          // Get the ticket answer template
          if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {

            if ($v["msgtype"] == 25 && $v["lang"] == JAK_LANG) {

              $pholdr = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
              $replacer   = array(str_replace(JAK_OPERATOR_LOC.'/', '', BASE_URL), JAK_TITLE, $ticketdata['email'], $ticketdata['name'], $ticketdata['credits'], $ticketdata['paid_until'], '#'.$ticketdata["id"], $ticketdata['subject'], $ticketratingurl, $answeremail);
              $ticktextr = str_replace($pholdr, $replacer, $v["message"]);
              break;

            }

          }

          if (!empty($ticktextr)) {

            // Get the email template
            $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

              // Change fake vars into real ones.
            $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
            $cssUrl   = array($ticktextr, str_replace(JAK_OPERATOR_LOC.'/', '', BASE_URL), JAK_TITLE);
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
              $mail->addAddress($ticketdata['email']);
              $mail->Subject = JAK_TITLE.' - '.$jkl['g85'].': '.$ticketdata['subject'];
              $mail->MsgHTML($body);

              $mail->Send();

              // Now we update the ticket table
              $jakdb->update($jaktable, ["reminder" => 2], ["id" => $ticketdata['id']]);

              }

            }

          }

          if (!$result) {
            $_SESSION["infomsg"] = $jkl['i'];
            jak_redirect($_SESSION['LCRedirect']);
          } else {

            // Write the log file each time someone tries to login before
            JAK_base::jakWhatslog('', JAK_USERID, 0, 19, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

            $_SESSION["successmsg"] = $jkl['g14'];
            jak_redirect($_SESSION['LCRedirect']);
          }

        } else {
          $_SESSION["errormsg"] = $jkl['i3'];
          jak_redirect($_SESSION['LCRedirect']);      
        }
        break;
        case 'merge':
          # code...

          // Let's go on with the script
          if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ticketid'])) {
            $jkp = $_POST;

            // Errors in Array
            $errors = array();

            if (empty($jkp['ticketid']) || !filter_var($jkp['ticketid'], FILTER_VALIDATE_INT)) {
              $errors['ticketid'] = $jkl['e15'];
            }

            if (!isset($errors['ticketid']) && $jkp['ticketid'] == $page2) {
              $errors['ticketid'] = $jkl['i8'];    
            }

            if (!isset($errors['ticketid']) && !jak_row_exist($jkp['ticketid'], $jaktable)) {
              $errors['ticketid'] = $jkl['i3'];
            }

            if (!$jakdb->has($jaktable, ["AND" => ["id" => $jkp['ticketid'], "clientid" => $page3]])) {
              $errors['ticketid'] = $jkl['i9'];
            }

            if (count($errors) > 0) {

              /* Outputtng the error messages */
              if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

                header('Cache-Control: no-cache');
                die(json_encode(array('status' => 0, 'errors' => $errors, 'html' => $errors['ticketid'])));

              } else {
                $errors = $errors;
              }

            } else {

              // Now we create an answer for the ticket we merge into it.
              $row = $jakdb->get($jaktable, ["id", "subject"], ["id" => $page2]);

              // Create the URL for the answer, so it is linked.
              $ticketlink = '<a href="'.str_replace(BASE_URL, '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'read', $row["id"])).'">'.$row["subject"].'</a>';

              // Get the sentence from the language file
              $content = sprintf($jkl['hd280'], $row["id"], $ticketlink);

              // Filter the content
              $contentf = jak_clean_safe_userpost($content);

              $result = $jakdb->insert($jaktable4, ["ticketid" => $jkp['ticketid'],
                "operatorid" => JAK_USERID,
                "content" => $contentf,
                "lastedit" => $jakdb->raw("NOW()"),
                "sent" => $jakdb->raw("NOW()")]);

              // Update the ticket we merged into it
              $jakdb->update($jaktable, ["updated" => time()], ["id" => $jkp["ticketid"]]);


              // Now let's update the ticket we merged so we need to update the ticket and close it for good.
              $row1 = $jakdb->get($jaktable, ["id", "subject"], ["id" => $jkp["ticketid"]]);

              // Create the URL for the answer, so it is linked.
              $ticketlinkn = '<a href="'.str_replace(BASE_URL, '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'read', $row1["id"])).'">'.$row1["subject"].'</a>';

              // Get the sentence from the language file
              $content = sprintf($jkl['hd279'], $row1["id"], $ticketlinkn);

              // Filter the content
              $contentf = jak_clean_safe_userpost($content);

              $result = $jakdb->insert($jaktable4, ["ticketid" => $page2,
                "operatorid" => JAK_USERID,
                "content" => $contentf,
                "lastedit" => $jakdb->raw("NOW()"),
                "sent" => $jakdb->raw("NOW()")]);

              // Get the ID from the ticket
              $lastid = $jakdb->id();

              // Update the ticket we merged into it
              $jakdb->update($jaktable, ["status" => 4, "mergeid" => $jkp["ticketid"], "mergeopid" => JAK_USERID, "mergetime" => time(), "ended" => time(), "updated" => time()], ["id" => $page2]);

              // Write the log file
              JAK_base::jakWhatslog('', JAK_USERID, 0, 21, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

              // Now it is time to inform the customer that the ticket has been merged and closed
              $ticketdata = $jakdb->get($jaktable, ["[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.name", "support_tickets.email", "support_tickets.subject", "support_tickets.initiated", "clients.credits", "clients.paid_until"], ["support_tickets.id" => $page2]);

              // Send email to customers if set so.
              if (JAK_TICKET_CLOSE_R == 1) {

                // Dashboard URL
                $ticketurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2, JAK_rewrite::jakCleanurl($ticketdata["subject"])));

                // Let's check if we have an imap
                $answeremail = $ticktext = '';
                $check_imap = $jakdb->get($jaktable7, "emailanswer", ["AND" => ["depid" => $ticketdata["depid"], "opid" => $opcacheid]]);
                if ($check_imap) $answeremail = $check_imap;

                // Get the ticket answer template
                if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {

                  if ($v["msgtype"] == 23 && $v["lang"] == JAK_LANG) {

                    $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
                    $replace   = array(BASE_URL_ORIG, JAK_TITLE, $ticketdata['email'], $ticketdata['name'], $ticketdata['credits'], $ticketdata['paid_until'], '#'.$ticketdata['id'], $ticketdata['subject'], $ticketurl, $answeremail);
                    $ticktext = str_replace($phold, $replace, $v["message"]);
                    break;

                  }

                }

                // Get the email template
                if (!empty($ticktext)) {
                  $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

                  // Change fake vars into real ones.
                  $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
                  $cssUrl   = array($ticktext, BASE_URL_ORIG, JAK_TITLE);
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
                  $mail->addAddress($ticketdata['email']);
                  $mail->Subject = JAK_TITLE.' - '.sprintf($jkl['hd281'], $ticketdata['subject']);
                  $mail->MsgHTML($body);
                  $mail->Send();

                }

              } // end sending closed message

              // Ajax Request
              if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

                header('Cache-Control: no-cache');
                die(json_encode(array('status' => 1, 'html' => $jkl["hd278"])));

              } else {

                jak_redirect($_SERVER['HTTP_REFERER']);

              }
            }
          }

          // Now we get the client and show the last 5 Tickets from this client.
          if (isset($page2) && is_numeric($page2) && jak_row_exist($page2, $jaktable)) {

            // Get the client id
            $clientid = filter_var($page3, FILTER_SANITIZE_NUMBER_INT);

            $CLI_TICKET = $jakdb->select($jaktable, ["id", "subject", "initiated", "updated"], ["AND" => ["id[!]" => $page2, "clientid" => $clientid, "status[!]" => 4], "ORDER" => ["updated" => "DESC"], "LIMIT" => 6]);

          }

        // Call the template
        $template = 'mergeticket.php';

      break;
      default:       
        // Let's go on with the script
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $jkp = $_POST;

          // import excel
          if (isset($jkp['action']) && $jkp['action'] == "import_excel") {
            ini_set("memory_limit","512M");
            date_default_timezone_set("Asia/Jakarta");
            include '../class/PHPExcel/IOFactory.php';
            
            $file = $_FILES['file_import'];
            $objReader = PHPExcel_IOFactory::createReaderForFile($file['tmp_name']);
            $objPHPExcel = $objReader->load($file['tmp_name']);

            $worksheet = $objPHPExcel->getActiveSheet();
            $maxrow = $objPHPExcel->getActiveSheet()->getHighestRow();

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
              for($row = 2; $row <= $maxrow; $row++){
                $name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $phone = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                $email = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                $email = ($email == NULL || $email == '') ? 'other@user.com' : $email;
                $judul_tiket = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                $isi_tiket = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                $awb = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                $operatorid = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                $status = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                $sumber_complaint = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                $jenis_complaint = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                $rincian_complaint = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                $dp_bersalah = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                $dp_bersalah = ($dp_bersalah == NULL || $dp_bersalah == '') ? NULL : $dp_bersalah;
                $sumber_denda = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                $sumber_denda = ($sumber_denda == NULL || $sumber_denda == '') ? NULL : $sumber_denda;
                $tipe_denda = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                $tipe_denda = ($tipe_denda == NULL || $tipe_denda == '') ? NULL : $tipe_denda;
                $nominal_denda = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                $nominal_denda = ($nominal_denda == NULL || $nominal_denda == '') ? NULL : $nominal_denda;
                $createddate_raw = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                $ended_date = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                $ended_date = ($ended_date == NULL || $ended_date == '') ? 0 : $ended_date;
                $clientid = 1;
                
                // convert nama dp to kode dp
                $m_dp = $jakdb->query("SELECT * FROM m_dp")->fetchAll();
                foreach($m_dp as $dp){
                  if($dp_bersalah == $dp['droppoint']) {
                    $dp_bersalah = $dp['id_dp'];
                  }
                }

                // created date convert to epoch
                $created_date = new DateTime($createddate_raw);
                $created_date = $created_date->getTimestamp();

                if ($ended_date > 0) {
                  $ended_date = new DateTime($ended_date);
                  $ended_date = $ended_date->getTimestamp();
                }

                // due value by jenis complaint
                $ticketpriority = $jakdb->select($jaktable3, "*");
                foreach($ticketpriority as $jc) {
                  if($jenis_complaint == $jc['id']){
                    $due_value = intval($jc['duetime']);
                  }
                }
                
                $duedatesql = date("Y-m-d", strtotime($createddate_raw. ' +'.$due_value.' days'));

                // insert to db
                $result = $jakdb->insert($jaktable, ["depid" => $sumber_complaint,
                  "subject" => $judul_tiket,
                  "awb" => $awb,
                  "content" => $isi_tiket,
                  "operatorid" => $operatorid,
                  "clientid" => $clientid,
                  "name" => $name,
                  "phone" => $phone,
                  "email" => $email,
                  "referrer" => '',
                  "notes" => '',
                  "private" => 1,
                  "dp_bersalah" => $dp_bersalah,
                  "priorityid" => $jenis_complaint,
                  "toptionid" => $rincian_complaint,
                  "status" => $status,
                  "sumber_denda" => $sumber_denda,
                  "tipe_denda" => $tipe_denda,
                  "nominal_denda" => $nominal_denda,
                  "ip" => 0,
                  "updated" => $created_date,
                  "initiated" => $created_date,
                  "ended" => $ended_date,
                  "duedate" => $duedatesql
                ]);                
              }

              // exit;
              if (!$result) {
                $_SESSION["infomsg"] = 'Failed!';
                jak_redirect($_SESSION['LCRedirect']);
              } else {
                $_SESSION["successmsg"] = 'Import success!';
                jak_redirect($_SESSION['LCRedirect']);
              }
            }
          }
        
          if (isset($jkp['action']) && $jkp['action'] == "depid") {
            if (isset($jkp['jak_depid']) && is_numeric($jkp['jak_depid']) && $jkp['jak_depid'] != 0) {
              if ($jakuser->getVar("support_dep") == 0 || in_array($jkp['jak_depid'], explode(",", $jakuser->getVar("support_dep")))) {
                $_SESSION["sortdepid"] = $jkp['jak_depid'];
                jak_redirect(JAK_rewrite::jakParseurl('support', $jkp['jak_depid']));
              }
            }
            unset($_SESSION["sortdepid"]);
            jak_redirect(JAK_rewrite::jakParseurl('support'));
          }
        
          if (isset($jkp['action']) && $jkp['action'] == "start_date_filter") {
            if (isset($jkp['jak_start_datefilter'])) {
              $_SESSION["jak_start_datefilter"] = $jkp['jak_start_datefilter'];
              jak_redirect(JAK_rewrite::jakParseurl('support', $jkp['jak_start_datefilter']));
            }
            unset($_SESSION["jak_start_datefilter"]);
            jak_redirect(JAK_rewrite::jakParseurl('support'));
          }
        
          if (isset($jkp['action']) && $jkp['action'] == "end_date_filter") {
            if (isset($jkp['jak_end_datefilter'])) {
              $_SESSION["jak_end_datefilter"] = $jkp['jak_end_datefilter'];
              jak_redirect(JAK_rewrite::jakParseurl('support', $jkp['jak_end_datefilter']));
            }
            unset($_SESSION["jak_end_datefilter"]);
            jak_redirect(JAK_rewrite::jakParseurl('support'));
          }

          if (isset($jkp['action']) && $jkp['action'] == "cat_filter") {
            unset($_SESSION["jak_statfilter"]);
            if (isset($jkp['jak_catfilter']) && is_numeric($jkp['jak_catfilter']) && $jkp['jak_catfilter'] != 0) {
              $_SESSION["jak_catfilter"] = $jkp['jak_catfilter'];
              jak_redirect(JAK_rewrite::jakParseurl('support', $jkp["jak_catfilter"]));
            }
            unset($_SESSION["jak_catfilter"]);
            jak_redirect(JAK_rewrite::jakParseurl('support'));
          }
        
          if (isset($jkp['action']) && $jkp['action'] == "stat_filter") {
            unset($_SESSION["jak_catfilter"]);
            if (isset($jkp['jak_statfilter']) && is_numeric($jkp['jak_statfilter']) && $jkp['jak_statfilter'] != 0) {
              $_SESSION["jak_statfilter"] = $jkp['jak_statfilter'];
              jak_redirect(JAK_rewrite::jakParseurl('support', $jkp["jak_statfilter"]));
            }
            unset($_SESSION["jak_statfilter"]);
            jak_redirect(JAK_rewrite::jakParseurl('support'));
          }

          if (isset($jkp['action']) && $jkp['action'] == "delete") {

            if (!JAK_USERID || !JAK_SUPERADMINACCESS) jak_redirect(BASE_URL);

            if (isset($jkp['jak_delete_tickets'])) {

              $delartic = $jkp['jak_delete_tickets'];

              for ($i = 0; $i < count($delartic); $i++) {
                $delart = $delartic[$i];

                // Delete the ticket
                $jakdb->delete($jaktable, ["id" => $delart]);

                // Delete the answer
                $jakdb->delete($jaktable4, ["ticketid" => $delart]);

                // Delete the support answer time entries
                $jakdb->delete($jaktable10, ["ticketid" => $delart]);

                // Delete all attachments
                $targetPath = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$delart.'/';
                $removedouble =  str_replace("//","/",$targetPath);
                foreach(glob($removedouble.'*.*') as $jak_unlink) {
                  // Delete all files
                  @unlink($jak_unlink);    
                }
                // Delete the folder
                @rmdir($targetPath);
                
              }
              
              $_SESSION["successmsg"] = $jkl['g14'];
              jak_redirect($_SESSION['LCRedirect']);
            }

            $_SESSION["errormsg"] = $jkl['i3'];
            jak_redirect($_SESSION['LCRedirect']);

          }

          if (isset($jkp['action']) && ($jkp['action'] == "status1" || $jkp['action'] == "status2" || $jkp['action'] == "status3")) {

            if (!JAK_USERID || !JAK_SUPERADMINACCESS) jak_redirect(BASE_URL);

            if (isset($jkp['jak_delete_tickets'])) {

              // Get all selected tickets
              $delartic = $jkp['jak_delete_tickets'];

              // Get the status
              $newstatus = filter_var($jkp['action'], FILTER_SANITIZE_NUMBER_INT);

              // Initiate the php mailer outside the for loop
              $mail = new PHPMailer(); // defaults to using php "mail()" or optional SMTP

              for ($i = 0; $i < count($delartic); $i++) {
                $delart = $delartic[$i];
                $jakdb->update($jaktable, ["status" => str_replace("status", "", $newstatus)], ["id" => $delart]);

              // Ticket is closed set an ending time
              if ($newstatus == 3) {
                $jakdb->update($jaktable, ["ended" => time()], ["id" => $delart]);

                // Send email to customers if set so.
                if (JAK_TICKET_CLOSE_R == 1) {

                  // Get the ticket data first
                  $ticketdata = $jakdb->get($jaktable, ["[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.name", "support_tickets.email", "support_tickets.subject", "clients.credits", "clients.paid_until"], ["support_tickets.id" => $delart]);

                  // Dashboard URL
                  $ticketurl = str_replace(JAK_OPERATOR_LOC.'/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $delart, JAK_rewrite::jakCleanurl($ticketdata["subject"])));

                  // Let's check if we have an imap
                  $answeremail = $ticktext = '';
                  $check_imap = $jakdb->get($jaktable7, "emailanswer", ["depid" => $ticketdata["depid"]]);
                  if ($check_imap) $answeremail = $check_imap;

                  // Get the ticket answer template
                  if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {

                    if ($v["msgtype"] == 23 && $v["lang"] == JAK_LANG) {

                      $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
                      $replace   = array(BASE_URL_ORIG, JAK_TITLE, $ticketdata['email'], $ticketdata['name'], $ticketdata['credits'], $ticketdata['paid_until'], '#'.$delart, $ticketdata['subject'], $ticketurl, $answeremail);
                      $ticktext = str_replace($phold, $replace, $v["message"]);
                      break;
                    }
                  }

                    // Get the email template
                  if (!empty($ticktext)) {
                    $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');

                    // Change fake vars into real ones.
                    $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
                    $cssUrl   = array($ticktext, BASE_URL_ORIG, JAK_TITLE);
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
                        $mail->ClearAllRecipients();
                        $mail->addAddress($ticketdata['email']);
                        $mail->Subject = JAK_TITLE.' - '.sprintf($jkl['hd192'], $ticketdata['subject']);
                        $mail->MsgHTML($body);

                        // Send email to customer
                        $mail->Send();
                      }

                  } // end sending closed message
                }
              }
              
              $_SESSION["successmsg"] = $jkl['g14'];
              jak_redirect($_SESSION['LCRedirect']);
            }

            $_SESSION["errormsg"] = $jkl['i3'];
            jak_redirect($_SESSION['LCRedirect']);
          }   
        }

    // Leads
    $totalAll = $totalAllOT = $totalAllWT = $totalAllCT = $total_vote = $total_voted = 0;

    // Get the totals
    $totalAll = $jakdb->count($jaktable);

    // Open Tickets
    $totalAllOT = $jakdb->count($jaktable, ["status" => 1]);
    
    // Awaiting Reply Tickets
    $totalAllWT = $jakdb->count($jaktable, ["status" => 2]);
    
    // Closed Tickets
    $totalAllCT = $jakdb->count($jaktable, ["AND" => ["status" => 3, "status" => 4]]);


    $total_voted = $jakdb->count($jaktable, ["reminder" => 3]);
    $total_vote = $jakdb->sum("ticket_rating", "vote");

    $PRIORITY_ALL = $jakdb->select($jaktable3, "*", ["depid" => [0]]);

    // Get the support departments
    if (count($HD_SUPPORT_DEPARTMENTS) == 1) {
      $dep_filter = false;
    } else {
      
      if (is_numeric($jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
        $dep_filter = $jakdb->get("support_departments", ["id", "title"], ["AND" => ["id" => $jakuser->getVar("support_dep"), "active" => 1], "ORDER" => ["dorder" => "ASC"]]);
        $dep_filter = false;
      } elseif (!((boolean)$jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
        $dep_filter = $jakdb->select("support_departments", ["id", "title"], ["AND" => ["id" => [$jakuser->getVar("support_dep")], "active" => 1], "ORDER" => ["dorder" => "ASC"]]);
      } else {
        $dep_filter = $HD_SUPPORT_DEPARTMENTS;
      }
    }

    // Title and Description
    $SECTION_TITLE = $jkl["hd"];
    $SECTION_DESC = "";

    // Call the template
    if (JAK_TICKET_DUEDATE) {
      // Include the javascript file for results
      $js_file_footer = 'js_supportdue.php';
      $template = 'supportdue.php';
    } else {
      // Include the javascript file for results
      $js_file_footer = 'js_support.php';
      $template = 'support.php';
    } 
  }
?>