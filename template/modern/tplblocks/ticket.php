<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<div class="container">
<div class="row">
	<div class="col-md-8">

		<?php if (JAK_USERISLOGGED || JAK_TICKET_GUEST_WEB) { ?>
			<p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);?>" class="btn btn-info"><i class="material-icons">undo</i></a> <a href="<?php echo (JAK_USERID ? JAK_rewrite::jakParseurl('operator', 'support', 'new') : JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'n'));?>" class="btn btn-primary"><i class="fa fa-ticket-alt-alt"></i> <?php echo $jkl['hd47'];?></a></p>
		<?php } ?>

		<div class="card bg-rose">
			<div class="card-body">
				<h6 class="card-category text-dark">
					<i class="material-icons">trending_up</i> <?php if (isset($JAK_FORM_DATA["department"])) echo $JAK_FORM_DATA["department"];?>
				</h6>
				<h3 class="card-title">
					<a href="javascript:void(0)"><?php echo $JAK_FORM_DATA["title"];?></a>
				</h3>
				
				<?php echo $JAK_FORM_DATA["content"];?>

			</div>
			<div class="card-footer ">
				<div class="author">
					<a href="javascript:void(0)">
						<img src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$JAK_FORM_DATA["picture"];?>" alt="<?php echo $JAK_FORM_DATA["name"];?>" class="avatar img-raised">
						<span><?php echo $JAK_FORM_DATA["name"];?></span>
					</a>
				</div>
				<div class="stats ml-auto">
					<i class="material-icons">schedule</i> <?php echo JAK_base::jakTimesince($JAK_FORM_DATA['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>
				</div>
			</div>
		</div>

		<?php if (isset($JAK_ANSWER_DATA) && !empty($JAK_ANSWER_DATA)) foreach ($JAK_ANSWER_DATA as $a) { ?>

			<div class="card<?php echo ($a["oname"] ? '  bg-info' : ' bg-secondary');?>">
				<div class="card-body">
					<h4 class="card-title">
						<div id="edit-content<?php echo $a["id"];?>"><?php echo $a["content"];?></div>
					</h4>
				</div>
				<div class="card-footer ">
					<div class="author">
						<a href="javascript:void(0)">
							<img src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.($a["cpicture"] ? $a["cpicture"] : $a["picture"]);?>" alt="<?php echo $JAK_FORM_DATA["name"];?>" class="avatar img-raised">
							<span><?php echo ($a["cname"] ? $a["cname"] : $a["oname"]);?></span>
						</a>
					</div>
					<div class="stats ml-auto">
						<i class="material-icons">schedule</i> <?php echo sprintf($jkl["hd85"], JAK_base::jakTimesince($a["sent"], JAK_DATEFORMAT, JAK_TIMEFORMAT));?>&nbsp;
						<?php if ((JAK_CLIENTID && $a['cid'] == JAK_CLIENTID && $a["sent"] > $mino) || (JAK_USERID && jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS))) { ?> <a href="javascript:void(0)" class="text-white" onclick="edit_post(<?php echo $a["id"];?>)"><i class="fa fa-edit"></i> <?php echo $jkl['hd66'];?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if ($errors) { ?>
			<div class="alert alert-danger">
				<?php if (isset($errors["e"])) echo $errors["e"];?>
			</div>
		<?php } ?>

		<?php if ($ticketwrite && $ticketopen) { ?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
				<div class="form-group">
					<label for="content-editor"><?php echo $jkl['hd90'];?></label>
					<textarea name="content" id="content-editor" rows="5" class="form-control"><?php if (isset($_REQUEST["content"]) && !empty($_REQUEST["content"])) { echo $_REQUEST["content"]; } elseif (isset($JAK_EDIT_CONTENT) && !empty($JAK_EDIT_CONTENT)) { echo $JAK_EDIT_CONTENT;}?></textarea>
				</div>

				<?php if ($uploadactive) { ?>

					<p><?php echo $jkl['hd117'];?></p>
					<div id="share-attachments" class="dropzone upload-ticket"></div>

				<?php } ?>

				<p class="pull-right"><button type="submit" class="btn btn-primary"><?php echo $jkl['g11'];?></button></p>
				<input type="hidden" name="action" value="send_answer">
				<input type="hidden" name="editpost" id="edit-post">
			
		<?php } ?>

		<div class="clearfix"></div>

		<?php if ($JAK_FORM_DATA["private"] == 0 || $JAK_NAV_PREV || $JAK_NAV_NEXT) { ?>
			<div class="section section-blog-info">
				<div class="row">
					<div class="col-md-12 ml-auto mr-auto">
						<?php if ($JAK_FORM_DATA["private"] == 0) { ?>

						<!-- Sharingbutton Twitter -->
						<a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet/?text=<?php echo urlencode($JAK_FORM_DATA["title"]);?>&amp;url=<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2))?>" target="_blank">
						  <div class="resp-sharing-button resp-sharing-button--twitter resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fab fa-twitter"></i>
						    </div>
						  </div>
						</a>

						<!-- Sharingbutton E-Mail -->
						<a class="resp-sharing-button__link" href="mailto:?subject=<?php echo $JAK_FORM_DATA["title"];?>&amp;body=<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2))?>" target="_self">
						  <div class="resp-sharing-button resp-sharing-button--email resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fa fa-envelope"></i>
						    </div>
						  </div>
						</a>

						<!-- Sharingbutton WhatsApp -->
						<a class="resp-sharing-button__link" href="whatsapp://send?text=<?php echo $JAK_FORM_DATA["title"];?>%20<?php echo urlencode(JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $page2))?>" target="_blank">
						  <div class="resp-sharing-button resp-sharing-button--whatsapp resp-sharing-button--small"><div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solid">
						    <i class="fab fa-whatsapp"></i>
						    </div>
						  </div>
						</a>

						<?php } ?>
						<hr>
						<?php if ($JAK_NAV_PREV) { ?>
							<a class="btn btn-rounded btn-sm btn-primary" href="<?php echo $JAK_NAV_PREV;?>" title="<?php echo $JAK_NAV_PREV_TITLE;?>"><i class="material-icons">arrow_back_ios</i> <?php echo $JAK_NAV_PREV_TITLE;?></a>
						<?php } if ($JAK_NAV_NEXT) { ?>
							<a class="btn btn-rounded btn-sm btn-primary pull-right" href="<?php echo $JAK_NAV_NEXT;?>" title="<?php echo $JAK_NAV_NEXT_TITLE;?>"><?php echo $JAK_NAV_NEXT_TITLE;?> <i class="material-icons">arrow_forward_ios</i></a>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>

	</div>
	<div class="col-md-4">

		<div class="card card-blog">
			<div class="card-header card-header-image">
				<a href="javascript:void(0)">
					<img src="<?php echo BASE_URL;?>/template/modern/img/card-project4.jpg" alt="priority">
					<div class="card-title">
						<?php echo sprintf($jkl['hd91'], $page2);?>
					</div>
				</a>
			</div>
			<div class="card-body">

				<?php if (isset($JAK_PRIORITY_DATA["title"])) { ?>
					<h4 class="info-title"><?php echo $jkl['hd12'];?> <small><?php echo $JAK_PRIORITY_DATA["title"];?></small></h4>
				<?php } if (isset($JAK_OPTION_DATA["title"])) { ?>
					<h4 class="info-title"><?php echo $jkl['hd98'];?> <small><?php echo $JAK_OPTION_DATA["title"]; if (!empty($JAK_OPTION_DATA["icon"])) {?> <i class="fa <?php echo $JAK_OPTION_DATA["icon"];?>"><?php } ?></i></small></h4>
				<?php } ?>
				<h4 class="info-title"><?php echo $jkl['hd83'];?> <small><?php echo $JAK_FORM_DATA["name"];?></small></h4>

				<h4 class="info-title"><?php echo $jkl['hd79'];?> <small><?php echo JAK_base::jakTimesince($JAK_FORM_DATA['updated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></small></h4>

				<h4 class="info-title"><?php echo $jkl['hd80'];?> <small><?php echo JAK_base::jakTimesince($JAK_FORM_DATA['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></small></h4>

				<h4 class="info-title"><?php echo $jkl['hd81'];?> <small><?php echo ($JAK_FORM_DATA['ended'] ? JAK_base::jakTimesince($JAK_FORM_DATA['ended'], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></small></h4>

				<?php if ($ticketwrite && $ticketopen) { ?>

					<h4 class="info-title"><?php echo $jkl['hd11'];?></h4>

					<div class="form-group">
						<label for="jak_status" class="bmd-label-floating sr-only"><?php echo $jkl['hd11'];?></label>
						<select name="jak_status" id="jak_status" class="form-control">
							<option value="1"<?php if ($JAK_FORM_DATA["status"] == 1) echo ' selected';?>><?php echo $jkl["hd14"];?></option>
							<?php if ($JAK_FORM_DATA["status"] == 2) { ?><option value="2" selected><?php echo $jkl["hd15"];?></option><?php } ?>
							<option value="3"<?php if ($JAK_FORM_DATA["status"] == 3 || $JAK_FORM_DATA["status"] == 4) echo ' selected';?>><?php echo $jkl["hd16"];?></option>
						</select>
					</div>

					<div class="form-check">
						<label class="form-check-label">
							<input class="form-check-input" type="checkbox" name="changestatus" value="1"> <?php echo $jkl["hd99"];?>
							<span class="form-check-sign">
								<span class="check"></span>
							</span>
						</label>
					</div>

					<?php echo $custom_fields;?>
					</form>
				<?php } else { ?>

					<h4 class="info-title"><?php echo $jkl['hd11'];?> <small><?php echo ($JAK_FORM_DATA["status"] == 1 ? $jkl['hd14'] : ($JAK_FORM_DATA["status"] == 2 ? $jkl['hd15'] : $jkl['hd16']));?></small></h4>

				<?php } ?>

			</div>
		</div>

		<div class="card">
			<div class="card-body">
				<h4 class="card-title">
					<a href="javascript:void(0)"><?php echo sprintf($jkl['hd82'], '<span id="upload-counter">'.$JAK_FORM_DATA["attachments"].'</span>');?></a>
				</h4>
				<div id="attach-list">

					<?php if (isset($JAK_TICKET_FILES) && is_array($JAK_TICKET_FILES)) foreach($JAK_TICKET_FILES as $k) { if (getimagesize($k["path"])) { ?>
					<p><a href="<?php echo BASE_URL;?>_showfile.php?i=<?php echo jak_encrypt_decrypt($k["encrypt"]);?>" target="_blank"><img src="<?php echo BASE_URL;?>_showfile.php?i=<?php echo jak_encrypt_decrypt($k["encrypt"]);?>" alt="<?php echo $k["name"];?>" class="img-thumbnail" width="250"></a></p>
					<?php } else { ?>
					<p><a href="<?php echo BASE_URL;?>_showfile.php?i=<?php echo jak_encrypt_decrypt($k["encrypt"]);?>" target="_blank"><?php echo $k["name"];?></a></p>
					<?php } } ?>

					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>