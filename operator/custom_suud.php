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
		echo "<pre>";
		$query = $jakdb->query("
		SELECT st.operatorid, st.priorityid, tp.title, us.username, COUNT(st.id) as jumlah,
			CASE
				WHEN st.ended = 0 THEN 3
				WHEN (UNIX_TIMESTAMP(CONCAT(st.duedate, ' 23:59:59')) - st.ended) < 0 THEN 2
				ELSE 1
			END AS prior
		FROM hd_support_tickets st
			RIGHT JOIN hd_ticketpriority tp ON st.priorityid = tp.id
			INNER JOIN hd_user us ON st.operatorid = us.id
		GROUP BY st.operatorid, st.priorityid,
			CASE
				WHEN st.ended = 0 THEN 3
				WHEN (UNIX_TIMESTAMP(CONCAT(st.duedate, ' 23:59:59')) - st.ended) < 0 THEN 2
				ELSE 1
			END
		ORDER BY prior, st.priorityid
		")->fetchAll();
		if ($query != null && !empty($query)) {
			include '../class/PHPExcel/IOFactory.php';
			
			$operator_list 	= [];
			$cat_list 		= [];
			$cat_list_query 	= $jakdb->query(" SELECT title FROM hd_ticketpriority ")->fetchAll();

			$result["operator_complain"] 					= [];
			$result["global_complain"] 						= [];
			$result["global_complain"]["clear"] 			= [];
			$result["global_complain"]["late"] 				= [];
			$result["global_complain"]["unclear"] 			= [];
			$result["global_complain"]["all"] 				= 0;
			$result["global_complain"]["clear"]["all"] 		= 0;
			$result["global_complain"]["late"]["all"] 		= 0;
			$result["global_complain"]["unclear"]["all"] 	= 0;

			foreach ($cat_list_query as $title) {
				array_push($cat_list, $title["title"]);
				$result["global_complain"]["clear"][$title["title"]] = 0;
				$result["global_complain"]["late"][$title["title"]] = 0;
				$result["global_complain"]["unclear"][$title["title"]] = 0;
			}

			foreach ($query as $data) {
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

					foreach ($cat_list_query as $title) {
						$result["operator_complain"][$data["username"]]["clear"][$title["title"]] 	= 0;
						$result["operator_complain"][$data["username"]]["late"][$title["title"]] 	= 0;
						$result["operator_complain"][$data["username"]]["unclear"][$title["title"]] = 0;
					}
				}

				if ($data["prior"] == 1) {			//case clear
					$result["global_complain"]["clear"]["all"] += $data["jumlah"];
					$result["global_complain"]["clear"][$data["title"]] += $data["jumlah"];
					$result["operator_complain"][$data["username"]]["clear"][$data["title"]] += $data["jumlah"];
				} elseif ($data["prior"] == 2) {	//case clear but late
					$result["global_complain"]["clear"]["all"] 	+= $data["jumlah"];
					$result["global_complain"]["late"]["all"] 	+= $data["jumlah"];
					$result["global_complain"]["clear"][$data["title"]] += $data["jumlah"];
					$result["global_complain"]["late"][$data["title"]] += $data["jumlah"];
					$result["operator_complain"][$data["username"]]["clear"][$data["title"]] += $data["jumlah"];
					$result["operator_complain"][$data["username"]]["late"][$data["title"]] += $data["jumlah"];
				} else {							//case unclear
					$result["global_complain"]["unclear"]["all"] += $data["jumlah"];
					$result["global_complain"]["unclear"][$data["title"]] += $data["jumlah"];
					$result["operator_complain"][$data["username"]]["unclear"][$data["title"]] += $data["jumlah"];
				}
				$result["global_complain"]["all"] += $data["jumlah"];


			}

			var_dump($result);
			var_dump($operator_list);
			var_dump($cat_list);

			exit;
		}

		echo "haii";
		exit;
  	break;
	default:
	echo "default";
	exit;
}

?>