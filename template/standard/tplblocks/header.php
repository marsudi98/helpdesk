<!DOCTYPE html>
<html lang="<?php echo $row["lang"];?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="keywords" content="<?php echo trim($row["meta_keywords"]);?>">
	<meta name="description" content="<?php echo trim($row["meta_description"]);?>">
	<meta name="author" content="<?php echo JAK_TITLE;?>">
	<?php if ($page == '404') { ?>
	<meta name="robots" content="noindex, follow">
	<?php } else { ?>
	<meta name="robots" content="index, follow">
	<?php } if ($page == "success" or $page == "logout") { ?>
	<meta http-equiv="refresh" content="1;URL=<?php echo $_SERVER['HTTP_REFERER'];?>">
	<?php } ?>
	<title><?php echo $row["title"];?></title>
	<?php if (isset($row["ogimg"]) && !empty($row["ogimg"])) { ?>
	<meta property="og:title" content="<?php echo $row["title"];?>">
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo JAK_rewrite::jakParseurl($page, $page1, $page2, $page3, $page4, $page5, $page6);?>">
	<meta property="og:image" content="<?php echo (JAK_USE_APACHE ? substr(BASE_URL, 0, -1) : BASE_URL).$row["ogimg"];?>">
	<meta property="og:description" content="<?php echo trim($row["meta_description"]);?>">
	<?php if (!empty(JAK_FACEBOOK_APP_ID)) { ?><meta property="fb:app_id" content="<?php echo JAK_FACEBOOK_APP_ID;?>"><?php } ?>
	<?php } ?>
	
	<link rel="canonical" href="<?php echo JAK_rewrite::jakParseurl($page, $page1, $page2, $page3, $page4, $page5, $page6);?>">
	<link rel="alternate" hreflang="<?php echo $row["lang"];?>" href="<?php echo BASE_URL;?>">

	<!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet"> -->

	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/stylesheet.css" media="screen">
	<link rel="stylesheet" href="<?php echo BASE_URL;?>template/<?php echo JAK_FRONT_TEMPLATE;?>/css/style.css" media="screen">
	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/dropzone.css">

	<?php if ($jkl["rtlsupport"]) { ?>
  	<!-- RTL Support -->
  	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/style-rtl.css?=<?php echo JAK_UPDATED;?>" type="text/css" media="screen">
  	<!-- End RTL Support -->
  	<?php } ?>

	<?php if (JAK_USERID && jak_get_access("answers", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
	<link rel="stylesheet" href="<?php echo BASE_URL;?>template/<?php echo JAK_FRONT_TEMPLATE;?>/editor/content-tools.min.css" media="screen">
	<?php } ?>

</head>
<body>

<!--[if lt IE 9]>
<div class="alert alert-danger">We have dropped Support for IE9 and below. Please update your browser or use the latest Firefox, Chrome or Safari.</div>
<![endif]-->

<?php if (JAK_HOLIDAY_MODE == 1 && JAK_USERID) { ?>
<div class="alert-offline"><?php echo $tl["title"]["t10"];?></div>
<?php } ?>

<?php if (!isset($_SESSION["webembed"])) { ?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-main">
	<div class="container">

	  	<!-- Logo -->
		<a class="navbar-brand" href="<?php echo BASE_URL;?>"><img src="<?php echo BASE_URL;?>template/<?php echo JAK_FRONT_TEMPLATE;?>/img/logo.png" class="d-inline-block align-top" id="main-logo" alt="logo"><span class="sr-only"><?php echo $row["title"];?></span></a>

		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-navbar-cont" aria-controls="main-navbar-cont" aria-expanded="false" aria-label="Toggle navigation">
		    <i class="fa fa-bars"></i>
		</button>
			
		<div class="collapse navbar-collapse" id="main-navbar-cont">
		<!-- Menu -->
		<?php echo jak_build_menu(0, $mheader, $page, 'navbar-nav mr-auto', 'dropdown', 'dropdown-menu', '', false, JAK_USERISLOGGED, 'nav-item', 'nav-link');?>
		</div>

	</div>
</nav>
<?php } ?>