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
    <p><a class="btn btn-primary" href="<?php echo JAK_rewrite::jakParseurl('support', 'new');?>"><?php echo $jkl["hd166"];?></a></p>
  </div>
  <div class="col-md-2">
    <label>Filter</label>
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
  </div>
  <div class="col-md-2">
    <label>Filter</label>
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