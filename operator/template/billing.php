<?php include_once 'header.php';?>

<div class="content">

<?php if (JAK_BILLING_MODE == 0) { ?>
<div class="alert alert-info"><?php echo $jkl['hd86'];?></div>
<?php } ?>

<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-md-3">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-3">
                <div class="icon icon-primary icon-circle">
                  <i class="fa fa-box"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo count($BILLING_ALL);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s57"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-box"></i> <?php echo $jkl["stat_s57"];?>
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
                  <i class="fal fa-box"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $busy_package["title"];?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s56"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fal fa-box"></i> <?php echo $jkl["stat_s56"];?>
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
                  <i class="fas fa-users-cog"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalChange;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s49"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-users-cog"></i> <?php echo $jkl["stat_s49"];?>
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
              <div class="col-3">
                <div class="icon icon-warning icon-circle">
                  <i class="fa fa-clock"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo ($lastChange ? JAK_base::jakTimesince($lastChange, JAK_DATEFORMAT, "") : "-");?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s53"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-clock"></i> <?php echo $jkl["stat_s53"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<p>
<ul class="nav nav-pills nav-pills-primary">
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('billing');?>"><?php echo $jkl["hd57"];?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('billing', 'packages');?>"><?php echo $jkl["hd60"];?></a>
  </li>
</ul>
</p>

<div class="row">
<div class="col-md-4">

	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-box-open"></i> <?php echo $jkl["hd70"];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php if (isset($errors["e"])) echo $errors["e"];
					  if (isset($errors["e1"])) echo $errors["e1"];
					  if (isset($errors["e2"])) echo $errors["e2"];
					  if (isset($errors["e3"])) echo $errors["e3"];?></div>
				<?php } ?>
				
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					
					<div class="form-group">
					    <label for="title"><?php echo $jkl["g16"];?></label>
						<input type="text" name="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["title"])) echo $_REQUEST["title"];?>">
					</div>

					<div class="form-group">
					    <label for="content"><?php echo $jkl["g52"];?></label>
						<textarea class="form-control" name="content" rows="3"><?php if (isset($_REQUEST["content"])) echo $_REQUEST["content"];?></textarea>
					</div>

					<div class="form-group">
						<label for="previmg"><?php echo $jkl["hd34"];?></label>
						<input type="text" name="previmg" id="previmg" class="form-control" value="<?php if (isset($_REQUEST["previmg"])) echo $_REQUEST["previmg"];?>">
						<span class="input-group-btn">
							<a class="btn btn-primary btn-round btn-icon" data-toggle="modal" data-target="#jakFM" type="button" href="javascript:void(0)"><i class="fa fa-file"></i></a>
						</span>
					</div>

					<?php if (JAK_BILLING_MODE == 1) { ?>
					<div class="form-group">
					    <label for="credits"><?php echo $jkl["hd74"];?></label>
						<input type="number" min="0" name="credits" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["credits"])) echo $_REQUEST["credits"];?>">
					</div>
					<?php } else { ?>
					<div class="form-group">
					    <p><label for="paidtill"><?php echo $jkl["hd62"];?></label></p>
						<select class="selectpicker" name="paidtill" data-size="4" data-live-search="true">
							<option value="1 DAY"><?php echo sprintf($jkl['hd63'], "1");?></option>
							<option value="2 DAYS"><?php echo sprintf($jkl['hd64'], "2");?></option>
							<option value="3 DAYS"><?php echo sprintf($jkl['hd64'], "3");?></option>
							<option value="4 DAYS"><?php echo sprintf($jkl['hd64'], "4");?></option>
							<option value="5 DAYS"><?php echo sprintf($jkl['hd64'], "5");?></option>
							<option value="6 DAYS"><?php echo sprintf($jkl['hd64'], "6");?></option>
							<option value="1 WEEK"><?php echo sprintf($jkl['hd65'], "1");?></option>
							<option value="2 WEEKS"><?php echo sprintf($jkl['hd66'], "2");?></option>
							<option value="3 WEEKS"><?php echo sprintf($jkl['hd66'], "3");?></option>
							<option value="1 MONTH"><?php echo sprintf($jkl['hd67'], "1");?></option>
							<option value="2 MONTHS"><?php echo sprintf($jkl['hd68'], "2");?></option>
							<option value="3 MONTHS"><?php echo sprintf($jkl['hd68'], "3");?></option>
							<option value="4 MONTHS"><?php echo sprintf($jkl['hd68'], "4");?></option>
							<option value="5 MONTHS"><?php echo sprintf($jkl['hd68'], "5");?></option>
							<option value="6 MONTHS"><?php echo sprintf($jkl['hd68'], "6");?></option>
							<option value="7 MONTHS"><?php echo sprintf($jkl['hd68'], "7");?></option>
							<option value="8 MONTHS"><?php echo sprintf($jkl['hd68'], "8");?></option>
							<option value="9 MONTHS"><?php echo sprintf($jkl['hd68'], "9");?></option>
							<option value="10 MONTHS"><?php echo sprintf($jkl['hd68'], "10");?></option>
							<option value="11 MONTHS"><?php echo sprintf($jkl['hd68'], "11");?></option>
							<option value="1 YEAR"><?php echo sprintf($jkl['hd69'], "1");?></option>
						</select>
					</div>
					<?php } ?>

					<div class="form-group">
						<p><label for="jak_depid"><?php echo $jkl["hd2"].$jkl['hd132'];?></label></p>
						<select name="jak_depid[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
							<option value="0"<?php if (isset($_REQUEST["jak_depid"]) && $_REQUEST["jak_depid"] == 0) echo ' selected';?>><?php echo $jkl["g246"];?></option>
				
						<?php if (isset($JAK_DEPARTMENTS) && is_array($JAK_DEPARTMENTS)) foreach($JAK_DEPARTMENTS as $v) { ?>
						
							<option value="<?php echo $v["id"];?>"><?php echo $v["title"];?></option>
						
						<?php } ?>
						</select>
					</div>

					<div class="form-group">
						<p><label for="jak_depids"><?php echo $jkl["hd3"].$jkl['hd132'];?></label></p>
						<select name="jak_depids[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
							<option value="0"<?php if (isset($_REQUEST["jak_depid"]) && $_REQUEST["jak_depid"] == 0) echo ' selected';?>><?php echo $jkl["g246"];?></option>
				
						<?php if (isset($JAK_DEP_SUPPORT) && is_array($JAK_DEP_SUPPORT)) foreach($JAK_DEP_SUPPORT as $d) { ?>
						
							<option value="<?php echo $d["id"];?>"><?php echo $d["title"];?></option>
						
						<?php } ?>
						</select>
					</div>

					<div class="form-group">
						<p><label for="jak_depidf"><?php echo $jkl["hd4"].$jkl['hd132'];?></label></p>
						<select name="jak_depidf[]" multiple="multiple" class="selectpicker" data-size="4" data-live-search="true">
							<option value="0"<?php if (isset($_REQUEST["jak_depid"]) && $_REQUEST["jak_depid"] == 0) echo ' selected';?>><?php echo $jkl["g246"];?></option>
				
						<?php if (isset($JAK_CAT_FAQ) && is_array($JAK_CAT_FAQ)) foreach($JAK_CAT_FAQ as $f) { ?>
						
							<option value="<?php echo $f["id"];?>"><?php echo $f["title"];?></option>
						
						<?php } ?>
						</select>
					</div>

					<div class="form-group">
					    <label for="amount"><?php echo $jkl["hd261"];?></label>
						<input type="number" min="0" name="amount" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["amount"])) echo $_REQUEST["amount"];?>">
					</div>

					<div class="form-group">
					    <label for="currency"><?php echo $jkl["hd72"];?></label>
						<input type="text" maxlength="3" minlength="3" name="currency" class="form-control<?php if (isset($errors["e3"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["currency"])) echo $_REQUEST["currency"];?>">
					</div>
					
					<div class="form-actions">
						<button type="submit" name="insert_billing" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
					</div>

				</form>
		</div>
		
	</div>
</div>
<div class="col-md-8">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-box-full"></i> <?php echo $jkl["hd60"];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">

	  		<?php if (isset($BILLING_ALL) && is_array($BILLING_ALL) && !empty($BILLING_ALL)) { ?>
			<div class="table-responsive">
			<table class="table table-striped">
			<thead>
			<tr>
			<th>#</th>
			<th><?php echo $jkl["g16"];?></th>
			<?php if (JAK_BILLING_MODE == 1) { ?>
			<th><?php echo $jkl["hd74"];?></th>
			<?php } else { ?>
			<th><?php echo $jkl["hd75"];?></th>
			<?php } ?>
			<th><?php echo $jkl["hd261"];?></th>
			<th><?php echo $jkl["g101"];?></th>
			<th><?php echo $jkl["g48"];?></th>
			</tr>
			</thead>
			<?php foreach($BILLING_ALL as $v) { ?>
			<tr>
			<td><?php echo $v["id"];?></td>
			<td class="title"><a href="<?php echo JAK_rewrite::jakParseurl('billing', 'packages', 'edit', $v["id"]);?>"><?php echo $v["title"];?></a></td>
			<?php if (JAK_BILLING_MODE == 1) { ?>
			<td><?php echo $v["credits"];?></td>
			<?php } else { ?>
			<td><?php echo $v["paidtill"];?></td>
			<?php } ?>
			<td><?php echo $v["amount"].' '.$v["currency"];?></td>
			<td><a class="btn btn-default btn-sm" href="<?php echo JAK_rewrite::jakParseurl('billing', 'packages', 'lock', $v["id"]);?>"><i class="fa fa-<?php if ($v["active"] == '1') { ?>check<?php } else { ?>lock<?php } ?>"></i></a></td>
			<td><a class="btn btn-danger btn-sm btn-confirm" href="<?php echo JAK_rewrite::jakParseurl('billing', 'packages', 'delete', $v["id"]);?>" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e31"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></a></td>
			</tr>
			<?php } ?>
			</table>
			</div>

			<?php } else { ?>

			<div class="alert alert-info">
				<?php echo $jkl['i3'];?>
			</div>

			<?php } ?>

		</div>
	</div>
</div>		
</div>

</div>

<?php include_once 'footer.php';?>