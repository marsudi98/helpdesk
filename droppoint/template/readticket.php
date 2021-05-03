<?php include_once 'header.php';?>

<div class="content">

<?php if (in_array($JAK_FORM_DATA["status"], [3,4]) && $JAK_FORM_DATA["ended"] != 0) { ?>
<div class="row">
  <div class="col-md-12">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-2">
                <div class="icon icon-success icon-circle">
                  <i class="fal fa-clock"></i>
                </div>
              </div>
              <div class="col-10 text-right">
                <h3 class="info-title"><?php echo secondsToTime(round($JAK_FORM_DATA["ended"] - $JAK_FORM_DATA["initiated"]), $jkl['g230']);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s71"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fal fa-clock"></i> <?php echo $jkl["stat_s71"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<?php } ?>

<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-md-9">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-2">
                <div class="icon icon-primary icon-circle">
                  <i class="fal fa-stopwatch"></i>
                </div>
              </div>
              <div class="col-10 text-right">
                <h3 class="info-title"><?php echo ($total_responses ? secondsToTime(round($total_responses_time / $total_responses), $jkl['g230']) : '-');?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s33"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fal fa-stopwatch"></i> <?php echo $jkl["stat_s33"];?>
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
                  <i class="fal fa-tally"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo count($JAK_ANSWER_DATA);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s34"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-tally"></i> <?php echo $jkl["stat_s34"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<?php if ($errors) { ?>
<div class="alert alert-danger">
<?php if (isset($errors["e"])) echo $errors["e"];
	  if (isset($errors["e1"])) echo $errors["e1"];?>
</div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

<div class="row">
	<div class="col-md-4">
		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-address-card"></i> <?php echo $jkl["hd180"];?></h3>
			</div><!-- /.card-header -->
			<div class="card-body">
				<dl class="row">

					<dt class="col-sm-5"><?php echo $jkl['hd191'];?></dt>
				  	<dd class="col-sm-7"><span class="badge badge-primary">#<?php echo $page2;?></span></dd>
					
					<dt class="col-sm-5">AWB Number</dt>
				  	<dd class="col-sm-7"><?php echo ($JAK_FORM_DATA["awb"] != null ? $JAK_FORM_DATA["awb"] : '-');?></dd>

				  	<dt class="col-sm-5"><?php echo $jkl['hd167'];?></dt>
				  	<dd class="col-sm-7"><span class="badge badge-<?php echo ($JAK_FORM_DATA["status"] == 1 ? "info" : ($JAK_FORM_DATA["status"] == 2 ? "warning" : ($JAK_FORM_DATA["status"] == 3 ? "success" : "success")));?>"><?php echo ($JAK_FORM_DATA["status"] == 1 ? $jkl['hd169'] : ($JAK_FORM_DATA["status"] == 2 ? $jkl['hd170'] : ($JAK_FORM_DATA["status"] == 3 ? $jkl['hd170'] : $jkl['g248'])));?></span></dd>

				  	<?php if (isset($OPERATOR_ALL) && !empty($OPERATOR_ALL)) { ?>
					<dt class="col-sm-5"><?php echo $jkl['g130'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_opid" id="jak_opid" class="selectpicker" data-live-search="true" disabled>
							<?php foreach ($OPERATOR_ALL as $o) {
								echo '<option value="'.$o["id"].'"'.($JAK_FORM_DATA["operatorid"] == $o["id"] ? ' selected' : '').'>'.$o["name"].' ('.$o["email"].')</option>';
							} ?>
						</select>
						<input type="hidden" name="oldopid" value="<?php echo $JAK_FORM_DATA["operatorid"];?>">
				  	</dd>
				  	<?php } ?>

				  	<?php if (isset($OPERATOR_ALL) && !empty($OPERATOR_ALL)) { ?>
					<dt class="col-sm-5"><?php echo $jkl['hd288'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_opidcc[]" id="jak_opidcc" class="selectpicker" data-live-search="true" multiple disabled>
							<?php foreach ($OPERATOR_ALL as $o) {
								echo '<option value="'.$o["id"].'"'.(in_array($o["id"], $OPERATOR_CC) ? ' selected' : '').'>'.$o["name"].' ('.$o["email"].')</option>';
							} ?>
						</select>
				  	</dd>
				  	<?php } ?>

				  	<?php if (isset($DEPARTMENTS_ALL) && !empty($DEPARTMENTS_ALL)) { ?>
					<dt class="col-sm-5" style="display:table-cell;vertical-align:middle;"><?php echo $jkl['g131'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_depid" id="jak_depid" class="selectpicker" data-live-search="true" disabled>
							<?php foreach ($DEPARTMENTS_ALL as $d) {
								echo '<option value="'.$d["id"].'"'.($JAK_FORM_DATA["depid"] == $d["id"] ? ' selected' : '').'>'.$d["title"].'</option>';
							} ?>
						</select>
						<input type="hidden" name="olddep" value="<?php echo $JAK_FORM_DATA["depid"];?>">
				  	</dd>
				  	<?php } ?>
					
					<form>
						<?php if (isset($PRIORITY_ALL) && !empty($PRIORITY_ALL)) { ?>
						<dt class="col-sm-5">Category</dt> <!-- <?php //echo $jkl['hd149'];?> -->
						<dd class="col-sm-7">
							<select name="jak_priority" id="jak_priority" class="selectpicker" data-live-search="true" disabled>
								<option value="0"<?php if ($JAK_FORM_DATA["priorityid"] == 0) echo 'selected';?>><?php echo $jkl['bw4'];?></option>
								<?php foreach ($PRIORITY_ALL as $p) {
									echo '<option value="'.$p["id"].'-'.$p["duetime"].'"'.($JAK_FORM_DATA["priorityid"] == $p["id"] ? ' selected' : '').'>'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $p["credits"]).')' : '').'</option>';
								} ?>
							</select>
							<input type="hidden" name="oldpriority" value="<?php echo $JAK_FORM_DATA["priorityid"];?>">
						</dd>
						<?php } ?>
					</form>

				  	<?php if (isset($TOPTIONS_ALL) && !empty($TOPTIONS_ALL)) { ?>
					<dt class="col-sm-5" id="sc-label"><?php echo $jkl['hd225'];?></dt>
				  	<dd class="col-sm-7" id="sc-select">
				  		<select name="jak_toption" id="jak_toption" class="selectpicker" data-live-search="true" disabled>
							<?php foreach ($TOPTIONS_ALL as $t) {
								echo '<option value="'.$t["id"].'"'.($JAK_FORM_DATA["toptionid"] == $t["id"] ? ' selected' : '').'>'.$t["title"].((JAK_BILLING_MODE == 1 && $t["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $t["credits"]).')' : '').'</option>';
							} ?>
						</select>
						<input type="hidden" name="oldtoption" value="<?php echo $JAK_FORM_DATA["toptionid"];?>">
				  	</dd>
				  	<?php } ?>

				  	<dt class="col-sm-5"><?php echo $jkl['hd77'];?></dt>
				  	<dd class="col-sm-7"><a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', 'edit', $JAK_FORM_DATA["clientid"]);?>"><?php echo $JAK_FORM_DATA["name"];?></a></dd>

				  	<dt class="col-sm-5"><?php echo $jkl['u1'];?></dt>
				  	<dd class="col-sm-7 hyphenate"><a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', 'edit', $JAK_FORM_DATA["clientid"]);?>"><?php echo $JAK_FORM_DATA["email"];?></a></dd>

				  	<dt class="col-sm-5">Created Date<?php //echo $jkl['g264']; ?></dt>
				  	<!-- <dd class="col-sm-7"><?php //echo JAK_base::jakTimesince($JAK_FORM_DATA["initiated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></dd> -->
				  	<dd class="col-sm-7">
						<input type="text" id="created_date" name="created_date" class="form-control datepicker" value="<?php echo date('Y-m-d', $JAK_FORM_DATA["initiated"]);?>" autocomplete="off"  <?php $username = $jakuser->getVar("username"); echo ($username != 'admin') ? 'readonly' : ''; ?>>
					</dd>

				  	<dt class="col-sm-5"><?php echo $jkl['hd181'];?></dt>
				  	<dd class="col-sm-7"><input class="form-control" value="<?php echo JAK_base::jakTimesince($JAK_FORM_DATA["updated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>" disabled></dd>

				  	<dt class="col-sm-5"><?php echo $jkl['hd182'];?></dt>
				  	<dd class="col-sm-7"><input class="form-control" value="<?php echo ($JAK_FORM_DATA["ended"] != 0 ? JAK_base::jakTimesince($JAK_FORM_DATA["ended"], JAK_DATEFORMAT) : '-');?>" readonly></dd>
				  	<!-- <dd class="col-sm-7"><?php //echo ($JAK_FORM_DATA["ended"] != 0 ? JAK_base::jakTimesince($JAK_FORM_DATA["ended"], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></dd> -->


				  	<dt class="col-sm-5"><?php echo $jkl['hd172'];?></dt>
				  	<dd class="col-sm-7">
				  		<select name="jak_private" id="jak_private" class="selectpicker" disabled>
				  			<option value="0"<?php if ($JAK_FORM_DATA["private"] == 0) echo ' selected';?>><?php echo $jkl['g18'];?></option>
				  			<option value="1"<?php if ($JAK_FORM_DATA["private"] == 1) echo ' selected';?>><?php echo $jkl['g19'];?></option>
						</select>
					</dd>

				  	<dt class="col-sm-5"><?php echo $jkl['hd168'];?></dt>
				  	<dd class="col-sm-7"><span id="upload-counter"><?php echo $JAK_FORM_DATA["attachments"];?></span></dd>

				  	<dt class="col-sm-5"><?php echo $jkl['g11'];?></dt>
				  	<dd class="col-sm-7"><?php echo $JAK_FORM_DATA["ip"];?></dd>

				  	<?php if (!empty($JAK_FORM_DATA["referrer"])) { ?>
				  	<dt class="col-sm-5"><?php echo $jkl['g169'];?></dt>
				  	<dd class="col-sm-7 hyphenate"><?php echo $JAK_FORM_DATA["referrer"];?></dd>
				  	<?php } ?>

				  	<dt class="col-sm-5"><?php echo $jkl['hd291'];?></dt>
				  	<dd class="col-sm-7"><input type="text" id="jak_duedate" name="jak_duedate" class="form-control" value="<?php echo JAK_base::jakTimesince($JAK_FORM_DATA["duedate"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>" autocomplete="off" readonly></dd>
					<!-- <dd class="col-sm-7"><input type="text" id="jak_duedate" name="jak_duedate" class="form-control datepicker" value="<?php //echo ($JAK_FORM_DATA["duedate"] != "1980-05-06" ? date($duedateformat[0], strtotime($JAK_FORM_DATA["duedate"])) : date($duedateformat[0], strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day')));?>" autocomplete="off"></dd> -->
				</dl>
			</div>
		</div>

		

		<div class="card">
			<div class="card-header with-border">
			  <h3 class="card-title"><i class="fa fa-check-square"></i> <?php echo $jkl["hd148"];?></h3>
			</div><!-- /.card-header -->
			<div class="card-body">
				<dl class="row">
					<dt class="col-sm-5"><?php echo "DP Bersalah";?></dt>
					<dd class="col-sm-7"><input class="form-control" value="<?php echo $JAK_FORM_DATA["dp_bersalah"];?>" disabled></dd>
					<dt class="col-sm-5"><?php echo "Denda";?></dt>
					<dd class="col-sm-7"><input class="form-control" value="<?php echo $JAK_FORM_DATA["denda"];?>" disabled></dd>
				</dl>
			</div>
		</div>

		<div class="card">
			<div class="card-header with-border">
			  <h3 class="card-title"><i class="fa fa-sticky-note"></i> <?php echo $jkl["g181"];?></h3>
			</div><!-- /.card-header -->
			<div class="card-body">
				<textarea name="notes" rows="3" class="form-control" disabled><?php echo $JAK_FORM_DATA["notes"];?></textarea>
			</div>
		</div>

	</div>
	<div class="col-md-8">
		<div class="card">
			<div class="card-body">
				<h6 class="mb-0"><i class="fa fa-ticket-alt"></i> <?php echo $JAK_FORM_DATA["subject"];?> <small><i class="fal fa-clock"></i> <?php echo JAK_base::jakTimesince($JAK_FORM_DATA["initiated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></small></h6>
			</div>
		</div>
		<div class="card card-timeline card-plain">
			<div class="card-body">
			<ul class="timeline timeline-simple">
				
				<?php if (isset($JAK_ANSWER_DATA) && !empty($JAK_ANSWER_DATA)) foreach ($JAK_ANSWER_DATA as $a) { ?>
				<li class="timeline-inverted">
				<div class="timeline-badge <?php echo ($a["private"] ? 'warning' : 'success');?>">
					<i class="fal fa-<?php echo ($a["private"] ? 'comment-alt-exclamation' : 'reply');?>"></i>
				</div>
				<div class="timeline-panel">
					<div class="timeline-body">
					<?php echo $a["content"];?>
					</div>
					<h6 class="small">
					<?php echo sprintf($jkl['hd188'], ($a["cname"] ? '<a href="'.JAK_rewrite::jakParseurl('users', 'clients', 'edit', $a["cid"]).'" class="text-dark">'.$a["cname"].'</a>' : $a["oname"]));?> <?php echo sprintf($jkl["hd179"], JAK_base::jakTimesince($a["sent"], JAK_DATEFORMAT, JAK_TIMEFORMAT)).' '.sprintf($jkl["hd189"], $a["id"]);?>
					</h6>
				</div>
				</li>
				<?php } ?>
				<li class="timeline-inverted">
				<div class="timeline-badge default">
					<i class="fa fa-ticket-alt"></i>
				</div>
				<div class="timeline-panel">
					<div class="timeline-heading">
					<span class="badge badge-primary"><?php echo $JAK_FORM_DATA["subject"];?></span>
					</div>
					<div class="timeline-body">
					<?php echo $JAK_FORM_DATA["content"];?>
					</div>
					<h6 class="small">
					<i class="fa fa-time"></i> <?php echo sprintf($jkl["hd178"], JAK_base::jakTimesince($JAK_FORM_DATA["initiated"], JAK_DATEFORMAT, JAK_TIMEFORMAT));?>
					</h6>
				</div>
				</li>
			</ul>
		</div>
	</div>
</div>
</div>

</form>

</div>

<?php include_once 'footer.php';?>