<?php include_once 'header.php';?>

<div class="content">

<?php if ($errors) { ?>
<div class="alert alert-danger">
<?php if (isset($errors["e"])) echo '(!) '.$errors["e"];
	  if (isset($errors["e_awb"])) echo '(!) '.$errors["e_awb"];
	  if (isset($errors["e1"])) echo '(!) '.$errors["e1"];
	  if (isset($errors["jp"])) echo '(!) '.$errors["jp"];?>
</div>
<?php } if (isset($CLIENTS_ALL) && !empty($CLIENTS_ALL)) { ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">

<div class="row">
	<div class="col-md-4">
		<div class="card">
			<div class="card-body">
				<button type="submit" name="start-fresh" value="1" class="btn btn-danger btn-block"><?php echo $jkl["hd196"];?></button>
			</div>
		</div>
		<?php if (!isset($_SESSION["userinfo"]) && isset($_SESSION["depinfo"])) { ?>
		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><?php echo $jkl["hd180"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">
				<dl class="row">
					
					<dt class="col-sm-5 my-auto my-auto"><?php echo $jkl['g130'];?></dt>
				  	<dd class="col-sm-7 my-auto">
					  	<?php  
							$username = $jakuser->getVar("username"); 
							if($username != 'admin') {
							$operator = $jakdb->select("user", ["id", "name", "email"], ["id" => JAK_USERID]);
							foreach($operator as $o){  
						?>
						<input type="text" class="form-control" name="jak_operator_show" value="<?php echo $o['name'].' ('.$o['email'].')'; ?>" title="<?php echo $o['name'].' ('.$o['email'].')'; ?>" readonly>
						<input type="hidden" class="form-control" name="jak_operator" value="<?php echo JAK_USERID; ?>" title="<?php echo $o['name'].' ('.$o['email'].')'; ?>" readonly>
						<?php } } else { ?>
				  		<select name="jak_operator" id="jak_operator" class="selectpicker" data-live-search="true">
							<?php foreach ($OPERATOR_ALL as $o) {
								echo '<option value="'.$o["id"].'"'.($o["id"] == JAK_USERID ? ' selected' : '').'>'.$o["name"].' ('.$o["email"].')</option>';
							} ?>
						</select>
						<?php } ?>
				  	</dd>
					<div class="col-sm-12 py-1"></div>
					<dt class="col-sm-5 my-auto"><?php echo $jkl['hd288'];?></dt>
				  	<dd class="col-sm-7 my-auto">
				  		<select name="jak_opidcc[]" id="jak_opidcc" class="selectpicker" data-live-search="true" multiple>
							<?php foreach ($OPERATOR_ALL as $o) {
								echo '<option value="'.$o["id"].'"'.(in_array($o["id"], $OPERATOR_CC) ? ' selected' : '').'>'.$o["name"].' ('.$o["email"].')</option>';
							} ?>
						</select>
				  	</dd>
					<div class="col-sm-12 py-1"></div>
					<dt class="col-sm-5 my-auto"><?php echo $jkl['hd167'];?></dt>
				  	<dd class="col-sm-7 my-auto">
				  		<select name="jak_status" id="jak_status" class="selectpicker">
							<option value="1" selected><?php echo $jkl["hd169"];?></option>
							<option value="2"><?php echo $jkl["hd170"];?></option>
							<option value="3"><?php echo $jkl["hd171"];?></option>
						</select>
				  	</dd>	
					<div class="col-sm-12 py-1"></div>
					<dt class="col-sm-5 my-auto"><?php echo 'Sumber Complaint'; ?></dt>
					<dd class="col-sm-7 my-auto">
					<select name="jak_depid" data-placeholder="<?php echo $jkl["hd195"];?>" id="jak_depid" class="selectpicker" data-live-search="true">
						<?php foreach ($DEPARTMENTS_ALL as $d) {
							echo '<option value="'.$d["id"].'">'.$d["title"].'</option>';
						} ?>
					</select>
					</dd> 
					<div class="col-sm-12 py-1"></div>
					<form>		
						<?php if (isset($PRIORITY_ALL) && !empty($PRIORITY_ALL)) { ?>
						<dt class="col-sm-5 my-auto">Jenis Complaint<?php //echo $jkl['hd149'];?></dt>
						<dd class="col-sm-7 my-auto">
							<select name="jak_priority" id="jak_priority" class="selectpicker<?php if (isset($errors["jp"])) echo " is-invalid";?>">
								<option value="-">Nothing selected</option>
								<?php foreach ($PRIORITY_ALL as $p) {
									echo '<option value="'.$p["id"].'-'.$p["duetime"].'">'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $p["credits"]).')' : '').'</option>';
								} ?>
							</select>
						</dd>
						<?php } ?>
					</form>
					<div id="sc-space" class="col-sm-12 py-1"></div>
					<dt id="sc-label" class="col-sm-5 my-auto">Rincian Complaint</dt>
					<dd id="sc-select" class="col-sm-7 my-auto">
						<select name="jak_toption" id="jak_toption" class="selectpicker">
							<option value="">Nothing selected</option>
						</select>
					</dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto" style="display:none;"><?php echo $jkl['hd172'];?></dt>
				  	<dd class="col-sm-7 my-auto" style="display:none;">
					<div class="form-check form-check-radio" style="padding-left:0;">
				   	    <label class="form-check-label">
				        	<input class="form-check-input" type="radio" name="jak_private" value="1"<?php if (isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 1 || !isset($_REQUEST["jak_private"])) echo " checked";?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g19"];?>
				        </label>
				    </div>
				    <div class="form-check form-check-radio" style="padding-left:0;">
				        <label class="form-check-label">
				            <input class="form-check-input" type="radio" name="jak_private" value="0"<?php if (isset($_REQUEST["jak_private"]) && $_REQUEST["jak_private"] == 0) echo " checked";?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g18"];?>
				        </label>
				    </div>
				  	</dd>
					<!-- <div class="col-sm-12 py-1"></div>								 -->
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['g169'];?></dt>
				  	<dd class="col-sm-7 my-auto"><input type="text" class="form-control" name="jak_referrer" value="<?php if (isset($_REQUEST["jak_referrer"]) && !empty($_REQUEST["jak_referrer"])) echo $_REQUEST["jak_referrer"];?>"></dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto" style="display:none"><?php echo $jkl['hd291'];?></dt>
				  	<dd class="col-sm-7 my-auto" style="display:none"><input type="text" name="jak_duedate" class="form-control datepicker" value="<?php echo ($_REQUEST["jak_duedate"] ? $_REQUEST["jak_duedate"] : date($duedateformat[0], strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day')));?>" autocomplete="off"></dd>

				</dl>
			</div>
			<!-- <div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div> -->
		</div>

		<!-- <div class="card">
			<div class="card-header">
			  <h3 class="card-title"><?php //echo $jkl["hd148"];?></h3>
			</div>
			<div class="card-body"> -->
				<?php //echo $custom_fields;?>
			<!-- </div>
			<div class="card-footer">
				<a href="<?php //echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php //echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php //echo $jkl["g38"];?></button>
			</div>
		</div> -->
		
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
			<!-- <div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div> -->
		</div>
		<?php } ?>
	</div>
	<div class="col-md-8">
		<?php //if (!isset($_SESSION["userinfo"])) { ?>
			
		<?php //} 
		//if (isset($_SESSION["userinfo"])) { ?>
		<!-- <div class="card box-danger">
			<div class="card-header">
			  <h3 class="card-title"><?php //echo $jkl["hd195"];?></h3>
			</div>
			<div class="card-body">
				<div class="form-group">
					<select name="jak_depid" data-placeholder="<?php //echo $jkl["hd195"];?>" id="jak_depid" class="selectpicker" data-live-search="true">
						<?php //foreach ($DEPARTMENTS_ALL as $d) {
						//echo '<option value="'.$d["id"].'">'.$d["title"].'</option>';
						//} ?>
					</select>
				</div>
			</div>
			<div class="card-footer">
				<a href="<?php //echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php //echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php //echo $jkl["g38"];?></button>
			</div>
		</div> -->
		<?php  if (!isset($_SESSION["userinfo"])) { ?>

		<div class="card">
			<div class="card-header">
			<h3 class="card-title"><?php echo 'Client';?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">
				<div class="form-group" style="margin-bottom:0;">
					<label style="color:#3f4254;"><?php echo 'Select Client';?></label>
					<select name="jak_clients" title="<?php echo $jkl["hd194"];?>" id="jak_clients" class="selectpicker" data-live-search="true">
						<option><?php echo $jkl["hd194"];?></option>
						<?php foreach ($CLIENTS_ALL as $v) {
							echo '<option value="'.$v["id"].':#:'.$v["support_dep"].':#:'.$v["name"].'"'.($v["id"] == 1 ? ' selected' : '').'>'.$v["name"].' ('.$v["email"].')</option>';
						} ?>
					</select>
				</div>
			</div>

			<div class="card-header pt-0">
			<div class="form-check" style="padding-left: 0;">
				<label class="form-check-label">
					<input class="form-check-input" type="checkbox" id="newclient_cb" name="newclient_cb" value="1">
					<span class="form-check-sign"></span> <label style="color:#3f4254;"><?php echo 'or New Client';?></label>
				</label>
			</div>

			</div><!-- /.box-header -->
			<div class="card-body">

				<div class="form-group">
					<label for="namec"><?php echo 'Complainer Name';?></label>
					<input disabled type="text" name="jak_namec" id="namec" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["jak_namec"])) echo $_REQUEST["jak_namec"];?>">
				</div>

				<div class="form-group">
					<label for="phonec"><?php echo 'Complainer CP';?></label>
					<input disabled type="text" name="jak_phonec" id="phonec" class="form-control" value="<?php if (isset($_REQUEST["jak_phonec"])) echo $_REQUEST["jak_phonec"];?>">
				</div>

				<div class="form-group" style="margin-bottom:0;">
					<label for="emailc"><?php echo $jkl["u1"];?></label>
					<input disabled type="text" name="jak_emailc" id="emailc" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["jak_emailc"])) echo $_REQUEST["jak_emailc"];?>">
				</div>

				<p class="text-muted" style="font-size:11px;margin-bottom:0;">Email tidak wajib diisi jika hanya ingin mengisi keterangan Nama dan CP Complainer.</p>

				<div class="form-check" style="padding-left: 0;">
					<label class="form-check-label">
						<input disabled class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" checked>
						<span class="form-check-sign"></span> <?php echo $jkl["hd120"];?>
					</label>
				</div>
			</div>
			<div class="card-footer">
				<!-- <a href="<?php //echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button> -->
			</div>
		</div>

		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><?php echo $jkl["hd193"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="subject"><?php echo $jkl["g16"];?></label>
							<input type="text" name="subject" id="subject" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_POST["subject"]) && !empty($_POST["subject"])) echo $_POST["subject"];?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="subject">AWB Number</label>
							<input type="text" name="awb" id="awb" class="form-control<?php if (isset($errors["e_awb"])) echo " is-invalid";?>" value="<?php if (isset($_POST["awb"]) && !empty($_POST["awb"])) echo $_POST["awb"];?>">
						</div>	
					</div>
				</div>
			<?php if (isset($JAK_RESPONSE_DATA) && !empty($JAK_RESPONSE_DATA)) { ?>
				<div class="form-group">
					<p style="margin:0;"><label for="supresp"><?php echo $jkl["u35"];?></label></p>
					<select id="supresp" class="selectpicker">
					<?php echo $JAK_RESPONSE_DATA;?>
					</select>
				</div>
			<?php } ?>

				<div class="form-group">
					<label for="content-editor"><?php echo $jkl["g321"];?></label>
					<textarea name="content" id="content-editor" rows="3" class="form-control"><?php if (isset($_REQUEST["content"]) && !empty($_REQUEST["content"])) echo $_REQUEST["content"];?></textarea>
				</div>
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="inform-client" value="1">
						<span class="form-check-sign"></span> <?php echo $jkl["hd198"];?>
					</label>
				</div>

			</div>
			<div class="card-footer float-right">
				<a href="<?php echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
		<?php } ?>
	</div>
</div>

	<input type="hidden" name="clientid" value="<?php echo $_SESSION["userinfo"];?>">
	<input type="hidden" name="depid" value="<?php echo $_SESSION["depinfo"];?>">
</form>

<?php } else { ?>
<div class="alert alert-info">
	<?php echo $jkl['i3'];?>
	<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', 'new');?>" class="alert-link"><?php echo $jkl['hd112'];?></a>
</div>
<?php } ?>

</div>

<?php include_once 'footer.php';?>