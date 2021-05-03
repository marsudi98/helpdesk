<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<div class="container">
	<div class="row">
		<?php if ($limitreached){?>
			<div class="col-md-12">
				<div class="alert alert-info"><?php echo sprintf($jkl['hd115'], '<a href="'.JAK_rewrite::jakParseurl(JAK_CLIENT_URL).'">'.JAK_rewrite::jakParseurl(JAK_CLIENT_URL).'</a>');?></div>
			</div>
		<?php } else { ?>
			<div class="col-md-12">

				<h3><?php echo $jkl['hd47'];?></h3>

				<?php if (isset($paymsg) && !empty($paymsg)) { ?>
					<div class="alert alert-info"><?php echo $paymsg;?></div>
				<?php } ?>

				<?php if ($errors) { ?>
					<div class="alert alert-danger">
						<?php if (isset($errors) && !empty($errors)) foreach ($errors as $e) { echo $e; }?>
					</div>
				<?php } ?>

				<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">

					<!-- <?php //if (!isset($_SESSION["depinfo"])) { echo json_encode($_SESSION["depinfo"]);  ?>

						

						<div class="row">
							<div class="col-6">
								<p><a href="<?php //echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);?>" class="btn btn-info"><i class="material-icons">undo</i></a></p>
							</div>
							<div class="col-6">
								<p><button type="submit" name="save" class="btn btn-primary float-right"><?php echo $jkl["hd53"];?></button></p>
							</div>
						</div> -->
					
					<!-- there's "}" before if -->
					<?php //if (!isset($_SESSION["depinfo"])) { echo json_encode($_SESSION["depinfo"]); ?>
						<?php if (!JAK_CLIENTID) { ?>
							<div class="form-group">
								<label for="name" class="bmd-label-floating"><?php echo $jkl['g4'];?></label>
								<input type="text" class="form-control<?php if (isset($errors["e3"])) echo " is-invalid";?>" maxlength="100" name="name" value="<?php if (isset($_REQUEST["name"]) && !empty($_REQUEST["name"])) echo $_REQUEST["name"];?>">
							</div>

							<div class="form-group">
								<label for="email" class="bmd-label-floating"><?php echo $jkl['g5'];?></label>
								<input type="text" class="form-control<?php if (isset($errors["e4"])) echo " is-invalid";?>" maxlength="200" name="email" value="<?php if (isset($_REQUEST["email"]) && !empty($_REQUEST["email"])) echo $_REQUEST["email"];?>">
							</div>

							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="createaccount" value="1" checked<?php if (JAK_TICKET_ACCOUNT) echo ' disabled';?>> <?php echo $jkl["hd118"];?>
									<span class="form-check-sign">
										<span class="check"></span>
									</span>
								</label>
							</div>

							<?php if (!empty(JAK_RECAP_CLIENT) && !JAK_CLIENTID) { ?>
								<div class="g-recaptcha my-3 ml-0" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div>
							<?php } ?>

						<?php } ?>
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="subject"><?php echo $jkl['hd7'];?></label>
									<input type="text" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" maxlength="200" name="subject" id="subject" value="<?php if (isset($_REQUEST["subject"]) && !empty($_REQUEST["subject"])) echo $_REQUEST["subject"];?>">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group bmd-form-group">
									<label for="content-editor" class="bmd-label-static">AWB Number</label>
									<input type="text" name="awb" id="awb" class="form-control">
								</div>
							</div>
							<!-- <div class="col-md-6">
								<div class="form-group bmd-form-group">
									<label for="content-editor" class="bmd-label-static"><?php //echo $jkl['hd102'];?></label>
									<select name="jak_depid" id="jak_depid" class="form-control">
										<option value="">- Choose Department -</option>
										<?php //foreach ($DEPARTMENTS_ALL as $d) {
											//echo '<option value="'.$d["id"].'">'.$d["title"].'</option>';
										//} ?>
									</select>
								</div>
							</div> -->
							<!-- <div class="col-md-6">
								<div class="form-group bmd-form-group">
									<label for="content-editor" class="bmd-label-static">Drop Point</label>
									<select name="droppoint" id="droppoint" class="form-control">
										<option value='0'>- Choose DP -</option>
									</select>
								</div>
							</div> -->
						</div>

						<div id="similarArticle" style="display: none">
							<div class="card bg-info">
								<div class="card-body">
									<h4 class="card-title"><?php echo $jkl['hd132'];?></h4>
									<p><?php echo $jkl['hd133'];?></p>
									<div id="loadsimArticle"></div> 
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="content-editor"><?php //echo $jkl['hd90']; ?>Content</label><br>
							<textarea name="content" id="content-editor" rows="5" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>"><?php if (isset($_REQUEST["content"]) && !empty($_REQUEST["content"])) { echo $_REQUEST["content"]; } else if (isset($JAK_EDIT_CONTENT) && !empty($JAK_EDIT_CONTENT)) { echo $JAK_EDIT_CONTENT;} else if (isset($JAK_PRE_CONTENT) && !empty($JAK_PRE_CONTENT)) { echo $JAK_PRE_CONTENT;}?></textarea>
							<small class="form-text text-muted">
								<?php echo $jkl['hd107'];?>
							</small>
						</div>

						<input type="hidden" name="action" value="send_ticket">
						<input type="hidden" name="depcredit" id="depcredit" value="<?php echo $DEP_CREDIT;?>">

					<?php //} ?>

				</div>
				<div class="col-md-8">
				</div>
				<div class="col-md-4">

					<?php //if (!isset($_SESSION["depinfo"])) { ?>

						<div class="card card-blog" style="display:none;">
							<div class="card-header card-header-image">
								<a href="javascript:void(0)">
									<img src="<?php echo BASE_URL;?>/template/modern/img/card-project4.jpg" alt="priority">
									<div class="card-title">
				                    	<?php if (isset($DEP_TITLE)) echo $DEP_TITLE;?>
				                    </div>
								</a>
							</div>
							<div class="card-body">

								<div class="form-group bmd-form-group">
									<label for="content-editor" class="bmd-label-static"><?php echo $jkl['hd102'];?></label>
									<select name="jak_depid" id="jak_depid" class="form-control">
										<?php foreach ($DEPARTMENTS_ALL as $d) {
											echo '<option value="'.$d["id"].'">'.$d["title"].'</option>';
										} ?>
									</select>
								</div>

								<?php if (isset($PRIORITY_ALL) && !empty($PRIORITY_ALL)) { ?>
									<div class="form-group">
										<label for="email" class="bmd-label-floating"><?php echo $jkl['hd12'];?></label>
										<select name="jak_priority" id="prioirty" class="form-control">
											<?php foreach ($PRIORITY_ALL as $p) {
												echo '<option value="'.$p["id"].'" data-credits="'.$p["credits"].'"'.(isset($_REQUEST["jak_priority"]) && $_REQUEST["jak_priority"] == $p["id"] ? ' selected' : '').'>'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' / '.sprintf($jkl['hd44'], $p["credits"]) : '').'</option>';
											} ?>
										</select>
									</div>
								<?php } if (isset($TOPTIONS_ALL) && !empty($TOPTIONS_ALL)) { ?>
									<div class="form-group">
										<label for="email" class="bmd-label-floating"><?php echo $jkl['hd98'];?> (Ticket Type)</label>
										<select name="jak_toption" id="toption" class="form-control">
											<?php foreach ($TOPTIONS_ALL as $t) {
												echo '<option value="'.$t["id"].'" data-credits="'.$t["credits"].'"'.(isset($_REQUEST["jak_toption"]) && $_REQUEST["jak_toption"] == $t["id"] ? ' selected' : '').'>'.$t["title"].((JAK_BILLING_MODE == 1 && $t["credits"] != 0) ? ' / '.sprintf($jkl['hd44'], $t["credits"]) : '').'</option>';
											} ?>
										</select>
									</div>
								<?php } ?>
								<div class="form-group" style="display:none;">
									<label for="email" class="bmd-label-floating"><?php echo $jkl['hd109'];?></label>
									<select name="jak_private" id="jak_private" class="form-control">
										<option value="1"<?php if ((isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 1) || (!isset($_REQUEST["jak_private"]) && JAK_TICKET_PRIVATE == 1)) echo ' selected';?>><?php echo $jkl["g72"];?></option>
										<!-- <option value="0"<?php if ((isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 0) || (!isset($_REQUEST["jak_private"]) && JAK_TICKET_PRIVATE == 0)) echo ' selected';?>><?php echo $jkl["g73"];?></option> -->
									</select>
								</div>

								<?php //echo $custom_fields; ?>
							</div>
						</div>

						<div class="row">
							<!-- <div class="col-sm-6">
								<p><button type="submit" name="start-fresh" value="1" class="btn btn-rose btn-block"><?php echo $jkl["hd106"];?></button></p>
							</div> -->
							<div class="col-sm-6">
								<p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);?>" class="btn btn-block btn-info"><i class="material-icons">undo</i></a></p>
							</div>
							<div class="col-sm-6">
								<p><button type="submit" class="btn btn-block btn-rose"><?php echo $jkl['hd108'];?></button></p>
							</div>
						</div>

					<?php //} ?>
				</form>
			</div>
		<?php } ?>
	</div>
</div>


<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>