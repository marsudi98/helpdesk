<?php include_once 'header.php';?>

<div class="content">

<?php if ($errors) { ?>
<div class="alert alert-danger">
<?php if (isset($errors["e"])) echo $errors["e"];
	  if (isset($errors["e1"])) echo $errors["e1"];?>
</div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">

<div class="row">
	<div class="col-md-8">

		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><?php echo $jkl["hd286"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<div class="form-group">
					<label for="subject"><?php echo $jkl["g16"];?></label>
					<input type="text" name="subject" id="subject" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_POST["subject"]) && !empty($_POST["subject"])) echo $_POST["subject"];?>">
				</div>

			<?php if (isset($JAK_RESPONSE_DATA) && !empty($JAK_RESPONSE_DATA)) { ?>
				<div class="form-group">
					<p><label for="supresp"><?php echo $jkl["u35"];?></label></p>
					<select id="supresp" class="selectpicker">
					<?php echo $JAK_RESPONSE_DATA;?>
					</select>
				</div>
			<?php } ?>

				<div class="form-group">
					<label for="content-editor"><?php echo $jkl["g321"];?></label>
					<textarea name="content" id="content-editor" rows="5" class="form-control"><?php if (isset($_REQUEST["content"]) && !empty($_REQUEST["content"])) echo $_REQUEST["content"];?></textarea>
				</div>
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="inform-client" value="1">
						<span class="form-check-sign"></span> <?php echo $jkl["hd198"];?>
					</label>
				</div>

			</div>
		</div>
		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-file-archive"></i> <?php echo $jkl["hd287"];?></h3>
			</div><!-- /.card-header -->
			<div class="card-body">
				
				<table class="table table-striped">
				<?php if (isset($JAK_TICKET_FILES) && is_array($JAK_TICKET_FILES)) foreach($JAK_TICKET_FILES as $k) { ?>
					
					<tr><td>
						<?php if (getimagesize(APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$k)) { ?>
							<a data-toggle="lightbox" href="<?php echo BASE_URL_ORIG.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$k;?>"><img src="<?php echo BASE_URL_ORIG.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$k;?>" alt="<?php echo $k;?>" class="img-thumbnail" width="50px"></a>
						<?php } else { ?>
							<?php echo $k;?>
						<?php } ?>
							<div class="form-check form-check-inline">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="move-files[]" value="<?php echo $k;?>">
									<span class="form-check-sign"> </span> 
								</label>
							</div>
					</td></tr>
				<?php } ?>
				</table>

				<div id="share-attachments" class="dropzone upload-ticket"></div>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $page2);?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
	</div>
	<div class="col-md-4">

		<div class="card box-info">
			<div class="card-header with-border">
			  <h3 class="card-title"><?php echo $jkl["hd180"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">
				<dl class="row">

					<dt class="col-sm-5"><?php echo $jkl['g130'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_operator" id="jak_operator" class="selectpicker" data-live-search="true">
							<?php foreach ($OPERATOR_ALL as $o) {
								echo '<option value="'.$o["id"].'"'.($o["id"] == JAK_USERID ? ' selected' : '').'>'.$o["name"].' ('.$o["email"].')</option>';
							} ?>
						</select>
				  	</dd>

				  	<dt class="col-sm-5"><?php echo $jkl['hd167'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_status" id="jak_status" class="selectpicker">
							<option value="1" selected><?php echo $jkl["hd169"];?></option>
							<option value="2"><?php echo $jkl["hd170"];?></option>
							<option value="3"><?php echo $jkl["hd171"];?></option>
						</select>
				  	</dd>

				  	<?php if (isset($DEPARTMENTS_ALL) && !empty($DEPARTMENTS_ALL)) { ?>
					<dt class="col-sm-5"><?php echo $jkl['g131'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_depid" id="jak_depid" class="selectpicker" data-live-search="true">
							<?php foreach ($DEPARTMENTS_ALL as $d) {
								echo '<option value="'.$d["id"].'"'.($ticketinfo["depid"] == $d["id"] ? ' selected' : '').'>'.$d["title"].'</option>';
							} ?>
						</select>
				  	</dd>
				  	<?php } ?>

					<dt class="col-sm-5"><?php echo $jkl['hd167'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_status" id="jak_status" class="selectpicker">
							<option value="1" selected><?php echo $jkl["hd169"];?></option>
							<option value="2"><?php echo $jkl["hd170"];?></option>
							<option value="3"><?php echo $jkl["hd171"];?></option>
						</select>
				  	</dd>

					<?php if (isset($PRIORITY_ALL) && !empty($PRIORITY_ALL)) { ?>
					<dt class="col-sm-5"><?php echo $jkl['hd149'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_priority" id="jak_priority" class="selectpicker">
							<?php foreach ($PRIORITY_ALL as $p) {
								echo '<option value="'.$p["id"].'">'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $p["credits"]).')' : '').'</option>';
							} ?>
						</select>
				  	</dd>
				  	<?php } ?>

				  	<?php if (isset($TOPTIONS_ALL) && !empty($TOPTIONS_ALL)) { ?>
					<dt class="col-sm-5"><?php echo $jkl['hd225'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_toption" id="jak_toption" class="selectpicker">
							<?php foreach ($TOPTIONS_ALL as $t) {
								echo '<option value="'.$t["id"].'">'.$t["title"].((JAK_BILLING_MODE == 1 && $t["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $t["credits"]).')' : '').'</option>';
							} ?>
						</select>
				  	</dd>
				  	<?php } ?>

				  	<dt class="col-sm-5"><?php echo $jkl['hd77'];?></dt>
				  	<dd class="col-sm-7"><a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', $JAK_CLIENT_DATA["id"]);?>"><?php echo $JAK_CLIENT_DATA["name"];?></a></dd>

				  	<dt class="col-sm-5"><?php echo $jkl['u1'];?></dt>
				  	<dd class="col-sm-7"><a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', $JAK_CLIENT_DATA["id"]);?>"><?php echo $JAK_CLIENT_DATA["email"];?></a></dd>

				  	<dt class="col-sm-5"><?php echo $jkl['hd172'];?></dt>
				  	<dd class="col-sm-7">

					<div class="form-check form-check-radio">
				   	    <label class="form-check-label">
				        	<input class="form-check-input" type="radio" name="jak_private" value="1"<?php if (isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 1 || !isset($_REQUEST["jak_private"])) echo " checked";?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g19"];?>
				        </label>
				    </div>
				    <div class="form-check form-check-radio">
				        <label class="form-check-label">
				            <input class="form-check-input" type="radio" name="jak_private" value="0"<?php if (isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 0) echo " checked";?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g18"];?>
				        </label>
				    </div>

				  	</dd>

				  	<dt class="col-sm-5"><?php echo $jkl['g169'];?></dt>
				  	<dd class="col-sm-7"><input type="text" class="form-control" name="jak_referrer" value="<?php if (isset($_REQUEST["jak_referrer"]) && !empty($_REQUEST["jak_referrer"])) echo $_REQUEST["jak_referrer"];?>"></dd>

				  	<dt class="col-sm-5"><?php echo $jkl['hd291'];?></dt>
				  	<dd class="col-sm-7"><input type="text" name="jak_duedate" class="form-control datepicker" value="<?php echo ($_REQUEST["jak_duedate"] ? $_REQUEST["jak_duedate"] : date($duedateformat[0], strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day')));?>" autocomplete="off"></dd>

				</dl>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $page2);?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><?php echo $jkl["hd148"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">
				<?php echo $custom_fields;?>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $page2);?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><?php echo $jkl["g181"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">
				<div class="form-group">
					<label for="content-editor"><?php echo $jkl["g181"];?></label>
					<textarea name="jak_notes" rows="3" class="form-control"><?php if (isset($_REQUEST["jak_notes"]) && !empty($_REQUEST["jak_notes"])) echo $_REQUEST["jak_notes"];?></textarea>
				</div>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $page2);?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
	</div>
</div>

	<input type="hidden" name="clientid" value="<?php echo $ticketinfo["clientid"];?>">
	<input type="hidden" name="depid" value="<?php echo $ticketinfo["clientid"];?>">

</form>

</div>

<?php include_once 'footer.php';?>