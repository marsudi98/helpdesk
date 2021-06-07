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
                  <i class="fa fa-id-badge"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title">-</h3>
                <h6 class="stats-title">-</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-id-badge"></i> -
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
                  <i class="fa fa-history"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title">-</h3>
                <h6 class="stats-title">-</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> -
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
                  <i class="fad fa-mobile"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title">-</h3>
                <h6 class="stats-title">-</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-mobile"></i> -
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
                  <i class="fa fa-file"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo iterator_count($totalFiles);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s45"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-file"></i> <?php echo $jkl["stat_s45"];?>
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

<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
<div class="content">
	<div class="row">
        <div class="col-md-9">
			<div class="card">
				<div class="card-header">
        			<h3 class="card-title"><i class="fa fa-tools"></i> <?php echo $jkl["m19"];?></h3>
        		</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped">
						<tr>
							<td><?php echo $jkl["g182"];?></td>
							<td><button type="submit" name="delCache" class="btn btn-danger"><?php echo $jkl["g48"];?></button></td>
						</tr>
						<tr>
							<td><?php echo $jkl["g313"];?></td>
							<td><button type="submit" name="delTokens" class="btn btn-danger"><?php echo $jkl["g48"];?></button></td>
						</tr>
						<tr>
							<td><?php echo $jkl["hd183"];?></td>
							<td><button type="submit" name="delTFiles" class="btn btn-danger"><?php echo $jkl["g48"];?></button></td>
						</tr>							<tr>
							<td><?php echo $jkl["g185"];?></td>
							<td><button type="submit" name="optimize" class="btn btn-success"><?php echo $jkl["g185"];?></button></td>
						</tr>
						</table>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
</form>

</div>

<?php include_once 'footer.php';?>