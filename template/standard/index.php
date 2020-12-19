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

// Get the stuff for the CMS
include_once 'include/functions_cms.php';

// Get the important database table
$jaktable = 'cms_pages';
$jaktable1 = 'translations';

// Get the referrer URL
$referrer = JAK_rewrite::jakParseurl($page, $page1, $page2);

// Some reset
$widgethtml = $slideimg = $JAK_PAGINATE = '';

// Get the client browser
$ua = new Browser();

// Is a robot just die
if ($ua->isRobot()) die(json_encode(array('status' => false, 'error' => "Robots do not need a live chat.")));
// Is mobile
if ($ua->isMobile()) {
	$_SESSION["clientismobile"] = true;
} else {
	unset($_SESSION["clientismobile"]);
}

// Set time on site in session so we can fire the pro active at the right time
if (!isset($_SESSION['jkchatontime'])) $_SESSION['jkchatontime'] = time();

// Set the cookie
if (!isset($_COOKIE["activation"])) JAK_base::jakCookie('activation', 'visited', JAK_COOKIE_TIME, JAK_COOKIE_PATH);
		
if (isset($_COOKIE["activation"]) || session_id()) {
		
	if (!isset($_SESSION['rlbid'])) {
			
		if (isset($_COOKIE['rlbid'])){
			$_SESSION['rlbid'] = $_COOKIE['rlbid'];
		} else {
			$salt = rand(100, 99999);
			$rlbid = $salt.time();
			JAK_base::jakCookie('rlbid', $rlbid, 31536000, JAK_COOKIE_PATH);
			$_SESSION['rlbid'] = $rlbid;
		}
				
	}
			
	// Now get the hits and referrer into sessions
	$_SESSION['jkchathits'] = (isset($_SESSION['jkchathits']) ? $_SESSION['jkchathits'] + 1 : 1);
	$_SESSION['jkchatref'] = $referrer;

	$btstat = $jakdb->update("buttonstats", ["clientid" => JAK_CLIENTID, "hits[+]" => 1, "referrer" => $referrer, "ip" => $ipa, "lasttime" => $jakdb->raw("NOW()")], ["session" => $_SESSION['rlbid']]);
		
	// Update database first to see who is online!
	if (!$btstat->rowCount()) {
				
		// get client information
		$clientsystem = $ua->getPlatform().' - '.$ua->getBrowser(). " " . $ua->getVersion();

		// Country Stuff
		$country_name = 'Disabled';
		$country_code = 'xx';
		$city = 'Disabled';
		$country_lng = $country_lat = '';

		// A "geoData" cookie has been previously set by the script, so we will use it
		if (isset($_COOKIE['WIOgeoData'])) {
			// Always escape any user input, including cookies:
			list($city, $country_name, $country_code, $country_lat, $country_lng) = explode('|', strip_tags(base64_decode($_COOKIE['WIOgeoData'])));
		} else {

			// Now let's check if the ip is ipv4
			if (JAK_SHOW_IPS && $ipa && !$ua->isRobot()) {

				$ipc = curl_init();
				curl_setopt($ipc, CURLOPT_URL, "https://ipgeo.jakweb.ch/api/".$ipa);
				curl_setopt($ipc, CURLOPT_HEADER, false);
				curl_setopt($ipc, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ipc);
				curl_close($ipc);

				$getinfo = json_decode($response, true);

				if (isset($getinfo) && !empty($getinfo)) {

					$country_name = ucwords(strtolower(filter_var($getinfo["country"]["name"], FILTER_SANITIZE_STRING)));
					$country_code = strtolower(filter_var($getinfo["country"]["code"], FILTER_SANITIZE_STRING));
					$city = filter_var($getinfo["city"], FILTER_SANITIZE_STRING);
					$country_lng = filter_var($getinfo["location"]["longitude"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
					$country_lat = filter_var($getinfo["location"]["latitude"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

				}

			}

			// Setting a cookie with the data, which is set to expire in a week:
			JAK_base::jakCookie('WIOgeoData', base64_encode($city.'|'.$country_name.'|'.$country_code.'|'.$country_lat.'|'.$country_lng), 604800, JAK_COOKIE_PATH);

		}

		$jakdb->insert("buttonstats", ["depid" => 0, "opid" => 0, "clientid" => JAK_CLIENTID, "referrer" => $referrer, "firstreferrer" => $referrer, "agent" => $clientsystem, "hits" => 1, "ip" => $ipa, "country" => $country_name, "countrycode" => $country_code, "latitude" => $country_lat, "longitude" => $country_lng, "session" => $_SESSION["rlbid"], "time" => $jakdb->raw("NOW()"), "lasttime" => $jakdb->raw("NOW()")]);
			
	}
			
	if (isset($_SESSION['jrc_userid']) && isset($_SESSION['convid'])) {

		// insert new referrer
		$jakdb->insert("transcript", ["name" => $jkl["g56"], "message" => $jkl["g55"].$referrer, "convid" => $_SESSION['convid'], "time" => $jakdb->raw("NOW()"), "class" => "notice", "plevel" => 2]);

		$jakdb->update("checkstatus", ["newo" => 1, "typec" => 0], ["convid" => $_SESSION['convid']]);
	}

	// We have already updated certain things
	if (!isset($_SESSION['jkwio']) || $_SESSION['jkwio'] == false) $_SESSION['jkwio'] = true;
}

// Get the database stuff
$row = $jakdb->get($jaktable, "*", ["id" => $pageid]);

// Errors in Array
$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$jkp = $_POST;
	
	if (isset($_POST['send_email'])) {

	if (empty($jkp['gname']) || strlen(trim($jkp['gname'])) <= 2) {
		$errors['gname'] = $jkl['e'];
	}
		
	if (JAK_EMAIL_BLOCK) {
		$blockede = explode(',', JAK_EMAIL_BLOCK);
		if (in_array($jkp['gemail'], $blockede) || in_array(strrchr($jkp['gemail'], "@"), $blockede)) {
			$errors['gemail'] = $jkl['e10'];
		}
	}

	if (!empty(JAK_DSGVO_CONTACT)) {
		if (!isset($jkp['gdsgvo'])) {
		    $errors['gdsgvo'] = $jkl['e19'];
		}
	}
		
		if ($jkp['gemail'] == '' || !filter_var($jkp['gemail'], FILTER_VALIDATE_EMAIL)) {
		    $errors['gemail'] = $jkl['e1'];
		}
		
		if (isset($jkp['gphone']) && !empty($jkp['gphone']) && !preg_match('^((\+)?(\d{2})[-])?(([\(])?((\d){3,5})([\)])?[-])|(\d{3,5})(\d{5,8}){1}?$^', $jkp['gphone'])) {
		    $errors['gphone'] = $jkl['e14'];
		}
		
		if (empty($jkp['gmessage']) || strlen(trim($jkp['gmessage'])) <= 2) {
		    $errors['gmessage'] = $jkl['e2'];
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
	            $errorsA['recaptcha'] = $jkl['e12'].'<br>';
	        }
	    }
		
		if (JAK_CAPTCHA) {
			
			$human_captcha = explode(':#:', $_SESSION['jrc_captcha']);
			
			if ($jkp[$human_captcha[0]] == '' || $jkp[$human_captcha[0]] != $human_captcha[1]) {
				$errors['human'] = $jkl['e12'];
			}
		}
		
		if (count($errors) > 0) {
			
			/* Outputtng the error messages */
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			
				header('Cache-Control: no-cache');
				die('{"status":0, "errors":'.json_encode($errors).'}');
				
			} else {
			
				$errors = $errors;
			}
			
		} else {
		
			// Country stuff
			$countryName = 'Disabled';
			$countryAbbrev = 'xx';
			$city = 'Disabled';
			$countryLong = $countryLat = '';
				
			// if ip is valid do the whole thing
			if ($ipa && !$ua->isRobot()) {
				
				if (isset($_COOKIE['WIOgeoData'])) {
					// A "geoData" cookie has been previously set by the script, so we will use it
							
					// Always escape any user input, including cookies:
					list($city, $countryName, $countryAbbrev, $countryLat, $countryLong) = explode('|', strip_tags(base64_decode($_COOKIE['WIOgeoData'])));
							
				}
					
			}
			
			// Get the referrer
			$rowref = '';
			if (!isset($_SESSION['rlbid'])) {
			
				if (isset($_COOKIE['rlbid'])){
				   $_SESSION['rlbid'] = $_COOKIE['rlbid'];
				} else {
					$salt = rand(100, 99999);
					$rlbid = $salt.time();
					JAK_base::jakCookie('rlbid', $rlbid, 31536000, JAK_COOKIE_PATH);
					$_SESSION['rlbid'] = $rlbid;
				}
				
			} else {
				$rowref = $jakdb->get("buttonstats", "referrer", ["session" => $_SESSION['rlbid']]);
			}
			
			// Get the department for the contact form if set
			$op_email = JAK_EMAIL;
			$depid = 0;
			
			// Reset phone var
			$cphone = '';
			
			$listform = $jkl["g27"].': '.$jkp['gname'].'<br />';
			$listform .= $jkl["g47"].': '.$jkp['gemail'].'<br />';
			if (isset($jkp['gphone'])) {
				$listform .= $jkl["g50"].': '.$jkp['gphone'].'<br />';
				$cphone = $jkp['gphone'];
			}
			$listform .= 'IP: '.$ipa.'<br />';
			$listform .= $jkl["g28"].': '.$jkp['gmessage'];
			
			// We save the data
			$jakdb->insert("contacts", [ 
			"depid" => $depid,
			"name" => $jkp['gname'],
			"email" => $jkp['gemail'],
			"phone" => $cphone,
			"message" => $jkp['gmessage'],
			"ip" => $ipa,
			"city" => $city,
			"country" => $countryName,
			"countrycode" => $countryAbbrev,
			"longitude" => $countryLong,
			"latitude" => $countryLat,
			"referrer" => $rowref,
			"sent" => $jakdb->raw("NOW()")]);
			
			// We send the email
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
				$mail->AddReplyTo($jkp['email'], $jkp['name']);
				$mail->SetFrom($op_email);
				$mail->AddAddress($op_email, JAK_TITLE);
				// CC? Yes it does, send it to following address
				if (!empty(JAK_EMAILCC)) {
					$emailarray = explode(',', JAK_EMAILCC);
					
					if (is_array($emailarray)) foreach($emailarray as $ea) { $mail->AddCC(trim($ea)); } 
					
				}
				
			} else {
			
				$mail->SetFrom($op_email, JAK_TITLE);
				$mail->AddAddress($op_email, JAK_TITLE);
				$mail->AddReplyTo($jkp['email'], $jkp['name']);

				// CC? Yes it does, send it to following address
				if (!empty(JAK_EMAILCC)) {
					$emailarray = explode(',', JAK_EMAILCC);
					
					if (is_array($emailarray)) foreach($emailarray as $ea) { $mail->AddCC(trim($ea)); } 
					
				}
			
			}
			
			$mail->Subject = JAK_TITLE;
			$mail->AltBody = $jkl['g45'];
			$mail->MsgHTML($listform);
			
			if ($mail->Send()) {
			
				unset($_SESSION['jrc_captcha']);
				unset($_SESSION['chatbox_redirected']);
				
				// Ajax Request
				if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				
					header('Cache-Control: no-cache');
					die(json_encode(array('status' => 1, 'html' => $jkl["g65"])));
					
				} else {
				
			        jak_redirect($_SERVER['HTTP_REFERER']);			    
			    }
			}
		}

	}

	if (isset($jkp['search_now'])) {
	
	    if (empty($page1) && $jkp['smart_search'] == '' || $jkp['smart_search'] == $jkl['hd']) {
	        $errors['e'] = $jkl['hd2'];
	    }
	
	    if (empty($page1) && strlen($jkp['smart_search']) < '3') {
	        $errors['e1'] = $jkl['hd3'];
	    }

	    if (count($errors) > 0) {
        	$errors = $errors;
    	} else {

    	}
	}
}

// Now let's check the hits cookie
if (!jak_cookie_voted_hits($jaktable, $row['id'], 'hits')) {

	jak_write_vote_hits_cookie($jaktable, $row['id'], 'hits');
	
	// Update hits each time we have a new customer only
	$jakdb->update($jaktable, ["hits[+]" => 1], ["id" => $pageid]);
}

// Get the url session
$_SESSION['jak_lastURL'] = JAK_rewrite::jakParseurl($page);

// Get the header navigation
$mheader = array(
    'items' => array(),
    'parents' => array()
);
// Builds the array lists with data from the menu table
foreach ($jakpages as $items) {
	
	if ($items["showheader"] == 1) {
		if ($items["ishome"] == 1) $items["url_slug"] = "";
		// Creates entry into items array with current menu item id ie. $menu['items'][1]
	    $mheader['items'][$items['id']] = $items;
	    // Creates entry into parents array. Parents array contains a list of all items with children
	    $mheader['parents'][0][] = $items['id'];
	}
}

// Get the footer navigation
$mfooter = array(
    'items' => array(),
    'parents' => array()
);
// Builds the array lists with data from the menu table
foreach ($jakpages as $itemf) {
	
	if ($itemf["showfooter"] == 1) {
		$itemf['title'] = '<i class="fa fa-chevron-right"></i> '.$itemf['title'];
		// Creates entry into items array with current menu item id ie. $menu['items'][1]
	    $mfooter['items'][$itemf['id']] = $itemf;
	    // Creates entry into parents array. Parents array contains a list of all items with children
	    $mfooter['parents'][0][] = $itemf['id'];
	}
}

// Get the translations
$cms_text = $jakdb->select($jaktable1, ["id", "cmsid", "title", "description"], ["AND" => ["lang" => $BT_LANGUAGE, "cmsid[!]" => 0]]);
if (JAK_CLIENTID && empty($cms_text)) $cms_text = $jakdb->select($jaktable1, ["id", "cmsid", "title", "description"], ["AND" => ["lang" => JAK_LANG, "cmsid[!]" => 0]]);

if (JAK_USERID && jak_get_access("answers", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS) && empty($cms_text)) {
	// Create the missing fields in the table so the operator can translate
	$jakdb->exec("INSERT INTO ".JAKDB_PREFIX."translations (`id`, `lang`, `chat_dep`, `support_dep`, `faq_cat`, `priorityid`, `customfieldid`, `toptionid`, `cmsid`, `title`, `description`, `faq_url`, `time`) VALUES
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 1, '<h3>\r\n    Smart FAQ Search\r\n</h3>', '<p class=\"txt-small\">Search through our FAQ with the build in smart search.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 2, '<h3><strong>Help</strong> <small>Desk 3</small></h3>', '<p class=\"txt-small\">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>\r\n<p class=\"mb-0\">Phone: <strong>+41 (0) 77 482 57 15</strong></p>\r\n<p>Email: <strong>youremail@domain.com</strong></p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 3, '<h4 class=\"title-green\">Features</h4>', NULL, NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 4, '<h4 class=\"title-green\">About Us</h4>', NULL, NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 5, '<h3 class=\"title-green\">\r\n    Latest News\r\n</h3>', '<p>Our blog will inform you about the latest news about our company.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 6, '<h4>Contact Form</h4>', '<p>Please fill at least the name, email and message field.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 7, '<h3 class=\"title-green\">Latest FAQ Articles</h3>', '<p>The latest FAQ article, browse through our frequently asked question database and get smart.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 8, '<h3 class=\"title-green\">Our FAQ Article</h3>', '<p>Check all our frequently asked questions and get smart.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 9, '<h3 class=\"title-green\">Latest Support Tickets</h3>', '<p>Our latest support tickets, grab some knowledge from our database.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 10, '<h3 class=\"title-green\">Support Tickets</h3>', '<p>Grab some knowledge from our public support tickets.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 11, '<h3>All Blog Articles</h3>', '<p>Check our latest articles now and in full length.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 12, '<h3>Client Dashboard</h3>', '<p>Welcome to your dashboard, you will find all your tickets, payments and profile information on this page.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 13, '<h3>Billing History</h3>', '<p>Your billing history on our site</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 14, '<h3>Subscriptions</h3>', '<p>Select the package that suits you.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 15, '<h3>Edit Profile</h3>', '<p>Edit your profile add your personal avatar and get some nuts.</p>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 17, '<h4>Mobile Apps</h4>', '<ul class=\"social-buttons\"><li><a href=\"https://play.google.com/store/apps/details?id=ch.jakweb.livechat\" class=\"btn btn-just-icon btn-link btn-android\"><i class=\"material-icons\">phone_android</i></a></li><li><a href=\"https://itunes.apple.com/us/app/live-chat-3-lcps/id1229573974\" class=\"btn btn-just-icon btn-link btn-apple\"><i class=\"material-icons\">phone_iphone</i></a></li></ul><h5>Numbers Don&apos;t Lie</h5><h4>14.521<small> Freelancers</small></h4><h4>1.423.183<small> Transactions</small></h4>', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 18, '', '', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 19, '', '', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 20, '', '', NULL, NOW()),
(NULL, '".$BT_LANGUAGE."', 0, 0, 0, 0, 0, 0, 21, '', '', NULL, NOW())");
	jak_redirect(BASE_URL);
}

// Make sure the page is available for everyone.
if ($row["access"] == 3 && !JAK_USERISLOGGED) {
	include_once APP_PATH.'template/standard/client.php';
} else {
	// Get the correct page
	if ($row["prepage"] == JAK_CLIENT_URL) {
		include_once APP_PATH.'template/standard/client.php';
	} elseif ($row["prepage"] == JAK_SEARCH_URL) {
		include_once APP_PATH.'template/standard/search.php';
	} elseif ($row["prepage"] == JAK_SUPPORT_URL) {
		include_once APP_PATH.'template/standard/support.php';
	} elseif ($row["prepage"] == JAK_FAQ_URL) {
		include_once APP_PATH.'template/standard/faq.php';
	} elseif ($row["prepage"] == JAK_BLOG_URL) {
		include_once APP_PATH.'template/standard/blog.php';
	} else {
		include_once APP_PATH.'template/standard/tplblocks/page.php';
	}
}
?>