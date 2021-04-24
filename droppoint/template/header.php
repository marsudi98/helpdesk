<!DOCTYPE html>
<html lang="<?php echo $USER_LANGUAGE;?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no">
  <meta name="description" content="HelpDesk 3 the most affordable support solution for your business.">
  <meta name="keywords" content="Your premium Live Support/Chat application from JAKWEB">
  <meta name="author" content="HelpDesk 3">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <title><?php echo $SECTION_TITLE;?> - <?php echo JAK_TITLE;?></title>

  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
  <link rel="stylesheet" href="<?php echo BASE_URL_ORIG;?>css/stylesheet.css?=<?php echo JAK_UPDATED;?>" type="text/css" media="screen">
  <link rel="stylesheet" href="<?php echo BASE_URL;?>css/screen.css?=<?php echo JAK_UPDATED;?>" type="text/css" media="screen">
  <?php if ($css_file_header) { ?>
  <link rel="stylesheet" href="<?php echo $css_file_header;?>" type="text/css">
  <?php } ?>
  
  <?php if ($jkl["rtlsupport"]) { ?>
  <!-- RTL Support -->
  <link rel="stylesheet" href="<?php echo BASE_URL_ORIG;?>css/style-rtl.css?=<?php echo JAK_UPDATED;?>" type="text/css" media="screen">
  <!-- End RTL Support -->
  <?php } ?>
  
  <!-- Le fav and touch icons -->
  <link rel="shortcut icon" href="<?php echo BASE_URL_ORIG;?>img/ico/favicon.ico">
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo BASE_URL_ORIG;?>img/ico/144.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo BASE_URL_ORIG;?>img/ico/114.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo BASE_URL_ORIG;?>img/ico/72.png">
  <link rel="apple-touch-icon-precomposed" href="<?php echo BASE_URL_ORIG;?>img/ico/57.png">
   
</head>
  <body>

  <div class="wrapper">
    <div class="sidebar">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
      -->
      <div class="logo">
        <a href="<?php echo BASE_URL_ADMIN;?>" class="simple-text logo-mini">
          DP
        </a>
        <a href="<?php echo BASE_URL_ADMIN;?>" class="simple-text logo-normal">
          J&T Express
        </a>
        <div class="navbar-minimize">
          <button id="minimizeSidebar" class="btn btn-simple btn-icon btn-neutral btn-round">
            <i class="fas fa-ellipsis-h text_align-center visible-on-sidebar-regular"></i>
            <i class="fas fa-ellipsis-v visible-on-sidebar-mini"></i>
          </button>
        </div>
      </div>
      <div class="sidebar-wrapper" id="sidebar-wrapper">
        <div class="user">
          <div class="photo">
            <img src="<?php echo BASE_URL_ORIG.basename(JAK_FILES_DIRECTORY).$jakclient->getVar("picture");?>" alt="operator image">
          </div>
          <div class="info">
            <a href="<?php echo JAK_rewrite::jakParseurl('users','edit',JAK_USERID);?>">
              <span>
                <?php echo $jakclient->getVar("name"); ?>
              </span>
            </a>
          </div>
        </div>
        <?php include_once 'navbar.php';?>
      </div>
    </div>
    <div class="main-panel" id="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent bg-primary navbar-absolute">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand d-none d-sm-none d-md-block" href="javascript:void(0)"><?php echo $SECTION_TITLE;?></a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <form action="<?php echo JAK_rewrite::jakParseurl('search');?>" method="post">
              <div class="input-group no-border">
                <input type="text" name="sitesearch" value="" class="form-control" placeholder="<?php echo $jkl['s5'];?>">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <i class="fas fa-search"></i>
                  </div>
                </div>
              </div>
            </form>
            <ul class="navbar-nav">
              <li class="nav-item">
                <a href="<?php echo JAK_rewrite::jakParseurl('logout');?>" class="nav-link btn-confirm" data-title="<?php echo addslashes($jkl["l18"]);?>" data-text="<?php echo addslashes($jkl["l20"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>" title="<?php echo $jkl["l18"];?>"><i class="fas fa-power-off"></i></a>
              </li>
            </ul>
          </div>
        </div>
      </nav><!-- End Navbar -->
    <div class="panel-header panel-header-sm">
    </div><!-- End Header -->
    <div class="content" id="topAlerts" style="display:none">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-stats">
            <div class="card-body">

              <!-- Show the user that the connection to the server has been interrupted -->
              <div class="alert alert-danger" id="connection-error" style="display:none"><i class="fa fa-exclamation-triangle"></i> <?php echo $jkl["g331"];?></div>

              <!-- Transfer Message -->
              <div id="transfer"></div>

            </div>
          </div>
        </div>
    </div>
  </div>

