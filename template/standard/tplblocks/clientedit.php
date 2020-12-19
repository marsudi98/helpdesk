<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-profile">
	<div class="container">
		<form method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>" enctype="multipart/form-data">
		<div class="row">
			<div class="col-4">
				<div class="card card-edit-profile text-center">
				  <img alt="" class="card-img-top" src="<?php echo BASE_URL;?>template/standard/img/profile.jpg">
				  <div class="card-block">
				    <img alt="profile-picture" class="card-img-profile" width="140" src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$jakclient->getVar("picture");?>">
				    <h4 class="card-title">
				      <?php echo $jakclient->getVar("name");?>
				      <small><?php echo $jakclient->getVar("email");?></small>
				    </h4>
				    <div class="card-links">
				    	<label class="custom-file">
						  <input type="file" name="avatar" id="avatar" class="custom-file-input" accept="image/*">
						  <span class="custom-file-control"></span>
						</label>
				    </div>
				    <div class="mx-3 py-3 text-left">

					<h4><?php echo $jkl['hd25'];?></h4>

					<?php if (defined('JAK_API_PROFILE') && !empty(JAK_API_PROFILE)) { ?>
					<p><a href="<?php echo JAK_API_PROFILE;?>" class="btn btn-secondary btn-block"><?php echo $jkl['hd119'];?></a></p>
					<?php } else { ?>

					<div class="form-group">
						<label for="jak_password"><?php echo $jkl["hd58"];?></label>
						<input type="text" name="jak_password" class="form-control form-control-sm<?php if ($errors["e2"] || $errors["e3"]) echo " is-invalid";?>" value="">
					</div>

					<div class="form-group">
						<label for="pass"><?php echo $jkl["hd25"];?></label>
						<input type="text" name="jak_newpassword" id="pass" class="form-control form-control-sm<?php if ($errors["e2"] || $errors["e3"]) echo " is-invalid";?>" value="">
					</div>

					<div class="form-group">
						<label for="jak_cpassword"><?php echo $jkl["hd59"];?></label>
						<input type="text" name="jak_cpassword" class="form-control form-control-sm<?php if ($errors["e2"] || $errors["e3"]) echo " is-invalid";?>" value="">
					</div>
					<div class="form-group">
						<div class="progress">
							<div id="jak_pstrength" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>

					<?php } ?>

					</div>
				  </div>
				</div>
			</div>
			<div class="col-8">
				<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
				  if ($t["cmsid"] == 15) {
				    echo '<div data-editable data-name="title-15">'.$t["title"].'</div>';
				    echo '<div data-editable data-name="text-15">'.$t["description"].'</div>';
				  }
				} ?>

			   	<div class="form-group">
    				<label for="name"><?php echo $jkl["g4"];?></label>
			      	<input type="text" name="name" id="name" class="form-control" value="<?php echo $jakclient->getVar("name");?>" placeholder="<?php echo $jkl["g4"];?>">
			    </div>

			    <div class="form-group">
					<label for="email"><?php echo $jkl["g5"];?></label>
					<input type="text" name="email" id="email" class="form-control" value="<?php echo $jakclient->getVar("email");?>" placeholder="<?php echo $jkl["g5"];?>"<?php if (defined('JAK_API_PROFILE') && !empty(JAK_API_PROFILE)) echo ' readonly';?>>
					<?php if (defined('JAK_API_PROFILE') && !empty(JAK_API_PROFILE)) { ?>
					<small class="form-text text-muted">
  						<?php echo sprintf($jkl['hd120'], JAK_API_PROFILE);?>
						</small>
					<?php } ?>
				</div>

				<div class="form-group">
					<label for="jak_lang"><?php echo $jkl["hd54"];?></label>
					<select name="jak_lang" class="form-control">
					<option value=""><?php echo $jkl["hd55"];?></option>
					<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if ($JAK_FORM_DATA["language"] == $lf) echo ' selected';?>><?php echo ucwords($lf);?></option><?php } ?>
					</select>
				</div>

				<?php echo $custom_fields;?>

			  <p class="pull-right mb-0"><button type="submit" class="btn btn-success btn-green ls-submit"><?php echo $jkl['hd53'];?></button></p>
			  <input type="hidden" name="action" value="save_client">
			</div>
			</form>
			</div>
		</div>
	</div>
</div>

<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>