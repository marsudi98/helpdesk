<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2019 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../../config.php')) die('ajax/[config.php] config.php not exist');
require_once '../../config.php';

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_SESSION['jak_lcp_idhash'])) die("Nothing to see here");

if (!file_exists('../../class/ssp.class.php')) die('ajax/[ssp.class.php] config.php not exist');
require_once '../../class/ssp.class.php';

$where = '';
if (isset($_GET["filter"]) && !empty($_GET["filter"])) {

	if ($_GET["filter"] == "tomorrow") {
		$where = 't1.duedate = CURDATE() + INTERVAL 1 DAY AND (t1.status = 1 OR t1.status = 2)';
	} elseif ($_GET["filter"] == "allopen") {
		$where = '(t1.status = 1 OR t1.status = 2)';
	} elseif ($_GET["filter"] == "allclosed") {
		$where = '(t1.status = 3 OR t1.status = 4)';
	} elseif ($_GET["filter"] == "alltickets") {
		$where = 't1.initiated != 0';
	}
} else {
	$where = 't1.duedate <= CURDATE() AND (t1.status = 1 OR t1.status = 2)';
}

if (isset($_SESSION["sortdepid"]) && is_numeric($_SESSION["sortdepid"])) {
	$where = '(t1.operatorid = '.$jakuser->getVar("id").' AND (t1.depid = '.$_SESSION["sortdepid"].') OR t1.depid = '.$_SESSION["sortdepid"].') AND '.$where;
} else {
	// and then we filter the support departments
	if (is_numeric($jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
		$where = '(t1.operatorid = '.$jakuser->getVar("id").' OR t1.depid = '.$jakuser->getVar("support_dep").') AND '.$where;
	} elseif (!((boolean)$jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
		$where = '(t1.operatorid = '.$jakuser->getVar("id").' OR t1.depid IN ('.$jakuser->getVar("support_dep").')) AND'.$where;
	}
}

// DB table to use
$table = JAKDB_PREFIX.'support_tickets AS t1';
$table2 = ' LEFT JOIN '.JAKDB_PREFIX.'support_departments AS t2 ON (t1.depid = t2.id)';
$table3 = '';

// Table's primary key
$primaryKey = 't1.id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 't1.id', 'dbjoin' => 'id', 'dt' => 0 ),
	array( 'db' => 't1.id', 'dbjoin' => 'id', 'dt' => 1, 'formatter' => function( $d, $row ) {
			return '<input type="checkbox" name="jak_delete_tickets[]" class="highlight" value="'.$d.'">';
		} ),
	array( 'db' => 't1.subject', 'dbjoin' => 'subject', 'dt' => 2, 'formatter' => function( $d, $row ) {
			return '<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'read', $row['id'])).'">'.$d.'</a>'.($row['mergeid'] ? ' <a class="badge badge-info" href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'read', $row['mergeid'])).'"><i class="far fa-code-merge"></i></a>' : '');
		} ),
	array( 'db' => 't2.title', 'dbjoin' => 'title', 'dt' => 3 ),
	array( 'db' => 't1.name', 'dbjoin' => 'name', 'dt' => 4 ),
	array( 'db' => 't1.private', 'dbjoin' => 'private', 'dt' => 5, 'formatter' => function( $d, $row ) {
			return (isset($d) && $d != 0 ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>');
		} ),
	array( 'db' => 't1.attachments', 'dbjoin' => 'attachments', 'dt' => 6, 'formatter' => function( $d, $row ) {
			return (isset($d) && $d != 0 ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>');
		} ),
	array( 'db' => 't1.reminder', 'dbjoin' => 'reminder', 'dt' => 7, 'formatter' => function( $d, $row ) {
			return (isset($d) && $d == 3 ? '<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'rating', $row['id'])).'" data-toggle="modal" data-target="#jakModal"><i class="fa fa-check"></i></a>' : '<i class="fa fa-times"></i>');
		} ),
	array( 'db' => 't1.status', 'dbjoin' => 'status', 'dt' => 8, 'formatter' => function( $d, $row ) {
			if (isset($_SESSION['jak_lcp_lang']) && file_exists(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$_SESSION['jak_lcp_lang'].'.php')) {
			    include (APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$_SESSION['jak_lcp_lang'].'.php');
			} else {
			    include (APP_PATH.JAK_OPERATOR_LOC.'/lang/'.JAK_LANG.'.php');
			}
			return '<div class="btn-group">
    <button id="ticket_status_change" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-cog"></i>
  </button>
    <div class="dropdown-menu" aria-labelledby="ticket_status_change">
    	<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'status', $row['id'], 1)).'" class="dropdown-item">'.$jkl['hd169'].($d == 1 ? ' <i class="fa fa-check"></i>' : '').'</a>
    	<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'status', $row['id'], 2)).'" class="dropdown-item">'.$jkl['hd170'].($d == 2 ? ' <i class="fa fa-check"></i>' : '').'</a>
    	<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'status', $row['id'], 3)).'" class="dropdown-item">'.$jkl['hd171'].($d == 3 ? ' <i class="fa fa-check"></i>' : '').'</a>
    	<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('support', 'status', $row['id'], 4)).'" class="dropdown-item">'.$jkl['g248'].($d == 4 ? ' <i class="fa fa-check"></i>' : '').'</a>
  </div>
</div>';
		} ),
	array( 'db' => 't1.duedate', 'dbjoin' => 'duedate', 'dt' => 9, 'formatter' => function( $d, $row ) {
			// Explode the time format so it is always available
			$duedateformat = explode(":#:", JAK_TICKET_DUEDATE_FORMAT);
			return date($duedateformat[0], strtotime($d));
		} ),
	array( 'db' => 't1.status', 'dbjoin' => 'status', 'dt' => 'tdc' ),
	array( 'db' => 't1.mergeid', 'dbjoin' => 'mergeid', 'dt' => 'mid' )
);

die(json_encode(SSP::join( $_GET, $table, $table2, $table3, $primaryKey, $columns, $where, $where )));
?>