<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-ticket">
	<div class="container">
		<div class="row">
			<?php if ($limitreached){?>
			<div class="col-md-12">
				<div class="alert alert-info"><?php echo sprintf($jkl['hd115'], '<a href="'.JAK_rewrite::jakParseurl(JAK_CLIENT_URL).'">'.JAK_rewrite::jakParseurl(JAK_CLIENT_URL).'</a>');?></div>
			</div>
			<?php } else { ?>
  			<div class="col-md-8">

  				<div class="ticket-header">
  					<span class="red-bg"><i class="fa fa-ticket-alt"></i></span>
  					<h1><?php echo $jkl['hd47'];?><br><small class="text-muted"><?php if (isset($DEP_TITLE)) echo $DEP_TITLE;?></small></h1>
  				</div>
  				<div class="clearfix"></div>

  				<?php if (isset($paymsg) && !empty($paymsg)) { ?>
  				<div class="alert alert-info"><?php echo $paymsg;?></div>
  				<?php } ?>
 
  				<div class="content-new-ticket">

  					<?php if ($errors) { ?>
					<div class="alert alert-danger">
					<?php if (isset($errors) && !empty($errors)) foreach ($errors as $e) { echo $e; }?>
					</div>
					<?php } ?>

					<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">

					<?php if (!isset($_SESSION["depinfo"])) { ?>

  					<div class="form-group">
  						<label for="content-editor"><?php echo $jkl['hd102'];?></label>
						<select name="jak_depid" id="jak_depid" class="form-control">
							<?php foreach ($DEPARTMENTS_ALL as $d) {
								echo '<option value="'.$d["id"].'">'.$d["title"].'</option>';
							} ?>
						</select>
					</div>

					<div class="row">
						<div class="col-6">
							<?php if (isset($_SESSION["webembed"])) { ?>
							<p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i></a></p>
							<?php } ?>
						</div>
						<div class="col-6">
							<p><button type="submit" name="save" class="btn btn-danger pull-right"><?php echo $jkl["hd53"];?></button></p>
						</div>
					</div>

					<?php } if (isset($_SESSION["depinfo"])) { ?>

					<?php if (!JAK_CLIENTID) { ?>
					<div class="form-group">
						<label for="name"><?php echo $jkl['g4'];?></label>
						<input type="text" class="form-control<?php if (isset($errors["e3"])) echo " is-invalid";?>" maxlength="100" name="name" value="<?php if (isset($_REQUEST["name"]) && !empty($_REQUEST["name"])) echo $_REQUEST["name"];?>" placeholder="<?php echo $jkl['g4'];?>">
					</div>

					<div class="form-group">
						<label for="email"><?php echo $jkl['g5'];?></label>
						<input type="text" class="form-control<?php if (isset($errors["e4"])) echo " is-invalid";?>" maxlength="200" name="email" value="<?php if (isset($_REQUEST["email"]) && !empty($_REQUEST["email"])) echo $_REQUEST["email"];?>" placeholder="<?php echo $jkl['g5'];?>">
					</div>

					<div class="form-check form-check-inline">
						<label class="form-check-label">
							<input class="form-check-input" type="checkbox" name="createaccount" value="1" checked<?php if (JAK_TICKET_ACCOUNT) echo ' disabled';?>> <?php echo $jkl["hd118"];?>
						</label>
					</div>

					<?php } ?>

					<div class="form-group">
						<label for="subject"><?php echo $jkl['hd7'];?></label>
						<input type="text" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" maxlength="200" name="subject" id="subject" value="<?php if (isset($_REQUEST["subject"]) && !empty($_REQUEST["subject"])) echo $_REQUEST["subject"];?>" placeholder="<?php echo $jkl['hd7'];?>">
					</div>

					<div id="similarArticle" style="display: none">
						<div class="card bg-white">
							<div class="card-body">
								<h4 class="card-title"><?php echo $jkl['hd132'];?></h4>
								<p><?php echo $jkl['hd133'];?></p>
								<div id="loadsimArticle"></div> 
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="content-editor"><?php echo $jkl['hd90'];?></label>
						<textarea name="content" id="content-editor" rows="5" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>"><?php if (isset($_REQUEST["content"]) && !empty($_REQUEST["content"])) { echo $_REQUEST["content"]; } else if (isset($JAK_EDIT_CONTENT) && !empty($JAK_EDIT_CONTENT)) { echo $JAK_EDIT_CONTENT;} else if (isset($JAK_PRE_CONTENT) && !empty($JAK_PRE_CONTENT)) { echo $JAK_PRE_CONTENT;}?></textarea>
						<small class="form-text text-muted">
  						<?php echo $jkl['hd107'];?>
						</small>
					</div>

					<?php if (!empty(JAK_RECAP_CLIENT) && !JAK_CLIENTID) { ?>
		            <p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
		            <?php } ?>

				 	<input type="hidden" name="action" value="send_ticket">
				 	<input type="hidden" name="depcredit" id="depcredit" value="<?php echo $DEP_CREDIT;?>">
				 	<?php } ?>
					
				</div>

			</div>
			<div class="col-md-4">
				<div class="ticket-information">
					<?php if (isset($_SESSION["depinfo"])) { ?>
					<div class="card mb-3" style="width: 100%">
					  <div class="card-body">
					    <div class="card-text">
					    	<table class="table table-striped">
					    		<?php if (isset($PRIORITY_ALL) && !empty($PRIORITY_ALL)) { ?>
					    		<tr>
					    			<td><?php echo $jkl['hd12'];?></td>
				  					<td><select name="jak_priority" id="prioirty" class="form-control form-control-sm">
											<?php foreach ($PRIORITY_ALL as $p) {
												echo '<option value="'.$p["id"].'" data-credits="'.$p["credits"].'"'.(isset($_REQUEST["jak_priority"]) && $_REQUEST["jak_priority"] == $p["id"] ? ' selected' : '').'>'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' / '.sprintf($jkl['hd44'], $p["credits"]) : '').'</option>';
											} ?>
										</select>
									</td>
								</tr>
				  				<?php } ?>

							  	<?php if (isset($TOPTIONS_ALL) && !empty($TOPTIONS_ALL)) { ?>
								<tr>
					    			<td><?php echo $jkl['hd98'];?></td>
				  					<td><select name="jak_toption" id="toption" class="form-control form-control-sm">
											<?php foreach ($TOPTIONS_ALL as $t) {
												echo '<option value="'.$t["id"].'" data-credits="'.$t["credits"].'"'.(isset($_REQUEST["jak_toption"]) && $_REQUEST["jak_toption"] == $t["id"] ? ' selected' : '').'>'.$t["title"].((JAK_BILLING_MODE == 1 && $t["credits"] != 0) ? ' / '.sprintf($jkl['hd44'], $t["credits"]) : '').'</option>';
											} ?>
										</select>
									</td>
								</tr>
				  				<?php } ?>
								<tr>
									<td><?php echo $jkl['hd109'];?></td>
									<td><select name="jak_private" id="jak_private" class="form-control form-control-sm">
											<option value="1"<?php if ((isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 1) || (!isset($_REQUEST["jak_private"]) && JAK_TICKET_PRIVATE == 1)) echo ' selected';?>><?php echo $jkl["g72"];?></option>
											<option value="0"<?php if ((isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 0) || (!isset($_REQUEST["jak_private"]) && JAK_TICKET_PRIVATE == 0)) echo ' selected';?>><?php echo $jkl["g73"];?></option>
										</select>
									</td>
								</tr>
								<?php echo $custom_fields;?>
							</table>
					    </div>
					  </div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<p><button type="submit" name="start-fresh" value="1" class="btn btn-danger btn-block"><?php echo $jkl["hd106"];?></button></p>
						</div>
						<div class="col-sm-6">
							<p><button type="submit" class="btn btn-block btn-lg btn-success btn-green"><?php echo $jkl['hd108'];?></button></p>
						</div>
					</div>
					<?php } ?>
					</form>
				</div>
			</div>
			<?php } ?>
  		</div>
  	</div>
</div>



<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>


