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
  <h3 class="card-title"><i class="fa fa-edit"></i> <?php echo $jkl["g47"];?></h3>
</div><!-- /.box-header -->
<div class="card-body">

	<div class="form-group">
		<label for="title"><?php echo $jkl["g16"];?></label>
		<input type="text" name="title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["title"];?>">
	</div>

	<div class="row">
		<div class="col-md-6">

			<div class="form-group">
				<p><label for="department"><?php echo $jkl["g131"];?></label></p>
				<select name="jak_catid" id="department" class="selectpicker">
				
					<option value="0"<?php if ($JAK_FORM_DATA["catid"] == 0) echo ' selected="selected"';?>><?php echo $jkl["g105"];?></option>
					<?php if (isset($JAK_CATEGORIES) && is_array($JAK_CATEGORIES)) foreach($JAK_CATEGORIES as $z) { ?>
				
					<option value="<?php echo $z["id"];?>"<?php if ($JAK_FORM_DATA["catid"] == $z["id"]) echo ' selected="selected"';?>><?php echo $z["title"];?></option>
				
					<?php } ?>
				
				</select>
			</div>

			<label><?php echo $jkl["hd33"];?></label>
			<div class="form-check form-check-radio">
			    <label class="form-check-label">
					<input class="form-check-input" type="radio" name="socialbutton" value="1"<?php if ($JAK_FORM_DATA["socialbutton"] == 1) echo ' checked';?>>
					<span class="form-check-sign"></span>
				    <?php echo $jkl["g19"];?>
				</label>
			</div>
			<div class="form-check form-check-radio">
			    <label class="form-check-label">
					<input class="form-check-input" type="radio" name="socialbutton" value="0"<?php if ($JAK_FORM_DATA["socialbutton"] == 0) echo ' checked';?>>
					<span class="form-check-sign"></span>
					<?php echo $jkl["g18"];?>
				</label>
			</div>

		</div>
		<div class="col-md-6">

			<div class="form-group">
				<p><label for="jak_lang"><?php echo $jkl["g22"];?></label></p>
				<select name="jak_lang" class="selectpicker">
				<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if ($JAK_FORM_DATA["lang"] == $lf) { ?> selected="selected"<?php } ?>><?php echo ucwords($lf);?></option><?php } ?>
				</select>
			</div>

			<div class="form-group">
				<label for="order"><?php echo $jkl["g102"];?></label>
				<input type="number" name="order" id="order" class="form-control" value="<?php echo $JAK_FORM_DATA["dorder"];?>">
			</div>

		</div>
	</div>

	<div class="form-group">
		<label for="content-editor"><?php echo $jkl["g321"];?></label>
		<textarea name="content" id="content-editor" rows="5" class="form-control"><?php echo $JAK_FORM_DATA["content"];?></textarea>
	</div>

</div>
<div class="card-footer">
	<a href="<?php echo JAK_rewrite::jakParseurl('faq');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
	<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
</div>
</div>
</form>

</div>
		
<?php include_once 'footer.php';?>