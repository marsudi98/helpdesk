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
                  <i class="fa fa-users"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo $totalAll;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd58"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-users"></i> <?php echo $jkl["hd58"];?>
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
                  <i class="fa fa-money-check-alt"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAllI;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd59"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-money-check-alt"></i> <?php echo $jkl["hd59"];?>
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
                  <i class="fad fa-box"></i>
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
          <i class="fa fa-box"></i> <?php echo $jkl["stat_s56"];?>
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
                <h3 class="info-title"><?php echo ($lastPaid ? JAK_base::jakTimesince($lastPaid, JAK_DATEFORMAT, "") : "-");?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s55"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-clock"></i> <?php echo $jkl["stat_s55"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<p>
<ul class="nav nav-pills nav-pills-primary">
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('billing');?>"><?php echo $jkl["hd57"];?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('billing', 'packages');?>"><?php echo $jkl["hd60"];?></a>
  </li>
</ul>
</p>

<?php if (isset($totalAll) && $totalAll != 0) { ?>

<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="card">
<div class="card-body">


<?php if (JAK_SUPERADMINACCESS) { ?>
<p class="pull-right">
<button class="btn btn-danger btn-sm btn-confirm" data-action="delete" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["hd81"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></button>
</p>
<div class="clearfix"></div>
<?php } ?>

<table id="dynamic-data" class="table table-striped" cellspacing="0" width="100%">
<thead>
  <tr>
    <th style="width: 4%">#</th>
    <th style="width: 3%"><input type="checkbox" id="jak_delete_all"></th>
    <th><?php echo $jkl["hd77"];?></th>
    <th><?php echo $jkl["hd71"];?></th>
    <th><?php echo $jkl["hd72"];?></th>
    <th><?php echo $jkl["hd78"];?></th>
    <th><?php echo $jkl["hd79"];?></th>
    <th><?php echo $jkl["hd80"];?></th>
  </tr>
</thead>
<tfoot>
  <tr>
    <th style="width: 4%">#</th>
    <th style="width: 3%"></th>
    <th><?php echo $jkl["hd77"];?></th>
    <th><?php echo $jkl["hd71"];?></th>
    <th><?php echo $jkl["hd72"];?></th>
    <th><?php echo $jkl["hd78"];?></th>
    <th><?php echo $jkl["hd79"];?></th>
    <th><?php echo $jkl["hd80"];?></th>
  </tr>
</tfoot>
<tbody></tbody>
</table>

</div>
</div>
<input type="hidden" name="action" id="action">
</form>

<?php } else { ?>

<div class="alert alert-info">
<?php echo $jkl['i3'];?>
</div>

<?php } ?>

</div>
		
<?php include_once 'footer.php';?>