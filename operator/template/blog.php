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
                  <i class="fa fa-file"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAll;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd23"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-file"></i> <?php echo $jkl["hd23"];?>
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
                  <i class="fal fa-comments"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAllC;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd37"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-comments"></i> <?php echo $jkl["hd37"];?>
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

<p><a class="btn btn-primary" href="<?php echo JAK_rewrite::jakParseurl('blog', 'new');?>"><?php echo $jkl["hd30"];?></a></p>

<?php if (isset($totalAll) && $totalAll != 0) { ?>

<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="card">
<div class="card-body">


<?php if (JAK_SUPERADMINACCESS) { ?>
<p class="pull-right">
<button class="btn btn-secondary btn-sm btn-confirm" data-action="status" data-title="<?php echo addslashes($jkl["hd25"]);?>" data-text="<?php echo addslashes($jkl["hd26"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-check"></i> / <i class="fa fa-times"></i></button> <button class="btn btn-danger btn-sm btn-confirm" data-action="delete" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e30"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></button>
</p>
<div class="clearfix"></div>
<?php } ?>

<table id="dynamic-data" class="table table-striped" cellspacing="0" width="100%">
<thead>
  <tr>
    <th style="width: 3%">#</th>
    <th style="width: 3%"><input type="checkbox" id="jak_delete_all"></th>
    <th><?php echo $jkl["g16"];?></th>
    <th><?php echo $jkl["g22"];?></th>
    <th><?php echo $jkl["g321"];?></th>
    <th><?php echo $jkl["g102"];?></th>
    <th><?php echo $jkl["g101"];?></th>
    <th><?php echo $jkl["g13"];?></th>
  </tr>
</thead>
<tfoot>
  <tr>
    <th style="width: 3%">#</th>
    <th style="width: 3%"></th>
    <th><?php echo $jkl["g16"];?></th>
    <th><?php echo $jkl["g22"];?></th>
    <th><?php echo $jkl["g321"];?></th>
    <th><?php echo $jkl["g102"];?></th>
    <th><?php echo $jkl["g101"];?></th>
    <th><?php echo $jkl["g13"];?></th>
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