<?php include_once APP_PATH.'template/modern/tplblocks/header.php';

$results = $row["content"];
$pattern = "/<p>{([a-z_]+)}<\/p>/";
$results = preg_replace_callback($pattern, "produce_replacement", $results);

echo $results;

include_once APP_PATH.'template/modern/tplblocks/footer.php';?>


