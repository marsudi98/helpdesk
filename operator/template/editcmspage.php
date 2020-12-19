<?php include_once 'header.php';?>

<div class="content">

<?php if ($errors) { ?>
	<div class="alert alert-danger">
		<?php if (isset($errors["e"])) echo $errors["e"];
		if (isset($errors["e1"])) echo $errors["e1"];
		if (isset($errors["e2"])) echo $errors["e2"];?>
	</div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><?php echo $jkl["g47"];?></h3>
		</div><!-- /.box-header -->
		<div class="card-body">

			<div class="row">
				<div class="col-md-6">

					<div class="form-group">
						<label for="title"><?php echo $jkl["g16"];?></label>
						<input type="text" maxlength="255" name="title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["title"];?>">
					</div>

					<div class="form-group">
						<p><label for="jak_lang"><?php echo $jkl["g22"];?></label></p>
						<select name="jak_lang" class="selectpicker">
						<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if ($JAK_FORM_DATA["lang"] == $lf) { ?> selected="selected"<?php } ?>><?php echo ucwords($lf);?></option><?php } ?>
						</select>
					</div>

					<div class="form-group">
						<label for="previmg"><?php echo $jkl["hd34"];?></label>
						<input type="text" name="previmg" id="previmg" class="form-control" value="<?php echo $JAK_FORM_DATA["ogimg"];?>">
						<span class="input-group-btn">
							<a class="btn btn-primary btn-round btn-icon" data-toggle="modal" data-target="#jakFM" type="button" href="javascript:void(0)"><i class="fa fa-file"></i></a>
						</span>
					</div>

					<!-- Custom Stuff from the template -->
					<?php if (isset($jaktplclient["custom"]) && is_array($jaktplclient["custom"])) { ?>
								
					<div class="form-group">
						<p><label for="order"><?php echo $jaktplclient["customtitle"];?></label></p>
						<select name="jak_custom" id="bg_change" class="selectpicker">
							<option value=""><?php echo $jkl["bw4"];?></option>
						<?php foreach($jaktplclient["custom"] as $k => $v) { ?>
							<option value="<?php echo $k;?>"<?php if ($JAK_FORM_DATA["custom"] == $k) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
						<?php } ?>
						</select>
					</div>

					<p id="bg_container"<?php if (empty($JAK_FORM_DATA["custom"])) echo ' style="display: none"';?>><img src="../template/<?php echo JAK_FRONT_TEMPLATE.'/img/'.$JAK_FORM_DATA["custom"];?>" class="img-rounded" id="bg_prev" width="100"></p>

					<?php } else { ?>
					<input type="hidden" name="jak_custom" value="">
					<?php } ?>

					<div class="row">
						<div class="col-md-6">

					<label><?php echo $jkl["hd201"];?></label>
					<div class="form-check form-check-radio">
				   	    <label class="form-check-label">
				        	<input class="form-check-input" type="radio" name="jak_header" value="1"<?php if ($JAK_FORM_DATA["showheader"] == 1) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g19"];?>
				        </label>
				    </div>
				    <div class="form-check form-check-radio">
				        <label class="form-check-label">
				            <input class="form-check-input" type="radio" name="jak_header" value="0"<?php if ($JAK_FORM_DATA["showheader"] == 0) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g18"];?>
				        </label>
				    </div>

				    <label><?php echo $jkl["hd18"];?></label>
					<div class="form-check form-check-radio">
				   	    <label class="form-check-label">
				        	<input class="form-check-input" type="radio" name="jak_membersonly" value="1"<?php if ($JAK_FORM_DATA["access"] == 1) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["hd207"];?>
				        </label>
				    </div>
				    <div class="form-check form-check-radio">
				        <label class="form-check-label">
				            <input class="form-check-input" type="radio" name="jak_membersonly" value="2"<?php if ($JAK_FORM_DATA["access"] == 2) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["hd208"];?>
				        </label>
				    </div>
				    <div class="form-check form-check-radio">
				        <label class="form-check-label">
				            <input class="form-check-input" type="radio" name="jak_membersonly" value="3"<?php if ($JAK_FORM_DATA["access"] == 3) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["hd209"];?>
				        </label>
				    </div>

				    </div>
				<div class="col-md-6">

				    <label><?php echo $jkl["hd202"];?></label>
					<div class="form-check form-check-radio">
				   	    <label class="form-check-label">
				        	<input class="form-check-input" type="radio" name="jak_footer" value="1"<?php if ($JAK_FORM_DATA["showfooter"] == 1) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g19"];?>
				        </label>
				    </div>
				    <div class="form-check form-check-radio">
				        <label class="form-check-label">
				            <input class="form-check-input" type="radio" name="jak_footer" value="0"<?php if ($JAK_FORM_DATA["showfooter"] == 0) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g18"];?>
				        </label>
				    </div>

				    <label><?php echo $jkl["hd203"];?></label>
					<div class="form-check form-check-radio">
				   	    <label class="form-check-label">
				        	<input class="form-check-input" type="radio" name="jak_ishome" value="1"<?php if ($JAK_FORM_DATA["ishome"] == 1) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g19"];?>
				        </label>
				    </div>
				    <div class="form-check form-check-radio">
				        <label class="form-check-label">
				            <input class="form-check-input" type="radio" name="jak_ishome" value="0"<?php if ($JAK_FORM_DATA["ishome"] == 0) echo ' checked';?>>
				            <span class="form-check-sign"></span>
				            <?php echo $jkl["g18"];?>
				        </label>
				    </div>

				    </div>
				    </div>				

				</div>
				<div class="col-md-6">

					<div class="form-group">
						<label for="url_slug"><?php echo $jkl["hd130"];?></label>
						<input type="text" name="url_slug" id="url_slug" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["url_slug"];?>">
					</div>

					<div class="form-group">
						<p><label for="jak_prepage"><?php echo $jkl["hd212"];?></label></p>
						<select name="jak_prepage" class="selectpicker">
							<option value="0"><?php echo $jkl["hd213"];?></option>
							<option value="<?php echo JAK_CLIENT_URL;?>"<?php if ($JAK_FORM_DATA["prepage"] == JAK_CLIENT_URL) echo ' selected';?>><?php echo JAK_CLIENT_URL;?></option>
							<option value="<?php echo JAK_SEARCH_URL;?>"<?php if ($JAK_FORM_DATA["prepage"] == JAK_SEARCH_URL) echo ' selected';?>><?php echo JAK_SEARCH_URL;?></option>
							<option value="<?php echo JAK_BLOG_URL;?>"<?php if ($JAK_FORM_DATA["prepage"] == JAK_BLOG_URL) echo ' selected';?>><?php echo JAK_BLOG_URL;?></option>
							<option value="<?php echo JAK_FAQ_URL;?>"<?php if ($JAK_FORM_DATA["prepage"] == JAK_FAQ_URL) echo ' selected';?>><?php echo JAK_FAQ_URL;?></option>
							<option value="<?php echo JAK_SUPPORT_URL;?>"<?php if ($JAK_FORM_DATA["prepage"] == JAK_SUPPORT_URL) echo ' selected';?>><?php echo JAK_SUPPORT_URL;?></option>
						</select>
					</div>

					<div class="form-group">
						<label for="meta_key"><?php echo $jkl["hd204"];?></label>
						<input type="text" maxlength="200" name="meta_key" class="form-control" value="<?php echo $JAK_FORM_DATA["meta_keywords"];?>">
					</div>

					<div class="form-group">
						<label for="order"><?php echo $jkl["g102"];?></label>
						<input type="number" min="1" name="order" id="order" class="form-control" value="<?php echo $JAK_FORM_DATA["dorder"];?>">
					</div>

					<div class="form-group">
						<label for="hits"><?php echo $jkl["hd42"];?></label>
						<input type="number" min="1" name="hits" id="hits" class="form-control" value="<?php echo $JAK_FORM_DATA["hits"];?>">
					</div>

					<div class="form-group">
						<label for="meta_desc"><?php echo $jkl["hd205"];?></label>
						<input type="text" maxlength="200" name="meta_desc" class="form-control" value="<?php echo $JAK_FORM_DATA["meta_description"];?>">
					</div>

					<?php if (isset($jaktplclient["custom2"]) && is_array($jaktplclient["custom2"])) { ?>
								
					<div class="form-group">
						<p><label for="order"><?php echo $jaktplclient["custom2title"];?></label></p>
						<select name="jak_custom2" class="selectpicker">
						<?php foreach($jaktplclient["custom2"] as $k => $v) { ?>
							<option value="<?php echo $k;?>"<?php if ($JAK_FORM_DATA["custom2"] == $k) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
						<?php } ?>
						</select>
					</div>

					<?php } else { ?>
					<input type="hidden" name="jak_custom2" value="">
					<?php } ?>
					<?php if (isset($jaktplclient["custom3"]) && is_array($jaktplclient["custom3"])) { ?>
				
					<div class="form-group">
						<p><label for="order"><?php echo $jaktplclient["custom3title"];?></label></p>
						<select name="jak_custom3" class="selectpicker">
						<?php foreach($jaktplclient["custom3"] as $k => $v) { ?>
							<option value="<?php echo $k;?>"<?php if ($JAK_FORM_DATA["custom3"] == $k) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
						<?php } ?>
						</select>
					</div>

					<?php } else { ?>
						<input type="hidden" name="jak_custom3" value="">
					<?php } ?>
					
					<?php if (isset($jaktplclient["custom5"]) && is_array($jaktplclient["custom5"])) { ?>
								
					<div class="form-group">
						<p><label for="order"><?php echo $jaktplclient["custom5title"];?></label></p>
						<select name="jak_custom5" class="selectpicker">
						<?php foreach($jaktplclient["custom5"] as $k => $v) { ?>
							<option value="<?php echo $k;?>"<?php if ($JAK_FORM_DATA["custom5"] == $k) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
						<?php } ?>
						</select>
					</div>

					<?php } else { ?>
						<input type="hidden" name="jak_custom5" value="">
					<?php } ?>

					<?php if (isset($jaktplclient["custom4"]) && is_array($jaktplclient["custom4"])) { ?>

					<div class="form-group">
						<p><label for="order"><?php echo $jaktplclient["custom4title"];?></label></p>
						<select name="jak_custom4" class="selectpicker">
						<?php foreach($jaktplclient["custom4"] as $k => $v) { ?>
							<option value="<?php echo $k;?>"<?php if ($JAK_FORM_DATA["custom4"] == $k) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
						<?php } ?>
						</select>
					</div>

					<?php } else { ?>
						<input type="hidden" name="jak_custom4" value="">
					<?php } ?>

				</div>
			</div>
	
			<div class="form-group">
				<label for="content-editor"><?php echo $jkl["g321"];?></label>
				<textarea name="content" id="content-editor" rows="5" class="form-control w-editor<?php if (isset($errors["e2"])) echo " is-invalid";?>"><?php echo $JAK_FORM_DATA["content"];?></textarea>
				<small class="form-text text-muted">
				<?php echo sprintf($jkl["hd185"], "{searchblock}, {supportnew}, {faqnew}, {blognew}, {contact}, {login}, {register}");?>
				</small>
			</div>

		</div>
		<div class="card-footer">
			<a href="<?php echo JAK_rewrite::jakParseurl('answers', 'cms');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
			<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
		</div>
	</div>
</form>

</div>

<?php include_once 'footer.php';?>