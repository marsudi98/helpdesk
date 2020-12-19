<?php include_once 'header.php';?>

<div class="content">

<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-md-3">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-5">
                <div class="icon icon-primary icon-circle">
                 <img src="<?php echo BASE_URL_ORIG.JAK_FILES_DIRECTORY.$JAK_FORM_DATA["picture"];?>" class="img-thumbnail img-fluid" alt="avatar">
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $JAK_FORM_DATA["name"];?></h3>
                <h6 class="stats-title"><?php echo $jkl["u"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-user"></i> <?php echo $jkl["u"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
  <div class="col-md-3">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-5">
                <div class="icon icon-info icon-circle">
                  <i class="fa fa-<?php echo (JAK_BILLING_MODE == 1 ? 'coins' : 'calendar-alt');?>"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo (JAK_BILLING_MODE == 1 ? $JAK_FORM_DATA["credits"] : ($JAK_FORM_DATA["paid_until"] == "1980-05-06" ? $jkl["hd260"] : $JAK_FORM_DATA["paid_until"]));?></h3>
                <h6 class="stats-title"><?php echo (JAK_BILLING_MODE == 1 ? $jkl["hd74"] : $jkl["hd75"]);?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo (JAK_BILLING_MODE == 1 ? $jkl["hd74"] : $jkl["hd75"]);?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
  <div class="col-md-3">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-5">
                <div class="icon icon-success icon-circle">
                  <i class="fad fa-comments"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $JAK_FORM_DATA["chatrequests"]?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd140"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["hd140"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
  <div class="col-md-3">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-5">
                <div class="icon icon-warning icon-circle">
                  <i class="fa fa-ticket-alt"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $JAK_FORM_DATA["supportrequests"]?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd141"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["hd141"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<?php if ($errors) { ?>
<div class="alert alert-danger"><?php if (isset($errors["e"])) echo $errors["e"];
	  if (isset($errors["e1"])) echo $errors["e1"];
	  if (isset($errors["e2"])) echo $errors["e2"];
	  if (isset($errors["e3"])) echo $errors["e3"];
	  if (isset($errors["e4"])) echo $errors["e4"];
	  if (isset($errors["e5"])) echo $errors["e5"];
	  if (isset($errors["e6"])) echo $errors["e6"];?></div>
<?php } ?>
<form class="jak_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="row">
	<div class="col-md-6">

	<div class="card">
		<div class="card-header">
		  <h3 class="card-title"><i class="fa fa-user"></i> <?php echo $jkl["g40"];?></h3>
		</div><!-- /.box-header -->
		<div class="card-body">

			<div class="form-group">
				<label for="name"><?php echo $jkl["u"];?></label>
				<input type="text" name="jak_name" id="name" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["name"];?>">
			</div>

			<div class="form-group">
				<label for="email"><?php echo $jkl["u1"];?></label>
				<input type="text" name="jak_email" id="email" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php echo $JAK_FORM_DATA["email"];?>">
			</div>

			<div class="form-group">
            	<p><label><?php echo $jkl["g22"];?></label></p>
            	<select name="jak_lang" class="selectpicker" title="<?php echo $jkl["g22"];?>" data-size="4" data-live-search="true">
					<option value=""><?php echo $jkl["u11"];?></option>
					<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if ($JAK_FORM_DATA["language"] == $lf) { ?> selected="selected"<?php } ?>><?php echo ucwords($lf);?></option><?php } ?>
				</select>
			</div>

			<label><?php echo $jkl["hd139"];?></label>
			<div class="form-check form-check-radio">
            	<label class="form-check-label">
                	<input class="form-check-input" type="radio" name="jak_upload" value="1"<?php if ($JAK_FORM_DATA["canupload"] == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_upload" value="0"<?php if ($JAK_FORM_DATA["canupload"] == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

            <hr>

            <div class="form-check">
			<label class="form-check-label">
				<input class="form-check-input" type="checkbox" name="reset_avatar" value="1">
			    <span class="form-check-sign"></span> <?php echo $jkl["u46"];?>
			</label>
		</div>

		</div>
		<div class="card-footer">
			<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
			<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
		</div>
	</div>

	<?php if (!empty($custom_fields)) { ?>
	<div class="card">
		<div class="card-header">
		  <h3 class="card-title"><?php echo $jkl["hd148"];?></h3>
		</div><!-- /.box-header -->
		<div class="card-body">
			
			<?php echo $custom_fields;?>
				
		</div>
		<div class="card-footer">
			<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
			<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
		</div>
	</div>
	<?php } ?>

	<div class="card">
		<div class="card-header">
		  <h3 class="card-title"><i class="fa fa-sack-dollar"></i> <?php echo $jkl["hd235"];?></h3>
		</div><!-- /.box-header -->
		<div class="card-body">
			<div class="input-group">
				<input type="number" name="custom_price" class="form-control" value="<?php echo $JAK_FORM_DATA["custom_price"];?>" placeholder="<?php echo $jkl["hd235"];?>" aria-describedby="custom-price">
				<div class="input-group-append">
				   <span class="input-group-text" id="custom-price">%</span>
				</div>
			</div>
		</div>

		<div class="card-footer">
			<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
			<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
		</div>
	</div>
	
	</div>
	<div class="col-md-6">

		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-city"></i> <?php echo $jkl["u36"];?> </h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<div class="form-group">
			        <p><label><?php echo $jkl["hd2"];?></label> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h6"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></p>
						
					<select name="jak_depid[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
						
						<option value="0"<?php if ($JAK_FORM_DATA["chat_dep"] == 0) echo ' selected';?>><?php echo $jkl["g105"];?></option>
				
						<?php if (isset($JAK_DEPARTMENTS) && is_array($JAK_DEPARTMENTS)) foreach($JAK_DEPARTMENTS as $v) { ?>
						
						<option value="<?php echo $v["id"];?>"<?php if (in_array($v["id"], explode(',', $JAK_FORM_DATA["chat_dep"]))) echo ' selected';?>><?php echo $v["title"];?></option>
						
						<?php } ?>
						
					</select>
				</div>

				<div class="form-group">
			        <p><label><?php echo $jkl["hd3"];?></label> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h6"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></p>

			        <select name="jak_depids[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
						
						<option value="0"<?php if ($JAK_FORM_DATA["support_dep"] == 0) echo ' selected';?>><?php echo $jkl["g105"];?></option>
				
						<?php if (isset($JAK_DEP_SUPPORT) && is_array($JAK_DEP_SUPPORT)) foreach($JAK_DEP_SUPPORT as $d) { ?>
						
						<option value="<?php echo $d["id"];?>"<?php if (in_array($d["id"], explode(',', $JAK_FORM_DATA["support_dep"]))) echo ' selected';?>><?php echo $d["title"];?></option>
						
						<?php } ?>
						
					</select>

				</div>

				<div class="form-group">
			        <p><label><?php echo $jkl["hd4"];?></label> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h6"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></p>

			        <select name="jak_depidf[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
						
						<option value="0"<?php if ($JAK_FORM_DATA["faq_cat"] == 0) echo ' selected';?>><?php echo $jkl["g105"];?></option>
				
						<?php if (isset($JAK_CAT_FAQ) && is_array($JAK_CAT_FAQ)) foreach($JAK_CAT_FAQ as $f) { ?>
						
						<option value="<?php echo $f["id"];?>"<?php if (in_array($f["id"], explode(',', $JAK_FORM_DATA["faq_cat"]))) echo ' selected';?>><?php echo $f["title"];?></option>
						
						<?php } ?>
						
					</select>

				</div>

			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-badge-check"></i> <?php echo $jkl["hd142"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">
			<?php if (JAK_BILLING_MODE == 1) { ?>

				<div class="form-group">
					<label for="credits"><?php echo $jkl["hd74"];?></label>
					<input type="number" name="jak_credits" id="credits" class="form-control" min="0" value="<?php echo $JAK_FORM_DATA["credits"];?>">
				</div>

				<input type="hidden" name="jak_validtill" value="1980-05-06">
			<?php } else { ?>

				<div class="form-group">
					<label for="validtill"><?php echo $jkl["hd143"];?></label>
					<input type="text" name="jak_validtill" id="validtill" class="form-control" value="<?php echo $JAK_FORM_DATA["paid_until"];?>">
					<small class="form-text text-muted">
					  <?php echo $jkl['hd144'];?>
					</small>
				</div>
			
				<input type="hidden" name="jak_credits" value="0">
			<?php } ?>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
			
		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-key"></i> <?php echo $jkl["g39"];?></h3>
			</div><!-- /.box-header -->
			<div class="card-body">

				<div class="form-group">
					<label for="pass"><?php echo $jkl["u4"];?></label>
					<input type="password" name="jak_password" id="pass" class="form-control<?php if (isset($errors["e5"]) || isset($errors["e6"])) echo " is-invalid";?>">
				</div>

				<div class="form-group">
					<label for="passwc"><?php echo $jkl["u5"];?></label>
					<input type="password" name="jak_confirm_password" id="passwc" class="form-control<?php if (isset($errors["e5"]) || isset($errors["e6"])) echo " is-invalid";?>">
				</div>

				<div class="progress">
					<div id="jak_pstrength" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
				</div>

			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
			
	</div>
</div>
</form>

</div>
		
<?php include_once 'footer.php';?>