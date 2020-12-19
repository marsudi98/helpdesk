<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<div class="container blog-post">
	<div class="section section-text">
		<div class="row">
			<div class="col-md-8 ml-auto mr-auto">

				<?php if ($JAK_FORM_DATA["previmg"]) { ?>
					<p><img class="img-raised rounded img-fluid" src="<?php echo BASE_URL.$JAK_FORM_DATA["previmg"];?>" alt="<?php echo $JAK_FORM_DATA["title"];?>"></p>
				<?php } ?>

				<?php echo $JAK_FORM_DATA["content"];?>

			</div>
		</div>
	</div>

	<div class="section section-blog-info">
		<div class="row">
			<div class="col-md-8 ml-auto mr-auto">
				<?php if ($JAK_FORM_DATA["socialbutton"]) { ?>
					<!-- Sharingbutton Twitter -->
						<a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet/?text=<?php echo urlencode($JAK_FORM_DATA["title"]);?>&amp;url=<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $page2, $page3))?>" target="_blank">
						  <div class="resp-sharing-button resp-sharing-button--twitter resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fab fa-twitter"></i>
						    </div>
						  </div>
						</a>

						<!-- Sharingbutton E-Mail -->
						<a class="resp-sharing-button__link" href="mailto:?subject=<?php echo $JAK_FORM_DATA["title"];?>&amp;body=<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $page2, $page3))?>" target="_self">
						  <div class="resp-sharing-button resp-sharing-button--email resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fa fa-envelope"></i>
						    </div>
						  </div>
						</a>

						<!-- Sharingbutton WhatsApp -->
						<a class="resp-sharing-button__link" href="whatsapp://send?text=<?php echo $JAK_FORM_DATA["title"];?>%20<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $page2, $page3))?>" target="_blank">
						  <div class="resp-sharing-button resp-sharing-button--whatsapp resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fab fa-whatsapp"></i>
						    </div>
						  </div>
						</a>
				<?php } ?>
				<hr>
				<div class="card card-profile card-plain">
					<div class="row">
						<div class="col-md-2">
							<div class="card-avatar">
								<a href="#pablo">
									<img class="img" src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$JAK_OP_DETAILS["picture"];?>" alt="author">
								</a>
								<div class="ripple-container"></div>
							</div>
						</div>
						<div class="col-md-10">
							<h4 class="card-title"><?php echo $JAK_OP_DETAILS["name"];?></h4>
							<p class="description"><?php echo $JAK_OP_DETAILS["aboutme"];?></p>
						</div>
					</div>
				</div>
				<hr>
				<?php if ($JAK_NAV_PREV) { ?>
					<a class="btn btn-rounded btn-sm btn-primary" href="<?php echo $JAK_NAV_PREV;?>" title="<?php echo $JAK_NAV_PREV_TITLE;?>"><i class="material-icons">arrow_back_ios</i> <?php echo $JAK_NAV_PREV_TITLE;?></a>
				<?php } if ($JAK_NAV_NEXT) { ?>
					<a class="btn btn-rounded btn-sm btn-primary pull-right" href="<?php echo $JAK_NAV_NEXT;?>" title="<?php echo $JAK_NAV_NEXT_TITLE;?>"><?php echo $JAK_NAV_NEXT_TITLE;?> <i class="material-icons">arrow_forward_ios</i></a>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="section section-comments">
		<div class="row">
			<div class="col-md-8 ml-auto mr-auto">

				<!-- Comments -->
				<?php if ($JAK_FORM_DATA["comments"]) { ?>
					<div class="media-area">
						<h3 class="title text-center"><?php echo sprintf($jkl['hd65'], '<span id="cComT">'.$JAK_COMMENTS_TOTAL.'</span>');?></h3>

						<?php if (isset($JAK_COMMENTS) && !empty($JAK_COMMENTS)) { echo jak_build_comments_modern(0, $JAK_COMMENTS, $BLOGADMIN, $CHECK_USR_SESSION, $jkl['hd69'], $jkl['hd67'], false, true); } else { ?>
							<div class="alert alert-info" id="comments-blank"><?php echo $jkl['hd74'];?></div>
						<?php } ?>
						<span id="insertPost"></span>
						<!-- End Comments -->
					</div>
				<?php } if ($JAK_FORM_DATA["comments"] && JAK_USERISLOGGED) { ?>

					<h3 class="title text-center"><?php echo $jkl['hd70'];?></h3>
					<div class="media media-post">
						<a class="author float-left" href="javascript:void(0)">
							<div class="avatar">
								<?php if (JAK_USERID) { ?>
									<img class="media-object" src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$jakuser->getVar("picture");?>" alt="<?php echo $jakuser->getVar("name");?>">
								<?php } else { ?>
									<img class="media-object" src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$jakclient->getVar("picture");?>" alt="<?php echo $jakclient->getVar("name");?>">
								<?php } ?>
							</div>
						</a>
						<div class="media-body">
							<form class="jak-blogform" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
								<div class="form-group label-floating bmd-form-group">
									<label class="form-control-label bmd-label-floating" for="bmessage"><?php echo $jkl["g28"];?></label>
									<textarea class="form-control" rows="5" name="bmessage" id="bmessage"></textarea>
								</div>
								<div class="media-footer">
									<button type="submit" class="btn btn-primary btn-round btn-wd float-right jak-submit"><?php echo $jkl['g11'];?></button>
									<input type="hidden" name="send_comment" value="1">
									<input type="hidden" name="comanswerid" id="comanswerid" value="">
									<input type="hidden" name="editpostid" id="editpostid" value="">
								</div>
							</form>
						</div>
					</div>
					<!-- end media-post -->
				<?php } ?>

			</div>
		</div>
	</div>
</div>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>