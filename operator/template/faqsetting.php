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
                  <i class="fa fa-question-circle"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalEntries;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd23"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-question-circle"></i> <?php echo $jkl["hd23"];?>
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

<p>
<ul class="nav nav-pills nav-pills-primary">
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings');?>"><?php echo $jkl["hd9"];?></a>
  </li>
  <?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'support');?>"><?php echo $jkl["hd10"];?></a>
  </li>
  <?php } if (jak_get_access("faq", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('settings', 'faq');?>"><?php echo $jkl["hd11"];?></a>
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
    		<h3 class="card-title"><i class="fa fa-cogs"></i> <?php echo $jkl["hd11"];?></h3>
    	</div><!-- /.box-header -->
    	<div class="card-body">

    		<label><?php echo $jkl["hd111"];?></label>
    		<div class="form-check form-check-radio">
               	<label class="form-check-label">
                   	<input class="form-check-input" type="radio" name="jak_faq" value="1"<?php if (JAK_FAQ_A == 1) echo ' checked="checked"';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g19"];?>
                </label>
            </div>
            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="jak_faq" value="0"<?php if (JAK_FAQ_A == 0) echo ' checked="checked"';?>>
                    <span class="form-check-sign"></span>
                    <?php echo $jkl["g18"];?>
                </label>
            </div>

        	<div class="form-group">
            <p><label><?php echo $jkl["hd109"];?></label></p>
        		<select name="jak_faq_home" class="selectpicker" title="<?php echo $jkl["hd109"];?>" data-size="4">
        			<option value="0"<?php if (JAK_FAQ_HOME == 0) echo ' selected';?>><?php echo $jkl["g304"];?></option>
    				  <?php for ($i = 1; $i < 13; $i++) {
    	  				if ($i == JAK_FAQ_HOME) {
    	  					echo '<option value="'.$i.'" selected>'.$i.'</option>';
    	  				} else {
    	  					echo '<option value="'.$i.'">'.$i.'</option>';
    	  				}
    	  			} ?>
        		</select>
    		</div>

    		<div class="form-group">
          <p><label><?php echo $jkl["hd110"];?></label></p>
        	<select name="jak_faq_footer" class="selectpicker" title="<?php echo $jkl["hd110"];?>" data-size="4">
        		<option value="0"<?php if (JAK_FAQ_FOOTER == 0) echo ' selected';?>><?php echo $jkl["g304"];?></option>
    				<?php for ($i = 1; $i < 11; $i++) {
    	  				if ($i == JAK_FAQ_FOOTER) {
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

  </div>
  <div class="col-md-6">

    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-file"></i> <?php echo $jkl["hd273"];?></h3>
      </div><!-- /.box-header -->
      <div class="card-body">

        <div class="form-group">
          <p><label class="control-label" for="jak_faq_page"><?php echo $jkl["hd274"];?></label></p>
          <select name="jak_faq_page" class="selectpicker" data-style="select-with-transition">
          <?php for ($i = 1; $i < 20; $i++) {
            if ($i == JAK_FAQ_PAGE) {
            echo '<option value="'.$i.'" selected>'.$i.'</option>';
            } else {
            echo '<option value="'.$i.'">'.$i.'</option>';
            }
          } ?>
          </select>
        </div>

        <div class="form-group">
          <p><label class="control-label" for="jak_faq_pagination"><?php echo $jkl["hd273"];?></label></p>
          <select name="jak_faq_pagination" class="selectpicker" data-style="select-with-transition">
          <?php for ($i = 5; $i < 20; $i++) {
            if ($i == JAK_FAQ_PAGINATION) {
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

  </div>

</form>

</div>

<?php include_once 'footer.php';?>