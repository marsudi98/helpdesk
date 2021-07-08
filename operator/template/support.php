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
                  <i class="fa fa-ticket-alt"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAll;?>
                <h6 class="stats-title"><?php echo $jkl["hd135"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["hd135"];?>
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
                  <i class="fa fa-ticket-alt"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAllOT;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd136"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["hd136"];?>
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
                <h3 class="info-title"><?php echo $totalAllWT;?></h3>
                <h6 class="stats-title"><?php echo 'On Process Tickets'; ?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo 'On Process Tickets';?>
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
                  <i class="fa fa-ticket-alt"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAllCT;?></h3>
                <h6 class="stats-title"><?php echo $jkl["hd138"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["hd138"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<div class="row">
  <div class="col-md-6 my-auto">
    <p>
      <a class="btn btn-primary" href="<?php echo JAK_rewrite::jakParseurl('support', 'new');?>"><?php echo $jkl["hd166"];?></a>
      <btn type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModalCenter">Import Ticket</btn>
      <!-- Modal -->
      <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Import Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- <a class="btn btn-sm btn-info" style="font-size:11px;" href="<?php echo BASE_URL_HOME.'files/standard/ticketimport_template.xls'; ?>">Download Template</a></br> -->
                <button class="btn btn-sm btn-info" style="font-size:11px;" onclick="download_template()">Download Template</button>
                <form id="form_import" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data">
                  <input type="file" id="file_import" name="file_import" accept=".xls,.xlsx">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary float-right" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Import</button>
              </div>
              <input type="hidden" name="action" value="import_excel">
              </form>
          </div>
        </div>
      </div>
    </p>
  </div>
  <div style="display:none;">
    <p>
    <?php if (isset($dep_filter) && is_array($dep_filter) && !empty($dep_filter)) { ?>
    <form id="jak_statform" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
      <select name="jak_depid" id="jak_depid" class="selectpicker">
        <option value="0"><?php echo $jkl['g105'];?></option>
        <?php foreach ($dep_filter as $v) { ?>
        <option value="<?php echo $v["id"];?>"<?php if (isset($page1) && $page1 == $v["id"]) echo ' selected';?>><?php echo $v["title"];?></option>
        <?php } ?>
      </select>
      <input type="hidden" name="action" value="depid">
    </form>
    <?php } ?>
    </p>
  </div>
  <div class="col-md-6 my-auto d-flex flex-row-reverse">
      <button class="btn btn-success" href="<?= BASE_URL_ORIG ?>" id="export_table">Export Data</button>
  </div>
</div>
<div class="row" style="margin-left: 0;">
  <div class="col-md-3 my-auto pl-0 pr-1">
    <label for="" >From : </label>
    <form id="start_date_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" class="mb-2">
      <input class="form-control datepicker" type="text" name="jak_start_datefilter" id="jak_start_datefilter" style="background-color: white;" autocomplete="off">
      <input type="hidden" name="action" value="start_date_filter">
    </form>
  </div>
  <div class="col-md-3 my-auto pl-1">
    <label for="" >To : </label>
    <form id="end_date_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" class="mb-2">
      <input class="form-control datepicker" type="text" name="jak_end_datefilter" id="jak_end_datefilter" style="background-color: white;" autocomplete="off">
      <input type="hidden" name="action" value="end_date_filter">
    </form>
  </div>
  <div class="col-md-4 my-auto px-1">
    <label for="" >Jenis Complaint Filter : </label>
    <form id="cat_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" class="mb-2">
      <select name="jak_catfilter" id="jak_catfilter" class="selectpicker">
        <option value="0"><?php echo $jkl['g105'];?> Jenis Complaint</option>
        <?php foreach ($PRIORITY_ALL as $p) {
          echo "<option value='".$p["id"]."' ".($_SESSION["jak_catfilter"] == $p["id"] ? ' selected' : '').">".$p["title"]."</option>";
        } ?>
        <!-- <option value="1" <?php //if ($_SESSION["jak_statfilter"] == 1) echo ' selected'; ?>>Open</option> -->
      </select>
      <input type="hidden" name="action" value="cat_filter">
    </form>
  </div>
  <div class="col-md-2 my-auto pl-1">
    <label for="" >Status Filter : </label>
    <form id="stat_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" class="mb-2">
      <select name="jak_statfilter" id="jak_statfilter" class="selectpicker">
        <option value="0"><?php echo $jkl['g105'];?> Status</option>
        <option value="1" <?php if ($_SESSION["jak_statfilter"] == 1) echo ' selected'; ?>>Open</option>
        <option value="2" <?php if ($_SESSION["jak_statfilter"] == 2) echo ' selected'; ?>>On Process</option>
        <option value="3" <?php if ($_SESSION["jak_statfilter"] == 3) echo ' selected'; ?>>Close</option>
        <option value="4" <?php if ($_SESSION["jak_statfilter"] == 4) echo ' selected'; ?>>Closed</option>
      </select>
      <input type="hidden" name="action" value="stat_filter">
    </form>
  </div>
</div>


<?php if (isset($totalAll) && $totalAll != 0) { ?>

<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI'];?>">
<div class="card">
<div class="card-body">


<?php if (true) { ?>
<p class="pull-right">
<button class="btn btn-info btn-sm btn-confirm" data-action="status1" data-title="<?php echo addslashes($jkl["hd25"]);?>" data-text="<?php echo addslashes($jkl["hd173"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><?php echo $jkl['hd169'];?></button> <button class="btn btn-warning btn-sm btn-confirm" data-action="status2" data-title="<?php echo addslashes($jkl["hd25"]);?>" data-text="<?php echo addslashes($jkl["hd173"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><?php echo $jkl['hd170'];?></button> <button class="btn btn-success btn-sm btn-confirm" data-action="status3" data-title="<?php echo addslashes($jkl["hd25"]);?>" data-text="<?php echo addslashes($jkl["hd173"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><?php echo $jkl['hd171'];?></button> <button class="btn btn-danger btn-sm btn-confirm" data-action="delete" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e30"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></button>
</p>
<div class="clearfix"></div>
<?php } ?>

<table id="dynamic-data" class="table table-hover" cellspacing="0" width="100%">
<thead>
  <tr>
    <th style="width: 4%">#</th>  
    <th style="width: 3%"><input type="checkbox" id="jak_delete_all"></th>
    <th >Ticket Title <i class="fal fa-ticket-alt" title="<?php echo $jkl["g16"];?>"></i></th>
    <th >AWB <i class="fal fa-ticket-alt" title="<?php echo 'AWB'?>;"></i></th>
    <th >Jenis Complaint <i class="fal fa-building" title="<?php echo $jkl["g131"];?>"></i></th>
    <th >User <i class="fal fa-user" title="<?php echo $jkl["hd77"];?>"></i></th>
    <th style="text-align: center;">Status <i class="fal fa-toggle-on" title="<?php echo $jkl["hd167"];?>"></i></th>
    <th >Created Date <i class="fal fa-clock" title="<?php echo $jkl["g174"];?>"></i></th>
    <th>Due Date <i class="fal fa-clock" title="<?php echo $jkl["g174"];?>"></i></th>
    <th style="text-align: center;">Status Action <i class="fal fa-toggle-on" title="<?php echo $jkl["hd167"];?>"></i></th>
  </tr>
</thead>
<tfoot>
  <tr>
    <th style="width: 4%">#</th>
    <th style="width: 3%"><input type="checkbox" id="jak_delete_all"></th>
    <th>Ticket Title <i class="fal fa-ticket-alt" title="<?php echo $jkl["g16"];?>"></i></th>
    <th >AWB <i class="fal fa-ticket-alt" title="<?php echo 'AWB'?>;"></i></th>
    <th>Jenis Complaint <i class="fal fa-building" title="<?php echo $jkl["g131"];?>"></i></th>
    <th>User <i class="fal fa-user" title="<?php echo $jkl["hd77"];?>"></i></th>
    <th style="text-align: center;">Status <i class="fal fa-toggle-on" title="<?php echo $jkl["hd167"];?>"></i></th>
    <th>Created Date <i class="fal fa-clock" title="<?php echo $jkl["g174"];?>"></i></th>
    <th>Due Date <i class="fal fa-clock" title="<?php echo $jkl["g174"];?>"></i></th>
    <th style="text-align: center;">Status Action <i class="fal fa-toggle-on" title="<?php echo $jkl["hd167"];?>"></i></th>
  </tr>
</tfoot>
<tbody>
</tbody>
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