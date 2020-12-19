<?php

$urlonly = parse_url(filter_var($_GET['crossurl'], FILTER_SANITIZE_URL));
$crossurl = $urlonly["scheme"].'://'.$urlonly["host"].(isset($urlonly['port']) ? ':'.$urlonly['port'] : '');

header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");
header("Access-Control-Allow-Origin: ".$crossurl);
header('Access-Control-Allow-Credentials: true');

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// filter url inputs
function jak_valid_get_cross($value) {
    $value = html_entity_decode($value);
    $value = preg_replace('/[^\w\-.]/', '', $value);
    return trim(filter_var($value, FILTER_SANITIZE_STRING));
}

// Check with callback
function is_valid_callback($input) {
    $identifier_syntax
      = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
      'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
      'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
      'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
      'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
      'private', 'public', 'yield', 'interface', 'package', 'protected', 
      'static', 'null', 'true', 'false');

    return preg_match($identifier_syntax, $input)
        && ! in_array(mb_strtolower($input, 'UTF-8'), $reserved_words);
}

// Check with callback 2
function is_valid_callback2($input) {
    return !preg_match( '/[^0-9a-zA-Z\$_]|^(abstract|boolean|break|byte|case|catch|char|class|const|continue|debugger|default|delete|do|double|else|enum|export|extends|false|final|finally|float|for|function|goto|if|implements|import|in|instanceof|int|interface|long|native|new|null|package|private|protected|public|return|short|static|super|switch|synchronized|this|throw|throws|transient|true|try|typeof|var|volatile|void|while|with|NaN|Infinity|undefined)$/', $input);
}

$callback = false;
$callback = jak_valid_get_cross($_GET['callback']);

if (!isset($callback) || !is_valid_callback($callback) || !is_valid_callback2($callback)) {
	header('status: 400 Bad Request', true, 400);
} else {
	header('content-type: application/javascript; charset=utf-8');
}

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && isset($_GET['id']) && !is_numeric($_GET['id'])) die(json_encode(array('status' => false, 'error' => "No valid ID.")));

if (!file_exists('../config.php')) die('include/[clientchat_cross.php] config.php not exist');
require_once '../config.php';

// We do not load any widget code if we are on hosted and expiring date is true.
if ($jakhs['hostactive'] && JAK_VALIDTILL != 0 && (JAK_VALIDTILL < time())) die(json_encode(array('status' => false, 'error' => "Account expired.")));

// Get the referrer URL
$referrer = selfURL();

// Some reset
$widgethtml = $slideimg = '';

// Now check the button id
$cachewidget = APP_PATH.JAK_CACHE_DIRECTORY.'/widget'.$_GET['id'].'.php';
if (file_exists($cachewidget)) {
	include_once $cachewidget;

	// Extra check if session exists.
	if (!isset($_SESSION["crossurl"]) || !isset($_COOKIE["crossurl"]) || $_SESSION["crossurl"] != $crossurl || $_COOKIE["crossurl"] != $crossurl) {

		// Now let's check if we have the url is whitelisted
		if (isset($crossurl) && !empty($crossurl) && isset($jakwidget['whitelist']) && !empty($jakwidget['whitelist']) && in_array($crossurl, explode(",", $jakwidget['whitelist']))) {
			$_SESSION["crossurl"] = $crossurl;
			JAK_base::jakCookie('crossurl', $crossurl, JAK_COOKIE_TIME, JAK_COOKIE_PATH);
		} else {
			die(json_encode(array('status' => false, 'error' => "Domain is not white listed.")));
		}
	}

	// Get the client browser
	$ua = new Browser();

	// Is a robot or from facebook just die
	if ($ua->isRobot() || $ua->isFacebook()) die(json_encode(array('status' => false, 'error' => "Robots/Facebook do not need a live chat.")));
	// Is mobile
	if ($ua->isMobile()) {
		$_SESSION["clientismobile"] = true;
	} else {
		unset($_SESSION["clientismobile"]);
	}

	// Language file
	$lang = $jakwidget['lang'];
	if (isset($_GET['lang']) && !empty($_GET['lang'])) $lang = $_GET['lang'];

	// Import the language file
	if ($lang && file_exists(APP_PATH.'lang/'.strtolower($lang).'.php')) {
	    include_once(APP_PATH.'lang/'.strtolower($lang).'.php');
	} else {
	    include_once(APP_PATH.'lang/'.JAK_LANG.'.php');
	    $lang = JAK_LANG;
	}

	// Set time on site in session so we can fire the pro active at the right time
	if (!isset($_SESSION['jkchatontime']) || !isset($_SESSION['jkchatontime']) && !isset($_SESSION['jkwio'])) $_SESSION['jkchatontime'] = time();

	// Set the cookie
	if (!isset($_COOKIE["activation"])) JAK_base::jakCookie('activation', 'visited', JAK_COOKIE_TIME, JAK_COOKIE_PATH);
		
	if (isset($_COOKIE["activation"]) || session_id()) {

		// Chat has not been placed on HelpDesk 3 itself
		if (!isset($_SESSION['jkwio'])) {
		
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

			$btstat = $jakdb->update("buttonstats", ["depid" => $jakwidget['depid'], "opid" => $jakwidget['opid'], "hits[+]" => 1, "referrer" => $referrer, "ip" => $ipa, "lasttime" => $jakdb->raw("NOW()")], ["session" => $_SESSION['rlbid']]);
			
			// Update database first to see who is online!
			if (!$btstat->rowCount()) {
					
				// get client information
				$depid = 0;
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

				$jakdb->insert("buttonstats", ["depid" => $jakwidget['depid'], "opid" => $jakwidget['opid'], "referrer" => $referrer, "firstreferrer" => $referrer, "agent" => $clientsystem, "hits" => 1, "ip" => $ipa, "country" => $country_name, "countrycode" => $country_code, "latitude" => $country_lat, "longitude" => $country_lng, "session" => $_SESSION["rlbid"], "time" => $jakdb->raw("NOW()"), "lasttime" => $jakdb->raw("NOW()")]);
				
			}
				
			if (isset($_SESSION['jrc_userid']) && isset($_SESSION['convid'])) {

				// insert new referrer
				$jakdb->insert("transcript", ["name" => $_SESSION['jrc_name'], "message" => $jkl["g55"].$referrer, "user" => $_SESSION['jrc_userid'], "convid" => $_SESSION['convid'], "time" => $jakdb->raw("NOW()"), "class" => "notice", "plevel" => 2]);

				$lastrefid = $jakdb->id();

				$jakdb->update("checkstatus", ["newo" => $lastrefid, "typec" => 0], ["convid" => $_SESSION['convid']]);

				// The correct url
				$chatstarturl = str_replace('include/', '', JAK_rewrite::jakParseurl('chat', '1'));
				$chatstarturlpop = str_replace('include/', '', JAK_rewrite::jakParseurl('chat', '2'));
			} else {

				// Now let's check if we are on a page where we do not want to show the chat aka Black List
				if (isset($HD_BLACKLIST) && !empty($HD_BLACKLIST)) if (filter_var($referrer, FILTER_VALIDATE_URL) && in_array($referrer, $HD_BLACKLIST)) die(json_encode(array('status' => false, 'error' => "Do not show chat on this page.")));

				// The correct url
				if ($jakwidget['chat_direct']) {
					$chatstarturl = str_replace('include/', '', JAK_rewrite::jakParseurl('start', '1'));
					$chatstarturlpop = str_replace('include/', '', JAK_rewrite::jakParseurl('start', '2'));
				} else {
					$chatstarturl = str_replace('include/', '', JAK_rewrite::jakParseurl('quickstart', '1'));
					$chatstarturlpop = str_replace('include/', '', JAK_rewrite::jakParseurl('quickstart', '2'));
				}

			}

		// Chat has been placed on HelpDesk 3 itself
		} else {

			$btstat = $jakdb->update("buttonstats", ["depid" => $jakwidget['depid'], "opid" => $jakwidget['opid'], "lasttime" => $jakdb->raw("NOW()")], ["session" => $_SESSION['rlbid']]);

			if (isset($_SESSION['jrc_userid']) && isset($_SESSION['convid'])) {

				// insert new referrer
				$jakdb->insert("transcript", ["name" => $jkl["g56"], "message" => $jkl["g55"].$referrer, "convid" => $_SESSION['convid'], "time" => $jakdb->raw("NOW()"), "class" => "notice", "plevel" => 2]);

				$jakdb->update("checkstatus", ["newo" => 1, "typec" => 0], ["convid" => $_SESSION['convid']]);
			} else {

				// Now let's check if we are on a page where we do not want to show the chat aka Black List
				if (isset($HD_BLACKLIST) && !empty($HD_BLACKLIST)) if (filter_var($referrer, FILTER_VALIDATE_URL) && in_array($referrer, $HD_BLACKLIST)) die(json_encode(array('status' => false, 'error' => "Do not show chat on this page.")));

			}
		}
	}

	// We have a holiday mode and hide chat or no one is online and the chat widget is set to hide
	if (!isset($_SESSION['jrc_userid']) && !isset($_SESSION['convid']) && JAK_HOLIDAY_MODE == 2) {
		die(json_encode(array('status' => false, 'error' => "No operator online and chat settings are set to hide.")));
	}

	// We have custom vars
	if (!empty($_GET['name']) || !empty($_GET['email']) || !empty($_GET['msg'])) $_SESSION['custom_vars'] = filter_var(jak_input_filter($_GET['name']), FILTER_SANITIZE_STRING).':#:'.filter_var($_GET['email'], FILTER_SANITIZE_EMAIL).':#:'.filter_var(jak_input_filter($_GET['msg']), FILTER_SANITIZE_STRING);

	// Write the chat language
	$_SESSION['widgetlang'] = $lang;

	// Write the chat widget id into a session
	$_SESSION['widgetid'] = $_GET['id'];

	// Run the safari fix temporaly until the socket version is ready.
	if ($_GET['safari'] == true) {

		// Now get the available departments
		$online_op = false;
		if (JAK_HOLIDAY_MODE != 0) {
			$online_op = false;
			$_SESSION['lastopstatus'] = false;
		} else {
			if (isset($_SESSION['widgetid'])) $online_op = online_operators($HD_DEPARTMENTS, $jakwidget['depid'], $jakwidget['opid']);
			$_SESSION['lastopstatus'] = (!empty($online_op) ? true : false);
		}

		$btnimgmodify = $jakwidget['buttonimg'];
		if (isset($_SESSION["clientismobile"]) && isset($jakwidget['mobilebuttonimg']) && !empty($jakwidget['mobilebuttonimg'])) {
			$btnimgmodify = $jakwidget['mobilebuttonimg'];
		}

		// Get image size
		if ($online_op) {
			list($btnwidth, $btnheight) = getimagesize(APP_PATH.JAK_FILES_DIRECTORY.'/buttons/'.$btnimgmodify);
			$buttonimg = $btnimgmodify;
			// The correct url
			if ($jakwidget['chat_direct']) {
				$chatstarturlpop = str_replace('include/', '', JAK_rewrite::jakParseurl('start', '2'));
			} else {
				$chatstarturlpop = str_replace('include/', '', JAK_rewrite::jakParseurl('quickstart', '2'));
			}
		} else {
			list($btnwidth, $btnheight) = getimagesize(APP_PATH.JAK_FILES_DIRECTORY.'/buttons/'.str_replace("_on", "_off", $btnimgmodify));
			$buttonimg = str_replace("_on", "_off", $btnimgmodify);
			$chatstarturlpop = str_replace('include/', '', JAK_rewrite::jakParseurl('contact', '2'));
		}

		if (isset($_SESSION['jrc_userid']) && isset($_SESSION['convid'])) {
			$chatstarturlpop = str_replace('include/', '', JAK_rewrite::jakParseurl('chat', '2'));
		}

		// Get the width of the button and add 50px for the hover
		$btnwidthdiv = 'style="width:'.($btnwidth+130).'px;"';
		$btnimg = '<img src="'.str_replace('include/', '', BASE_URL).JAK_FILES_DIRECTORY.'/buttons/'.$buttonimg.'" width="'.$btnwidth.'" height="'.$btnheight.'" alt="live chat">';
		$safaribtn = '<div id="lcjframesize" class="live-chat-button-container animated"><a href="'.$chatstarturlpop.'" onclick="if(navigator.userAgent.toLowerCase().indexOf(\'opera\') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open(\''.$chatstarturlpop.'\', \'livechat3_popup_window\', \'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=780,height=640,resizable=1\');this.newWindow.focus();this.newWindow.opener=window;return false;">'.$btnimg.'</a></div>';
	}

	// page to load
	$pageload = JAK_rewrite::jakParseurl('btn', $_GET['id'], $lang, $jakwidget['depid'], $jakwidget['opid']);
	if (isset($_GET['p']) && $_GET['p'] == "start") $pageload = $chatstarturl;

	if (isset($_GET['popup']) && $_GET['popup'] == true) {
		die(json_encode(array('status' => true, 'chaturl' => $chatstarturlpop, 'ctitle' => $jakwidget['title'])));
	} else {
		die(json_encode(array('status' => true, 'widgethtml' => '<iframe id="livesupportchat'.$_GET['id'].'" seamless="seamless" allowtransparency="true" style="background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; border: 0px none; bottom: 0px; float: none; height: 100%; left: 0px; margin: 0px; padding: 0px; position: absolute; right: 0px; top: 0px; width: 100%;" scrolling="no" src="'.str_replace('include/', '', $pageload).'"></iframe>', 'useapache' => JAK_USE_APACHE, 'url' => str_replace('include/', '', BASE_URL), 'widgetpos' => jak_html_widget_css($jakwidget['floatpopup'], $jakwidget['floatcss_safari'], $jakwidget['floatcss_safari']), 'btnwidth' => $btnwidth, 'btnheight' => $btnheight, 'safaribtn' => $safaribtn)));
	}

} else {
	die(json_encode(array('status' => false, 'error' => "No Widget available with this ID.")));
}
?>