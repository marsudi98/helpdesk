<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.1                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_PREVENT_ACCESS')) die('You cannot access this file directly.');

// OK the chat window is already open, let's redirect
if (isset($_SESSION["slidestatus"]) && $_SESSION["slidestatus"] == "open") {
	if ($online_op) {
		jak_redirect($chatstarturl);
	} else {
		jak_redirect($chatcontacturl);
	}
}

// We set the session for open or not
if (!isset($_SESSION["slidestatus"])) $_SESSION["slidestatus"] = "closed";

// buffer flush
ob_start();
?>
<!DOCTYPE html>
<html lang="<?php echo $BT_LANGUAGE;?>">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="author" content="Live Chat 3">
	<title><?php echo $jkl["g"];?> - <?php echo JAK_TITLE;?></title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/compress.css.php" media="screen">

	<!-- Custom Styles -->
	<?php include_once('package/'.$jakwidget['template'].'/style.php');?>
	
	<!--[if lt IE 9]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<script src="js/respond_ie.js"></script>
	<![endif]-->
	
	<?php if ($jkl["rtlsupport"]) { ?>
  	<!-- RTL Support -->
  	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/style-rtl.css?=<?php echo JAK_UPDATED;?>" type="text/css" media="screen">
  	<!-- End RTL Support -->
  	<?php } ?>

	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" href="<?php echo BASE_URL;?>img/ico/favicon.ico">
	 
</head>
<body>

<?php include_once('package/'.$jakwidget['template'].'/btn.php');?>

<script type="text/javascript" src="<?php echo BASE_URL;?>js/resizer.js?=<?php echo JAK_UPDATED;?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/howler.js?=<?php echo JAK_UPDATED;?>"></script>
<?php if (!empty($jakgraphix["btnjs"])) echo '<script type="text/javascript" src="'.BASE_URL.'package/'.$jakwidget['template'].'/'.$jakgraphix["btnjs"].'"></script>';?>
<script type="text/javascript">
	// The Widget ID
	lcjakwidgetid = <?php echo $_SESSION['widgetid'];?>;
	// The Base Url
	base_url = '<?php echo BASE_URL;?>';
	cross_url = '<?php echo (isset($_SESSION["crossurl"]) ? $_SESSION["crossurl"] : BASE_URL);?>';
	apply_animation = '<?php echo $jakwidget['btn_animation'];?>';
	// The Iframe Height/Width
	var btncont = document.getElementById('lcjframesize');
	var width = btncont.offsetWidth;
	var height = btncont.offsetHeight;
	iframe_resize(width, height, "<?php echo jak_html_widget_css($jakwidget['floatpopup'], $jakwidget['floatcss'], $jakwidget['floatcsschat']);?>", cross_url);
</script>
</body>
</html>
<?php ob_flush(); ?>