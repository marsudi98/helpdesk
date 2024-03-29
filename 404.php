<!DOCTYPE html>
<html lang="<?php echo $BT_LANGUAGE;?>">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="author" content="Live Chat Business">
	<title><?php echo $jkl["g2"];?> - <?php echo JAK_TITLE;?></title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?php echo BASE_URL;?>css/compress.css.php" media="screen">
	
	<!--[if lt IE 9]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
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
 
<div class="navbar navbar-default">
	<div class="container">
    	<div class="navbar-header">
        	<a class="navbar-brand" href="<?php echo $_SERVER['REQUEST_URI'];?>"><?php echo $jkl["g2"];?></a> - <a class="navbar-brand" href="<?php echo BASE_URL;?>"><?php echo JAK_TITLE;?></a>
    	</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<p><?php echo $jkl["e5"];?>
			<ul>
				<li><?php echo $jkl["e6"];?></li>
				<li><?php echo $jkl["e7"];?></li>
				<li><?php echo $jkl["e8"];?></li>
			</ul>
			</p>
		</div>
	</div>
	
</div>
</body>
</html>