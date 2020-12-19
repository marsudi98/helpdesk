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

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && isset($_GET['id']) && !is_numeric($_GET['id'])) die(json_encode(array('status' => false, 'error' => "No valid ID.")));

if (!file_exists('../config.php')) die('include/[clientchat.php] config.php not exist');
require_once '../config.php';

if (isset($_SESSION["crossurl"])) unset($_SESSION["crossurl"]);

// We do not load any widget code if we are on hosted and and expiring date is true.
if ($jakhs['hostactive'] && JAK_VALIDTILL != 0 && (JAK_VALIDTILL < time())) die(json_encode(array('status' => false, 'error' => "Account expired.")));

// Get the client browser
$ua = new Browser();

// Is a robot just die
if ($ua->isRobot()) die(json_encode(array('status' => false, 'error' => "Robots do not need a embed support area.")));

// Set the session for the embed part
if (!isset($_SESSION["webembed"])) $_SESSION["webembed"] = true;

// Now let's set the category id if we have any
$faqurl = str_replace('include/', '', JAK_rewrite::jakParseurl(JAK_FAQ_URL));
if (isset($_GET['catid']) && is_numeric($_GET['catid'])) $faqurl = str_replace('include/', '', JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'c', $_GET['catid']));

// Now get the support frame into the div.
die(json_encode(array('status' => true, 'widgethtml' => '<iframe id="hd3support" seamless="seamless" allowtransparency="true" style="background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; border: 0px none; bottom: 0px; height: 100%; margin: 0px; padding: 0px; width: 100%;" src="'.$faqurl.'"></iframe>')));
?>