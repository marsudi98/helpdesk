<?php include_once 'header.php';?>

<div class="content">

<?php if ($errors) { ?>
<div class="alert alert-danger">
<?php if (isset($errors["e"])) echo $errors["e"];
	  if (isset($errors["e1"])) echo $errors["e1"];?>
</div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="row">
<div class="col-md-8">
<div class="card">
<div class="card-header">
  <h3 class="card-title"><i class="fa fa-edit"></i> <?php echo $jkl["hd153"];?></h3>
</div><!-- /.box-header -->
<div class="card-body">

	<div class="form-group">
		<label for="title"><?php echo $jkl["g16"];?></label>
		<input type="text" name="jak_title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["title"];?>">
	</div>

	<div class="form-group">
		<label for="slug"><?php echo $jkl["hd155"];?></label>
		<input type="text" name="jak_slug" id="slug" class="form-control" value="<?php echo $JAK_FORM_DATA["val_slug"];?>" readonly>
	</div>

	<div class="form-group">
		<p><label for="jak_fieldloc"><?php echo $jkl["g224"];?></label></p>
		<select name="jak_fieldloc" class="selectpicker" readonly>
			<option value="1"<?php if ($JAK_FORM_DATA["fieldlocation"] == 1) echo ' selected';?>><?php echo $jkl['hd156'];?></option>
			<option value="2"<?php if ($JAK_FORM_DATA["fieldlocation"] == 2) echo ' selected';?>><?php echo $jkl['hd157'];?></option>
		</select>
	</div>

	<div class="form-group">
		<p><label for="department"><?php echo $jkl["g131"];?></label></p>
		<select name="jak_depid" id="department" class="selectpicker" data-size="4" data-live-search="true">
	
		<option value="0"<?php if ($JAK_FORM_DATA["depid"] == 0) echo ' selected="selected"';?>><?php echo $jkl["g105"];?></option>
		<?php if (isset($JAK_DEPARTMENTS) && is_array($JAK_DEPARTMENTS)) foreach($JAK_DEPARTMENTS as $z) { ?>
	
		<option value="<?php echo $z["id"];?>"<?php if ($JAK_FORM_DATA["depid"] == $z["id"]) echo ' selected';?>><?php echo $z["title"];?></option>
	
		<?php } ?>
	
		</select>
	</div>

	<div class="form-group">
		<label for="field_html"><?php echo $jkl["hd158"];?></label>
		<input type="text" name="jak_field_html" id="field_html" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["field_html"];?>">
		<small class="form-text text-muted">
			<?php echo $jkl["hd161"];?>
		</small>
	</div>
	
	<div class="form-group">
		<p><label for="jak_fieldtype"><?php echo $jkl["hd160"];?></label></p>
		<select name="jak_fieldtype" class="selectpicker" data-size="4" data-live-search="true">
			<option value="1"<?php if ($JAK_FORM_DATA["fieldtype"] == "1") { ?> selected="selected"<?php } ?>>INPUT</option>
			<option value="2"<?php if ($JAK_FORM_DATA["fieldtype"] == "2") { ?> selected="selected"<?php } ?>>RADIO</option>
			<option value="3"<?php if ($JAK_FORM_DATA["fieldtype"] == "3") { ?> selected="selected"<?php } ?>>CHECKBOX</option>
			<option value="4"<?php if ($JAK_FORM_DATA["fieldtype"] == "4") { ?> selected="selected"<?php } ?>>SELECT</option>
			<option value="5"<?php if ($JAK_FORM_DATA["fieldtype"] == "5") { ?> selected="selected"<?php } ?>>TEXTAREA</option>
		</select>
	</div>

	<label><?php echo $jkl["hd159"];?></label>
	<div class="form-check form-check-radio">
		<label class="form-check-label">
			<input class="form-check-input" type="radio" name="jak_mandatory" value="1"<?php if (isset($JAK_FORM_DATA["mandatory"]) && $JAK_FORM_DATA["mandatory"] == 1) echo " checked";?>>
			<span class="form-check-sign"></span>
			<?php echo $jkl["g19"];?>
		</label>
	</div>
	<div class="form-check form-check-radio">
		<label class="form-check-label">
			<input class="form-check-input" type="radio" name="jak_mandatory" value="0"<?php if (isset($JAK_FORM_DATA["mandatory"]) && $JAK_FORM_DATA["mandatory"] == 0) echo " checked";?>>
			<span class="form-check-sign"></span>
			<?php echo $jkl["g18"];?>
		</label>
	</div>

	<label><?php echo $jkl["hd163"];?></label>
	<div class="form-check form-check-radio">
		<label class="form-check-label">
			<input class="form-check-input" type="radio" name="jak_onregister" value="1"<?php if (isset($JAK_FORM_DATA["onregister"]) && $JAK_FORM_DATA["onregister"] == 1) echo " checked";?>>
			<span class="form-check-sign"></span>
			<?php echo $jkl["g19"];?>
		</label>
	</div>
	<div class="form-check form-check-radio">
		<label class="form-check-label">
			<input class="form-check-input" type="radio" name="jak_onregister" value="0"<?php if (isset($JAK_FORM_DATA["onregister"]) && $JAK_FORM_DATA["onregister"] == 0) echo " checked";?>>
			<span class="form-check-sign"></span>
			<?php echo $jkl["g18"];?>
		</label>
	</div>
	<small class="form-text text-muted">
		<?php echo $jkl["hd164"];?>
	</small>

	<div class="form-group">
		<label for="order"><?php echo $jkl["g102"];?></label>
		<input type="number" min="1" name="order" id="order" class="form-control" value="<?php echo $JAK_FORM_DATA["dorder"];?>">
	</div>

</div>
<div class="card-footer">
	<a href="<?php echo JAK_rewrite::jakParseurl('customfield', 'form');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
	<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
</div>
</div>
</div>
<div class="col-md-4">
	<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-language"></i> <?php echo $jkl['hd146'];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?>

					<?php $newtransl = array();
					if (isset($JAK_FIELD_TRANSLATION) && is_array($JAK_FIELD_TRANSLATION)) foreach($JAK_FIELD_TRANSLATION as $tr) {
						if ($tr['lang'] == $lf) {
							$newtransl = $tr;
						}
					}?>

					<div class="form-group">
						<label for="title_<?php echo $lf;?>"><?php echo strtoupper($lf).' - '.$jkl["g16"];?></label>
						<input type="text" name="title_<?php echo $lf;?>" id="title_<?php echo $lf;?>" class="form-control" value="<?php if (isset($newtransl['title'])) echo $newtransl['title'];?>">
					</div>

					<div class="form-group">
						<label for="field_html_<?php echo $lf;?>"><?php echo strtoupper($lf).' - '.$jkl["hd158"];?></label>
						<input type="text" name="field_html_<?php echo $lf;?>" id="field_html_<?php echo $lf;?>" class="form-control" value="<?php if (isset($newtransl['description'])) echo $newtransl['description'];?>">
					</div>

					<?php if (is_array($newtransl) && !empty($newtransl)) { ?>
					<div class="form-check form-check-inline">
				  		<label class="form-check-label">
				    		<input class="form-check-input" type="checkbox" name="deltrans_<?php echo $lf;?>" value="<?php echo $lf;?>"> <?php echo $jkl["hd147"];?>
				  		</label>
					</div>
					<?php } } ?>

			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('customfield', 'form');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
</div>
</div>
</form>

</div>

<?php include_once 'footer.php';?>