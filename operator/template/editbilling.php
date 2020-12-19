<?php include_once 'header.php';?>

<div class="content">

<?php if ($errors) { ?>
<div class="alert alert-danger">
<?php if (isset($errors["e"])) echo $errors["e"];
	  if (isset($errors["e1"])) echo $errors["e1"];
	  if (isset($errors["e2"])) echo $errors["e2"];
	  if (isset($errors["e3"])) echo $errors["e3"];?>
</div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
<div class="card">
<div class="card-header">
  <h3 class="card-title"><i class="fa fa-edit"></i> <?php echo $jkl["hd76"];?></h3>
</div><!-- /.box-header -->
<div class="card-body">

	<div class="form-group">
		<label for="title"><?php echo $jkl["g16"];?></label>
		<input type="text" name="title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["title"];?>" />
	</div>

	<div class="form-group">
		<label for="content"><?php echo $jkl["g52"];?></label>
		<textarea name="content" id="content" rows="5" class="form-control"><?php echo $JAK_FORM_DATA["content"];?></textarea>
	</div>

	<div class="row">
		<div class="col-md-6">

			<div class="form-group">
				<label for="amount"><?php echo $jkl["hd261"];?></label>
				<input type="number" min="0" name="amount" id="amount" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["amount"];?>">
			</div>

			<?php if (JAK_BILLING_MODE == 1) { ?>

			<div class="form-group">
				<label for="credits"><?php echo $jkl["hd74"];?></label>
				<input type="number" name="credits" id="credits" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["credits"];?>">
			</div>

			<?php } else { ?>

			<div class="form-group">
				<p><label for="paidtill"><?php echo $jkl["hd62"];?></label></p>
				<select class="selectpicker" name="paidtill">
					<option value="1 DAY"<?php if ($JAK_FORM_DATA["paidtill"] == "1 DAY") echo " selected";?>><?php echo sprintf($jkl['hd63'], "1");?></option>
					<option value="2 DAYS"<?php if ($JAK_FORM_DATA["paidtill"] == "2 DAYS") echo " selected";?>><?php echo sprintf($jkl['hd64'], "2");?></option>
					<option value="3 DAYS"<?php if ($JAK_FORM_DATA["paidtill"] == "3 DAYS") echo " selected";?>><?php echo sprintf($jkl['hd64'], "3");?></option>
					<option value="4 DAYS"<?php if ($JAK_FORM_DATA["paidtill"] == "4 DAYS") echo " selected";?>><?php echo sprintf($jkl['hd64'], "4");?></option>
					<option value="5 DAYS"<?php if ($JAK_FORM_DATA["paidtill"] == "5 DAYS") echo " selected";?>><?php echo sprintf($jkl['hd64'], "5");?></option>
					<option value="6 DAYS"<?php if ($JAK_FORM_DATA["paidtill"] == "6 DAYS") echo " selected";?>><?php echo sprintf($jkl['hd64'], "6");?></option>
					<option value="1 WEEK"<?php if ($JAK_FORM_DATA["paidtill"] == "1 WEEK") echo " selected";?>><?php echo sprintf($jkl['hd65'], "1");?></option>
					<option value="2 WEEKS"<?php if ($JAK_FORM_DATA["paidtill"] == "2 WEEKS") echo " selected";?>><?php echo sprintf($jkl['hd66'], "2");?></option>
					<option value="3 WEEKS"<?php if ($JAK_FORM_DATA["paidtill"] == "3 WEEKS") echo " selected";?>><?php echo sprintf($jkl['hd66'], "3");?></option>
					<option value="1 MONTH"<?php if ($JAK_FORM_DATA["paidtill"] == "1 MONTH") echo " selected";?>><?php echo sprintf($jkl['hd67'], "1");?></option>
					<option value="2 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "2 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "2");?></option>
					<option value="3 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "3 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "3");?></option>
					<option value="4 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "4 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "4");?></option>
					<option value="5 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "5 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "5");?></option>
					<option value="6 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "6 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "6");?></option>
					<option value="7 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "7 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "7");?></option>
					<option value="8 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "8 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "8");?></option>
					<option value="9 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "9 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "9");?></option>
					<option value="10 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "10 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "10");?></option>
					<option value="11 MONTHS"<?php if ($JAK_FORM_DATA["paidtill"] == "11 MONTHS") echo " selected";?>><?php echo sprintf($jkl['hd68'], "11");?></option>
					<option value="1 YEAR"<?php if ($JAK_FORM_DATA["paidtill"] == "1 YEAR") echo " selected";?>><?php echo sprintf($jkl['hd69'], "1");?></option>
				</select>
			</div>

			<?php } ?>

			<div class="form-group">
				<label for="previmg"><?php echo $jkl["hd34"];?></label>
				<input type="text" name="previmg" id="previmg" class="form-control" value="<?php echo $JAK_FORM_DATA["previmg"];?>">
				<span class="input-group-btn">
					<a class="btn btn-primary btn-round btn-icon" data-toggle="modal" data-target="#jakFM" type="button" href="javascript:void(0)"><i class="fa fa-file"></i></a>
				</span>
			</div>

			<div class="form-group">
				<label for="order"><?php echo $jkl["g102"];?></label>
				<input type="number" min="1" name="order" id="order" class="form-control" value="<?php echo $JAK_FORM_DATA["dorder"];?>">
			</div>

		</div>
		<div class="col-md-6">

			<div class="form-group">
				<label for="currency"><?php echo $jkl["hd72"];?></label>
				<input type="text" maxlength="3" minlength="3" name="currency" id="currency" class="form-control<?php if (isset($errors["e3"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["currency"];?>">
			</div>

			<div class="form-group">
				<p><label for="jak_depid"><?php echo $jkl["hd2"].$jkl['hd132'];?></label></p>
				<select name="jak_depid[]" multiple="multiple" class="selectpicker">
					<option value="0"<?php if (isset($JAK_FORM_DATA["chat_dep"]) && $JAK_FORM_DATA["chat_dep"] == 0) echo ' selected';?>><?php echo $jkl["g246"];?></option>
					
					<?php if (isset($JAK_DEPARTMENTS) && is_array($JAK_DEPARTMENTS)) foreach($JAK_DEPARTMENTS as $v) { ?>
							
					<option value="<?php echo $v["id"];?>"<?php if (in_array($v["id"], explode(',', $JAK_FORM_DATA["chat_dep"]))) echo ' selected';?>><?php echo $v["title"];?></option>
							
					<?php } ?>
				</select>
			</div>

			<div class="form-group">
				<p><label for="jak_depids"><?php echo $jkl["hd3"].$jkl['hd132'];?></label></p>
				<select name="jak_depids[]" multiple="multiple" class="selectpicker">
					<option value="0"<?php if (isset($JAK_FORM_DATA["support_dep"]) && $JAK_FORM_DATA["support_dep"] == 0) echo ' selected';?>><?php echo $jkl["g246"];?></option>
					
					<?php if (isset($JAK_DEP_SUPPORT) && is_array($JAK_DEP_SUPPORT)) foreach($JAK_DEP_SUPPORT as $d) { ?>
							
					<option value="<?php echo $d["id"];?>"<?php if (in_array($d["id"], explode(',', $JAK_FORM_DATA["support_dep"]))) echo ' selected';?>><?php echo $d["title"];?></option>
							
					<?php } ?>
				</select>
			</div>

			<div class="form-group">
				<p><label for="jak_depidf"><?php echo $jkl["hd4"].$jkl['hd132'];?></label></p>
				<select name="jak_depidf[]" multiple="multiple" class="selectpicker">
					<option value="0"<?php if (isset($JAK_FORM_DATA["faq_cat"]) && $JAK_FORM_DATA["faq_cat"] == 0) echo ' selected';?>><?php echo $jkl["g246"];?></option>
					
					<?php if (isset($JAK_CAT_FAQ) && is_array($JAK_CAT_FAQ)) foreach($JAK_CAT_FAQ as $f) { ?>
							
					<option value="<?php echo $f["id"];?>"<?php if (in_array($f["id"], explode(',', $JAK_FORM_DATA["faq_cat"]))) echo ' selected';?>><?php echo $f["title"];?></option>
							
					<?php } ?>
				</select>
			</div>

		</div>
	</div>

</div>
<div class="card-footer">
	<a href="<?php echo JAK_rewrite::jakParseurl('billing', 'packages');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
	<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
</div>
</div>
</form>

</div>
		
<?php include_once 'footer.php';?>