<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2019 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_ADMIN_PREVENT_ACCESS')) die('You cannot access this file directly.');

// Login IN
if (!empty($_POST['action']) && $_POST['action'] == 'login') {
	
	$lcookies = false;
    $username = $_POST['username'];
    $userpass = $_POST['password'];
    if (isset($_POST['lcookies'])) $lcookies = $_POST['lcookies'];
    
    // Security fix
    $valid_agent = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);
    $valid_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    $valid_username = filter_var($username, FILTER_SANITIZE_STRING);
    
    // Write the log file each time someone tries to login before
    JAK_base::jakWhatslog('', 0, 0, 1, 0, (isset($_COOKIE['WIOgeoData']) ? $_COOKIE['WIOgeoData'] : ''), $valid_username, $_SERVER['REQUEST_URI'], $valid_ip, $valid_agent);

    $user_check = $jakuserlogin->jakCheckuserdata($valid_username, $userpass);
    if ($user_check == true) {
    
    	// Now login in the user
        $jakuserlogin->jakLogin($user_check["username"], $userpass, $lcookies);
        
        // Write the log file each time someone login after to show success
        JAK_base::jakWhatslog('', $user_check["id"], 0, 2, 0, '', $user_check["username"], '', $valid_ip, '');
        
        // Unset the recover message
        if (isset($_SESSION['password_recover'])) unset($_SESSION['password_recover']);
        
        $is_dp = $jakdb->get("user", ["id"], ["AND" => ["username" => $username, "access" => 1, "is_dp" => 1]]); 

        if (!empty($is_dp)) {
            jak_redirect(BASE_URL_HOME."droppoint/");
        } else {
            if (isset($_SESSION['LCRedirect']) && strpos($_SESSION['LCRedirect'], JAK_OPERATOR_LOC) !== false) {
                jak_redirect($_SESSION['LCRedirect']);
            } else {
                jak_redirect(BASE_URL);
            }
        }
    } else {
        $ErrLogin = $jkl['l'];
    }
}

// Forgot password
 if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['forgotP'])) {
 	$jkp = $_POST;
 	
 	$errors = array();
 
 	if ($jkp['lsE'] == '' || !filter_var($jkp['lsE'], FILTER_VALIDATE_EMAIL)) {
 	    $errors['e'] = $jkl['e19'];
 	}
 	
 	// transform user email
    $femail = filter_var($_POST['lsE'], FILTER_SANITIZE_EMAIL);
    $fwhen = time();
 	
 	// Check if this user exist
    $user_check = $jakuserlogin->jakForgotpassword($femail, $fwhen);
     
    if (!$user_check) {
        $errors['e'] = $jkl['e19'];
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
            $oname = $jakdb->get("user", "name", ["AND" => ["email" => $femail, "access" => 1]]);
         	
         	$mail->AddAddress($femail);
         	
         	$mail->Subject = JAK_TITLE.' - '.$jkl['l13'];
         	$body = sprintf($jkl['l14'], $oname, '<a href="'.JAK_rewrite::jakParseurl('forgot-password', $fwhen).'">'.JAK_rewrite::jakParseurl('forgot-password', $fwhen).'</a>', JAK_TITLE);
         	
         	$mail->MsgHTML($body);
         	$mail->AltBody = strip_tags($body);
         	
         	if ($mail->Send()) {
         		$_SESSION["infomsg"] = $jkl["l7"];
         		jak_redirect(BASE_URL);	
         	}
 
     } else {
         $errorfp = $errors;
     }
}

// Title and Description
$SECTION_TITLE = $jkl["l3"];
$SECTION_DESC = "";

// Include the javascript file for results
$js_file_footer = 'js_login.php';

$template = 'login.php';

?>