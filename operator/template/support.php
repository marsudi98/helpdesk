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
                <h3 class="info-title"><?php echo $totalAll;?></h3>
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
                <div class="icon icon-success icon-circle">
                  <i class="fa fa-ticket-alt"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalAllWT;?></h3>
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
  <div class="col-md-3">
    <div class="card card-stats">
      <div class="card-body">
        <div class="statistics statistics-horizontal">
          <div class="info info-horizontal">
            <div class="row">
              <div class="col-5">
                <div class="icon icon-warning icon-circle">
                  <i class="fa fa-star"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo ($total_voted ? round($total_vote / $total_voted, 1).'/5' : '-');?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s7"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["stat_s7"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<div class="row">
  <div class="col-md-8">
    <p>
      <a class="btn btn-primary" href="<?php echo JAK_rewrite::jakParseurl('support', 'new');?>"><?php echo $jkl["hd166"];?></a>
      <btn type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModalCenter">Import Ticket</btn>
      <!-- Modal -->
      <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <form id="form_import" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Import Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a style="font-size:11px;" href="<?php echo BASE_URL_HOME.'files/standard/ticketimport_template.xlsx'; ?>">DOWNLOAD TEMPLATE</a></br>
                <input type="file" id="file_import" name="file_import" accept=".xls,.xlsx">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary float-right" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
              <input type="hidden" name="action" value="import_excel">
            </form>
          </div>
        </div>
      </div>
    </p>
  </div>
  <div class="col-md-2" style="visibility:hidden;">
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
  <div class="col-md-2">
    <p>
      <form id="stat_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
        <select name="jak_statfilter" id="jak_statfilter" class="selectpicker">
          <option value="0"><?php echo $jkl['g105'];?> Status</option>
          <option value="1" <?php if ($_SESSION["jak_statfilter"] == 1) echo ' selected'; ?>>Open</option>
          <option value="2" <?php if ($_SESSION["jak_statfilter"] == 2) echo ' selected'; ?>>Await Reply</option>
          <option value="3" <?php if ($_SESSION["jak_statfilter"] == 3) echo ' selected'; ?>>Close</option>
          <option value="4" <?php if ($_SESSION["jak_statfilter"] == 4) echo ' selected'; ?>>Closed</option>
        </select>
        <input type="hidden" name="action" value="stat_filter">
      </form>
    </p>
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
    <th >Complaint Source <i class="fal fa-building" title="<?php echo $jkl["g131"];?>"></i></th>
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
    <th>Complaint Source <i class="fal fa-building" title="<?php echo $jkl["g131"];?>"></i></th>
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