<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Do not go any further if install folder still exists
if (is_dir('../install')) die('Please delete or rename install folder.');

// The DB connections data
require_once '../include/db.php';

// Get the real stuff
require_once '../config.php';

define('BASE_URL_ADMIN', BASE_URL);
define('BASE_URL_ORIG', str_replace('/'.JAK_OPERATOR_LOC.'/', '/', BASE_URL));
define('BASE_PATH_ORIG', str_replace('/'.JAK_OPERATOR_LOC.'', '/', _APP_MAIN_DIR));

// Include some functions for the ADMIN Area
include_once 'include/admin.function.php';
include_once '../class/class.paginator.php';

// Get the license file
require_once '../class/class.jaklic.php';
$jaklic = new JAKLicenseAPI();

// Set the last activity and session into cookies
JAK_base::jakCookie('lastactivity', time(), 86400, JAK_COOKIE_PATH);
JAK_base::jakCookie('usrsession', session_id(), 86400, JAK_COOKIE_PATH);
?>