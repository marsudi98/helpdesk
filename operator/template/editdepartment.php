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
  <h3 class="card-title"><i class="fa fa-edit"></i> <?php echo $jkl["g47"];?></h3>
</div><!-- /.box-header -->
<div class="card-body">

<?php if ($page1 == 'faq') { ?>

	<div class="form-group">
		<p><label for="jak_class"><?php echo $jkl["m13"];?></label></p>
		<select name="jak_class" class="selectpicker">
			<option value="secondary"<?php if ($JAK_FORM_DATA["class"] == "default") echo ' selected';?>>Default</option>
			<option value="primary"<?php if ($JAK_FORM_DATA["class"] == "primary") echo ' selected';?>>Primary</option>
			<option value="success"<?php if ($JAK_FORM_DATA["class"] == "success") echo ' selected';?>>Success</option>
			<option value="info"<?php if ($JAK_FORM_DATA["class"] == "info") echo ' selected';?>>Info</option>
			<option value="warning"<?php if ($JAK_FORM_DATA["class"] == "warning") echo ' selected';?>>Warning</option>
			<option value="danger"<?php if ($JAK_FORM_DATA["class"] == "danger") echo ' selected';?>>Danger</option>
			<option value="dark"<?php if ($JAK_FORM_DATA["class"] == "dark") echo ' selected';?>>Dark</option>
		</select>
	</div>

<?php } ?>

	<div class="form-group">
		<label for="title"><?php echo $jkl["g16"];?></label>
		<input type="text" name="title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["title"];?>">
	</div>

	<div class="form-group">
		<label for="email"><?php echo $jkl["g68"];?></label>
		<input type="text" name="email" id="email" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["email"];?>">
	</div>

	<?php if ($page1 != 'faq') { ?>

	<div class="form-group">
		<label for="credits"><?php echo $jkl["hd56"];?></label>
		<input type="number" min="0" name="credits" id="credits" class="form-control" value="<?php echo $JAK_FORM_DATA["credits"];?>">
	</div>

	<div class="form-group">
		<label for="faq"><?php echo $jkl["g278"];?></label>
		<input type="text" name="faq" id="faq" class="form-control" value="<?php echo $JAK_FORM_DATA["faq_url"];?>">
	</div>

	<?php } ?>

	<label><?php echo $jkl["hd18"];?></label>
	<div class="form-check form-check-radio">
		<label class="form-check-label">
			<input class="form-check-input" type="radio" name="jak_guesta" value="1"<?php if (isset($JAK_FORM_DATA["guesta"]) && $JAK_FORM_DATA["guesta"] == 1) echo " checked";?>>
			<span class="form-check-sign"></span>
	 		<?php echo $jkl["g19"];?>
	  	</label>
	</div>
	<div class="form-check form-check-radio">
		<label class="form-check-label">
			<input class="form-check-input" type="radio" name="jak_guesta" value="0"<?php if (isset($JAK_FORM_DATA["guesta"]) && $JAK_FORM_DATA["guesta"] == 0) echo " checked";?>>
			<span class="form-check-sign"></span>
			<?php echo $jkl["g18"];?>
		</label>
	</div>

	<div class="form-group">
		<label for="desc"><?php echo $jkl["g52"];?></label>
		<textarea name="description" id="desc" rows="5" class="form-control"><?php echo $JAK_FORM_DATA["description"];?></textarea>
	</div>

	<?php if ($page1 == 'support') { ?>
	<div class="form-group">
		<label for="predefined_content"><?php echo $jkl["g151"];?> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h13"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></label>
		<textarea name="predefined_content" id="content-editor" rows="5" class="form-control"><?php echo $JAK_FORM_DATA["pre_content"];?></textarea>
	</div>
	<?php } ?>

</div>
<div class="card-footer">
	<a href="<?php echo (!in_array($page1, array("support", "faq")) ? JAK_rewrite::jakParseurl('departments') : JAK_rewrite::jakParseurl('departments', $page1));?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
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
					if (isset($JAK_DEP_TRANSLATION) && is_array($JAK_DEP_TRANSLATION)) foreach($JAK_DEP_TRANSLATION as $tr) {
						if ($tr['lang'] == $lf) {
							$newtransl = $tr;
						}
					}?>

					<div class="form-group">
						<label for="title_<?php echo $lf;?>"><?php echo strtoupper($lf).' - '.$jkl["g16"];?></label>
						<input type="text" name="title_<?php echo $lf;?>" id="title_<?php echo $lf;?>" class="form-control" value="<?php echo $newtransl['title'];?>">
					</div>

					<?php if ($page1 != 'faq') { ?>
					<div class="form-group">
						<label for="faq_<?php echo $lf;?>"><?php echo strtoupper($lf).' - '.$jkl["g278"];?></label>
						<input type="text" name="faq_<?php echo $lf;?>" id="faq_<?php echo $lf;?>" class="form-control" value="<?php echo $newtransl['faq_url'];?>">
					</div>
					<?php } ?>

					<div class="form-group">
						<label for="desc_<?php echo $lf;?>"><?php echo strtoupper($lf).' - '.$jkl["g52"];?></label>
						<textarea name="description_<?php echo $lf;?>" id="desc_<?php echo $lf;?>" rows="3" class="form-control"><?php echo $newtransl['description'];?></textarea>
					</div>
					<?php if (is_array($newtransl) && !empty($newtransl)) { ?>
					<div class="form-check form-check-inline">
						<label class="form-check-label">
							<input class="form-check-input" type="checkbox" name="deltrans_<?php echo $lf;?>" value="<?php echo $lf;?>">
							<span class="form-check-sign"></span> <?php echo $jkl["hd147"];?>
						</label>
					</div>
		
					<?php } } ?>
			</div>
			<div class="card-footer">
				<a href="<?php echo (!in_array($page1, array("support", "faq")) ? JAK_rewrite::jakParseurl('departments') : JAK_rewrite::jakParseurl('departments', $page1));?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
</div>
</div>
</form>

</div>
		
<?php include_once 'footer.php';?>