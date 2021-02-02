<!DOCTYPE html>
<html lang="<?php echo $row["lang"];?>">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="keywords" content="<?php echo (isset($row["meta_keywords"]) ? trim($row["meta_keywords"]) : '');?>">
	<meta name="description" content="<?php echo (isset($row["meta_description"]) ? trim($row["meta_description"]) : '');?>">
	<meta name="author" content="<?php echo JAK_TITLE;?>">
	<?php if ($page == '404') { ?>
	<meta name="robots" content="noindex, follow">
	<?php } else { ?>
	<meta name="robots" content="index, follow">
	<?php } if ($page == "success" or $page == "logout") { ?>
	<meta http-equiv="refresh" content="1;URL=<?php echo $_SERVER['HTTP_REFERER'];?>">
	<?php } ?>
	<title><?php echo $row["title"];?> - <?php echo JAK_TITLE;?></title>
	<?php if (isset($row["ogimg"]) && !empty($row["ogimg"])) { ?>
	<meta property="og:title" content="<?php echo $row["title"];?>">
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo JAK_rewrite::jakParseurl($page, $page1, $page2, $page3, $page4, $page5, $page6);?>">
	<meta property="og:image" content="<?php echo (JAK_USE_APACHE ? substr(BASE_URL, 0, -1) : BASE_URL).$row["ogimg"];?>">
	<meta property="og:description" content="<?php echo trim($row["meta_description"]);?>">
	<?php if (!empty(JAK_FACEBOOK_APP_ID)) { ?><meta property="fb:app_id" content="<?php echo JAK_FACEBOOK_APP_ID;?>"><?php } ?>
	<?php } ?>
	
	<link rel="canonical" href="<?php echo JAK_rewrite::jakParseurl($page, $page1, $page2, $page3, $page4, $page5, $page6);?>">
	<link rel="alternate" hreflang="<?php echo (isset($row["lang"]) ? $row["lang"] : '');?>" href="<?php echo BASE_URL;?>">

	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

	<link rel="shortcut icon" href="<?php echo BASE_URL;?>img/ico/favicon.ico" />

	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/fontawesome.css" media="screen">
	<link rel="stylesheet" href="<?php echo BASE_URL;?>template/<?php echo JAK_FRONT_TEMPLATE;?>/css/style.css" media="screen">
	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/dropzone.css">

	<?php if ($jkl["rtlsupport"]) { ?>
  	<!-- RTL Support -->
  	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/style-rtl.css?=<?php echo JAK_UPDATED;?>" type="text/css" media="screen">
  	<!-- End RTL Support -->
  	<?php } ?>

	<?php if (JAK_USERID && jak_get_access("answers", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
	<link rel="stylesheet" href="<?php echo BASE_URL;?>template/<?php echo JAK_FRONT_TEMPLATE;?>/editor/content-tools.min.css" type="text/css" media="screen">
	<?php } ?>

</head>
<body class="<?php echo (isset($row["custom2"]) && !empty($row["custom2"]) ? 'login-page ' : '');?>sidebar-collapse">

<!--[if lt IE 9]>
<div class="alert alert-danger">We have dropped Support for IE9 and below. Please update your browser or use the latest Firefox, Chrome or Safari.</div>
<![endif]-->

<?php if (JAK_HOLIDAY_MODE == 1 && JAK_USERID) { ?>
<div class="alert-offline"><?php echo $tl["title"]["t10"];?></div>
<?php } ?>

<?php if (!isset($_SESSION["webembed"])) { ?>
<!-- Navbar -->
<nav class="navbar fixed-top navbar-expand-lg" id="sectionsNav">
	<div class="container">

	  	<!-- Logo -->
		<!-- <a class="navbar-brand header-title" href="<?php echo BASE_URL;?>"><?php echo JAK_TITLE;?></span></a> -->
		<a href="<?php echo BASE_URL;?>">
			<img alt="Logo" src="<?php echo BASE_URL ?>files/logo-red-flat.png" class="logo-default h-25px" style="height:25px;"/>
		</a>

		<button class="navbar-toggler" type="button" data-toggle="collapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="sr-only">Toggle navigation</span>
          <span class="navbar-toggler-icon"></span>
          <span class="navbar-toggler-icon"></span>
          <span class="navbar-toggler-icon"></span>
        </button>
			
		<div class="collapse navbar-collapse" id="main-navbar-cont">
		<!-- Menu -->
		<?php echo jak_build_menu(0, $mheader, $page, 'navbar-nav ml-auto', 'dropdown', 'dropdown-menu', '', false, JAK_USERISLOGGED, 'nav-item', 'nav-link');?>
		</div>

	</div>
</nav>
<?php echo (isset($row["custom2"]) && !empty($row["custom2"]) ? '<div class="page-header header-filter" style="background-image: url(\''.BASE_URL.'template/'.JAK_FRONT_TEMPLATE.'/img/'.(isset($row["custom"]) && !empty($row["custom"]) ? $row["custom"] : "bg7.jpg").'\'); background-size: cover; background-position: top center;">' : '<div class="page-header header-filter header-small" data-parallax="true" style="background-image: url(\''.BASE_URL.'template/'.JAK_FRONT_TEMPLATE.'/img/'.(isset($row["custom"]) && !empty($row["custom"]) ? $row["custom"] : "bg3.jpg").'\');">
	<div class="container">
      <div class="row">
        <div class="col-md-2 ml-auto mr-auto text-center">
          <h1 class="title">'.(isset($JAK_FORM_DATA["title"]) ? $JAK_FORM_DATA["title"] : $row["title"]).'</h1>
          '.(isset($JAK_FORM_DATA['time']) ? '<h4>'.JAK_base::jakTimesince($JAK_FORM_DATA['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT).'</h4>' : '').'
        </div>
      </div>
    </div>
    </div>
    <div class="main main-raised">
    <div class="section">');?>
	<div class="container">
      <div class="row">
<?php } ?>