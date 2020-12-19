<?php
/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

$cron_url = dirname(__file__) . DIRECTORY_SEPARATOR;

if (!file_exists(str_replace("cron/", "", $cron_url.'config.php'))) die('cron.php] config.php not exist');
require_once str_replace("cron/", "", $cron_url.'config.php');

// Import the language file
include_once(str_replace("cron/", "", $cron_url.'lang/'.JAK_LANG.'.php'));

// We need the correct url to filter either from web or cron
$sapi_type = php_sapi_name();
if(substr($sapi_type, 0, 3) == 'cli' || empty($_SERVER['REMOTE_ADDR'])) {
    $path_parts = pathinfo($cron_url);
    $url_filter = $cron_url;
    $url_replace = "/".basename($path_parts['dirname'])."/";
} else {
    $url_filter = "/cron/";
    $url_replace = "/";
}

// Tables
$jaktable = 'support_tickets';
$jaktable1 = 'tickets_answer';
$jaktable2 = 'php_imap';

// Calculate which tickets we have to reminder
$ticketreminder = (time() - (JAK_TICKET_REMINDER * 86400));

// Write the log file each time someone tries to login before
JAK_base::jakWhatslog('System', 0, 0, 38, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), 'Cron Job', $_SERVER['REQUEST_URI'], 0, 'phpimap');

// First check if we need to send a ticket reminder
$result = $jakdb->select($jaktable, ["[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.name", "support_tickets.email", "support_tickets.subject", "clients.credits", "clients.paid_until"], ["AND" => ["support_tickets.ended" => 0, "support_tickets.reminder" => 0, "support_tickets.updated[<]" => $ticketreminder]]);

if (isset($result) && !empty($result)) foreach ($result as $row) {

    // Dashboard URL
    $ticketurl = str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $row["id"], JAK_rewrite::jakCleanurl($row["subject"])));

    // Let's check if we have an imap
    $answeremail = $ticktext = '';
    $check_imap = $jakdb->get($jaktable2, "emailanswer", ["depid" => $row["depid"]]);
    if ($check_imap) $answeremail = $check_imap;

    // Get the ticket answer template
    if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
    
        if ($v["msgtype"] == 22 && $v["lang"] == JAK_LANG) {

            $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
            $replace   = array(str_replace($cron_url, '', BASE_URL), JAK_TITLE, $row['email'], $row['name'], $row['credits'], $row['paid_until'], '#'.$row["id"], $row['subject'], $ticketurl, $answeremail);
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
        $cssUrl   = array($ticktext, str_replace($cron_url, '', BASE_URL), JAK_TITLE);
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
        $mail->addAddress($row['email']);
        $mail->Subject = JAK_TITLE.' - RE:'.$row['subject'];
        $mail->MsgHTML($body);

        $mail->Send();

    }
    
    // Now we update the ticket table
    $jakdb->update($jaktable, ["reminder" => 1], ["id" => $row['id']]);
    
}

// Calculate which tickets we have to close
$ticketclose = (time() - (JAK_TICKET_CLOSE_C * 86400));

// First check if we need to send a ticket reminder
$result = $jakdb->select($jaktable, ["[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.name", "support_tickets.email", "support_tickets.subject", "clients.credits", "clients.paid_until"], ["AND" => ["support_tickets.ended" => 0, "support_tickets.updated[<]" => $ticketclose]]);

if (isset($result) && !empty($result)) foreach ($result as $row) {

    // Send email to customers if set so.
    if (JAK_TICKET_CLOSE_R == 1) {

        // Dashboard URL
        $ticketurl = str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $row["id"], JAK_rewrite::jakCleanurl($row["subject"])));

        // Let's check if we have an imap
        $answeremail = $ticktext = '';
        $check_imap = $jakdb->get($jaktable2, "emailanswer", ["depid" => $row["depid"]]);
        if ($check_imap) $answeremail = $check_imap;

        // Get the ticket answer template
        if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
              
            if ($v["msgtype"] == 23 && $v["lang"] == JAK_LANG) {

            $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
            $replace   = array(str_replace($cron_url, '', BASE_URL), JAK_TITLE, $row['email'], $row['name'], $row['credits'], $row['paid_until'], '#'.$row["id"], $row['subject'], $ticketurl, $answeremail);
            $ticktext = str_replace($phold, $replace, $v["message"]);
            break;
                              
            }
                        
        }

        // Get the email template
        if (!empty($ticktext)) {
            $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
                      
            // Change fake vars into real ones.
            $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
            $cssUrl   = array($ticktext, str_replace($cron_url, '', BASE_URL), JAK_TITLE);
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
            $mail->addAddress($row['email']);
            $mail->Subject = JAK_TITLE.' - '.sprintf($jkl['hd101'], $row['subject']);
            $mail->MsgHTML($body);

            // Send email to customer
            $mail->Send();
        }

    } // end sending closed message
    
    // Now we update the ticket table
    $jakdb->update($jaktable, ["ended" => time(), "status" => 3], ["id" => $row["id"]]);
    
}

if (JAK_TICKET_RATING) {

    // Calculate which tickets we will send a rating email
    $ticketrating = (time() - (JAK_TICKET_REOPEN * 86400));

    // Send some ticket ratings emails which cannot be reopen again.
    $restr = $jakdb->select($jaktable, ["[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.name", "support_tickets.email", "support_tickets.subject", "support_tickets.initiated", "clients.credits", "clients.paid_until"], ["AND" => ["support_tickets.status[>]" => 2, "support_tickets.reminder" => 1, "support_tickets.ended[<]" => $ticketrating]]);

    if (isset($restr) && !empty($restr)) foreach ($restr as $rowtr) {

        // Dashboard URL
        $ticketratingurl = str_replace($url_filter, $url_replace, JAK_rewrite::jakParseurl(JAK_CLIENT_URL, 'rt', $rowtr["id"], $rowtr["initiated"]));

        // Let's check if we have an imap
        $answeremail = $ticktext = '';
        $check_imap = $jakdb->get($jaktable2, "emailanswer", ["depid" => $rowtr["depid"]]);
        if ($check_imap) $answeremail = $check_imap;

        // Get the ticket answer template
        if (!empty($HD_ANSWERS) && is_array($HD_ANSWERS)) foreach ($HD_ANSWERS as $v) {
        
            if ($v["msgtype"] == 25 && $v["lang"] == JAK_LANG) {

                $phold = array('{url}', '{title}', '{cemail}', '{cname}', '{credits}', '{paid_until}', '{ticket}', '{subject}', '{ticketurl}', '{email}');
                $replace   = array(str_replace($cron_url, '', BASE_URL), JAK_TITLE, $rowtr['email'], $rowtr['name'], $rowtr['credits'], $rowtr['paid_until'], '#'.$rowtr["id"], $rowtr['subject'], $ticketratingurl, $answeremail);
                $ticktext = str_replace($phold, $replace, $v["message"]);
                break;
                    
            }
                  
        }

        if (!empty($ticktext)) {

            // Get the email template
            $nlhtml = file_get_contents(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/email/index.html');
                
            // Change fake vars into real ones.
            $cssAtt = array('{emailcontent}', '{weburl}', '{title}');
            $cssUrl   = array($ticktext, str_replace($cron_url, '', BASE_URL), JAK_TITLE);
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
            $mail->addAddress($rowtr['email']);
            $mail->Subject = JAK_TITLE.' - '.$jkl['g29'].': '.$rowtr['subject'];
            $mail->MsgHTML($body);

            $mail->Send();

        }
        
        // Now we update the ticket table
        $jakdb->update($jaktable, ["reminder" => 2], ["id" => $rowtr['id']]);
        
    }
}
?>