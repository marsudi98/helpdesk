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
                  <i class="fa fa-comments"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $sessCtotal;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s25"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["stat_s25"];?>
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
                  <i class="fa fa-comment-lines"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $commCtotal;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s26"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["stat_s26"];?>
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
                <h3 class="info-title"><?php echo $statsCtotal;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s10"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["stat_s10"];?>
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
                  <i class="fa fa-users"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $visitCtotal;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s27"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["stat_s27"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->
  <div class="card">
    <div class="card-body">
      <form class="form-inline" action="#" id="report_form">
        <div class="row">
          <div class="col-12 form-inline">
            <label for="report_date"> Tanggal Report : &nbsp;&nbsp;&nbsp;</label>
            <input class="form-control datepicker" type="text" name="report_date" id="report_date">&nbsp;&nbsp;&nbsp;
            <button type="button" class="btn btn-primary" style="margin-top: 5" onclick="go_to_download_page()">Download Daily Report</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
    <iframe
        id="iframe_metabase"
        frameborder="0"
        width="100%"
        height="500"
        allowtransparency
    ></iframe>
    </div>
  </div>

</div>

<?php include_once 'footer.php';?>
<?php include_once 'js_report.php';?>
