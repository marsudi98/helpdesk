<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-faq-article">
	<div class="container">
		<div class="row">
  			<div class="col-md-9">

  				<div class="faq-header">
  					<span class="green-bg"><i class="fa fa-question-circle-o"></i></span>
  					<h1><?php echo $JAK_FORM_DATA["title"];?><br><small class="text-muted"><?php echo $incat;?></small></h1>
  				</div>

  				<div class="faq-content">
  					<div class="faq-inner-content">
  					<?php echo $JAK_FORM_DATA["content"];?>
  					<p class="info-faq"><?php echo JAK_base::jakTimesince($JAK_FORM_DATA['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
  					</div>
  				</div>

  				<div class="faq-rating">
  					<h5><?php echo $jkl['hd77'];?></h5>
	  				<a href="javascript:void(0)" data-cvote="up" data-id="<?php echo $JAK_FORM_DATA["id"];?>" class="jak-cvote"><i class="fa fa-thumbs-up"></i></a>
	                <a href="javascript:void(0)" data-cvote="down" data-id="<?php echo $JAK_FORM_DATA["id"];?>" class="jak-cvote"><i class="fa fa-thumbs-down"></i></a>
	                <!-- Votes -->
	                <span id="jak-cvotec<?php echo $JAK_FORM_DATA["id"];?>" class="label label-<?php echo jak_comment_votes($JAK_FORM_DATA["votes"]);?>'"><?php echo $JAK_FORM_DATA["votes"];?></span>
	            </div>

  				<div class="alert alert-secondary">
  					<div class="row">
  						<div class="col-10">
		  					<?php if ($JAK_FORM_DATA["socialbutton"]) { ?>
		  					<div id="sharing-sucks"></div>
		  					<?php } ?>
		  				</div>
		  				<div class="col-2">
		  					<div class="pull-right">
							<?php if ($JAK_NAV_PREV) { ?>
								<a class="btn btn-danger" href="<?php echo $JAK_NAV_PREV;?>" title="<?php echo $JAK_NAV_PREV_TITLE;?>"><i class="fa fa-arrow-left"></i></a>
							<?php } if ($JAK_NAV_NEXT) { ?>
								<a class="btn btn-danger" href="<?php echo $JAK_NAV_NEXT;?>" title="<?php echo $JAK_NAV_NEXT_TITLE;?>"><i class="fa fa-arrow-right"></i></a>
							<?php } ?>
							</div>
		  				</div>
  					</div>
  				</div>

			</div>
			<div class="col-md-3">
				<?php if (isset($_SESSION["webembed"])) { ?>
				<p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL);?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i></a></p>
				<?php } else { ?>
				<div class="faq-categories">
					<?php if (isset($allcategories) && !empty($allcategories)) { ?>
					<h4><?php echo $jkl['hd76'];?></h4>
					<ul class="list-unstyled faq-list mt-3">
				    <?php foreach ($allcategories as $c) { ?>
				      <li>
				        <dl class="row">
				        <dt class="col-sm-1"><i class="fa fa-chevron-right"></i></dt>
				        <dd class="col-sm-10">
				         <p class="mb-0"><a href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'c', $c["id"], JAK_rewrite::jakCleanurl($c["title"]));?>" class="text-danger"><?php echo $c["title"];?></a></p>
				        </dd>
				        </dl>
				        <hr>
				      </li>
				    <?php } ?>
				  </ul>
				  <?php } ?>
				</div>
				<div class="faq-similar">
					<?php if (isset($similarart) && !empty($similarart)) { ?>
					<h4><?php echo $jkl['hd78'];?></h4>
					<ul class="list-unstyled faq-list mt-3">
				    <?php foreach ($similarart as $s) { ?>
				      <li>
				        <dl class="row">
				        <dt class="col-sm-1"><i class="fa fa-chevron-right"></i></dt>
				        <dd class="col-sm-10">
				         <p class="mb-0"><a href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $s["id"], JAK_rewrite::jakCleanurl($s["title"]));?>" class="text-danger"><?php echo $s["title"];?></a></p>
				        </dd>
				        </dl>
				        <hr>
				      </li>
				    <?php } ?>
				  </ul>
				<?php } ?>
				</div>
				<?php } ?>
			</div>
  		</div>
  	</div>
</div>

<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>