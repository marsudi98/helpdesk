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
  <h3 class="card-title"><i class="fa fa-edit"></i> <?php echo $jkl["hd152"];?></h3>
</div><!-- /.box-header -->
<div class="card-body">

	<div class="form-group">
		<label for="title"><?php echo $jkl["g16"];?></label>
		<input type="text" name="jak_title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["title"];?>">
	</div>

	<div class="form-group">
		<p><label for="department"><?php echo $jkl["g131"];?></label></p>
		<select name="jak_depid" id="department" class="selectpicker" data-size="4" data-live-search="true">
	
		<option value="0"<?php if ($JAK_FORM_DATA["depid"] == 0) echo ' selected="selected"';?>><?php echo $jkl["g105"];?></option>
		<?php if (isset($JAK_DEPARTMENTS) && is_array($JAK_DEPARTMENTS)) foreach($JAK_DEPARTMENTS as $z) { ?>
	
		<option value="<?php echo $z["id"];?>"<?php if ($JAK_FORM_DATA["depid"] == $z["id"]) echo ' selected="selected"';?>><?php echo $z["title"];?></option>
	
		<?php } ?>
	
		</select>
	</div>

	<div class="form-group">
		<p><label for="jak_class"><?php echo $jkl["m13"];?></label></p>
		<select name="jak_class" class="selectpicker" data-size="4" data-live-search="true">
			<option value="secondary"<?php if ($JAK_FORM_DATA["class"] == "default") echo ' selected';?>>Default</option>
			<option value="primary"<?php if ($JAK_FORM_DATA["class"] == "primary") echo ' selected';?>>Primary</option>
			<option value="success"<?php if ($JAK_FORM_DATA["class"] == "success") echo ' selected';?>>Success</option>
			<option value="info"<?php if ($JAK_FORM_DATA["class"] == "info") echo ' selected';?>>Info</option>
			<option value="warning"<?php if ($JAK_FORM_DATA["class"] == "warning") echo ' selected';?>>Warning</option>
			<option value="danger"<?php if ($JAK_FORM_DATA["class"] == "danger") echo ' selected';?>>Danger</option>
			<option value="dark"<?php if ($JAK_FORM_DATA["class"] == "dark") echo ' selected';?>>Dark</option>
		</select>
	</div>

	<div class="form-group">
		<label for="credits"><?php echo $jkl["hd56"];?></label>
		<input type="number" min="0" name="credits" id="credits" class="form-control" value="<?php echo $JAK_FORM_DATA["credits"];?>">
	</div>
	
	<div class="form-group">
		<label><?php echo $jkl["hd150"];?></label>
		<div class="form-check form-check-radio">
			<label class="form-check-label">
				<input class="form-check-input" type="radio" name="jak_oponly" value="1"<?php if (isset($JAK_FORM_DATA["oponly"]) && $JAK_FORM_DATA["oponly"] == 1) echo " checked";?>>
				<span class="form-check-sign"></span>
				<?php echo $jkl["g19"];?>
			</label>
		</div>
		<div class="form-check form-check-radio">
			<label class="form-check-label">
				<input class="form-check-input" type="radio" name="jak_oponly" value="0"<?php if (isset($JAK_FORM_DATA["oponly"]) && $JAK_FORM_DATA["oponly"] == 0) echo " checked";?>>
				<span class="form-check-sign"></span>
				<?php echo $jkl["g18"];?>
			</label>
		</div>
	</div>

	<div class="form-group">
		<label for="order"><?php echo $jkl["g102"];?></label>
		<input type="number" name="jak_order" id="order" class="form-control" value="<?php echo $JAK_FORM_DATA["dorder"];?>">
	</div>

	<div class="form-group">
		<label for="order">Due Time</label>
		<input type="number" name="due_time" id="due_time" class="form-control" value="<?php echo $JAK_FORM_DATA["duetime"];?>">
		<small class="text-muted">*day</small>
	</div>

	<div class="form-group">
		<p><label>PIC Jenis Complaint </label> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h6"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></p>
		<select name="op_id[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
			<option value="0"<?php if ($JAK_FORM_DATA["op_id"] == 0) { ?> selected="selected"<?php } ?>><?php echo $jkl["g105"];?></option>
			<?php if (isset($JAK_OPERATORS) && is_array($JAK_OPERATORS)) foreach($JAK_OPERATORS as $z) { ?>
			<option value="<?php echo $z["id"];?>"<?php if (in_array($z["id"], explode(',', $JAK_FORM_DATA["op_id"]))) echo ' selected';?>><?php echo $z["name"];?></option>
			
			<?php } ?>
		
		</select>
	</div>

</div>
<div class="card-footer">
	<a href="<?php echo JAK_rewrite::jakParseurl('customfield');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
	<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
</div>
</div>
</div>
<div class="col-md-4">
	<div class="card">
			<div class="card-header with-border">
			  <h3 class="card-title"><i class="fa fa-language"></i> <?php echo $jkl['hd146'];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?>

					<?php $newtransl = array();
					if (isset($JAK_PRIO_TRANSLATION) && is_array($JAK_PRIO_TRANSLATION)) foreach($JAK_PRIO_TRANSLATION as $tr) {
						if ($tr['lang'] == $lf) {
							$newtransl = $tr;
						}
					}?>

					<div class="form-group">
						<label for="title_<?php echo $lf;?>"><?php echo strtoupper($lf).' - '.$jkl["g16"];?></label>
						<input type="text" name="title_<?php echo $lf;?>" id="title_<?php echo $lf;?>" class="form-control" value="<?php if (isset($newtransl['title'])) echo $newtransl['title'];?>">
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
				<a href="<?php echo JAK_rewrite::jakParseurl('customfield');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
</div>
</div>
</form>

</div>
		
<?php include_once 'footer.php';?>