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
              <div class="col-3">
                <div class="icon icon-primary icon-circle">
                  <i class="fa fa-code"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo count($CHATWIDGET_ALL);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s58"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-code"></i> <?php echo $jkl["stat_s58"];?>
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
                <div class="icon icon-success icon-circle">
                  <i class="fal fa-language"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo strtoupper(JAK_LANG);?></h3>
                <h6 class="stats-title"><?php echo $jkl["u11"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-language"></i> <?php echo $jkl["u11"];?>
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
    <a class="nav-link<?php if (!in_array($page1, array("support", "faq"))) echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl('widget');?>"><?php echo $jkl["hd15"];?></a>
  </li>
  <?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link<?php if ($page1 == 'support') echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl('widget', 'support');?>"><?php echo $jkl["hd16"];?></a>
  </li>
  <?php } ?>
  <?php if (jak_get_access("faq", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link<?php if ($page1 == 'faq') echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl('widget', 'faq');?>"><?php echo $jkl["hd17"];?></a>
  </li>
  <?php } ?>
</ul>
</p>

<hr>

<p><?php echo $jkl['bw1'];?> <a href="javascript:void(0)" data-clipboard-target="#widget-code" class="btn btn-primary btn-sm clipboard"><i class="fa fa-clipboard"></i></a></p>

<div class="row">
	<div class="col-md-12">
		<div class="card border-success text-center">
		  <div class="card-body">
			<textarea rows="11" class="form-control" id="widget-code" readonly="readonly"><?php echo htmlentities('<!-- helpdesk 3 widget -->
<script type="text/javascript">
	(function(w, d, s, u) {
		w.lcjUrl = u; w.catID = \'\';
		var h = d.getElementsByTagName(s)[0], j = d.createElement(s);
		j.async = true; j.src = \''.BASE_URL_ORIG.'js/jaklcp'.$page1.'.js\';
		h.parentNode.insertBefore(j, h);
	})(window, document, \'script\', \''.BASE_URL_ORIG.'\');
</script>
<div id="jaklcp-'.$page1.'-container"></div>
<!-- end helpdesk 3 widget -->');?></textarea>
			</div>
		</div>
	</div>
</div>

</div>
		
<?php include_once 'footer.php';?>