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

if (!file_exists('../config.php')) die('include/[clientchat_cross.php] config.php not exist');
require_once '../config.php';

// We do not load any widget code if we are on hosted and expiring date is true.
if ($jakhs['hostactive'] && JAK_VALIDTILL != 0 && (JAK_VALIDTILL < time())) die(json_encode(array('status' => false, 'error' => "Account expired.")));

// Get the client browser
$ua = new Browser();

// Is a robot just die
if ($ua->isRobot() || $ua->isFacebook()) die(json_encode(array('status' => false, 'error' => "Robots do not need a live chat.")));

// Set the session for the embed part
if (!isset($_SESSION["webembed"])) $_SESSION["webembed"] = true;

// Now let's set the category id if we have any
$faqurl = str_replace('include/', '', JAK_rewrite::jakParseurl(JAK_FAQ_URL));
if (isset($_GET['catid']) && is_numeric($_GET['catid'])) $faqurl = str_replace('include/', '', JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'c', $_GET['catid']));

// Now get the support frame into the div.
die(json_encode(array('status' => true, 'widgethtml' => '<iframe id="hd3support" seamless="seamless" allowtransparency="true" style="background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; border: 0px none; bottom: 0px; height: 100%; margin: 0px; padding: 0px; width: 100%;" scrolling="no" src="'.$faqurl.'"></iframe>')));
?>