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
                  <i class="fa fa-cogs"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAll;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s42"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-cogs"></i> <?php echo $jkl["stat_s42"];?>
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
                  <i class="fad fa-users-cog"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalChange;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s44"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-users-cog"></i> <?php echo $jkl["stat_s44"];?>
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
                  <i class="fa fa-filter"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo (count($PRIORITY_ALL) + count($TOPTIONS_ALL));?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s46"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-filter"></i> <?php echo $jkl["stat_s46"];?>
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
                  <i class="fa fa-file"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo iterator_count($totalFiles);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s38"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-file"></i> <?php echo $jkl["stat_s38"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<div class="alert alert-info">
	<p><?php echo $jkl["hd90"];?></p>
	<i><?php echo APP_PATH.'cron/tickets.php';?></i>
</div>

<p>
<ul class="nav nav-pills nav-pills-primary">
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings');?>"><?php echo $jkl["hd9"];?></a>
  </li>
  <?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('settings', 'support');?>"><?php echo $jkl["hd10"];?></a>
  </li>
  <?php } if (jak_get_access("faq", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'faq');?>"><?php echo $jkl["hd11"];?></a>
  </li>
  <?php } if (jak_get_access("blog", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'blog');?>"><?php echo $jkl["hd12"];?></a>
  </li>
  <?php } ?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'email');?>"><?php echo $jkl["hd119"];?></a>
  </li>
  <?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'phpimap');?>"><?php echo $jkl["hd43"];?></a>
  </li>
  <?php } ?>
</ul>
</p>

<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI'];?>">

<div class="row">
	<div class="col-md-6">
		<div class="card">
		<div class="card-header">
		  <h3 class="card-title"><i class="fa fa-cogs"></i> <?php echo $jkl["hd91"];?></h3>
		</div><!-- /.box-header -->
		<div class="card-body">

			<label><?php echo $jkl["hd92"];?></label>
			<div class="form-check form-check-radio">
            	<label class="form-check-label">
                	<input class="form-check-input" type="radio" name="jak_ticket_close_r" value="1"<?php if (JAK_TICKET_CLOSE_R == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_ticket_close_r" value="0"<?php if (JAK_TICKET_CLOSE_R == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

            <label><?php echo $jkl["hd93"];?></label>
			      <div class="form-check form-check-radio">
            	<label class="form-check-label">
                	<input class="form-check-input" type="radio" name="jak_ticket_inform" value="1"<?php if (JAK_TICKET_INFORM_R == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_ticket_inform" value="0"<?php if (JAK_TICKET_INFORM_R == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

            <label><?php echo $jkl["hd94"];?></label>
			      <div class="form-check form-check-radio">
            	<label class="form-check-label">
                	<input class="form-check-input" type="radio" name="jak_ticket_guest" value="1"<?php if (JAK_TICKET_GUEST == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_ticket_guest" value="0"<?php if (JAK_TICKET_GUEST == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

            <label><?php echo $jkl["hd208"];?></label>
			      <div class="form-check form-check-radio">
            	<label class="form-check-label">
                	<input class="form-check-input" type="radio" name="jak_ticket_guest_web" value="1"<?php if (JAK_TICKET_GUEST_WEB == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_ticket_guest_web" value="0"<?php if (JAK_TICKET_GUEST_WEB == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

            <label><?php echo $jkl["hd95"];?></label>
			      <div class="form-check form-check-radio">
            	<label class="form-check-label">
                	<input class="form-check-input" type="radio" name="jak_ticket_account" value="1"<?php if (JAK_TICKET_ACCOUNT == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_ticket_account" value="0"<?php if (JAK_TICKET_ACCOUNT == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

            <label><?php echo $jkl["g85"];?></label>
            <div class="form-check form-check-radio">
              <label class="form-check-label">
                  <input class="form-check-input" type="radio" name="jak_ticket_rating" value="1"<?php if (JAK_TICKET_RATING == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_ticket_rating" value="0"<?php if (JAK_TICKET_RATING == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

            <div class="form-group">
            <p><label><?php echo $jkl["hd229"];?></label></p>
			<select name="jak_priority" class="selectpicker" title="<?php echo $jkl["hd229"];?>" data-size="4">
				<option value="0"<?php if (JAK_STANDARD_TICKET_PRIORITY == 0) echo ' selected';?>><?php echo $jkl["g304"];?></option>
				<?php if (isset($PRIORITY_ALL) && !empty($PRIORITY_ALL)) foreach ($PRIORITY_ALL as $p) {
	  				if ($p["id"] == JAK_STANDARD_TICKET_PRIORITY) {
	  					echo '<option value="'.$p["id"].'" selected>'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $p["credits"]).')' : '').'</option>';
	  				} else {
	  					echo '<option value="'.$p["id"].'">'.$p["title"].((JAK_BILLING_MODE == 1 && $p["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $p["credits"]).')' : '').'</option>';
	  				}
	  			} ?>
			</select>
			</div>

			<div class="form-group">
        <p><label><?php echo $jkl["hd230"];?></label></p>
			  <select name="jak_option" class="selectpicker" title="<?php echo $jkl["hd230"];?>" data-size="4">
				  <option value="0"<?php if (JAK_STANDARD_TICKET_OPTION == 0) echo ' selected';?>><?php echo $jkl["g304"];?></option>
				  <?php if (isset($TOPTIONS_ALL) && !empty($TOPTIONS_ALL)) foreach ($TOPTIONS_ALL as $o) {
	  				if ($o["id"] == JAK_STANDARD_TICKET_OPTION) {
	  					echo '<option value="'.$o["id"].'" selected>'.$o["title"].((JAK_BILLING_MODE == 1 && $o["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $o["credits"]).')' : '').'</option>';
	  				} else {
	  					echo '<option value="'.$o["id"].'">'.$o["title"].((JAK_BILLING_MODE == 1 && $o["credits"] != 0) ? ' ('.sprintf($jkl['hd232'], $o["credits"]).')' : '').'</option>';
	  				}
	  			} ?>
			  </select>
			</div>

			<label><?php echo $jkl["hd231"];?></label>
			<div class="form-check form-check-radio">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="jak_private" value="1"<?php if (JAK_TICKET_PRIVATE == 1) echo ' checked';?>>
          <span class="form-check-sign"></span>
            <?php echo $jkl["g19"];?>
          </label>
      </div>
      <div class="form-check form-check-radio">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="jak_private" value="0"<?php if (JAK_TICKET_PRIVATE == 0) echo ' checked';?>>
          <span class="form-check-sign"></span>
            <?php echo $jkl["g18"];?>
          </label>
      </div>

      <label><?php echo $jkl["hd304"];?></label>
            <div class="form-check form-check-radio">
              <label class="form-check-label">
                  <input class="form-check-input" type="radio" name="jak_ticket_similar" value="1"<?php if (JAK_TICKET_SIMILAR == 1) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_ticket_similar" value="0"<?php if (JAK_TICKET_SIMILAR == 0) echo ' checked';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

		</div>
		<div class="card-footer">
			<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
		</div>
		</div>

	</div>
	<div class="col-md-6">
	
		<div class="card">
		<div class="card-header">
		  <h3 class="card-title"><i class="fa fa-ticket-alt"></i> <?php echo $jkl["hd105"];?></h3>
		</div><!-- /.box-header -->
		<div class="card-body">

			<div class="form-group">
	      <p><label><?php echo $jkl["hd99"];?></label></p>
				<select name="jak_ticket_limit" class="selectpicker" title="<?php echo $jkl["hd99"];?>" data-size="4">
					<option value="0"<?php if (JAK_TICKET_LIMIT == 0) echo ' selected';?>><?php echo $jkl["hd100"];?></option>
					<?php for ($i = 1; $i < 31; $i++) {
		  				if ($i == JAK_TICKET_LIMIT) {
		  					echo '<option value="'.$i.'" selected>'.$i.'</option>';
		  				} else {
		  					echo '<option value="'.$i.'">'.$i.'</option>';
		  				}
		  			} ?>
				</select>
			</div>

			<div class="form-group">
	      <p><label><?php echo $jkl["hd101"];?></label></p>
				<select name="jak_ticket_reminder" class="selectpicker" title="<?php echo $jkl["hd101"];?>" data-size="4">
					<option value="0"<?php if (JAK_TICKET_REMINDER == 0) echo ' selected';?>><?php echo $jkl["g304"];?></option>
					<?php for ($i = 1; $i < 31; $i++) {
		  				if ($i == JAK_TICKET_REMINDER) {
		  					echo '<option value="'.$i.'" selected>'.$i.'</option>';
		  				} else {
		  					echo '<option value="'.$i.'">'.$i.'</option>';
		  				}
		  			} ?>
				</select>
			</div>

			<div class="form-group">
	      <p><label><?php echo $jkl["hd102"];?></label></p>
				<select name="jak_ticket_close" class="selectpicker" title="<?php echo $jkl["hd102"];?>" data-size="4">
					<option value="0"<?php if (JAK_TICKET_CLOSE_C == 0) echo ' selected';?>><?php echo $jkl["g304"];?></option>
					<?php for ($i = 1; $i < 31; $i++) {
		  				if ($i == JAK_TICKET_CLOSE_C) {
		  					echo '<option value="'.$i.'" selected>'.$i.'</option>';
		  				} else {
		  					echo '<option value="'.$i.'">'.$i.'</option>';
		  				}
		  			} ?>
				</select>
			</div>

			<div class="form-group">
	      <p><label><?php echo $jkl["hd103"];?></label></p>
				<select name="jak_ticket_reopen" class="selectpicker" title="<?php echo $jkl["hd103"];?>" data-size="4">
					<option value="0"<?php if (JAK_TICKET_REOPEN == 0) echo ' selected';?>><?php echo $jkl["g304"];?></option>
					<?php for ($i = 1; $i < 31; $i++) {
		  				if ($i == JAK_TICKET_REOPEN) {
		  					echo '<option value="'.$i.'" selected>'.$i.'</option>';
		  				} else {
		  					echo '<option value="'.$i.'">'.$i.'</option>';
		  				}
		  			} ?>
				</select>
			</div>

			<div class="form-group">
	            <p><label><?php echo $jkl["hd104"];?></label></p>
				<select name="jak_ticket_attach" class="selectpicker" title="<?php echo $jkl["hd104"];?>" data-size="4">
					<option disabled><?php echo $jkl["hd104"];?></option>
					<option value="0"<?php if (JAK_TICKET_ATTACH == 0) echo ' selected';?>><?php echo $jkl["hd100"];?></option>
					<?php for ($i = 1; $i < 31; $i++) {
		  				if ($i == JAK_TICKET_ATTACH) {
		  					echo '<option value="'.$i.'" selected>'.$i.'</option>';
		  				} else {
		  					echo '<option value="'.$i.'">'.$i.'</option>';
		  				}
		  			} ?>
				</select>
			</div>

		</div>
		<div class="card-footer">
			<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
		</div>
		</div>

    <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa fa-calendar-alt"></i> <?php echo $jkl["hd297"];?></h3>
    </div><!-- /.box-header -->
    <div class="card-body">

      <label><?php echo $jkl["hd292"];?></label>
      <div class="form-check form-check-radio">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="jak_ticket_duedate" value="1"<?php if (JAK_TICKET_DUEDATE == 1) echo ' checked';?>>
          <span class="form-check-sign"></span>
            <?php echo $jkl["g19"];?>
        </label>
      </div>
      <div class="form-check form-check-radio">
        <label class="form-check-label">
        <input class="form-check-input" type="radio" name="jak_ticket_duedate" value="0"<?php if (JAK_TICKET_DUEDATE == 0) echo ' checked';?>>
        <span class="form-check-sign"></span>
          <?php echo $jkl["g18"];?>
        </label>
      </div>

      <div class="form-group">
        <p><label><?php echo $jkl["hd293"];?></label></p>
        <select name="jak_ticket_duedate_format" class="selectpicker" title="<?php echo $jkl["hd293"];?>">
          <option value="F d, Y:#:LL"<?php if (JAK_TICKET_DUEDATE_FORMAT == "F d, Y:#:LL") echo ' selected';?>><?php echo date("F d, Y");?></option>
          <option value="d.m.Y:#:D.M.YYYY"<?php if (JAK_TICKET_DUEDATE_FORMAT == "d.m.Y:#:D.M.YYYY") echo ' selected';?>><?php echo date("d.m.Y");?></option>
          <option value="m-d-Y:#:M-D-YYYY"<?php if (JAK_TICKET_DUEDATE_FORMAT == "m-d-Y:#:M-D-YYYY") echo ' selected';?>><?php echo date("m-d-Y");?></option>
          <option value="m/d/Y:#:M/D/YYYY"<?php if (JAK_TICKET_DUEDATE_FORMAT == "m/d/Y:#:M/D/YYYY") echo ' selected';?>><?php echo date("m/d/Y");?></option>
        </select>
      </div>

      <div class="form-group">
        <p><label><?php echo $jkl["hd294"];?></label></p>
        <select name="jak_ticket_duedate_preset" class="selectpicker" title="<?php echo $jkl["hd294"];?>">
          <option value="0"<?php if (JAK_TICKET_DUEDATE_PRESET == 0) echo ' selected';?>><?php echo $jkl["hd295"];?></option>
          <option value="1"<?php if (JAK_TICKET_DUEDATE_PRESET == 1) echo ' selected';?>><?php echo $jkl["hd296"];?></option>
          <option value="2"<?php if (JAK_TICKET_DUEDATE_PRESET == 2) echo ' selected';?>><?php echo (sprintf($jkl['hd64'], 2));?></option>
          <option value="3"<?php if (JAK_TICKET_DUEDATE_PRESET == 3) echo ' selected';?>><?php echo (sprintf($jkl['hd64'], 3));?></option>
        </select>
      </div>

    </div>
    <div class="card-footer">
      <button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
    </div>
    </div>
		
	</div>
</div>

</form>

</div>

<?php include_once 'footer.php';?>