<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../config.php')) die('include/[support.php] config.php not exist');
require_once '../config.php';

if (!file_exists('../class/ssp.class.php')) die('include/[support.php] ssp.class.php not exist');
require_once '../class/ssp.class.php';

// Get the correct tickets
$where = '';
if (JAK_CLIENTID) {
	if ($jakclient->getVar("support_dep") == 0) {
		// ["AND" => ["OR" => ["support_tickets.private" => 0, "support_tickets.clientid" => JAK_CLIENTID], "support_departments.guesta" => 1]]
		$where = "(t1.private = 0 OR t1.clientid = ".JAK_CLIENTID.")";
	} else {
		// ["AND" => ["OR" => ["support_tickets.private" => 0, "support_tickets.depid" => [$jakclient->getVar("support_dep")], "support_tickets.clientid" => JAK_CLIENTID], "support_departments.guesta" => 1]]
		$where = "(t1.private = 0 OR t1.clientid = ".JAK_CLIENTID." OR t1.depid IN('".explode(",", $jakclient->getVar("support_dep"))."'))";
	}
} elseif (JAK_USERID) {
    if ($jakuser->getVar("support_dep") == 0) {
		// ["AND" => ["OR" => ["support_tickets.private" => 0, "support_tickets.clientid" => JAK_CLIENTID], "support_departments.guesta" => 1]]
		$where = '';
	} else {
		// ["AND" => ["OR" => ["support_tickets.private" => 0, "support_tickets.depid" => [$jakclient->getVar("support_dep")], "support_tickets.clientid" => JAK_CLIENTID], "support_departments.guesta" => 1]]
		$where = "t1.depid IN('".explode(",", $jakuser->getVar("support_dep"))."')";
	}
} else {
    $where = "t1.private = 0 AND t2.guesta = 1";
}

if (isset($_SESSION["sortdepid"]) && is_numeric($_SESSION["sortdepid"])) $where .= ' AND t1.depid = '.$_SESSION["sortdepid"];

// DB table to use
$table = JAKDB_PREFIX.'support_tickets AS t1';
$table2 = ' LEFT JOIN '.JAKDB_PREFIX.'support_departments AS t2 ON (t1.depid = t2.id)';
$table3 = ' LEFT JOIN '.JAKDB_PREFIX.'ticketpriority AS t3 ON (t1.priorityid = t3.id)';

// Table's primary key
$primaryKey = 't1.id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 't1.subject', 'dbjoin' => 'subject', 'dt' => 0, 'formatter' => function( $d, $row ) {
			return '<a href="'.str_replace('include/', '', JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $row['id'], JAK_rewrite::jakCleanurl($row["subject"]))).'">'.$d.'</a>';
		} ),
	array( 'db' => 't2.title', 'dbjoin' => 'title', 'dt' => 1 ),
	array( 'db' => 't1.name', 'dbjoin' => 'name', 'dt' => 2 ),
	array( 'db' => 't1.id', 'dbjoin' => 'id', 'dt' => 3 ),
	array( 'db' => 't1.initiated', 'dbjoin' => 'initiated', 'dt' => 4, 'formatter' => function( $d, $row ) {
			return JAK_base::jakTimesince($d, JAK_DATEFORMAT, JAK_TIMEFORMAT);
		} ),
	array( 'db' => 't1.status', 'dbjoin' => 'status', 'dt' => 5, 'formatter' => function( $d, $row ) {
			if (isset($_SESSION['jak_lcp_lang']) && file_exists(APP_PATH.'lang/'.$BT_LANGUAGE.'.php')) {
			    include (APP_PATH.'lang/'.$BT_LANGUAGE.'.php');
			} else {
			    include (APP_PATH.'lang/'.JAK_LANG.'.php');
			}
			return (isset($d) && $d == 1 ? '<span class="badge badge-info">'.$jkl['hd14'].'</span>' : ($d == 2 ? '<span class="badge badge-warning">'.$jkl['hd15'].'</span>' : '<span class="badge badge-success">'.$jkl['hd16'].'</span>')).' <span class="badge badge-'.$row["class"].'">'.$row["prioritytitle"].'</span>';
		} ),
	array( 'db' => 't1.updated', 'dbjoin' => 'updated', 'dt' => 6 ),
	array( 'db' => 't3.title AS prioritytitle', 'dbjoin' => 'prioritytitle', 'dt' => 7 ),
	array( 'db' => 't3.class', 'dbjoin' => 'class', 'dt' => 8 )
);

die(json_encode(SSP::join( $_GET, $table, $table2, $table3, $primaryKey, $columns, $where, $where )));
?>