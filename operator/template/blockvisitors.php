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
                <div class="icon icon-info icon-circle">
                  <i class="fa fa-building"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo (count($JAK_DEPARTMENTS) + count($JAK_DEP_SUPPORT) + count($JAK_CAT_FAQ));?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s43"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-building"></i> <?php echo $jkl["stat_s43"];?>
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

<?php if ($success) { ?>
<div class="alert alert-success">
	<?php if (isset($success["e"])) echo $success["e"];?>
</div>
<?php } ?>
<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI'];?>">

<div class="card">
	<div class="card-header">
		<h3 class="box-title"><?php echo $jkl["g97"];?></h3>
	</div><!-- /.box-header -->
	<div class="card-body">

		<div class="form-group">
			<label for="twoco"><?php echo $jkl["g95"];?> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h3"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></label>
			<textarea name="ip_block" rows="5" class="form-control"><?php echo JAK_IP_BLOCK;?></textarea>
		</div>

		<div class="form-group">
			<label for="twoco"><?php echo $jkl["g96"];?> <a href="javascript:void(0)" class="jakweb-help" data-content="<?php echo $jkl["h4"];?>" data-original-title="<?php echo $jkl["t"];?>"><i class="fa fa-question-circle"></i></a></label>
			<textarea name="email_block" rows="5" class="form-control"><?php echo JAK_EMAIL_BLOCK;?></textarea>
		</div>

	</div>
	<div class="card-footer">
		<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
	</div>
</div>

</form>

</div>

<?php include_once 'footer.php';?>