<?php

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_ADMIN_PREVENT_ACCESS')) die('You cannot access this file directly.');

// All the tables we need for this plugin
$errors = array();

// We reset some vars
$totalChange = 0;
$lastChange = '';

switch ($page1) {
	case 'operator':
		$report_date = ($_GET["report_date"] != null && $_GET["report_date"] != "") ? $_GET["report_date"] : date("Y-m-d");
		$level 		= (JAK_SUPERADMINACCESS) ? "admin" : (($jakuser->getVar('is_dp')) ? "dp" : "op");
		$level_var 	= ($jakuser->getVar('is_dp')) ? $jakuser->getVar('username') : $jakuser->getVar('id');
		$dan_1 		= (JAK_SUPERADMINACCESS) ? "" : (($jakuser->getVar('is_dp')) ? (" AND st.dp_bersalah = '".$level_var."' ") : (" AND st.operatorid = '".$level_var."' "));
		$dan_1 		= (JAK_SUPERADMINACCESS) ? "" : (($jakuser->getVar('is_dp')) ? (" AND st.dp_bersalah = '".$level_var."' ") : (" AND st.operatorid = '".$level_var."' "));
		// echo "<pre>";
		$query = $jakdb->query("
		SELECT st.operatorid, st.priorityid, tp.title, us.username, COUNT(st.id) as jumlah,
			CASE
				WHEN st.status IN (1,2) THEN CASE
					WHEN FROM_UNIXTIME(st.updated,'%Y-%m-%d') = '$report_date' THEN 23
					ELSE 13
				END
				WHEN (UNIX_TIMESTAMP(CONCAT(st.duedate, ' 23:59:59')) - st.ended) < 0 THEN CASE
					WHEN FROM_UNIXTIME(st.ended,'%Y-%m-%d') = '$report_date' THEN 22
					ELSE 12
				END
				ELSE CASE
					WHEN FROM_UNIXTIME(st.ended,'%Y-%m-%d') = '$report_date' THEN 21
					ELSE 11
				END
			END AS prior
		FROM hd_support_tickets st
			RIGHT JOIN hd_ticketpriority tp ON st.priorityid = tp.id
			INNER JOIN hd_user us ON st.operatorid = us.id
			WHERE (st.status IN (1,2) OR FROM_UNIXTIME(st.initiated) BETWEEN DATE_FORMAT(DATE_SUB('$report_date' , INTERVAL 1 YEAR) ,'%Y-%m-01') AND '$report_date')
			$dan_1
		GROUP BY st.operatorid, st.priorityid,
			CASE
				WHEN st.status IN (1,2) THEN CASE
					WHEN FROM_UNIXTIME(st.updated,'%Y-%m-%d') = '$report_date' THEN 23
					ELSE 13
				END
				WHEN (UNIX_TIMESTAMP(CONCAT(st.duedate, ' 23:59:59')) - st.ended) < 0 THEN CASE
					WHEN FROM_UNIXTIME(st.ended,'%Y-%m-%d') = '$report_date' THEN 22
					ELSE 12
				END
				ELSE CASE
					WHEN FROM_UNIXTIME(st.ended,'%Y-%m-%d') = '$report_date' THEN 21
					ELSE 11
				END
			END
		ORDER BY prior, st.priorityid
		")->fetchAll();
		if ($query != null && !empty($query)) {

			$operator_list 	= [];
			$cat_list 		= [];
			$stat 			= ["TOTAL KOMPLAIN CLEAR SESUAI DEADLINE", "TOTAL KOMPLAIN CLEAR OVER DEADLINE", "TOTAL KOMPLAIN UNCLEAR", ""];
			$cat_list_query = $jakdb->query(" SELECT title FROM hd_ticketpriority ")->fetchAll();

			$result["operator_complain"] 					= [];
			$result["global_complain"] 						= [];
			$result["global_complain"]["clear"] 			= [];
			$result["global_complain"]["late"] 				= [];
			$result["global_complain"]["unclear"] 			= [];
			$result["global_complain"]["all"] 				= 0;
			$result["global_complain"]["clear"]["all"] 		= 0;
			$result["global_complain"]["late"]["all"] 		= 0;
			$result["global_complain"]["unclear"]["all"] 	= 0;
			$result["today"]["global_complain"]["pc"] 		= 0;
			$result["today"]["global_complain"]["np"] 		= 0;

			array_push($cat_list_query, ["title" => ""]);
			foreach ($cat_list_query as $title) {
				array_push($cat_list, $title["title"]);
				$title["title"] = ($title["title"] == null || $title["title"] == "") ? "???" : $title["title"];
				$result["global_complain"]["clear"][$title["title"]] 	= 0;
				$result["global_complain"]["late"][$title["title"]] 	= 0;
				$result["global_complain"]["unclear"][$title["title"]] 	= 0;
				$result["today"]["global_complain"][$title["title"]] 	= 0;
			}

			foreach ($query as $data) {
				$data["title"] = ($data["title"] == null || $data["title"] == "") ? "???" : $data["title"];
				if (!array_key_exists($data["username"], $result["operator_complain"])) {
					array_push($operator_list, $data["username"]);
					$result["operator_complain"][$data["username"]] 					= [];
					$result["operator_complain"][$data["username"]]["clear"] 			= [];
					$result["operator_complain"][$data["username"]]["late"] 			= [];
					$result["operator_complain"][$data["username"]]["unclear"] 			= [];
					$result["operator_complain"][$data["username"]]["all"] 				= 0;
					$result["operator_complain"][$data["username"]]["clear"]["all"] 	= 0;
					$result["operator_complain"][$data["username"]]["late"]["all"] 		= 0;
					$result["operator_complain"][$data["username"]]["unclear"]["all"] 	= 0;
					$result["today"][$data["username"]]["pc"] 							= 0;
					$result["today"][$data["username"]]["np"] 							= 0;

					foreach ($cat_list_query as $title) {
						$title["title"] = ($title["title"] == null || $title["title"] == "") ? "???" : $title["title"];
						$result["operator_complain"][$data["username"]]["clear"][$title["title"]] 	= 0;
						$result["operator_complain"][$data["username"]]["late"][$title["title"]] 	= 0;
						$result["operator_complain"][$data["username"]]["unclear"][$title["title"]] = 0;
						$result["today"][$data["username"]][$title["title"]] 						= 0;
					}
				}

				if ($data["prior"] == 11) {			//case cleared - old
					$result["global_complain"]["clear"]["all"] 									+= $data["jumlah"];
					$result["global_complain"]["clear"][$data["title"]] 						+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["clear"]["all"] 			+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["clear"][$data["title"]] 	+= $data["jumlah"];
				} elseif ($data["prior"] == 21) {	//case cleared - today
					$result["global_complain"]["clear"]["all"] 									+= $data["jumlah"];
					$result["global_complain"]["clear"][$data["title"]] 						+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["clear"]["all"] 			+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["clear"][$data["title"]] 	+= $data["jumlah"];
					$result["today"]["global_complain"]["pc"] 									+= $data["jumlah"];
					$result["today"]["global_complain"][$title["title"]] 						+= $data["jumlah"];
					$result["today"][$data["username"]]["pc"] 									+= $data["jumlah"];
					$result["today"][$data["username"]][$title["title"]] 						+= $data["jumlah"];
				} elseif ($data["prior"] == 12) {	//case cleared but late - old
					$result["global_complain"]["late"]["all"] 									+= $data["jumlah"];
					$result["global_complain"]["late"][$data["title"]] 							+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["late"]["all"] 				+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["late"][$data["title"]] 	+= $data["jumlah"];
				} elseif ($data["prior"] == 22) {	//case clear but late - today
					$result["global_complain"]["late"]["all"] 									+= $data["jumlah"];
					$result["global_complain"]["late"][$data["title"]] 							+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["late"]["all"] 				+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["late"][$data["title"]] 	+= $data["jumlah"];
					$result["today"]["global_complain"]["pc"] 									+= $data["jumlah"];
					$result["today"]["global_complain"][$title["title"]] 						+= $data["jumlah"];
					$result["today"][$data["username"]]["pc"] 									+= $data["jumlah"];
					$result["today"][$data["username"]][$title["title"]] 						+= $data["jumlah"];
				} elseif ($data["prior"] == 13) {	//case unclear no progress
					$result["global_complain"]["unclear"]["all"] 								+= $data["jumlah"];
					$result["global_complain"]["unclear"][$data["title"]] 						+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["unclear"]["all"] 			+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["unclear"][$data["title"]] 	+= $data["jumlah"];
					$result["today"]["global_complain"]["np"] 									+= $data["jumlah"];
					$result["today"][$data["username"]]["np"] 									+= $data["jumlah"];
				} elseif ($data["prior"] == 23) {	//case unclear with progress
					$result["global_complain"]["unclear"]["all"] 								+= $data["jumlah"];
					$result["global_complain"]["unclear"][$data["title"]] 						+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["unclear"]["all"] 			+= $data["jumlah"];
					$result["operator_complain"][$data["username"]]["unclear"][$data["title"]] 	+= $data["jumlah"];
					$result["today"]["global_complain"]["pc"] 									+= $data["jumlah"];
					$result["today"][$data["username"]]["pc"] 									+= $data["jumlah"];
				}
				$result["global_complain"]["all"] += $data["jumlah"];
				$result["operator_complain"][$data["username"]]["all"] += $data["jumlah"];
			}

			include '../class/PHPExcel/IOFactory.php';
			$cell_color = [
				'fill' => [
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => $jkl["warna_putih"])
				]
			];

			$warna = [["nama" => "aqua", "kode" => "00FFFF"]];
			foreach($jkl as $jklkey=>$jklvalue){
				if (strpos($jklkey, "warna_d") !== false) {
					array_push($warna, ["nama" => $jklkey, "kode" => $jklvalue]);
				}
			}

			$thin_border = [
				'borders' => [
					'allborders' => [
						'style' => PHPExcel_Style_Border::BORDER_THIN
					]
				],
			];

			$text_white = [
				'font'  => [
        			'color' => ['rgb' => $jkl["warna_putih"]]
				],
			];

			$text_black = [
				'font'  => [
        			'color' => ['rgb' => $jkl["warna_hitam"]]
				],
			];

			$text_center = [
		        'alignment' => [
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        ]
		    ];

			$col = 2; $start_col = 2;
			$row = 2; $start_row = 2;
			$report_excel = new PHPExcel();
			$report_excel->setActiveSheetIndex(0);
			$report_excel->getActiveSheet()->setTitle("PERFORMANCE");
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_hitam"];
			$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+5)).$row);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
			$tanggal_report = explode("-", $report_date);
			$report_day 	= $tanggal_report[2];
			$report_month 	= $tanggal_report[1];
			$report_year 	= $tanggal_report[0];
			$bulan_start 	= ($report_day > 25) ? date("M",strtotime($report_date)) : date("M",strtotime("-1 MONTH", strtotime($report_date)));
			$bulan_end 		= ($report_day > 25) ? date("M",strtotime("+1 MONTH", strtotime($report_date))) : date("M",strtotime($report_date));
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "|CUT OFF ".$bulan_end." 2021 | DATA KOMPLAIN 26 ".$bulan_start." - 25 ".$bulan_end);
			$col--;
			$row++; $row++;
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dungu"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "TOTAL SEMUA KOMPLAIN");
			$row++;
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_hitam"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col+(count($result["operator_complain"]))+1)).$row)->applyFromArray($cell_color);
			$row++;
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dhijau"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "TOTAL KOMPLAIN CLEAR");
			$row++;

			foreach ($stat as $stot => $stut) {
				$cell_color["fill"]["color"]["rgb"] = ($stot == 0) ? $jkl["warna_hijau"] : (($stot == 1) ? $jkl["warna_kuning"] : (($stot == 2) ? $jkl["warna_merah"] : $jkl["warna_putih"]));
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stut);
				if ($stut == "") {
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_hitam"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col+(count($result["operator_complain"])*2)+2)).$row)->applyFromArray($cell_color);
					$row++;
				}
				$row++;
				foreach ($cat_list as $category) {
					$category = ($category == NULL || $category == "") ? "???" : $category;
					$cell_color["fill"]["color"]["rgb"] = ($stot == 0) ? $jkl["warna_shijau"] : (($stot == 1) ? $jkl["warna_skuning"] : (($stot == 2) ? $jkl["warna_smerah"] : (($stot == 3) ? $jkl["warna_biru"] : $jkl["warna_putih"])));
					if ($stot == 3) {
						$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					}
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $category);
					$row++;
				}
				if ($stut == "") {
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "PROGRESS HARI INI TERISI + COMP CLEAR");
					$row++;
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_sbiru"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "PROGRESS HARI INI KOSONG");
					$row++;
				}
			}

			$col = $start_col;
			$row = $start_row;
			$col--;
			$today_header = false;
			$warna_nama = 0;
			foreach ($result["operator_complain"] as $operator_name => $operator) {
				$row = $start_row;
				$col++;
				$row++;
				$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row);
				$cell_color["fill"]["color"]["rgb"] = $warna[$warna_nama]["kode"]; $warna_nama++;
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $operator_name);
				$row++;
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_ungu"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $operator["all"]);
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dungu"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format(($operator["all"]/$result["global_complain"]["all"]*100), 2, '.', '')."%");
				$row++; $row++;
				$op_all_clear = $operator["clear"]["all"] + $operator["late"]["all"];
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dhijau"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $op_all_clear);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format(($op_all_clear/$operator["all"]*100), 2, '.', '')."%");
				$row++;
				foreach ($operator as $stat_name => $stat_op) {
					if (is_array($stat_op)) {
						$another_stat = [];
						foreach ($operator as $not_stat_name => $not_stat_op) {
							if (is_array($not_stat_op) && $not_stat_name != $stat_name) {
								array_push($another_stat, $not_stat_name);
							}
						}

						foreach ($stat_op as $detail_name => $stat_op_detail) {
							$cell_color["fill"]["color"]["rgb"] = ($stat_name == "clear") ? $jkl["warna_shijau"] : (($stat_name == "late") ? $jkl["warna_skuning"] : (($stat_name == "unclear") ? $jkl["warna_smerah"] : $jkl["warna_putih"]));
							$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
							$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stat_op_detail);
							$percentage = $stat_op_detail;
							$cell_color["fill"]["color"]["rgb"] = ($stat_name == "clear") ? $jkl["warna_hijau"] : (($stat_name == "late") ? $jkl["warna_kuning"] : (($stat_name == "unclear") ? $jkl["warna_merah"] : $jkl["warna_putih"]));
							$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
							if ($stat_name != "unclear" || $detail_name == 'all') {
								foreach ($another_stat as $a_stat) {
									$percentage += $result["operator_complain"][$operator_name][$a_stat][$detail_name];
								}
								$percentage = $stat_op_detail/$percentage*100;
								$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
							} else {
								$percentage += $result["today"][$operator_name][$detail_name];
								$percentage = $stat_op_detail/$percentage*100;
								$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
							}
							$row++;
						}
					}
				}

				$row++;
				if (!$today_header) {
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+5)).$row);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col-1)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col+(count($result["operator_complain"])*2)+1)).$row)->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($start_col, $row, "PENYELESAIAN KOMPLAIN HARI INI (COMPLAINT CLEAR)");
					$today_header = true;
				}
				$row++;

				foreach ($result["today"][$operator_name] as $op_today_title => $op_today) {
					if ($op_today_title != "pc" && $op_today_title != "np") {
						$cell_color["fill"]["color"]["rgb"] = $jkl["warna_sbiru"];
						$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $op_today);
						$percentage = $op_today;
						$percentage += $result["operator_complain"][$operator_name]["unclear"][$op_today_title];
						$percentage = $op_today/$percentage*100;
						$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru"];
						$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($text_white);
						$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
						$row++;
					}
				}
				$percentage = $result["today"][$operator_name]["pc"]/($result["today"][$operator_name]["pc"]+$result["today"][$operator_name]["np"])*100;
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result["today"][$operator_name]["pc"]);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
				$row++;
				$percentage = ($percentage-100);
				$percentage = ($percentage > 0) ? $percentage : ($percentage*-1);
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_sbiru"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result["today"][$operator_name]["np"]);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");

				$col++;
			}
			$col++;

			$row = $start_row;
			$row++;

			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_hitam"];
			$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "All Complain");
			$row++;
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_ungu"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result["global_complain"]["all"]);
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dungu"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, "100%");

			$row++; $row++;

			$atas = $result["global_complain"]["clear"]["all"]+$result["global_complain"]["late"]["all"];

			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dhijau"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $atas);
			$bawah = $atas+$result["global_complain"]["unclear"]["all"];
			$percentage = $atas/$bawah*100;
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dhijau"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
			$row++;
			foreach ($result["global_complain"] as $stat_name => $stat_gl) {
				if (is_array($stat_gl)) {
					$another_stat = [];
					foreach ($result["global_complain"] as $not_stat_name => $not_stat_gl) {
						if (is_array($not_stat_gl) && $not_stat_name != $stat_name) {
							array_push($another_stat, $not_stat_name);
						}
					}

					foreach ($stat_gl as $detail_name => $stat_gl_detail) {
						$cell_color["fill"]["color"]["rgb"] = ($stat_name == "clear") ? $jkl["warna_shijau"] : (($stat_name == "late") ? $jkl["warna_skuning"] : (($stat_name == "unclear") ? $jkl["warna_smerah"] : $jkl["warna_putih"]));
						$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $stat_gl_detail);
						$percentage = $stat_gl_detail;
						$cell_color["fill"]["color"]["rgb"] = ($stat_name == "clear") ? $jkl["warna_hijau"] : (($stat_name == "late") ? $jkl["warna_kuning"] : (($stat_name == "unclear") ? $jkl["warna_merah"] : $jkl["warna_putih"]));

						if ($stat_name != "unclear" || $detail_name == 'all') {
							foreach ($another_stat as $a_stat) {
								$percentage += $result["global_complain"][$a_stat][$detail_name];
							}
							$percentage = $stat_gl_detail/$percentage*100;
							$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
						} else {
							$percentage += $result["today"]["global_complain"][$detail_name];
							$percentage = $stat_gl_detail/$percentage*100;
							$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
						}
						$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
						$row++;
					}
				}
			}
			$row++;
			$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $report_date);


			$row++;
			foreach ($result["today"]["global_complain"] as $gl_today_title => $gl_today) {
				if ($gl_today_title != "pc" && $gl_today_title != "np") {
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_sbiru"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $gl_today);
					$percentage = $gl_today;
					$percentage += $result["global_complain"]["unclear"][$gl_today_title];
					$percentage = $gl_today/$percentage*100;
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col+1))).$row)->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
					$row++;
				}
			}
			$percentage = $result["today"]["global_complain"]["pc"]/($result["today"]["global_complain"]["pc"]+$result["today"]["global_complain"]["np"])*100;
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row)->applyFromArray($text_white);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result["today"]["global_complain"]["pc"]);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
			$row++;
			$percentage = ($percentage-100);
			$percentage = ($percentage > 0) ? $percentage : ($percentage*-1);
			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_sbiru"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row)->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result["today"]["global_complain"]["np"]);
			$report_excel->getActiveSheet()->setCellValueByColumnAndRow(($col+1), $row, number_format($percentage, 2, '.', '')."%");
			
			$col++;

			$cell_color["fill"]["color"]["rgb"] = $jkl["warna_hitam"];
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($start_col-1)).($start_row+3).":".(PHPExcel_Cell::stringFromColumnIndex($start_col+(count($result["operator_complain"])*2)+1)).($start_row+3))->applyFromArray($cell_color);
			$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($start_col-1)).$start_row.":".(PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($thin_border);

			//====================================== START TABEL REPORT 2 ==============================================

			// $col++; $col++;
			// $start_col_2 = $col;
			$start_col_2 = 0;
			$start_row_2 = 2;
			$row = 2;
			$col = 0;

			$report_excel->createSheet();
			$report_excel->setActiveSheetIndex(1);
			$report_excel->getActiveSheet()->setTitle("SISA COMPLAINT TIAP ERM");
			$report_2 = $jakdb->query("
				SELECT dp.erm, tp.title, count(*) as jumlah, CASE WHEN st.status IN (1,2) THEN 1 ELSE 2 END AS stat
				FROM hd_support_tickets st
					LEFT JOIN m_dp dp ON dp.id_dp = st.dp_bersalah
					LEFT JOIN hd_ticketpriority tp ON tp.id = st.priorityid
				WHERE (st.status IN (1,2) OR FROM_UNIXTIME(st.ended, '%Y-%m-%d') = '$report_date')
					$dan_1
				GROUP BY CASE WHEN st.status IN (1,2) THEN 1 ELSE 2 END, dp.erm, tp.title
				ORDER BY stat, dp.erm, tp.title
			")->fetchAll();

			if ($report_2 != null && !empty($report_2)) {
				$result2 = [];
				$result2["all"]["unclear"]["all"] = 0;
				$result2["all"]["unclear"]["erm"] = [];
				$result2["all"]["unclear"]["cat"] = [];
				$result2["all"]["remain"]["all"] = 0;
				$result2["all"]["remain"]["erm"] = [];
				$result2["all"]["clear"]["all"] = 0;
				$result2["all"]["clear"]["erm"] = [];
				$erm_list = $jakdb->query("SELECT DISTINCT(erm) FROM m_dp ORDER BY erm")->fetchAll();
				array_push($erm_list, ["erm" => "", 0 => ""]);
				foreach ($cat_list as $category) {
					$category = ($category == null || $category == "") ? "???" : $category;
					$result2["all"]["unclear"]["cat"][$category] = 0;
				}

				foreach ($erm_list as $erm_key => $erm) {
					$erm["erm"] = ($erm["erm"] == null || $erm["erm"] == "") ? "???" : $erm["erm"];
					$result2[$erm["erm"]] = [];
					$result2["all"]["unclear"]["erm"][$erm["erm"]] = 0;
					$result2["all"]["clear"]["erm"][$erm["erm"]] = 0;
					foreach ($cat_list as $category) {
						$category = ($category == null || $category == "") ? "???" : $category;
						$result2[$erm["erm"]][$category] = 0;
					}
				}

				foreach ($report_2 as $rep_2) {
					$rep_2["title"] = ($rep_2["title"] == null || $rep_2["title"] == "") ? "???" : $rep_2["title"];
					$rep_2["erm"] = ($rep_2["erm"] == null || $rep_2["erm"] == "") ? "???" : $rep_2["erm"];
					//unclear
					if ($rep_2["stat"] == 1) {
						$result2["all"]["unclear"]["all"] 					+= $rep_2["jumlah"];
						$result2["all"]["unclear"]["erm"][$rep_2["erm"]] 	+= $rep_2["jumlah"];
						$result2["all"]["unclear"]["cat"][$rep_2["title"]] 	+= $rep_2["jumlah"];
						$result2[$rep_2["erm"]][$rep_2["title"]] 			+= $rep_2["jumlah"];
						$result2["all"]["remain"]["all"] 					+= $rep_2["jumlah"];
						$result2["all"]["remain"]["erm"][$rep_2["title"]] 	+= $rep_2["jumlah"];
					}
					//clear - today
					else {
						$result2["all"]["clear"]["all"] 					+= $rep_2["jumlah"];
						$result2["all"]["clear"]["erm"][$rep_2["erm"]] 		+= $rep_2["jumlah"];
						$result2["all"]["remain"]["all"] 					-= $rep_2["jumlah"];
						$result2["all"]["remain"]["erm"][$rep_2["title"]] 	-= $rep_2["jumlah"];
					}
				}

				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $report_date);
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				// $col++;
				$row++;
				// $col = $start_col_2;
				foreach ($erm_list as $erm) {
					$erm["erm"] = ($erm["erm"] == null || $erm["erm"] == "") ? "???" : $erm["erm"];
					$col = $start_col_2;
					$col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $erm["erm"]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
					foreach ($cat_list as $category) {
						$category = ($category == null || $category == "") ? "???" : $category;
						$cell_color["fill"]["color"]["rgb"] = $jkl["warna_sbiru1"];
							$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result2[$erm["erm"]][$category]);
						$col++;
					}

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result2["all"]["unclear"]["erm"][$erm["erm"]]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($result2["all"]["unclear"]["erm"][$erm["erm"]] - $result2["all"]["clear"]["erm"][$erm["erm"]]));
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result2["all"]["clear"]["erm"][$erm["erm"]]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, number_format(($result2["all"]["clear"]["erm"][$erm["erm"]]/($result2["all"]["unclear"]["erm"][$erm["erm"]] - $result2["all"]["clear"]["erm"][$erm["erm"]])*100), 2, '.', '')."%");
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);

					$row++;
				}
				$col = $start_col_2;
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "TOTAL");
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$col++;
				foreach ($cat_list as $category) {
					$category = ($category == null || $category == "") ? "???" : $category;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($erm_list)+1)), $category);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result2["all"]["unclear"]["cat"][$category]);

					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($erm_list)+1)))->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($erm_list)+1)))->applyFromArray($cell_color);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
				}
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($erm_list)+1)), "TOTAL SISA KOMPLAIN - YESTERDAY");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result2["all"]["unclear"]["all"]);
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($erm_list)+1)), "TOTAL SISA KOMPLAIN - PRESENT");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($result2["all"]["unclear"]["all"]-$result2["all"]["clear"]["all"]));
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($erm_list)+1)), "TOTAL KOMPLAIN CLEAR - PRESENT");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result2["all"]["clear"]["all"]);
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($erm_list)+1)), "TOTAL SISA KOMPLAIN(%) - PRESENT");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, number_format(($result2["all"]["clear"]["all"]/($result2["all"]["unclear"]["all"]-$result2["all"]["clear"]["all"])*100), 2, '.', '')."%");

				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).($row-(count($erm_list)+1)).":".(PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($erm_list)+1)))->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).($row-(count($erm_list)+1)).":".(PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($erm_list)+1)))->applyFromArray($cell_color);
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
			}


			//====================================== START TABEL REPORT 3 ==============================================

			// $col++; $col++;
			// $start_col_3 = $col;
			$start_col_3 = 0;
			$row = 2;
			$col = 0;

			$report_excel->createSheet();
			$report_excel->setActiveSheetIndex(2);
			$report_excel->getActiveSheet()->setTitle("SISA COMPLAINT TIAP AREA");
			$report_3 = $jakdb->query("
				SELECT dp.erm, dp.area, tp.title, count(*) as jumlah, CASE WHEN st.status IN (1,2) THEN 1 ELSE 2 END AS stat
				FROM hd_support_tickets st
					LEFT JOIN m_dp dp ON dp.id_dp = st.dp_bersalah
					LEFT JOIN hd_ticketpriority tp ON tp.id = st.priorityid
				WHERE (st.status IN (1,2) OR FROM_UNIXTIME(st.ended, '%Y-%m-%d') = '$report_date')
					$dan_1
				GROUP BY CASE WHEN st.status IN (1,2) THEN 1 ELSE 2 END, dp.erm, dp.area, tp.title
				ORDER BY stat, dp.erm, dp.area, tp.title
			")->fetchAll();

			if ($report_3 != null && !empty($report_3)) {
				$result3 = [];
				$result3["all"]["unclear"]["all"] = 0;
				$result3["all"]["unclear"]["area"] = [];
				$result3["all"]["unclear"]["cat"] = [];
				$result3["all"]["remain"]["all"] = 0;
				$result3["all"]["remain"]["area"] = [];
				$result3["all"]["clear"]["all"] = 0;
				$result3["all"]["clear"]["area"] = [];
				$area_list = $jakdb->query("SELECT erm, area FROM m_dp GROUP BY erm, area ORDER BY erm, area")->fetchAll();
				array_push($area_list, ["area" => "", 0 => ""]);
				foreach ($cat_list as $category) {
					$category = ($category == null || $category == "") ? "???" : $category;
					$result3["all"]["unclear"]["cat"][$category] = 0;
				}

				foreach ($area_list as $erm_key => $erm) {
					$erm["area"] 	= ($erm["area"] == null || $erm["area"] == "") ? "???" : $erm["area"];
					$erm["erm"] 	= ($erm["erm"] == null || $erm["erm"] == "") ? "???" : $erm["erm"];
					$result3[$erm["area"]] = [];
					$result3["all"]["unclear"]["area"][$erm["erm"]."-".$erm["area"]] 	= 0;
					$result3["all"]["clear"]["area"][$erm["erm"]."-".$erm["area"]] 		= 0;
					foreach ($cat_list as $category) {
						$category = ($category == null || $category == "") ? "???" : $category;
						$result3[$erm["erm"]."-".$erm["area"]][$category] = 0;
					}
				}

				foreach ($report_3 as $rep_3) {
					$rep_3["title"] = ($rep_3["title"] == null || $rep_3["title"] == "") ? "???" : $rep_3["title"];
					$rep_3["area"] = ($rep_3["area"] == null || $rep_3["area"] == "") ? "???" : $rep_3["area"];
					$rep_3["erm"] = ($rep_3["erm"] == null || $rep_3["erm"] == "") ? "???" : $rep_3["erm"];
					//unclear
					if ($rep_3["stat"] == 1) {
						$result3["all"]["unclear"]["all"] 										+= $rep_3["jumlah"];
						$result3["all"]["unclear"]["area"][$rep_3["erm"]."-".$rep_3["area"]] 	+= $rep_3["jumlah"];
						$result3["all"]["unclear"]["cat"][$rep_3["title"]] 						+= $rep_3["jumlah"];
						$result3[$rep_3["erm"]."-".$rep_3["area"]][$rep_3["title"]] 			+= $rep_3["jumlah"];
						$result3["all"]["remain"]["all"] 										+= $rep_3["jumlah"];
						$result3["all"]["remain"]["area"][$rep_3["title"]] 						+= $rep_3["jumlah"];
					}
					//clear - today
					else {
						$result3["all"]["clear"]["all"] 					+= $rep_3["jumlah"];
						$result3["all"]["clear"]["area"][$rep_3["erm"]."-".$rep_3["area"]] 	+= $rep_3["jumlah"];
						$result3["all"]["remain"]["all"] 					-= $rep_3["jumlah"];
						$result3["all"]["remain"]["area"][$rep_3["title"]] 	-= $rep_3["jumlah"];
					}
				}

				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $report_date);
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Area");
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				// $col++;
				$row++;
				// $col = $start_col_3;
				foreach ($area_list as $area) {
					$area["area"] = ($area["area"] == null || $area["area"] == "") ? "???" : $area["area"];
					$area["erm"] = ($area["erm"] == null || $area["erm"] == "") ? "???" : $area["erm"];
					$col = $start_col_3;
					$col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $area["erm"]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $area["area"]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
					foreach ($cat_list as $category) {
						$category = ($category == null || $category == "") ? "???" : $category;
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result3[$area["erm"]."-".$area["area"]][$category]);
						$cell_color["fill"]["color"]["rgb"] = $jkl["warna_sbiru1"];
						$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
						$col++;
					}
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result3["all"]["unclear"]["area"][$area["erm"]."-".$area["area"]]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($result3["all"]["unclear"]["area"][$area["erm"]."-".$area["area"]] - $result3["all"]["clear"]["area"][$area["area"]]));
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result3["all"]["clear"]["area"][$area["area"]]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_biru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, number_format(($result3["all"]["clear"]["area"][$area["area"]]/($result3["all"]["unclear"]["area"][$area["erm"]."-".$area["area"]] - $result3["all"]["clear"]["area"][$area["area"]])*100), 2, '.', '')."%");
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);

					$row++;
				}
				$col = $start_col_3;
				$col++;
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "TOTAL");
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col-1)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col-1)).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
				$col++;
				foreach ($cat_list as $category) {
					$category = ($category == null || $category == "") ? "???" : $category;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($area_list)+1)), $category);
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result3["all"]["unclear"]["cat"][$category]);
					$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($area_list)+1)))->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($area_list)+1)))->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
					$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);
					$col++;
				}
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($area_list)+1)), "TOTAL SISA KOMPLAIN - YESTERDAY");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result3["all"]["unclear"]["all"]);
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($area_list)+1)), "TOTAL SISA KOMPLAIN - PRESENT");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($result3["all"]["unclear"]["all"]-$result3["all"]["clear"]["all"]));
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($area_list)+1)), "TOTAL KOMPLAIN CLEAR - PRESENT");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result3["all"]["clear"]["all"]);
				$col++;
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($row-(count($area_list)+1)), "TOTAL SISA KOMPLAIN(%) - PRESENT");
				$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, number_format(($result3["all"]["clear"]["all"]/($result3["all"]["unclear"]["all"]-$result3["all"]["clear"]["all"])*100), 2, '.', '')."%");

				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).($row-(count($area_list)+1)).":".(PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($area_list)+1)))->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).($row-(count($area_list)+1)).":".(PHPExcel_Cell::stringFromColumnIndex($col)).($row-(count($area_list)+1)))->applyFromArray($cell_color);
				$cell_color["fill"]["color"]["rgb"] = $jkl["warna_dbiru1"];
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($text_white);
				$report_excel->getActiveSheet()->getStyle((PHPExcel_Cell::stringFromColumnIndex(($col-3))).$row.":".(PHPExcel_Cell::stringFromColumnIndex($col)).$row)->applyFromArray($cell_color);

				if(JAK_SUPERADMINACCESS == 1) {
					$row = 2;
					$col = 1;

					$report_excel->createSheet();
					$report_excel->setActiveSheetIndex(3);
					$report_excel->getActiveSheet()->setTitle("TOP 10 MOST COMPLAIN");
					$tahun_ini 		= intval(date("Y"));
					$bulan_ini 		= intval(date("m"));
					$hari_ini 		= intval(date("d"));
					$bulan_before 	= [];
					$worse_dp 		= [];
					$rank 			= [];
					$d_bulan_lalu 	= [];

					for ($i=1; $i < $bulan_ini; $i++) { 
						array_push($bulan_before, $i);
					}

					$d_bulan_ini = $jakdb->query("
						SELECT
							st.dp_bersalah, COUNT(*) AS jumlah, dp.droppoint, dp.area
						FROM hd_support_tickets st
							LEFT JOIN m_dp dp ON st.dp_bersalah = dp.id_dp
						WHERE YEAR(FROM_UNIXTIME(st.initiated)) = YEAR(CURRENT_DATE()) AND MONTH(FROM_UNIXTIME(st.initiated)) = $bulan_ini
						GROUP BY st.dp_bersalah
						ORDER BY jumlah DESC, st.dp_bersalah ASC
						LIMIT 10;
					")->fetchAll();

					$get_rank = $jakdb->query("
						SELECT
							st.dp_bersalah, COUNT(*) AS jumlah
						FROM hd_support_tickets st
						WHERE YEAR(FROM_UNIXTIME(st.initiated)) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 YEAR)) AND MONTH(FROM_UNIXTIME(st.initiated)) = $bulan_ini
						GROUP BY st.dp_bersalah
						ORDER BY jumlah DESC, st.dp_bersalah ASC;
					")->fetchAll();

					foreach ($d_bulan_ini as $data) {
						array_push($worse_dp, $data["dp_bersalah"]);
					}

					foreach ($get_rank as $the_rank => $last_year_rank) {
						if (in_array($last_year_rank["dp_bersalah"], $worse_dp)) {
							$rank[$last_year_rank["dp_bersalah"]] = ($the_rank+1);
						}
					}

					foreach ($bulan_before as $b_before) {
						$d_this_month = $jakdb->query("
							SELECT
								st.dp_bersalah, DAY(LAST_DAY(FROM_UNIXTIME(st.initiated))) AS hari, COUNT(*) AS jumlah
							FROM hd_support_tickets st
							WHERE YEAR(FROM_UNIXTIME(st.initiated)) = YEAR(CURRENT_DATE()) AND MONTH(FROM_UNIXTIME(st.initiated)) = $b_before
							GROUP BY st.dp_bersalah
							ORDER BY jumlah DESC, st.dp_bersalah ASC;
						")->fetchAll();

						array_push($d_bulan_lalu, $d_this_month);
					}

					$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+7+(count($bulan_before)))).$row);
					$cell_color["fill"]["color"]["rgb"] = "AA94A7";
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "COMPLAINT DAILY PROGRESS ".date("M Y"));
					$row++;

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Data (".date("Y", strtotime("-1 years")).")");
					$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+4)).$row);
					$col = $col+5;

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Data (".date("Y").")");
					$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+(count($bulan_before))-1)).$row);
					$col = $col+(count($bulan_before));

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "1 - ".date("d")." ".date("M")." ".date("Y"));
					$report_excel->getActiveSheet()->mergeCells((PHPExcel_Cell::stringFromColumnIndex($col)).$row.':'.(PHPExcel_Cell::stringFromColumnIndex($col+2)).$row);
					$col = 1;
					$row++;

					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Old Rank"); $col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "New Rank"); $col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Droppoint"); $col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Code"); $col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "RM"); $col++;
					foreach ($bulan_before as $bulan_b) {
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "POINT ".date("M", strtotime(date("Y")."-".$bulan_b."-".date("d")))); $col++;
					}
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "NUM COM"); $col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "AVE"); $col++;
					$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "DESC"); $col++;

					$report_excel->getActiveSheet()->getStyle("B".($row-2).":".(PHPExcel_Cell::stringFromColumnIndex($col-1)).$row)->applyFromArray($cell_color);
					$report_excel->getActiveSheet()->getStyle("B".($row-2).":".(PHPExcel_Cell::stringFromColumnIndex($col-1)).$row)->applyFromArray($text_white);

					$col = 1;
					$row++;
					foreach ($d_bulan_ini as $rank_bulanini => $bulanini) {
						if (!empty($rank)) {
							foreach ($rank as $dp_last_year_rank => $last_year_rank) {
								if ($dp_last_year_rank == $bulanini["dp_bersalah"]) {
									$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $last_year_rank);
									break;
								} else {
									$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "-");
								}
							}
						} else {
							$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "-");
						}
						$col++;
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($rank_bulanini+1));
						$col++;
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $bulanini["droppoint"]);
						$col++;
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $bulanini["dp_bersalah"]);
						$col++;
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $bulanini["area"]);
						$col++;
						foreach ($d_bulan_lalu as $bulanlalu) {
							foreach ($bulanlalu as $d_bulanlalu) {
								if ($d_bulanlalu["dp_bersalah"] == $bulanini["dp_bersalah"]) {
									$isi_cell = ($d_bulanlalu["jumlah"] == 0 || $d_bulanlalu["jumlah"] == "" || $d_bulanlalu["jumlah"] == null) ? 0 : ($d_bulanlalu["jumlah"]/$d_bulanlalu["hari"]);
									$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $isi_cell);
									break;
								} else {
									$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "0");
								}
							}
							$col++;
						}
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $bulanini["jumlah"]);
						$col++;
						$report_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($bulanini["jumlah"]/$hari_ini));
						$report_excel->getActiveSheet()->getStyle("B2:".(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row)->applyFromArray($thin_border);
						$report_excel->getActiveSheet()->getStyle("B2:".(PHPExcel_Cell::stringFromColumnIndex($col+1)).$row)->applyFromArray($text_center);
						$col++;
						$row++;
						$col = 1;
					}
				}
			}

			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=abc.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			$objWriter = PHPExcel_IOFactory::createWriter($report_excel, 'Excel5');

			$objWriter->save('php://output');
			unset($objPHPExcel);
		}
		// echo "<pre>";
		// var_dump($d_bulan_lalu);
		// var_dump($result3);
		exit;
  	break;
	default:
	echo "default";
	exit;
}

?>