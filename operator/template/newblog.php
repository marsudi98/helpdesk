<?php include_once 'header.php';?>

<div class="content">

<?php if ($errors) { ?>
<div class="alert alert-danger">
<?php if (isset($errors["e"])) echo $errors["e"];
	  if (isset($errors["e1"])) echo $errors["e1"];?>
</div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
<div class="card">
<div class="card-header">
  <h3 class="card-title"><?php echo $jkl["hd30"];?></h3>
</div><!-- /.box-header -->
<div class="card-body">

	<div class="form-group">
		<label for="title"><?php echo $jkl["g16"];?></label>
		<input type="text" name="title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["title"])) echo $_REQUEST["title"];?>">
	</div>

	<div class="row">
		<div class="col-md-6">

			<div class="form-group">
				<p><label for="jak_lang"><?php echo $jkl["g22"];?></label></p>
				<select name="jak_lang" class="selectpicker">
				<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if (isset($_REQUEST["jak_lang"]) && $_REQUEST["jak_lang"] == $lf) echo ' selected'; elseif (JAK_LANG == $lf) echo ' selected';?>><?php echo ucwords($lf);?></option><?php } ?>
				</select>
			</div>

			<div class="form-group">
				<label for="previmg"><?php echo $jkl["hd34"];?></label>
				<input type="text" name="previmg" id="previmg" class="form-control" value="<?php if (isset($_REQUEST["previmg"])) echo $_REQUEST["previmg"]; ?>">
				<span class="input-group-btn">
					<a class="btn btn-primary btn-round btn-icon" data-toggle="modal" data-target="#jakFM" type="button" href="javascript:void(0)"><i class="fa fa-file"></i></a>
				</span>
			</div>

	</div>
		<div class="col-md-6">

			<div class="row">
				<div class="col-md-6">

					<label><?php echo $jkl["hd29"];?></label>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="comments" value="1"<?php if (!isset($_REQUEST["socialbutton"]) || isset($_REQUEST["comments"]) && $_REQUEST["comments"] == 1) echo ' checked';?>>
							<span class="form-check-sign"></span>
						    <?php echo $jkl["g19"];?>
						</label>
					</div>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="comments" value="0"<?php if (isset($_REQUEST["comments"]) && $_REQUEST["comments"] == 0) echo ' checked';?>>
							<span class="form-check-sign"></span>
							<?php echo $jkl["g18"];?>
						</label>
					</div>

					<label><?php echo $jkl["hd33"];?></label>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="socialbutton" value="1"<?php if (!isset($_REQUEST["socialbutton"]) || isset($_REQUEST["socialbutton"]) && $_REQUEST["socialbutton"] == 1) echo ' checked';?>>
							<span class="form-check-sign"></span>
						    <?php echo $jkl["g19"];?>
						</label>
					</div>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="socialbutton" value="0"<?php if (isset($_REQUEST["socialbutton"]) && $_REQUEST["socialbutton"] == 0) echo ' checked';?>>
							<span class="form-check-sign"></span>
							<?php echo $jkl["g18"];?>
						</label>
					</div>
				</div>
				<div class="col-md-6">

					<label><?php echo $jkl["hd32"];?></label>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="showdate" value="1"<?php if (!isset($_REQUEST["showdate"]) || isset($_REQUEST["showdate"]) && $_REQUEST["showdate"] == 1) echo ' checked';?>>
							<span class="form-check-sign"></span>
						    <?php echo $jkl["g19"];?>
						</label>
					</div>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="showdate" value="0"<?php if (isset($_REQUEST["showdate"]) && $_REQUEST["showdate"] == 0) echo ' checked';?>>
							<span class="form-check-sign"></span>
							<?php echo $jkl["g18"];?>
						</label>
					</div>

					<label><?php echo $jkl["hd36"];?></label>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="membersonly" value="1"<?php if (!isset($_REQUEST["membersonly"]) || isset($_REQUEST["membersonly"]) && $_REQUEST["membersonly"] == 1) echo ' checked';?>>
							<span class="form-check-sign"></span>
						    <?php echo $jkl["g19"];?>
						</label>
					</div>
					<div class="form-check form-check-radio">
					    <label class="form-check-label">
							<input class="form-check-input" type="radio" name="membersonly" value="0"<?php if (isset($_REQUEST["membersonly"]) && $_REQUEST["membersonly"] == 0) echo ' checked';?>>
							<span class="form-check-sign"></span>
							<?php echo $jkl["g18"];?>
						</label>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="form-group">
		<label for="content-editor"><?php echo $jkl["g321"];?></label>
		<textarea name="content" id="content-editor" rows="5" class="form-control"><?php if (isset($_REQUEST["content"])) echo $_REQUEST["content"];?></textarea>
	</div>

	</div>
	<div class="card-footer">
		<a href="<?php echo JAK_rewrite::jakParseurl('blog');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
		<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
	</div>
</div>
</form>

</div>

<?php include_once 'footer.php';?>