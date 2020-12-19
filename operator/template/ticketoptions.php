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
                  <i class="fa fa-ballot-check"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo count($TOPTIONS_ALL);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s66"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-ballot-check"></i> <?php echo $jkl["stat_s66"];?>
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
                <div class="icon icon-info icon-circle">
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

<p>
<ul class="nav nav-pills nav-pills-primary">
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('customfield');?>"><?php echo $jkl["hd149"];?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('customfield', 'options');?>"><?php echo $jkl["hd224"];?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('customfield', 'form');?>"><?php echo $jkl["hd148"];?></a>
  </li>
</ul>
</p>

<div class="row">
<div class="col-md-4">

	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fal fa-ballot-check"></i> <?php echo $jkl["hd221"];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php if (isset($errors["e"])) echo $errors["e"];?></div>
				<?php } ?>
				
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					
					<div class="form-group">
					    <label for="title"><?php echo $jkl["g16"];?></label>
						<input type="text" name="jak_title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["jak_title"])) echo $_REQUEST["jak_title"];?>">
					</div>

					<div class="form-group">
						<label for="jak_icon"><?php echo $jkl["hd223"];?></label>
						<div class="input-group">
							<input type="text" name="jak_icon" class="form-control" value="<?php if (isset($_REQUEST["jak_icon"]) && !empty($_REQUEST["jak_icon"])) echo $_REQUEST["jak_icon"];?>">
							<div class="input-group-append">
								<span class="input-group-text">
									<a href="http://fontawesome.com/icons/" target="_blank"><i class="fa fa-link"></i></a>
								</span>
							</div>
						</div>
					</div>

					<div class="form-group">
					    <label for="faq"><?php echo $jkl["hd56"];?></label>
						<input type="number" min="0" name="credits" class="form-control" value="<?php if (isset($_REQUEST["credits"])) { echo $_REQUEST["credits"]; } else { echo "0";}?>">
					</div>
					
					<div class="form-group">
					    <p><label for="jak_depid"><?php echo $jkl["g131"];?></label></p>
					    <select name="jak_depid" id="jak_depid" class="selectpicker" data-size="4" data-live-search="true">
					    	<option value="0"<?php if (isset($_REQUEST["jak_depid"]) && $_REQUEST["jak_depid"] == 0) echo ' selected="selected"';?>><?php echo $jkl["g105"];?></option>
					    <?php if (isset($JAK_DEPARTMENTS) && is_array($JAK_DEPARTMENTS)) foreach($JAK_DEPARTMENTS as $z) { ?>
					    	<option value="<?php echo $z["id"];?>"<?php if (isset($_REQUEST["jak_depid"]) && $z["id"] == $_REQUEST["jak_depid"]) echo ' selected';?>><?php echo $z["title"];?></option>
					    <?php } ?>
					    </select>
					</div>

					<label><?php echo $jkl["hd150"];?></label>
					<div class="form-check form-check-radio">
						<label class="form-check-label">
							<input class="form-check-input" type="radio" name="jak_oponly" value="1"<?php if (isset($_REQUEST["jak_oponly"]) && $_REQUEST["jak_oponly"] == 1) echo " checked";?>>
							<span class="form-check-sign"></span>
							<?php echo $jkl["g19"];?>
						</label>
					</div>
					<div class="form-check form-check-radio">
						<label class="form-check-label">
							<input class="form-check-input" type="radio" name="jak_oponly" value="0"<?php if (isset($_REQUEST["jak_oponly"]) && $_REQUEST["jak_oponly"] == 0) echo " checked";?>>
							<span class="form-check-sign"></span>
							<?php echo $jkl["g18"];?>
						</label>
					</div>

				</div>
					
				<div class="card-footer">
					<button type="submit" name="insert_priority" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
				</div>

			</form>
		
	</div>
</div>
<div class="col-md-8">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-ballot-check"></i> <?php echo $jkl["hd224"];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">

	  		<?php if (isset($TOPTIONS_ALL) && is_array($TOPTIONS_ALL) && !empty($TOPTIONS_ALL)) { ?>
			<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th><?php echo $jkl["g16"];?></th>
						<th><?php echo $jkl["hd223"];?></th>
						<th><?php echo $jkl["g102"];?></th>
						<th><?php echo $jkl["g47"];?></th>
						<th><?php echo $jkl["g48"];?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th><?php echo $jkl["g16"];?></th>
						<th><?php echo $jkl["hd223"];?></th>
						<th><?php echo $jkl["g102"];?></th>
						<th><?php echo $jkl["g47"];?></th>
						<th><?php echo $jkl["g48"];?></th>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach($TOPTIONS_ALL as $v) { ?>
					<tr>
					<td><?php echo $v["id"];?></td>
					<td class="title"><a href="<?php echo JAK_rewrite::jakParseurl('customfield', 'options', 'edit', $v["id"]);?>"><?php echo $v["title"];?></a></td>
					<td class="desc"><i class="fa <?php echo $v["icon"];?>"></i></td>
					<td class="desc"><?php echo $v["dorder"];?></td>
					<td><a class="btn btn-default btn-sm" href="<?php echo JAK_rewrite::jakParseurl('customfield', 'options', 'edit', $v["id"]);?>"><i class="fa fa-pencil"></i></a></td>
					<td><a class="btn btn-danger btn-sm btn-confirm" href="<?php echo JAK_rewrite::jakParseurl('customfield', 'options', 'delete', $v["id"]);?>" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e31"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</div>

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