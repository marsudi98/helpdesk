<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>


<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
				if ($t["cmsid"] == 15) {
					echo '<div data-editable data-name="title-15">'.$t["title"].'</div>';
					echo '<div data-editable data-name="text-15">'.$t["description"].'</div>';
				}
			} ?>
		</div>
	</div>
</div>

<div class="container">
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-3">
				<div class="card card-profile">
					<div class="card-header card-header-image">
						<a href="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL, 'edit');?>">
							<img class="img" src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$jakclient->getVar("picture");?>" alt="profile-picture">
						</a>
					</div>
					<div class="card-body">
						<h4 class="card-title"><?php echo $jakclient->getVar("name");?></h4>
						<h6 class="card-category text-gray"><?php echo $jakclient->getVar("email");?></h6>
						<h6 class="card-title"><?php echo $jkl['hd25'];?></h6>

						<?php if (defined('JAK_API_PROFILE') && !empty(JAK_API_PROFILE)) { ?>
							<p><a href="<?php echo JAK_API_PROFILE;?>" class="btn btn-sm btn-default"><?php echo $jkl['hd119'];?></a></p>
						<?php } else { ?>

							<div class="form-group<?php if ($errorsp["e2"] || $errorsp["e3"]) echo " has-danger";?>">
								<label for="pass" class="bmd-label-floating"><?php echo $jkl["hd25"];?></label>
								<input type="text" name="jak_newpassword" id="pass" class="form-control">
							</div>

							<div class="form-group<?php if ($errorsp["e2"] || $errorsp["e3"]) echo " has-danger";?>">
								<label for="jak_cpassword" class="bmd-label-floating"><?php echo $jkl["hd59"];?></label>
								<input type="text" name="jak_cpassword" id="jak_cpassword" class="form-control">
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
			<div class="col-md-9">
				<div class="form-group">
					<label for="name" class="bmd-label-floating"><?php echo $jkl["g4"];?></label>
					<input type="text" name="name" id="name" class="form-control" value="<?php echo $jakclient->getVar("name");?>">
				</div>

				<div class="form-group">
					<label for="email" class="bmd-label-floating"><?php echo $jkl["g5"];?></label>
					<input type="text" name="email" id="email" class="form-control" value="<?php echo $jakclient->getVar("email");?>"<?php if (defined('JAK_API_PROFILE') && !empty(JAK_API_PROFILE)) echo ' readonly';?>>
					<?php if (defined('JAK_API_PROFILE') && !empty(JAK_API_PROFILE)) { ?>
						<small class="form-text text-muted">
							<?php echo sprintf($jkl['hd120'], JAK_API_PROFILE);?>
						</small>
					<?php } ?>
				</div>

				<div class="form-group form-file-upload form-file-multiple<?php if ($errorsp["e4"]) echo " has-danger";?>">
					<label for="avatar" class="bmd-label-floating"><?php echo $jkl["g18"];?></label>
					<input type="file" name="avatar" id="avatar" class="inputFileHidden" accept="image/*">
					<div class="input-group">
						<input type="text" class="form-control inputFileVisible" name="avatar" id="avatar" autocomplete="off">
						<span class="input-group-btn">
							<button type="button" class="btn btn-link btn-fab btn-primary">
								<i class="material-icons">attach_file</i>
							</button>
						</span>
					</div>
				</div>

				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="deleteavatar" value="1"> <?php echo $jkl["hd126"];?>
						<span class="form-check-sign">
							<span class="check"></span>
						</span>
					</label>
				</div>

				<div class="form-group">
					<label for="jak_lang" class="bmd-label-floating"><?php echo $jkl["hd54"];?></label>
					<select name="jak_lang" class="form-control">
						<option value=""><?php echo $jkl["hd55"];?></option>
						<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if ($JAK_FORM_DATA["language"] == $lf) echo ' selected';?>><?php echo ucwords($lf);?></option><?php } ?>
					</select>
				</div>

				<?php echo $custom_fields;?>

				<p class="pull-right"><button type="submit" class="btn btn-primary"><?php echo $jkl['hd53'];?></button></p>
				<input type="hidden" name="action" value="save_client">
			</div>
		</form>
	</div>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>