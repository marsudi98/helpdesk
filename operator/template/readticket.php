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

<div class="row">
	<div class="col-md-4">
		<p><a class="btn btn-primary" href="<?php echo JAK_rewrite::jakParseurl('support', 'new');?>"><?php echo $jkl["hd166"];?></a><?php if ($JAK_FORM_DATA["status"] != 4) { ?> <a class="btn btn-info" href="<?php echo JAK_rewrite::jakParseurl('support', 'merge', $page2, $JAK_FORM_DATA["clientid"]);?>" data-toggle="modal" data-target="#jakModal"><?php echo $jkl["hd275"];?></a><?php } ?> <a href="<?php echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a></p>
	</div>
	<div class="col-md-8">
		
	</div>
</div>

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
					<dt class="col-sm-5 my-auto"><?php echo $jkl['hd191'];?></dt>
				  	<dd class="col-sm-7 my-auto"><span class="badge badge-primary">#<?php echo $page2;?></span></dd>
					<div class="col-sm-12 py-1"></div>
					<dt class="col-sm-5 my-auto">AWB Number</dt>
				  	<dd class="col-sm-7 my-auto"><?php echo ($JAK_FORM_DATA["awb"] != null ? $JAK_FORM_DATA["awb"] : '-');?></dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['hd167'];?></dt>
				  	<dd class="col-sm-7 my-auto"><span class="badge badge-<?php echo ($JAK_FORM_DATA["status"] == 1 ? "info" : ($JAK_FORM_DATA["status"] == 2 ? "warning" : ($JAK_FORM_DATA["status"] == 3 ? "success" : "success")));?>"><?php echo ($JAK_FORM_DATA["status"] == 1 ? $jkl['hd169'] : ($JAK_FORM_DATA["status"] == 2 ? $jkl['hd170'] : ($JAK_FORM_DATA["status"] == 3 ? $jkl['hd170'] : $jkl['g248'])));?></span></dd>
					<div class="col-sm-12 py-1"></div>
				  	<?php if (isset($OPERATOR_ALL) && !empty($OPERATOR_ALL)) { ?>
					<dt class="col-sm-5 my-auto"><?php echo $jkl['g130'];?></dt>
				  	<dd class="col-sm-7 my-auto">
						<?php  
							$username = $jakuser->getVar("username"); 
							if($username != 'admin') {
							$operator = $jakdb->select("user", ["id", "name", "email"], ["id" => [$JAK_FORM_DATA["operatorid"]]]);
							foreach($operator as $o){  
						?>
						<input type="text" class="form-control" name="jak_opid_show" value="<?php echo $o['name'].' ('.$o['email'].')'; ?>" title="<?php echo $o['name'].' ('.$o['email'].')'; ?>" readonly>
						<input type="hidden" class="form-control" name="jak_opid" value="<?php echo $o['id']; ?>" title="<?php echo $o['name'].' ('.$o['email'].')'; ?>" readonly>
						<?php } } else { ?>
							<select name="jak_opid" id="jak_opid" class="selectpicker" data-live-search="true">
								<?php foreach ($OPERATOR_ALL as $o) {
									echo '<option value="'.$o["id"].'"'.($JAK_FORM_DATA["operatorid"] == $o["id"] ? ' selected' : '').'>'.$o["name"].' ('.$o["email"].')</option>';
								} ?>
							</select>
							<input type="hidden" name="oldopid" value="<?php echo $JAK_FORM_DATA["operatorid"];?>">
						<?php } ?>
				  	</dd>
				  	<?php } ?>
					<div class="col-sm-12 py-1"></div>
				  	<?php if (isset($OPERATOR_ALL) && !empty($OPERATOR_ALL)) { ?>
					<dt class="col-sm-5 my-auto"><?php echo $jkl['hd288'];?></dt>
				  	<dd class="col-sm-7 my-auto">
				  		<select name="jak_opidcc[]" id="jak_opidcc" class="selectpicker" data-live-search="true" multiple>
							<?php foreach ($OPERATOR_ALL as $o) {
								echo '<option value="'.$o["id"].'"'.(in_array($o["id"], $OPERATOR_CC) ? ' selected' : '').'>'.$o["name"].' ('.$o["email"].')</option>';
							} ?>
						</select>
				  	</dd>
				  	<?php } ?>
					<div class="col-sm-12 py-1"></div>
				  	<?php if (isset($DEPARTMENTS_ALL) && !empty($DEPARTMENTS_ALL)) { ?>
					<dt class="col-sm-5 my-auto" style="display:table-cell;vertical-align:middle;">Sumber Complaint</dt>
				  	<dd class="col-sm-7 my-auto">
				  		<select name="jak_depid" id="jak_depid" class="selectpicker" data-live-search="true">
							<?php foreach ($DEPARTMENTS_ALL as $d) {
								echo '<option value="'.$d["id"].'"'.($JAK_FORM_DATA["depid"] == $d["id"] ? ' selected' : '').'>'.$d["title"].'</option>';
							} ?>
						</select>
						<input type="hidden" name="olddep" value="<?php echo $JAK_FORM_DATA["depid"];?>">
				  	</dd>
				  	<?php } ?>
					<div class="col-sm-12 py-1"></div>
					<form>
						<?php if (isset($PRIORITY_ALL) && !empty($PRIORITY_ALL)) { ?>
						<dt class="col-sm-5 my-auto">Jenis Complaint</dt> <!-- <?php //echo $jkl['hd149'];?> -->
						<dd class="col-sm-7 my-auto">
							<select name="jak_priority" id="jak_priority" class="selectpicker" data-live-search="true">
								<option value="0"<?php if ($JAK_FORM_DATA["priorityid"] == 0) echo 'selected';?>><?php echo $jkl['bw4'];?></option>
								<?php foreach ($PRIORITY_ALL as $p) {
									echo '<option value="'.$p["id"].'-'.$p["duetime"].'"'.($JAK_FORM_DATA["priorityid"] == $p["id"] ? ' selected' : '').'>'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $p["credits"]).')' : '').'</option>';
								} ?>
							</select>
							<input type="hidden" name="oldpriority" value="<?php echo $JAK_FORM_DATA["priorityid"];?>">
						</dd>
						<?php } ?>
					</form>
					<div class="col-sm-12 py-1"></div>
				  	<?php if (isset($TOPTIONS_ALL) && !empty($TOPTIONS_ALL)) { ?>
					<dt class="col-sm-5 my-auto" id="sc-label">Rincian Complaint</dt>
				  	<dd class="col-sm-7 my-auto" id="sc-select">
				  		<select name="jak_toption" id="jak_toption" class="selectpicker" data-live-search="true">
							<?php foreach ($TOPTIONS_ALL as $t) {
								echo '<option value="'.$t["id"].'"'.($JAK_FORM_DATA["toptionid"] == $t["id"] ? ' selected' : '').'>'.$t["title"].((JAK_BILLING_MODE == 1 && $t["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $t["credits"]).')' : '').'</option>';
							} ?>
						</select>
						<input type="hidden" name="oldtoption" value="<?php echo $JAK_FORM_DATA["toptionid"];?>">
				  	</dd>
				  	<?php } ?>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['hd77'];?></dt>
				  	<dd class="col-sm-7 my-auto"><a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', 'edit', $JAK_FORM_DATA["clientid"]);?>"><?php echo $JAK_FORM_DATA["name"];?></a></dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['u1'];?></dt>
				  	<dd class="col-sm-7 my-auto hyphenate"><a href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', 'edit', $JAK_FORM_DATA["clientid"]);?>"><?php echo $JAK_FORM_DATA["email"];?></a></dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto">Created Date<?php //echo $jkl['g264']; ?></dt>
				  	<!-- <dd class="col-sm-7 my-auto"><?php //echo JAK_base::jakTimesince($JAK_FORM_DATA["initiated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></dd> -->
				  	<dd class="col-sm-7 my-auto">
						<input type="text" id="created_date" name="created_date" class="form-control datepicker" value="<?php echo date('Y-m-d', $JAK_FORM_DATA["initiated"]);?>" autocomplete="off"  <?php $username = $jakuser->getVar("username"); echo ($username != 'admin') ? 'readonly' : ''; ?>>
					</dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['hd181'];?></dt>
				  	<dd class="col-sm-7 my-auto"><input class="form-control" value="<?php echo JAK_base::jakTimesince($JAK_FORM_DATA["updated"], JAK_DATEFORMAT, '');?>" readonly></dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['hd182'];?></dt>
				  	<dd class="col-sm-7 my-auto"><input class="form-control" value="<?php echo ($JAK_FORM_DATA["ended"] != 0 ? JAK_base::jakTimesince($JAK_FORM_DATA["ended"], JAK_DATEFORMAT, '') : '-');?>" readonly></dd>
				  	<!-- <dd class="col-sm-7 my-auto"><?php //echo ($JAK_FORM_DATA["ended"] != 0 ? JAK_base::jakTimesince($JAK_FORM_DATA["ended"], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></dd> -->
					  <div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['hd291'];?></dt>
				  	<dd class="col-sm-7 my-auto"><input type="text" id="jak_duedate" name="jak_duedate" class="form-control datepicker" value="<?php echo JAK_base::jakTimesince($JAK_FORM_DATA["duedate"], JAK_DATEFORMAT, '');?>" autocomplete="off" readonly></dd>
					<!-- <dd class="col-sm-7 my-auto"><input type="text" id="jak_duedate" name="jak_duedate" class="form-control datepicker" value="<?php //echo ($JAK_FORM_DATA["duedate"] != "1980-05-06" ? date($duedateformat[0], strtotime($JAK_FORM_DATA["duedate"])) : date($duedateformat[0], strtotime('+'.JAK_TICKET_DUEDATE_PRESET.'day')));?>" autocomplete="off"></dd> -->
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto" style="display: none;"><?php echo $jkl['hd172'];?></dt>
				  	<dd class="col-sm-7 my-auto" style="display: none;">
				  		<select name="jak_private" id="jak_private" class="selectpicker">
				  			<option value="0"<?php if ($JAK_FORM_DATA["private"] == 0) echo ' selected';?>><?php echo $jkl['g18'];?></option>
				  			<option value="1"<?php if ($JAK_FORM_DATA["private"] == 1) echo ' selected';?>><?php echo $jkl['g19'];?></option>
						</select>
					</dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['hd168'];?></dt>
				  	<dd class="col-sm-7 my-auto"><span id="upload-counter"><?php echo $JAK_FORM_DATA["attachments"];?></span></dd>
					<div class="col-sm-12 py-1"></div>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['g11'];?></dt>
				  	<dd class="col-sm-7 my-auto"><?php echo $JAK_FORM_DATA["ip"];?></dd>
					<div class="col-sm-12 py-1"></div>
				  	<?php if (!empty($JAK_FORM_DATA["referrer"])) { ?>
				  	<dt class="col-sm-5 my-auto"><?php echo $jkl['g169'];?></dt>
				  	<dd class="col-sm-7 my-auto hyphenate"><?php echo $JAK_FORM_DATA["referrer"];?></dd>
				  	<?php } ?>
				</dl>
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="store-dep" value="1">
						<span class="form-check-sign"></span> <?php echo $jkl["hd226"];?>
					</label>
				</div>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-file-archive"></i> <?php echo $jkl["g288"];?></h3>
			</div><!-- /.card-header -->
			<div class="card-body">
				
				<table class="table table-striped" id="attach-list">
				<?php if (isset($JAK_TICKET_FILES) && is_array($JAK_TICKET_FILES)) foreach($JAK_TICKET_FILES as $k) { ?>
					
					<tr><td>
						<?php if (getimagesize(APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$k)) { ?>
							<a data-toggle="lightbox" href="<?php echo BASE_URL_ORIG.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$k;?>"><img src="<?php echo BASE_URL_ORIG.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$k;?>" alt="<?php echo $k;?>" class="img-thumbnail" width="50px"></a>
						<?php } else { ?>
							<?php echo $k;?>
						<?php } ?>
							<a href="<?php echo BASE_URL_ORIG.JAK_FILES_DIRECTORY.'/support/'.$page2.'/'.$k;?>" class="btn btn-info btn-sm"><i class="fa fa-file-download"></i></a> <a href="<?php echo JAK_rewrite::jakParseurl('support', 'deletef', $page2, $k);?>" class="btn btn-danger btn-sm btn-confirm" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e32"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></a>
					</td></tr>
				<?php } ?>
				</table>

				<div id="share-attachments" class="dropzone upload-ticket"></div>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
			</div>
		</div>
		<?php if (isset($custom_fields) && !empty($custom_fields)) { ?>
		<div class="card">
			<div class="card-header">
			  <h3 class="card-title"><i class="fa fa-check-square"></i> <?php echo $jkl["hd148"];?></h3>
			</div>
			<div class="card-body">
				<table class="table table-striped">
				<?php echo $custom_fields;?>
				</table>
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="store-fields" value="1">
						<span class="form-check-sign"></span> <?php echo $jkl["hd187"];?>
					</label>
				</div>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
		</div>
		<?php } ?>

		<div class="card">
			<div class="card-header with-border">
			  <h3 class="card-title"><i class="fa fa-sticky-note"></i> <?php echo $jkl["g181"];?></h3>
			</div><!-- /.card-header -->
			<div class="card-body">
				<textarea name="notes" rows="3" class="form-control"><?php echo $JAK_FORM_DATA["notes"];?></textarea>
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="store-notes" value="1">
						<span class="form-check-sign"></span> <?php echo $jkl["hd186"];?>
					</label>
				</div>
			</div>
			<div class="card-footer">
				<a href="<?php echo JAK_rewrite::jakParseurl('support');?>" class="btn btn-default"><?php echo $jkl["g103"];?></a>
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
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
                	<li class="timeline-inverted">
                    <div class="timeline-badge primary">
                      <img src="<?php echo BASE_URL_ORIG.basename(JAK_FILES_DIRECTORY).$JAK_FORM_DATA["picture"];?>" class="rounded-circle img-fluid" alt="picture">
                    </div>
                    <div class="timeline-panel">
                      <div class="timeline-body">
                      	<h4 class="mt-0"><i class="fa fa-reply"></i> <?php echo $jkl['g135'];?></h4>
                        <?php if (isset($JAK_EDIT_ANSWER) && !empty($JAK_EDIT_ANSWER)) { ?>
						<div class="alert alert-info"><?php echo sprintf($jkl["hd190"], $page3);?></div>
					<?php } ?>

					<?php if (isset($JAK_EDIT_CONTENT) && !empty($JAK_EDIT_CONTENT)) { ?>
						<div class="alert alert-info"><?php echo $jkl["g47"];?></div>
					<?php } ?>

					<?php if (isset($JAK_RESPONSE_DATA) && !empty($JAK_RESPONSE_DATA)) { ?>
						<div class="form-group">
							<p><label for="supresp"><?php echo $jkl["u35"];?></label></p>
						 	<select id="supresp" class="selectpicker" data-live-search="true">
						 	<?php echo $JAK_RESPONSE_DATA;?>
						 	</select>
						 </div>
					<?php } ?>
					<?php if (isset($JAK_EDIT_CONTENT) && !empty($JAK_EDIT_CONTENT)) { ?>
						<div class="form-group">
							<label for="subject"><?php echo $jkl["g16"];?></label>
							<input type="text" name="subject" id="subject" class="form-control" value="<?php echo $JAK_EDIT_CONTENT["subject"];?>">
						</div>
					<?php } ?>
						<div class="form-group">
							<p><label for="content-editor"><?php echo $jkl["g321"];?></label></p>
							<textarea name="content" id="content-editor" rows="5" class="selectpicker"><?php if (isset($_REQUEST["content"]) && !empty($_REQUEST["content"])) { echo $_REQUEST["content"]; } elseif (isset($JAK_EDIT_ANSWER) && !empty($JAK_EDIT_ANSWER)) { echo htmlspecialchars($JAK_EDIT_ANSWER["content"]);} elseif (isset($JAK_EDIT_CONTENT) && !empty($JAK_EDIT_CONTENT)) { echo htmlspecialchars($JAK_EDIT_CONTENT["content"]);}?></textarea>
						</div>

						<div class="form-group">
							<div class="form-check form-check-inline">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="private-note" id="private_note" value="1" <?php if ($JAK_EDIT_ANSWER["private"]){ echo ' checked';}?> >
									<span class="form-check-sign"></span> <?php echo $jkl['hd248'];?> <i class="fal fa-comment-alt-exclamation"></i>
								</label>
							</div>
						</div>
                        
                      </div>
                      <div class="timeline-footer">
                      	<button type="submit" name="save_close" class="btn btn-default"><?php echo $jkl["hd284"];?></button>
						<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g4"];?></button>
						<input type="hidden" name="edit-content" value="<?php echo $editcont;?>">
						<input type="hidden" name="edit-answer" value="<?php echo $editid;?>">
                      </div>
                    </div>
                  </li>
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
                      <div class="timeline-buttons">
                      	<?php if ($JAK_FORM_DATA["status"] != 4) { ?><a href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $page2, $a["id"]);?>" class="btn btn-link btn-default"><i class="fa fa-edit"></i></a> <a href="<?php echo JAK_rewrite::jakParseurl('support', 'split', $page2, $a["id"]);?>" class="btn btn-link btn-default"><i class="fa fa-cut"></i></a> <a href="<?php echo JAK_rewrite::jakParseurl('support', 'deletea', $page2, $a["id"]);?>" class="btn btn-link btn-danger"><i class="fa fa-trash"></i></a><?php } ?>
                      </div>
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
                      <div class="timeline-buttons">
                      	<?php if ($JAK_FORM_DATA["status"] != 4) { ?><a href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $page2, 'edit');?>" class="btn btn-link"><i class="fa fa-edit"></i></a><?php } ?>
                      </div>
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