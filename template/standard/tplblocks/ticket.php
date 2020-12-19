<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-ticket">
	<div class="container">
		<div class="row">
  			<div class="col-md-8">

  				<div class="ticket-header">
  					<span class="blue-bg"><i class="fa fa-ticket-alt"></i></span>
  					<h1><?php echo $JAK_FORM_DATA["subject"];?><br><small class="text-muted"><?php echo $JAK_FORM_DATA["title"];?></small></h1>
  				</div>
  				<div class="clearfix"></div>

  				<div class="ticket-content">
  					<div class="ticket-inner-content">
  						<?php echo $JAK_FORM_DATA["content"];?>
  						<p class="info-ticket"><?php echo JAK_base::jakTimesince($JAK_FORM_DATA['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
  					</div>
  				</div>

  				<?php if (isset($JAK_ANSWER_DATA) && !empty($JAK_ANSWER_DATA)) foreach ($JAK_ANSWER_DATA as $a) { ?>
  				<div class="ticket-answer">
  					<div class="ticket-inner-answer<?php echo ($a["oname"] ? ' operator-answer' : '');?>">
						<div id="edit-content<?php echo $a["id"];?>"><?php echo $a["content"];?></div>
						<p class="info-ticket"><?php echo sprintf($jkl["hd86"], $a["id"]).' '.sprintf($jkl["hd85"], JAK_base::jakTimesince($a["sent"], JAK_DATEFORMAT, JAK_TIMEFORMAT));?> / <?php echo ($a["cname"] ? $a["cname"] : $a["oname"]); if ((JAK_CLIENTID && $a['cid'] == JAK_CLIENTID && $a["sent"] > $mino) || (JAK_USERID && jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS))) { ?> / <a href="javascript:void(0)" onclick="edit_post(<?php echo $a["id"];?>)"><i class="fa fa-edit"></i></a><?php } ?></p>
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

				<p><button type="submit" class="btn btn-block btn-lg btn-success btn-green"><?php echo $jkl['g11'];?></button></p>
			 	<input type="hidden" name="action" value="send_answer">
			 	<input type="hidden" name="editpost" id="edit-post">
				</form>
				<?php } ?>

  				<div class="alert alert-secondary">
  					<div class="row">
  						<div class="col-md-10">
		  					<?php if ($JAK_FORM_DATA["private"] == 0) { ?>
		  					<div id="sharing-sucks"></div>
		  					<?php } ?>
		  				</div>
		  				<div class="col-md-2">
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
			<div class="col-md-4">
				<div class="ticket-information">
					<div class="card mb-3" style="width: 100%">
					  <div class="card-header ticket-blue-bg text-center"><?php echo $jkl['hd84'];?><hr><div class="ticket-id"><?php echo sprintf($jkl['hd91'], $page2);?></div></div>
					  <div class="card-body">
					    <div class="card-text">
					    	<table class="table table-striped">
					    		<?php if (isset($JAK_PRIORITY_DATA["title"])) { ?>
								<tr class="table-<?php echo $JAK_PRIORITY_DATA["class"];?>">
									<td><?php echo $jkl['hd12'];?></td>
									<td><?php echo $JAK_PRIORITY_DATA["title"];?></td>
								</tr>
								<?php } if (isset($JAK_OPTION_DATA["title"])) { ?>
								<tr>
									<td><?php echo $jkl['hd98'];?></td>
									<td><?php echo $JAK_OPTION_DATA["title"]; if (!empty($JAK_OPTION_DATA["icon"])) {?> <i class="fa <?php echo $JAK_OPTION_DATA["icon"];?>"><?php } ?></i></td>
								</tr>
								<?php } ?>
								<tr>
									<td><?php echo $jkl['hd83'];?></td>
									<td><?php echo $JAK_FORM_DATA["name"];?></td>
								</tr>
								<tr>
									<td><?php echo $jkl['hd79'];?></td>
									<td><?php echo JAK_base::jakTimesince($JAK_FORM_DATA['updated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
								</tr>
								<tr>
									<td><?php echo $jkl['hd80'];?></td>
									<td><?php echo JAK_base::jakTimesince($JAK_FORM_DATA['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
								</tr>
								<tr>
									<td><?php echo $jkl['hd81'];?></td>
									<td><?php echo ($JAK_FORM_DATA['ended'] ? JAK_base::jakTimesince($JAK_FORM_DATA['ended'], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></td>
								</tr>
								<?php if ($ticketwrite && $ticketopen) { ?>
								<tr>
									<td><?php echo $jkl['hd11'];?></td>
									<td><select name="jak_status" id="jak_status" class="form-control form-control-sm">
											<option value="1"<?php if ($JAK_FORM_DATA["status"] == 1) echo ' selected';?>><?php echo $jkl["hd14"];?></option>
											<?php if ($JAK_FORM_DATA["status"] == 2) { ?><option value="2" selected><?php echo $jkl["hd15"];?></option><?php } ?>
											<option value="3"<?php if ($JAK_FORM_DATA["status"] == 3 || $JAK_FORM_DATA["status"] == 4) echo ' selected';?>><?php echo $jkl["hd16"];?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input class="form-check-input" type="checkbox" name="changestatus" value="1"> <?php echo $jkl["hd99"];?>
											</label>
										</div>
									</td>
								</tr>
								<?php echo $custom_fields;?>
								<?php } else { ?>
								<tr>
									<td><?php echo $jkl['hd11'];?></td>
									<td><?php echo ($JAK_FORM_DATA["status"] == 1 ? $jkl['hd14'] : ($JAK_FORM_DATA["status"] == 2 ? $jkl['hd15'] : $jkl['hd16']));?></td>
								</tr>
								<?php } ?>
								<tr class="ticket-attach text-center">
									<td colspan="2"><?php echo sprintf($jkl['hd82'], '<span id="upload-counter">'.$JAK_FORM_DATA["attachments"].'</span>');?></td>
								</tr>
								<tr>
									<td colspan="2">
										<table class="table table-striped table-responsive w-100 d-block d-md-table" id="attach-list">
										<?php if (isset($JAK_TICKET_FILES) && is_array($JAK_TICKET_FILES)) foreach($JAK_TICKET_FILES as $k) { ?>
											<tr><td>
											<?php if (getimagesize($k["path"])) { ?>
												<a href="<?php echo BASE_URL;?>_showfile.php?i=<?php echo jak_encrypt_decrypt($k["encrypt"]);?>"><img src="<?php echo BASE_URL;?>_showfile.php?i=<?php echo jak_encrypt_decrypt($k["encrypt"]);?>" alt="<?php echo $k["name"];?>" class="img-thumbnail" width="50"></a>
											<?php } else { ?>
												<a href="<?php echo BASE_URL;?>_showfile.php?i=<?php echo jak_encrypt_decrypt($k["encrypt"]);?>" class="btn btn-info btn-sm"><?php echo $k["name"];?></a>
											<?php } ?>
											</td></tr>
										<?php } ?>
										</table>
									</td>
								</tr>
							</table>
					    </div>
					  </div>
					</div>
				</div>
				<?php if (isset($_SESSION["webembed"])) { ?>
				<p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i></a></p>
				<?php } else { ?>
				<div class="ticket-categories">
					<?php if (isset($allcategories) && !empty($allcategories)) { ?>
					<h4><?php echo $jkl['hd76'];?></h4>
					<ul class="list-unstyled faq-list mt-3">
				    <?php foreach ($allcategories as $c) { ?>
				      <li>
				        <dl class="row">
				        <dt class="col-sm-1"><i class="fa fa-chevron-right"></i></dt>
				        <dd class="col-sm-10">
				         <p class="mb-0"><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'c', $c["id"], JAK_rewrite::jakCleanurl($c["title"]));?>" class="text-danger"><?php echo $c["title"];?></a></p>
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
					<h4><?php echo $jkl['hd87'];?></h4>
					<ul class="list-unstyled faq-list mt-3">
				    <?php foreach ($similarart as $s) { ?>
				      <li>
				        <dl class="row">
				        <dt class="col-sm-1"><i class="fa fa-chevron-right"></i></dt>
				        <dd class="col-sm-10">
				         <p class="mb-0"><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $s["id"], JAK_rewrite::jakCleanurl($s["subject"]));?>" class="text-danger"><?php echo $s["subject"];?></a></p>
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