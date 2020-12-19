<?php include_once APP_PATH.JAK_OPERATOR_LOC.'/template/header.php';?>

<div class="content">

<?php if ($errors) { ?>
<div class="alert alert-danger"><?php if (isset($errors) && !empty($errors)) foreach ($errors as $e) { echo $e; }?></div>
<?php } if ($success) { ?>
<div class="alert alert-success"><?php echo $success["e"];?></div>
<?php } ?>

<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="card">
<div class="card-header">
  <h3 class="card-title"><i class="fa fa-edit"></i> <?php echo $jkl['hd45'];?></h3>
</div><!-- /.card-header -->
<div class="card-body">

	<div class="form-group">
		<label for="jak_depid"><?php echo $jkl['g131'];?></label>
		<select name="jak_depid" id="jak_depid" class="selectpicker" data-size="4" data-live-search="true">
		<?php foreach ($JAK_DEPARTMENTS as $p) { ;?><option value="<?php echo $p["id"];?>"<?php if (isset($JAK_FORM_DATA['depid']) && $JAK_FORM_DATA['depid'] == $p["id"]) { ?> selected="selected"<?php } ?>><?php echo $p["title"];?></option><?php } ?>
		</select>
	</div>

	<div class="form-group">
		<label for="mailbox"><?php echo $jkl['hd48'];?></label>
		<input type="text" name="mailbox" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["mailbox"];?>" />
	</div>

	<div class="form-group">
		<label for="usrphpimap"><?php echo $jkl['u2'];?></label>
		<input type="text" name="usrphpimap" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["username"];?>">
	</div>

	<div class="form-group">
		<label for="passphpimap"><?php echo $jkl['u4'];?></label>
		<input type="password" name="passphpimap" class="form-control" value="<?php echo $JAK_FORM_DATA["password"];?>">
	</div>

	<div class="form-group">
		<label for="encryption"><?php echo $jkl['hd49'];?></label>
		<input type="text" name="encryption" class="form-control" value="<?php echo $JAK_FORM_DATA["encryption"];?>">
	</div>

	<div class="form-group">
		<label for="inbox"><?php echo $jkl['hd50'];?></label>
		<input type="text" name="inbox" class="form-control" value="<?php echo $JAK_FORM_DATA["scanfolder"];?>">
	</div>

	<div class="form-group">
		<label for="email"><?php echo $jkl['u1'];?></label>
		<input type="text" name="email" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["emailanswer"];?>">
	</div>

	<label><?php echo $jkl["hd51"];?></label>
	<div class="form-check form-check-radio">
        <label class="form-check-label">
            <input class="form-check-input" type="radio" name="jak_msgdel" value="1"<?php if ($JAK_FORM_DATA["msgdel"] == 1) echo " checked";?>>
            <span class="form-check-sign"></span>
            <?php echo $jkl["hd52"];?>
        </label>
    </div>
    <div class="form-check form-check-radio">
        <label class="form-check-label">
        	<input class="form-check-input" type="radio" name="jak_msgdel" value="0"<?php if ($JAK_FORM_DATA["msgdel"] == 0) echo " checked";?>>
        	<span class="form-check-sign"></span>
            <?php echo $jkl["hd53"];?>
        </label>
    </div>
	
	<button type="submit" name="testMail" class="btn btn-success" id="sendTM"><i id="loader" class="fa fa-spinner fa-pulse"></i> <?php echo $jkl["hd55"];?></button>
	
</div>
<div class="card-footer">
	<a href="<?php echo JAK_rewrite::jakParseurl('settings', 'phpimap');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
	<button type="submit" name="save" class="btn btn-primary pull-right"><?php echo $jkl["g38"];?></button>
</div>
</div>
</form>

</div>
		
<?php include_once APP_PATH.JAK_OPERATOR_LOC.'/template/footer.php';?>