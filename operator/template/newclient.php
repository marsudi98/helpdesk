<?php include_once 'header.php';?>

<div class="content">

<?php if ($errors) { ?>
<div class="alert alert-danger"><?php if (isset($errors["e"])) echo $errors["e"];
	  if (isset($errors["e1"])) echo $errors["e1"];
	  if (isset($errors["e2"])) echo $errors["e2"];
	  if (isset($errors["e3"])) echo $errors["e3"];
	  if (isset($errors["e4"])) echo $errors["e4"];
	  if (isset($errors["e5"])) echo $errors["e5"];
	  if (isset($errors["e6"])) echo $errors["e6"];?></div>
<?php } ?>
<form class="jak_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="row">
	<div class="col-md-6">

	<div class="card">
	<div class="card-header">
	  <h3 class="card-title"><i class="fa fa-user"></i> <?php echo $jkl["g40"];?></h3>
	</div><!-- /.box-header -->
	<div class="card-body">

		<div class="form-group">
			<label for="name"><?php echo $jkl["u"];?></label>
			<input type="text" name="jak_name" id="name" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["jak_name"])) echo $_REQUEST["jak_name"];?>">
		</div>

		<div class="form-group">
			<label for="phone">Phone Number</label>
			<input type="text" name="phone" id="phone" class="form-control" value="<?php if (isset($_REQUEST["phone"])) echo $_REQUEST["phone"];?>">
		</div>

		<div class="form-group">
			<label for="email"><?php echo $jkl["u1"];?></label>
			<input type="text" name="jak_email" id="email" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["jak_email"])) echo $_REQUEST["jak_email"];?>">
		</div>

		<div class="form-group">
            <p><label><?php echo $jkl["g22"];?></label></p>
            <select name="jak_lang" class="selectpicker" title="<?php echo $jkl["g22"];?>" data-size="4" data-live-search="true">
				<option value=""><?php echo $jkl["u11"];?></option>
				<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"><?php echo ucwords($lf);?></option><?php } ?>
			</select>
		</div>

		<label><?php echo $jkl["hd139"];?></label>
		<div class="form-check form-check-radio">
           	<label class="form-check-label">
              	<input class="form-check-input" type="radio" name="jak_upload" value="1" checked>
                <span class="form-check-sign"></span>
                <?php echo $jkl["g19"];?>
            </label>
        </div>
        <div class="form-check form-check-radio">
       	    <label class="form-check-label">
               	<input class="form-check-input" type="radio" name="jak_upload" value="0">
                <span class="form-check-sign"></span>
                <?php echo $jkl["g18"];?>
            </label>
        </div>

        <hr>

        <div class="form-check">
			<label class="form-check-label">
				<input class="form-check-input" type="checkbox" name="send_email" value="1">
			    <span class="form-check-sign"></span> <?php echo $jkl["hd120"];?>
			</label>
		</div>

	</div>
	<div class="card-footer">
		<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
		<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
	</div>
	</div>

	<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-key"></i> <?php echo $jkl["g39"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<div class="form-group">
					<label for="pass"><?php echo $jkl["u4"];?></label>
					<input type="password" name="jak_password" id="pass" class="form-control<?php if (isset($errors["e5"]) || isset($errors["e6"])) echo " is-invalid";?>">
				</div>

				<div class="form-group">
					<label for="passwc"><?php echo $jkl["u5"];?></label>
					<input type="password" name="jak_confirm_password" id="passwc" class="form-control<?php if (isset($errors["e5"]) || isset($errors["e6"])) echo " is-invalid";?>">
				</div>

				<div class="progress">
					<div id="jak_pstrength" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
				</div>

			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
	
	</div>
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-city"></i> <?php echo $jkl["u36"];?> </h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<div class="form-group">
			        <p><label><?php echo $jkl["hd2"];?></label> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h6"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></p>
						
					<select name="jak_depid[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
						
						<option value="0"<?php if (isset($_REQUEST["jak_depid"]) && $_REQUEST["jak_depid"] == '0') { ?> selected="selected"<?php } ?>><?php echo $jkl["g105"];?></option>
						
						<?php if (isset($JAK_DEPARTMENTS) && is_array($JAK_DEPARTMENTS)) foreach($JAK_DEPARTMENTS as $v) { ?>
							
						<option value="<?php echo $v["id"];?>"><?php echo $v["title"];?></option>
								
						<?php } ?>
						
					</select>
				</div>

				<div class="form-group">
			        <p><label><?php echo $jkl["hd3"];?></label> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h6"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></p>

			        <select name="jak_depids[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
						
						<option value="0"<?php if (isset($_REQUEST["jak_depids"]) && $_REQUEST["jak_depids"] == '0') { ?> selected="selected"<?php } ?>><?php echo $jkl["g105"];?></option>
						
						<?php if (isset($JAK_DEP_SUPPORT) && is_array($JAK_DEP_SUPPORT)) foreach($JAK_DEP_SUPPORT as $d) { ?>
								
						<option value="<?php echo $d["id"];?>"><?php echo $d["title"];?></option>
								
						<?php } ?>
						
					</select>

				</div>

				<div class="form-group">
			        <p><label><?php echo $jkl["hd4"];?></label> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h6"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></p>

			        <select name="jak_depidf[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
						
						<option value="0"<?php if (isset($_POST["jak_depidf"]) && $_POST["jak_depidf"] == '0') { ?> selected="selected"<?php } ?>><?php echo $jkl["g105"];?></option>
				
						<?php if (isset($JAK_CAT_FAQ) && is_array($JAK_CAT_FAQ)) foreach($JAK_CAT_FAQ as $f) { ?>
						
						<option value="<?php echo $f["id"];?>"><?php echo $f["title"];?></option>
						
						<?php } ?>
						
					</select>

				</div>

			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
		
	</div>
</div>
</form>
		
<?php include_once 'footer.php';?>