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

// All the tables we need for this plugin
$errors = $success = array();
$ss = false;

$jaktable = 'php_imap';
$jaktable1 = 'support_departments';
$jaktable2 = 'departments';
$jaktable3 = 'faq_categories';
$jaktable4 = 'cms_pages';
$jaktable5 = 'ticketpriority';
$jaktable6 = 'ticketoptions';
$jaktable7 = 'clients';
$jaktable8 = 'user';
$jaktable9 = 'chatwidget';

// Reset some vars
$totalAll = $totalChange = $totalFiles = $totalEntries = $totalSMTP = $totalMAIL = 0;

// Get the total settings
$totalAll = $jakdb->count("settings");

switch ($page1) {
    case 'support':

        // Let's go on with the script
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;

        if (isset($jkp['save'])) {

            if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

                // Update the fields
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_close_r']], ["varname" => "ticket_close_r"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_inform']], ["varname" => "ticket_inform_r"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_guest']], ["varname" => "ticket_guest"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_guest_web']], ["varname" => "ticket_guest_web"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_account']], ["varname" => "ticket_account"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_limit']], ["varname" => "ticket_limit"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_reminder']], ["varname" => "ticket_reminder"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_close']], ["varname" => "ticket_close_c"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_reopen']], ["varname" => "ticket_reopen"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_attach']], ["varname" => "ticket_attach"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_private']], ["varname" => "ticket_private"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_similar']], ["varname" => "ticket_similar"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_rating']], ["varname" => "ticket_rating"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_priority']], ["varname" => "standard_ticket_priority"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_option']], ["varname" => "standard_ticket_option"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_duedate']], ["varname" => "ticket_duedate"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_duedate_format']], ["varname" => "ticket_duedate_format"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_ticket_duedate_preset']], ["varname" => "ticket_duedate_preset"]);

                    // Now let us delete the define cache file
                $cachedefinefile = APP_PATH.JAK_CACHE_DIRECTORY.'/define.php';
                if (file_exists($cachedefinefile)) {
                    unlink($cachedefinefile);
                }

                // Write the log file each time someone login after to show success
                JAK_base::jakWhatslog('', JAK_USERID, 0, 43, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                $_SESSION["successmsg"] = $jkl['g14'];
                jak_redirect($_SESSION['LCRedirect']);

            }

        }

    }

        // Get all priorities
    $PRIORITY_ALL = $jakdb->select($jaktable5, ["id", "title", "credits"], ["depid" => 0]);
        // Get all options
    $TOPTIONS_ALL = $jakdb->select($jaktable6, ["id", "title", "credits"], ["depid" => 0]);

    // How often has it been changed
    $totalChange = $jakdb->count("whatslog", ["whatsid" => 43]);

    // Count all files
    $totalFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APP_PATH.JAK_FILES_DIRECTORY.'/support'), RecursiveIteratorIterator::SELF_FIRST);

        // Title and Description
    $SECTION_TITLE = $jkl["hd91"];
    $SECTION_DESC = "";

        // Call the template
    $template = 'supportsetting.php';

    break;
    case 'faq':
        // Let's go on with the script
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $jkp = $_POST;

            if (isset($jkp['save'])) {

                if (jak_get_access("faq", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

                        // Update the fields
                    $jakdb->update("settings", ["used_value" => $jkp['jak_faq']], ["varname" => "faq_a"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_faq_home']], ["varname" => "faq_home"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_faq_footer']], ["varname" => "faq_footer"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_faq_page']], ["varname" => "faq_page"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_faq_pagination']], ["varname" => "faq_pagination"]);

                        // Now let us delete the define cache file
                    $cachedefinefile = APP_PATH.JAK_CACHE_DIRECTORY.'/define.php';
                    if (file_exists($cachedefinefile)) {
                        unlink($cachedefinefile);
                    }

                    // Write the log file each time someone login after to show success
                    JAK_base::jakWhatslog('', JAK_USERID, 0, 41, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                    $_SESSION["successmsg"] = $jkl['g14'];
                    jak_redirect($_SESSION['LCRedirect']);

                }

            }

        }

        // How often has it been changed
        $totalChange = $jakdb->count("whatslog", ["whatsid" => 41]);

        // Total FAQ articles
        $totalEntries = $jakdb->count("faq_article");

        // Count all files
        $totalFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APP_PATH.JAK_FILES_DIRECTORY.'/editor'), RecursiveIteratorIterator::SELF_FIRST);

        // Title and Description
        $SECTION_TITLE = $jkl["hd106"];
        $SECTION_DESC = "";

        // Call the template
        $template = 'faqsetting.php';

    break;
    case 'blog':

        // Let's go on with the script
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $jkp = $_POST;

            if (isset($jkp['save'])) {

                if (jak_get_access("blog", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

                        // Update the fields
                    $jakdb->update("settings", ["used_value" => $jkp['jak_blog']], ["varname" => "blog_a"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_blogc']], ["varname" => "blogpostapprove"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_blog_home']], ["varname" => "blog_home"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_blog_footer']], ["varname" => "blog_footer"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_blog_page']], ["varname" => "blog_page"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_blog_pagination']], ["varname" => "blog_pagination"]);

                        // Now let us delete the define cache file
                    $cachedefinefile = APP_PATH.JAK_CACHE_DIRECTORY.'/define.php';
                    if (file_exists($cachedefinefile)) {
                        unlink($cachedefinefile);
                    }

                    // Write the log file each time someone login after to show success
                    JAK_base::jakWhatslog('', JAK_USERID, 0, 40, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                    $_SESSION["successmsg"] = $jkl['g14'];
                    jak_redirect($_SESSION['LCRedirect']);

                }

            }

        }

        // How often has it been changed
        $totalChange = $jakdb->count("whatslog", ["whatsid" => 40]);

        // Total FAQ articles
        $totalEntries = $jakdb->count("blog");

        // Count all files
        $totalFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APP_PATH.JAK_FILES_DIRECTORY.'/editor'), RecursiveIteratorIterator::SELF_FIRST);

        // Title and Description
        $SECTION_TITLE = $jkl["hd107"];
        $SECTION_DESC = "";

        // Call the template
        $template = 'blogsetting.php';

    break;
    case 'email':
        // Let's go on with the script
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $jkp = $_POST;

        if (isset($jkp['save'])) {

            if (jak_get_access("settings", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

                    // Update the fields
                $jakdb->update("settings", ["used_value" => $jkp['jak_smpt']], ["varname" => "smtp_mail"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_host']], ["varname" => "smtphost"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_port']], ["varname" => "smtpport"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_alive']], ["varname" => "smtp_alive"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_auth']], ["varname" => "smtp_auth"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_prefix']], ["varname" => "smtp_prefix"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_smtpusername']], ["varname" => "smtpusername"]);
                $jakdb->update("settings", ["used_value" => $jkp['jak_smtppassword']], ["varname" => "smtppassword"]);

                    // Now let us delete the define cache file
                $cachedefinefile = APP_PATH.JAK_CACHE_DIRECTORY.'/define.php';
                if (file_exists($cachedefinefile)) {
                    unlink($cachedefinefile);
                }

                // Write the log file each time someone login after to show success
                JAK_base::jakWhatslog('', JAK_USERID, 0, 42, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                $_SESSION["successmsg"] = $jkl['g14'];
                jak_redirect($_SESSION['LCRedirect']);

            }

        } else {

                $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

                // Send email the smpt way or else the mail way
                if (JAK_SMTP_MAIL) {

                    try {
                        $mail->IsSMTP(); // telling the class to use SMTP
                        $mail->Host = JAK_SMTPHOST;
                        $mail->SMTPAuth = (JAK_SMTP_AUTH ? true : false); // enable SMTP authentication
                        $mail->SMTPSecure = JAK_SMTP_PREFIX; // sets the prefix to the server
                        $mail->SMTPAutoTLS = false;
                        $mail->SMTPKeepAlive = (JAK_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
                        $mail->Port = JAK_SMTPPORT; // set the SMTP port for the GMAIL server
                        $mail->Username = JAK_SMTPUSERNAME; // SMTP account username
                        $mail->Password = JAK_SMTPPASSWORD;        // SMTP account password
                        $mail->SetFrom(JAK_EMAIL);
                        $mail->AddReplyTo(JAK_EMAIL);
                        $mail->AddAddress(JAK_EMAIL);
                        $mail->AltBody = $jkl["g215"]; // optional, comment out and test
                        $mail->Subject = $jkl["g216"];
                        $mail->MsgHTML($jkl["g217"].'SMTP.');
                        $mail->Send();
                        $success['e'] = $jkl["g217"].'SMTP.';
                    } catch (phpmailerException $e) {
                        $errors['e'] = $e->errorMessage(); //Pretty error messages from PHPMailer
                    } catch (Exception $e) {
                        $errors['e'] = $e->getMessage(); //Boring error messages from anything else!
                    }

                    // Write the log file each time someone login after to show success
                    JAK_base::jakWhatslog('', JAK_USERID, 0, 48, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);
                    
                } else {

                    try {
                        $mail->SetFrom(JAK_EMAIL);
                        $mail->AddReplyTo(JAK_EMAIL);
                        $mail->AddAddress(JAK_EMAIL);
                        $mail->AltBody = $jkl["g215"]; // optional, comment out and test
                        $mail->Subject = $jkl["g216"];
                        $mail->MsgHTML($jkl["g217"].'Mail().');
                        $mail->Send();
                        $success['e'] = $jkl["g217"].'Mail().';
                    } catch (phpmailerException $e) {
                        $errors['e'] = $e->errorMessage(); //Pretty error messages from PHPMailer
                    } catch (Exception $e) {
                        $errors['e'] = $e->getMessage(); //Boring error messages from anything else!
                    }

                    // Write the log file each time someone login after to show success
                    JAK_base::jakWhatslog('', JAK_USERID, 0, 47, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                }
            }
        }

        // How often has it been changed
        $totalChange = $jakdb->count("whatslog", ["whatsid" => 43]);

        // How often has it been tested
        $totalSMTP = $jakdb->count("whatslog", ["whatsid" => 47]);

        // How often has it been tested
        $totalMAIL = $jakdb->count("whatslog", ["whatsid" => 48]);

        // Title and Description
        $SECTION_TITLE = $jkl["hd119"];
        $SECTION_DESC = "";

        // Include the javascript file for results
        $js_file_footer = 'js_email.php';

        // Call the template
        $template = 'emailsetting.php';
        break;
        case 'phpimap':

        // Check if the user has access to this file
        if (!jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

        switch ($page2) {
            case 'edit':
                // Check if the user exists
            if (is_numeric($page3) && jak_row_exist($page3, $jaktable)) {

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $jkp = $_POST;

                    if (isset($jkp['save'])) {

                        if (empty($jkp['mailbox'])) {
                            $errors['e'] = $jkl['e'];
                        }

                        if (empty($jkp['usrphpimap'])) {
                            $errors['e1'] = $jkl['e7'];
                        }

                        if (!filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) {
                            $errors['e2'] = $jkl['e3'];
                        }

                        // Now we make sure the email address is not used elsewhere
                        if (empty($errors['e2'])) {

                            // First we check if it is the same as the settings email address
                            if ($jkp['email'] == JAK_EMAIL) {
                                $errors['e2'] = $jkl['e18'];
                            }

                            // Then we check the php imap table
                            if (empty($errors['e2']) && jak_field_not_exist(strtolower($jkp['email']), $jaktable, "email")) {
                                $errors['e2'] = $jkl['e18'];
                            }

                            // Then we check the clients table
                            if (empty($errors['e2']) && jak_field_not_exist(strtolower($jkp['email']), $jaktable7, "email")) {
                                $errors['e2'] = $jkl['e18'];
                            }

                            // Then we check the user table
                            if (empty($errors['e2']) && jak_field_not_exist(strtolower($jkp['email']), $jaktable8, "email")) {
                                $errors['e2'] = $jkl['e18'];
                            }
                        }
                        
                        if (count($errors) == 0) {

                            $result = $jakdb->update($jaktable, ["depid" => $jkp['jak_depid'],
                              "mailbox" => $jkp['mailbox'],
                              "username" => $jkp['usrphpimap'],
                              "password" => $jkp['passphpimap'],
                              "encryption" => $jkp['encryption'],
                              "scanfolder" => $jkp['inbox'],
                              "emailanswer" => strtolower($jkp['email']),
                              "msgdel" => $jkp['jak_msgdel'],
                              "created" => $jakdb->raw("NOW()")], ["id" => $page3]);

                            if (!$result) {
                                $_SESSION["infomsg"] = $jkl['i'];
                                jak_redirect($_SESSION['LCRedirect']);
                            } else {

                                // Write the log file each time someone login after to show success
                                JAK_base::jakWhatslog('', JAK_USERID, 0, 44, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                                $_SESSION["successmsg"] = $jkl['g14'];
                                jak_redirect($_SESSION['LCRedirect']);
                            }

                        // Output the errors
                        } else {

                            $errors = $errors;
                        }

                    } else {

                        if (!file_exists(APP_PATH.'class/class.imap.php')) die('settings.php] class.imap.php not exist, test impossible.');
                        require_once APP_PATH.'class/class.imap.php';

                        // Test the imap connection
                        $imap = new Imap($jkp["mailbox"], $jkp["usrphpimap"], $jkp["passphpimap"], $jkp["encryption"]);

                        if ($imap->isConnected()) {

                            $success['e'] = $jkl['hd55'].' - '.$jkl['g14'];

                        } else {

                            $errors['e4'] = "Following error occured when trying to connect to the mailbox ".$jkp["mailbox"].", with username ".$jkp["usrphpimap"].": ".$imap->getError();

                            // close connection
                            $imap->close();

                        }

                    }

                }

                    // Get all departments
                $JAK_DEPARTMENTS = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
                
                $JAK_FORM_DATA = jak_get_data($page3, $jaktable);

                    // Title and Description
                $SECTION_TITLE = $jkl["hd45"];
                $SECTION_DESC = "";

                    // Include the javascript file for results
                $js_file_footer = 'js_email.php';

                    // Call the template
                $template = 'editphpimap.php';
                
            } else {
                $_SESSION["infomsg"] = $jkl['i3'];
                jak_redirect(JAK_rewrite::jakParseurl('settings', 'phpimap'));
            }
            break;
            case 'delete':
                // Check if user exists and can be deleted
            if (is_numeric($page3)) {

                    // Now check how many languages are installed and do the dirty work
                $result = $jakdb->delete($jaktable, ["id" => $page3]);
                
                if ($result->rowCount() != 1) {
                    $_SESSION["infomsg"] = $jkl['i'];
                    jak_redirect($_SESSION['LCRedirect']);
                } else {

                    // Write the log file each time someone login after to show success
                    JAK_base::jakWhatslog('', JAK_USERID, 0, 46, $page3, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                    $_SESSION["successmsg"] = $jkl['g14'];
                    jak_redirect($_SESSION['LCRedirect']);
                }

            } else {
                $_SESSION["infomsg"] = $jkl['i2'];
                jak_redirect($_SESSION['LCRedirect']);
            }
            break;
            case 'lock':
                // Check if user exists and can be deleted
            if (is_numeric($page3)) {

                    // Check what we have to do
                $datausrac = $jakdb->get($jaktable, "active", ["id" => $page3]);
                    // update the table
                if ($datausrac) {
                    $result = $jakdb->update($jaktable, ["active" => 0], ["id" => $page3]);
                } else {
                    $result = $jakdb->update($jaktable, ["active" => 1], ["id" => $page3]);
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

                if (isset($_POST['insert_phpimap'])) {

                    if (empty($jkp['mailbox'])) {
                        $errors['e'] = $jkl['e'];
                    }

                    if (!filter_var($jkp['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors['e2'] = $jkl['e3'];
                    }
                    
                    if (empty($jkp['usrphpimap'])) {
                        $errors['e1'] = $jkl['e7'];
                    }

                    // Now we make sure the email address is not used elsewhere
                    if (empty($errors['e2'])) {

                        // First we check if it is the same as the settings email address
                        if ($jkp['email'] == JAK_EMAIL) {
                            $errors['e2'] = $jkl['e18'];
                        }

                        // Then we check the php imap table
                        if (empty($errors['e2']) && jak_field_not_exist(strtolower($jkp['email']), $jaktable, "email")) {
                            $errors['e2'] = $jkl['e18'];
                        }

                        // Then we check the clients table
                        if (empty($errors['e2']) && jak_field_not_exist(strtolower($jkp['email']), $jaktable7, "email")) {
                            $errors['e2'] = $jkl['e18'];
                        }

                        // Then we check the user table
                        if (empty($errors['e2']) && jak_field_not_exist(strtolower($jkp['email']), $jaktable8, "email")) {
                            $errors['e2'] = $jkl['e18'];
                        }
                    }

                    if (count($errors) == 0) {

                        $jakdb->insert($jaktable, ["depid" => $jkp['jak_depid'],
                          "mailbox" => $jkp['mailbox'],
                          "username" => $jkp['usrphpimap'],
                          "password" => $jkp['passphpimap'],
                          "encryption" => $jkp['encryption'],
                          "scanfolder" => $jkp['inbox'],
                          "emailanswer" => strtolower($jkp['email']),
                          "created" => $jakdb->raw("NOW()")]);

                        $lastid = $jakdb->id();

                        if (!$lastid) {
                            $_SESSION["infomsg"] = $jkl['i'];
                            jak_redirect($_SESSION['LCRedirect']);
                        } else {

                            // Write the log file each time someone login after to show success
                            JAK_base::jakWhatslog('', JAK_USERID, 0, 45, $lastid, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                            $_SESSION["successmsg"] = $jkl['g14'];
                            jak_redirect($_SESSION['LCRedirect']);
                        }

                    // Output the errors
                    } else {

                        $errors = $errors;
                    }
                }

            }

            // Get all departments
            $JAK_DEPARTMENTS = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);

            // Get all responses
            $PHPIMAP_ALL = jak_get_page_info($jaktable);

            // How often has it been changed
            $totalChange = $jakdb->count("whatslog", ["whatsid" => [44,45,46]]);

            // How often has it run
            $totalEntries = $jakdb->count("whatslog", ["whatsid" => 39]);

            // Title and Description
            $SECTION_TITLE = $jkl["hd44"];
            $SECTION_DESC = "";

                // Call the template
            $template = 'phpimap.php';
            break;
        }
        break;

        default:

        // Check if the user has access to this file
        if (!jak_get_access("settings", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS) && !jak_get_access("blocklist", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) jak_redirect(BASE_URL);

        // Let's go on with the script
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $jkp = $_POST;

            if (isset($jkp['save'])) {

                if (jak_get_access("settings", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {

                    if (!filter_var($jkp['jak_email'], FILTER_VALIDATE_EMAIL)) { 
                       $errors['e1'] = $jkl['e3'];
                   }

                   if ($jkp['jak_lang'] == '') { $errors['e6'] = $jkl['e29']; }

                   if (count($errors) == 0) {

                    // Clean the dsgvo link
                    include_once '../include/htmlawed.php';
                    $htmlconfig = array('comment'=>0, 'cdata'=>1, 'elements'=>'a, strong'); 
                    $dsgvo_clean = htmLawed($_REQUEST['jak_dsgvo'], $htmlconfig);

                    // Update the fields
                    $jakdb->update("settings", ["used_value" => $jkp['jak_title']], ["varname" => "title"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_email']], ["varname" => "email"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_emailcc']], ["varname" => "emailcc"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_holidaym']], ["varname" => "holiday_mode"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_billing']], ["varname" => "billing_mode"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_trans']], ["varname" => "send_tscript"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_rating']], ["varname" => "crating"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_captcha']], ["varname" => "captcha"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_showip']], ["varname" => "show_ips"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_lang']], ["varname" => "lang"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_avatwidth']], ["varname" => "useravatwidth"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_avatheight']], ["varname" => "useravatheight"]);
                    $jakdb->update("settings", ["used_value" => $jkp['allowed_files']], ["varname" => "allowed_files"]);
                    $jakdb->update("settings", ["used_value" => $jkp['allowedo_files']], ["varname" => "allowedo_files"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_date']], ["varname" => "dateformat"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_time']], ["varname" => "timeformat"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_timezone_server']], ["varname" => "timezoneserver"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_ringtone']], ["varname" => "ring_tone"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_msgtone']], ["varname" => "msg_tone"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_register']], ["varname" => "register"]);
                    $jakdb->update("settings", ["used_value" => $jkp['showalert']], ["varname" => "pro_alert"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_user_expired']], ["varname" => "client_expired"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_user_left']], ["varname" => "client_left"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_pushrem']], ["varname" => "push_reminder"]);
                    $jakdb->update("settings", ["used_value" => $jkp['ip_block']], ["varname" => "ip_block"]);
                    $jakdb->update("settings", ["used_value" => $jkp['email_block']], ["varname" => "email_block"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_twilio_nexmo']], ["varname" => "twilio_nexmo"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_tw_phone']], ["varname" => "tw_phone"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_tw_msg']], ["varname" => "tw_msg"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_tw_sid']], ["varname" => "tw_sid"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_tw_token']], ["varname" => "tw_token"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_openop']], ["varname" => "openop"]);
                    $jakdb->update("settings", ["used_value" => trim($jkp['jak_nativtok'])], ["varname" => "native_app_token"]);
                    $jakdb->update("settings", ["used_value" => trim($jkp['jak_nativkey'])], ["varname" => "native_app_key"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_recapclient']], ["varname" => "recap_client"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_recapserver']], ["varname" => "recap_server"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_client_push_not']], ["varname" => "client_push_not"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_engage_sound']], ["varname" => "engage_sound"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_engage_icon']], ["varname" => "engage_icon"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_client_sound']], ["varname" => "client_sound"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_live_online_status']], ["varname" => "live_online_status"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_chat_upload_standard']], ["varname" => "chat_upload_standard"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_template']], ["varname" => "front_template"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_chatwidget_id']], ["varname" => "chatwidget_id"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_proactive_time']], ["varname" => "proactive_time"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_facebookid']], ["varname" => "facebook_app_id"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_offline_page']], ["varname" => "offline_cms_page"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_stripe_publish']], ["varname" => "stripe_publish_key"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_stripe_secret']], ["varname" => "stripe_secret_key"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_paypal_email']], ["varname" => "paypal_email"]);
                    $jakdb->update("settings", ["used_value" => $jkp['twoco']], ["varname" => "twoco"]);
                    $jakdb->update("settings", ["used_value" => $jkp['twoco_secret']], ["varname" => "twoco_secret"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_calendar_tickets']], ["varname" => "calendar_tickets"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_calendar_chats']], ["varname" => "calendar_chats"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_calendar_offline']], ["varname" => "calendar_offline"]);
                    $jakdb->update("settings", ["used_value" => $jkp['jak_calendar_purchases']], ["varname" => "calendar_purchases"]);
                    $jakdb->update("settings", ["used_value" => trim($dsgvo_clean)], ["varname" => "dsgvo_contact"]);

                    // Chat departments
                    if (!isset($jkp['jak_depid']) OR in_array("0", $jkp['jak_depid'])) {
                        $depa = 0;
                    } else {
                        $depa = join(',', $jkp['jak_depid']);
                    }

                    // Support Departments
                    if (!isset($jkp['jak_depids']) OR in_array("0", $jkp['jak_depids'])) {
                        $depas = 0;
                    } else {
                        $depas = join(',', $jkp['jak_depids']);
                    }

                    // FAQ Categories
                    if (!isset($jkp['jak_depidf']) OR in_array("0", $jkp['jak_depidf'])) {
                        $depaf = 0;
                    } else {
                        $depaf = join(',', $jkp['jak_depidf']);
                    }

                    // Update the standard departments
                    $jakdb->update("settings", ["used_value" => $depa], ["varname" => "standard_chat_dep"]);
                    $jakdb->update("settings", ["used_value" => $depas], ["varname" => "standard_support_dep"]);
                    $jakdb->update("settings", ["used_value" => $depaf], ["varname" => "standard_faq_cat"]);


                    $ss = true;

                } else {

                    $errors['e'] = $jkl['e'];
                    $errors = $errors;
                }


            } elseif (jak_get_access("blocklist", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
                $jakdb->update("settings", ["used_value" => $jkp['ip_block']], ["varname" => "ip_block"]);
                $jakdb->update("settings", ["used_value" => $jkp['email_block']], ["varname" => "email_block"]);
                $ss = true;
            }

            if ($ss) {

                // Now let us delete the define cache file
                $cachedefinefile = APP_PATH.JAK_CACHE_DIRECTORY.'/define.php';
                if (file_exists($cachedefinefile)) {
                    unlink($cachedefinefile);
                }

                // Write the log file each time someone login after to show success
                JAK_base::jakWhatslog('', JAK_USERID, 0, 16, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $jakuser->getVar("username"), $_SERVER['REQUEST_URI'], $ipa, $valid_agent);

                $_SESSION["successmsg"] = $jkl['g14'];
                jak_redirect($_SESSION['LCRedirect']);

            }
        }
    }

    // Get the unique api key
    $api_key = hash_hmac('md5', FULL_SITE_DOMAIN.JAK_O_NUMBER, DB_PASS_HASH);
    $api_key1 = hash_hmac('md5', JAK_O_NUMBER.FULL_SITE_DOMAIN, DB_PASS_HASH);

    // Call the settings function
    $lang_files = jak_get_lang_files();

    // Get all sound files
    $sound_files = jak_get_sound_files();

    // Get all templates
    $templates = jak_get_templates();

    // Get all departments / categories
    $JAK_DEPARTMENTS = $jakdb->select($jaktable2, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
    $JAK_DEP_SUPPORT = $jakdb->select($jaktable1, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
    $JAK_CAT_FAQ = $jakdb->select($jaktable3, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
    $JAK_CMS_PAGES = $jakdb->select($jaktable4, ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
    // Get all chat widgets
    $CHATWIDGET_ALL = $jakdb->select($jaktable9, ["id", "title"]);

    // How often has it been changed
    $totalChange = $jakdb->count("whatslog", ["whatsid" => 16]);

    // Count all files
    $totalFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APP_PATH.JAK_FILES_DIRECTORY), RecursiveIteratorIterator::SELF_FIRST);

    // Title and Description
    $SECTION_TITLE = $jkl["m5"];
    $SECTION_DESC = "";

    // Include the javascript file for results
    $js_file_footer = 'js_settings.php';

    // Call the template
    if (jak_get_access("settings", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) {
        $template = 'setting.php';
    } else {
        $template = 'blockvisitors.php';
    }

}
?>