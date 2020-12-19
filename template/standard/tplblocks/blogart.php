<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-blog-article">
	<div class="container">
		<div class="row">
  			<div class="col-md-9">

  				<div class="blog-header">
  					<span class="red-bg"><i class="fa fa-file-text-o"></i></span>
  					<h1><?php echo $JAK_FORM_DATA["title"];?><br><small class="text-muted"><?php echo JAK_base::jakTimesince($JAK_FORM_DATA['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></small></h1>
  				</div>

  				<?php if ($JAK_FORM_DATA["previmg"]) { ?>
  				<div class="blog-preview">
  					<img class="card-img-top img-fluid" src="<?php echo BASE_URL.$JAK_FORM_DATA["previmg"];?>" alt="<?php echo $JAK_FORM_DATA["title"];?>">
  				</div>
  				<?php } ?>

  				<div class="blog-content">
  					<div class="blog-inner-content">
  					<?php echo $JAK_FORM_DATA["content"];?>
  					</div>
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


  				<!-- Comments -->
  				<?php if ($JAK_FORM_DATA["comments"]) { ?>
  				<div class="blog-comments">
					<h4><?php echo sprintf($jkl['hd65'], '<span id="cComT">'.$JAK_COMMENTS_TOTAL.'</span>');?></h4>
					<div class="post-coments mt-3">
						<?php if (isset($JAK_COMMENTS) && !empty($JAK_COMMENTS)) { echo jak_build_comments(0, $JAK_COMMENTS, 'post-comments', $BLOGADMIN, $CHECK_USR_SESSION, $jkl['hd69'], $jkl['hd67'], false, true); } else { ?>
						<div class="alert alert-info" id="comments-blank"><?php echo $jkl['hd74'];?></div>
						<?php } ?>
					<ul class="post-comments">
						<li id="insertPost"></li>
					</ul>	
				</div>
				<!-- End Comments -->
				</div>
				<?php } if ($JAK_FORM_DATA["comments"] && JAK_USERISLOGGED) { ?>
				<div class="blog-comments">
					<h4><?php echo $jkl['hd70'];?></h4>
					<form class="jak-blogform" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">

						<div class="form-group">
						    <label for="bmessage" class="sr-only"><?php echo $jkl["g28"];?></label>
						    <textarea class="form-control" name="bmessage" id="bmessage" rows="5" placeholder="<?php echo $jkl["g28"];?>"><?php if (isset($_REQUEST["bmessage"]) && !empty($_REQUEST["bmessage"])) echo $_REQUEST["bmessage"];?></textarea>
						</div>
						<p class="mb-0"><button type="submit" class="btn btn-danger jak-submit"><?php echo $jkl['g11'];?></button></p>
						<input type="hidden" name="send_comment" value="1">
						<input type="hidden" name="comanswerid" id="comanswerid" value="">
						<input type="hidden" name="editpostid" id="editpostid" value="">
					</form>
				</div>
				<?php } ?>

				</div>
				<div class="col-md-3">
				  <?php if (isset($jak_comments) && !empty($jak_comments)) { ?>
				  <h4><?php echo $jkl['hd64'];?></h4>
				  <ul class="list-unstyled new-comments mt-3">
				    <?php foreach ($jak_comments as $c) { ?>
				      <li>
				        <dl class="row">
				        <dt class="col-sm-1"><i class="fa fa-chevron-right"></i></dt>
				        <dd class="col-sm-10">
				         <p class="mb-0"><a href="<?php echo JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $c["id"], JAK_rewrite::jakCleanurl($c["title"]));?>" class="text-danger"><?php echo jak_cut_text($c["message"], 20, '...');?></a></p><p class="text-muted mb-0"><?php echo JAK_base::jakTimesince($c['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
				        </dd>
				        </dl>
				        <hr>
				      </li>
				    <?php } ?>
				  </ul>
				  <?php } ?>
				</div>
			</div>
  		</div>
  	</div>
</div>



<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>


