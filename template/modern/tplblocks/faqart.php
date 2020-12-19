<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<div class="container blog-post">
	<div class="section section-text">
		<div class="row">
			<div class="col-md-8 ml-auto mr-auto">

				<?php echo $JAK_FORM_DATA["content"];?>

			</div>
		</div>
	</div>

	<div class="section section-blog-info">
		<div class="row">
			<div class="col-md-8 ml-auto mr-auto">
				<?php if ($JAK_FORM_DATA["socialbutton"]) { ?>
					<!-- Sharingbutton Twitter -->
						<a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet/?text=<?php echo urlencode($JAK_FORM_DATA["title"]);?>&amp;url=<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $page2, $page3))?>" target="_blank">
						  <div class="resp-sharing-button resp-sharing-button--twitter resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fab fa-twitter"></i>
						    </div>
						  </div>
						</a>

						<!-- Sharingbutton E-Mail -->
						<a class="resp-sharing-button__link" href="mailto:?subject=<?php echo $JAK_FORM_DATA["title"];?>&amp;body=<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $page2, $page3))?>" target="_self">
						  <div class="resp-sharing-button resp-sharing-button--email resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fa fa-envelope"></i>
						    </div>
						  </div>
						</a>

						<!-- Sharingbutton WhatsApp -->
						<a class="resp-sharing-button__link" href="whatsapp://send?text=<?php echo $JAK_FORM_DATA["title"];?>%20<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $page2, $page3))?>" target="_blank">
						  <div class="resp-sharing-button resp-sharing-button--whatsapp resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fab fa-whatsapp"></i>
						    </div>
						  </div>
						</a>
				<?php } ?>
				<hr>
				<div class="card card-profile card-plain">
					<div class="row">
						<div class="col-md-8">
							<h5><?php echo $incat;?></h5>
						</div>
						<div class="col-md-4">
							<h5><?php echo $jkl['hd77'];?></h5>
							<a href="javascript:void(0)" data-cvote="up" data-id="<?php echo $JAK_FORM_DATA["id"];?>" class="text-success jak-cvote"><i class="material-icons">thumb_up</i></a>
							<a href="javascript:void(0)" data-cvote="down" data-id="<?php echo $JAK_FORM_DATA["id"];?>" class="text-danger jak-cvote"><i class="material-icons">thumb_down</i></a>
							<!-- Votes -->
							<span id="jak-cvotec<?php echo $JAK_FORM_DATA["id"];?>" class="label label-<?php echo jak_comment_votes($JAK_FORM_DATA["votes"]);?>'"><?php echo $JAK_FORM_DATA["votes"];?></span>
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
	
</div>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>