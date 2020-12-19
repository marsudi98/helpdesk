<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.3                   # ||
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
$table = JAKDB_PREFIX.'faq_article AS t1';
$table2 = ' LEFT JOIN '.JAKDB_PREFIX.'faq_categories AS t2 ON (t1.catid = t2.id)';

// Table's primary key
$primaryKey = 't1.id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 't1.id', 'dbjoin' => 'id', 'dt' => 0 ),
	array( 'db' => 't1.id', 'dbjoin' => 'id', 'dt' => 1, 'formatter' => function( $d, $row ) {
			return '<input type="checkbox" name="jak_delete_faqs[]" class="highlight" value="'.$d.':#:'.$row['active'].'">';
		} ),
	array( 'db' => 't1.title AS faqtitle', 'dbjoin' => 'faqtitle', 'dt' => 2, 'formatter' => function( $d, $row ) {
			return '<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('faq', 'edit', $row['id'])).'">'.$d.'</a>';
		} ),
	array( 'db' => 't1.lang', 'dbjoin' => 'lang', 'dt' => 3 ),
	array( 'db' => 't2.title', 'dbjoin' => 'title', 'dt' => 4, 'formatter' => function( $d, $row ) {
			return (isset($d) ? '<a href="'.str_replace('ajax/', '', JAK_rewrite::jakParseurl('departments', 'faq', 'edit', $row['id'])).'">'.$d.'</a>' : '-');
		} ),
	array( 'db' => 't1.dorder', 'dbjoin' => 'dorder', 'dt' => 5 ),
	array( 'db' => 't1.active', 'dbjoin' => 'active', 'dt' => 6, 'formatter' => function( $d, $row ) {
			return ($d == 1 ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>');
		} ),
	array( 'db' => 't1.time', 'dbjoin' => 'time', 'dt' => 7, 'formatter' => function( $d, $row ) {
			return JAK_base::jakTimesince($d, JAK_DATEFORMAT, JAK_TIMEFORMAT);
		} )
);

die(json_encode(SSP::join( $_GET, $table, $table2, '', $primaryKey, $columns, $where, $where )));
?>