<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.1                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2017 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../../config.php')) die('ajax/[config.php] config.php not exist');
require_once '../../config.php';

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_SESSION['jak_lcp_idhash'])) die("Nothing to see here");

if (!file_exists('../../class/ssp.class.php')) die('ajax/[ssp.class.php] config.php not exist');
require_once '../../class/ssp.class.php';

$where = '';
// DB table to use
$table = JAKDB_PREFIX.'subscriptions AS t1';
$table2 = ' LEFT JOIN '.JAKDB_PREFIX.'clients AS t2 ON (t1.clientid = t2.id)';

// Table's primary key
$primaryKey = 't1.id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 't1.id', 'dbjoin' => 'id', 'dt' => 0 ),
	array( 'db' => 't1.id', 'dbjoin' => 'id', 'dt' => 1, 'formatter' => function( $d, $row ) {
			return '<input type="checkbox" name="jak_delete_subscriptions[]" class="highlight" value="'.$d.'">';
		} ),
	array( 'db' => 't2.email', 'dbjoin' => 'email', 'dt' => 2, 'formatter' => function( $d, $row ) {
			return '<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('users', 'clients', 'edit', $row['clientid'])).'">'.$d.'</a>';
		} ),
	array( 'db' => 't1.amount', 'dbjoin' => 'amount', 'dt' => 3 ),
	array( 'db' => 't1.currency', 'dbjoin' => 'currency', 'dt' => 4 ),
	array( 'db' => 't1.paidwhen', 'dbjoin' => 'paidwhen', 'dt' => 5, 'formatter' => function( $d, $row ) {
			return JAK_base::jakTimesince($d, JAK_DATEFORMAT, JAK_TIMEFORMAT);
		} ),
	array( 'db' => 't1.paidhow', 'dbjoin' => 'paidhow', 'dt' => 6 ),
	array( 'db' => 't1.success', 'dbjoin' => 'success', 'dt' => 7, 'formatter' => function( $d, $row ) {
			return ($d == 1 ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>');
		} ),
	array( 'db' => 't1.clientid', 'dbjoin' => 'clientid', 'dt' => 'tdc' )
);

die(json_encode(SSP::join( $_GET, $table, $table2, '', $primaryKey, $columns, $where, $where )));
?>