<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Versio 3.5                    # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Database connection and setup
define('JAKDB_HOST', 'localhost'); // Database host ## Datenbank Server
define('JAKDB_DBTYPE', 'pgsql'); // Database host ## Datenbank Server
define('JAKDB_PORT', 5432); // Enter the database port for your mysql server
define('JAKDB_USER', 'postgres'); // Database user ## Datenbank Benutzername
define('JAKDB_PASS', 'postgres'); // Database password ## Datenbank Passwort
define('JAKDB_NAME', 'jnt_hd'); // Database name ## Datenbank Name
define('JAKDB_PREFIX', 'hd_'); // Database prefix use (a-z) and (_)

// Define a unique key for your site, don't change after, or people can't login anymore. (https://www.jakweb.ch/faq/a/99/database-and-password-hash)
define('DB_PASS_HASH', 'something_strong_goes_in_here');

// Define your site url, for example: www.jakweb.ch (https://www.jakweb.ch/faq/a/98/full-site-domain)
define('FULL_SITE_DOMAIN', 'localhost');

// URL Rewrite
define('JAK_USE_APACHE', 0); // Use 1 for Apache / Nginx (SEO URL's) or 0 for all others

// Set http or https
define('JAK_SITEHTTPS', 0); // Site is running in HTTP 0 = / HTTPS = 1

// Operator panel
define('JAK_OPERATOR_LOC', 'operator'); // The operator folder, due not change except you have changed the operator folder name as well

// Define cookie path and lifetime
define('JAK_COOKIE_PATH', '/');  // Available in the whole domain
define('JAK_COOKIE_TIME', 60*60*24*30); // 30 days by default

// Choose a cache directory to reduce page and server load
define('JAK_CACHE_DIRECTORY', 'cache');

// Choose the userfiles directory, rename if you like different location
define('JAK_FILES_DIRECTORY', 'files');

// Operator Time
define('OPERATOR_CHAT_EXPIRE', '7200'); // If we have no activity from the operator it will expire after x seconds

// File and Image Editor Path (for absolute use '' for relative use 'http://www.yourdomain.com/installation_path' or '/subfolder
define('JAK_EDITOR_PATH_BASE', '');
define('JAK_EDITOR_PATH', '/files/editor/');
define('JAK_EDITOR_PATH_THUMBS', '/files/thumbs/');

// Important Stuff
define('JAK_SUPERADMIN', '1'); // Undeletable and SuperADMIN User, more user seperate with comma. e.g. 1,4,5,6 (userid)

// Client, Support, Search, Blog and FAQ URL
define('JAK_CLIENT_URL', 'client');
define('JAK_BLOG_URL', 'blog');
define('JAK_FAQ_URL', 'faq');
define('JAK_SEARCH_URL', 'search');
define('JAK_SUPPORT_URL', 'support');

// When using the API you can set the URL where to change the email address and password
define('JAK_API_PROFILE', '');

##### From here we go towards a hosted solution (Cloud Chat 3), should you host this for your customer you might want to set some limitations. #####

// Database connection for the main site where user control is active when used with Live CHAT PHP Server on standalone ignore it.
define('JAKDB_MAIN_HOST', 'localhost'); // Database host ## Datenbank Server
define('JAKDB_MAIN_DBTYPE', 'pgsql'); // Database host ## Datenbank Server
define('JAKDB_MAIN_PORT', 5432); // Enter the database port for your mysql server
define('JAKDB_MAIN_USER', 'postgres'); // Database user ## Datenbank Benutzername
define('JAKDB_MAIN_PASS', 'postgres'); // Database password ## Datenbank Passwort
define('JAKDB_MAIN_NAME', 'jnt_hd'); // Database name ## Datenbank Name
define('JAKDB_MAIN_PREFIX', 'hd_'); // Database prefix use (a-z) and (_)

// Location ID as defined in your MAIN Database
define('JAK_MAIN_LOC', 1);

// Cloud Chat 3 settings = $jakhs
$jakhs = array();

$jakhs['hostactive'] = 0; // 0 = No, all below will be ignored, 1 = Yes, all below will count.
$jakhs['pricemonth'] = '5'; // Set the price here in a number (e.g. 4.5, 5, 4.99)
$jakhs['operators'] = 5; // 0 = Unlimited, or any number for example 5, will allow to create 5 operators
$jakhs['departments'] = 5; // 0 = Unlimited, or any number for example 5, will allow to create 5 departments
$jakhs['chatwidgets'] = 2; // 0 = Unlimited, or any number for example 5, will allow to create 5 chat widgets
$jakhs['privateopchat'] = 0; // 0 = not available, 1 = available
$jakhs['publicopchat'] = 0; // 0 = not available, 1 = available
$jakhs['groupchat'] = 0; // 0 = not available, 1 = available
$jakhs['copyright'] = '<a href="https://www.jakweb.ch" target="_blank">Powered by HelpDesk 3</a>'; // the copyright link/text e.g. <a href="https://www.jakweb.ch" target="_blank">Powered by Live Chat 3</a>
$jakhs['files'] = 1; // 0 = not available, 1 = available
$jakhs['filetype'] = ".zip,.rar,.jpg,.jpeg,.png,.gif"; // files allowed to upload for client e.g. .zip,.rar,.jpg,.jpeg,.png,.gif
$jakhs['filetypeo'] = ".zip,.rar,.jpg,.jpeg,.png,.gif"; // files allowed to upload for operator e.g. .zip,.rar,.jpg,.jpeg,.png,.gif
?>