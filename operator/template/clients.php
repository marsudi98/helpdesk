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
                  <i class="fa fa-users"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAll;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd118"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-users"></i> <?php echo $jkl["hd118"];?>
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
                  <i class="fa fa-user-clock"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalSeven;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s32"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-user-clock"></i> <?php echo $jkl["stat_s32"];?>
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
                <h3 class="info-title"><?php echo (!empty($totalChat) ? $totalChat : 0);?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd140"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-comments"></i> <?php echo $jkl["hd140"];?>
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
                <h3 class="info-title"><?php echo (!empty($totalSupport) ? $totalSupport : 0);?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd141"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-ticket-alt"></i> <?php echo $jkl["hd141"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<p>
<ul class="nav nav-pills nav-pills-primary">
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('users');?>"><?php echo $jkl["hd5"];?></a>
  </li>
  <?php if (jak_get_access("client", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('users', 'clients');?>"><?php echo $jkl["hd6"];?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('users', 'droppoints');?>"><?php echo 'Droppoints';?></a>
  </li>
  <?php } ?>
</ul>
</p>

<div class="row">
  <div class="col-md-12">
<div class="card">
<div class="card-body">

<?php if ($newop && jak_get_access("client", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
<p><a class="btn btn-primary" href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', 'new');?>"><?php echo $jkl["hd112"];?></a></p>
<?php } ?>

<?php if (isset($totalAll) && $totalAll != 0) { ?>

<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI'];?>">

<div class="toolbar">
<?php if (JAK_SUPERADMINACCESS) { ?>
<div class="pull-right">
<button class="btn btn-secondary btn-sm btn-confirm" data-action="lock" data-title="<?php echo addslashes($jkl["hd25"]);?>" data-text="<?php echo addslashes($jkl["hd26"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-check"></i> / <i class="fa fa-times"></i></button> <button class="btn btn-danger btn-sm btn-confirm" data-action="delete" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e30"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></button>
</div>
<div class="clearfix"></div>
<?php } ?>
</div>

<table id="dynamic-data" class="table table-striped" cellspacing="0" width="100%">
<thead>
  <tr>
    <th>#</th>
    <th><input type="checkbox" id="jak_delete_all"></th>
    <th><?php echo $jkl["u"];?></th>
    <th><?php echo $jkl["u1"];?></th>
    <th><?php echo $jkl["hd74"];?></th>
    <th><?php echo $jkl["hd143"];?></th>
    <th><?php echo $jkl["g101"];?></th>
    <th><?php echo $jkl["g174"];?></th>
  </tr>
</thead>
<tfoot>
  <tr>
    <th></th>
    <th></th>
    <th><?php echo $jkl["u"];?></th>
    <th><?php echo $jkl["u1"];?></th>
    <th><?php echo $jkl["hd74"];?></th>
    <th><?php echo $jkl["hd143"];?></th>
    <th><?php echo $jkl["g101"];?></th>
    <th><?php echo $jkl["g174"];?></th>
  </tr>
</tfoot>
<tbody>
</tbody>
</table>

<input type="hidden" name="action" id="action">
</form>

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