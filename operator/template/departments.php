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
                  <i class="fa fa-city"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo count($DEPARTMENTS_ALL);?></h3>
                <h6 class="stats-title"><?php echo ($page1 == 'faq' ? $jkl["stat_s61"] : $jkl["stat_s43"]);?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-city"></i> <?php echo ($page1 == 'faq' ? $jkl["stat_s61"] : $jkl["stat_s43"]);?>
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
                  <i class="fal fa-building"></i>
                </div>
              </div>
              <div class="col-9 text-right">
                <h3 class="info-title"><?php echo $busy_department["title"];?></h3>
                <h6 class="stats-title"><?php echo ($page1 == 'faq' ? $jkl["stat_s60"] : $jkl["stat_s59"]);?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-building"></i> <?php echo ($page1 == 'faq' ? $jkl["stat_s60"] : $jkl["stat_s59"]);?>
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
    <a class="nav-link<?php if (!in_array($page1, array("support", "faq"))) echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl('departments');?>"><?php echo $jkl["hd2"];?></a>
  </li>
  <?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link<?php if ($page1 == 'support') echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl('departments', 'support');?>"><?php echo $jkl["hd3"];?></a>
  </li>
  <?php } if (jak_get_access("faq", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link<?php if ($page1 == 'faq') echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl('departments', 'faq');?>"><?php echo $jkl["hd4"];?></a>
  </li>
  <?php } ?>
</ul>
</p>

<div class="row">
<div class="col-md-9">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-city"></i> <?php echo ($page1 == 'faq' ? $jkl["hd20"] : $jkl["g98"]);?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
			
			<?php if (isset($DEPARTMENTS_ALL) && is_array($DEPARTMENTS_ALL)) { ?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th><?php echo $jkl["g16"];?></th>
						<th><?php echo $jkl["g52"];?></th>
						<th><?php echo $jkl["g47"];?></th>
						<th><?php echo $jkl["g101"];?></th>
						<th><?php echo $jkl["g48"];?></th>
						<th><?php echo $jkl["g102"];?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th><?php echo $jkl["g16"];?></th>
						<th><?php echo $jkl["g52"];?></th>
						<th><?php echo $jkl["g47"];?></th>
						<th><?php echo $jkl["g101"];?></th>
						<th><?php echo $jkl["g48"];?></th>
						<th><?php echo $jkl["g102"];?></th>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach($DEPARTMENTS_ALL as $v) { ?>
					<tr>
					<td><?php echo $v["id"];?><input type="hidden" name="real_dep_id[]" value="<?php echo $v["id"];?>" /></td>
					<td class="title"><a href="<?php echo (!in_array($page1, array("support", "faq")) ? JAK_rewrite::jakParseurl('departments', 'edit', $v["id"]) : JAK_rewrite::jakParseurl('departments', $page1, 'edit', $v["id"]));?>"><?php echo $v["title"];?></a></td>
					<td class="desc"><?php echo $v["description"];?></td>
					<td><a class="btn btn-default btn-sm" href="<?php echo (!in_array($page1, array("support", "faq")) ? JAK_rewrite::jakParseurl('departments', 'edit', $v["id"]) : JAK_rewrite::jakParseurl('departments', $page1, 'edit', $v["id"]));?>"><i class="fa fa-pencil"></i></a></td>
					<td><?php if ($v["id"] != 1) { ?><a class="btn btn-default btn-sm" href="<?php echo (!in_array($page1, array("support", "faq")) ? JAK_rewrite::jakParseurl('departments', 'lock', $v["id"]) : JAK_rewrite::jakParseurl('departments', $page1, 'lock', $v["id"]));?>"><i class="fa fa-<?php if ($v["active"] == '1') { ?>check<?php } else { ?>lock<?php } ?>"></i></a><?php } ?></td>
					<td><?php if ($v["id"] != 1) { ?><a class="btn btn-danger btn-sm btn-confirm" href="<?php echo (!in_array($page1, array("support", "faq")) ? JAK_rewrite::jakParseurl('departments', 'delete', $v["id"]) : JAK_rewrite::jakParseurl('departments', $page1, 'delete', $v["id"]));?>" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e30"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></a><?php } ?></td>
					<td><input type="number" name="corder[]" class="corder form-control" min="1" max="100" value="<?php echo $v["dorder"];?>" /></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</div>

     </div> 
			
			<div class="card-footer">
				<button type="submit" name="save" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
			</div>
			
			<?php } ?>
			</form>
		
</div>
			
</div>
<div class="col-md-3">
	<?php if ($newdep) { ?>
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-building"></i> <?php echo ($page1 == 'faq' ? $jkl["hd21"] : $jkl["g99"]);?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php if (isset($errors["e"])) echo $errors["e"];
					  if (isset($errors["e1"])) echo $errors["e1"];?></div>
				<?php } ?>
				
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				
					<div class="form-group">
					    <label for="title"><?php echo $jkl["g16"];?></label>
						<input type="text" name="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["title"])) echo $_REQUEST["title"];?>" />
					</div>
					<div class="form-group">
					    <label for="email"><?php echo $jkl["g68"];?></label>
						<input type="text" name="email" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["email"])) echo $_REQUEST["email"];?>" />
					</div>
					<?php if ($page1 != 'faq') { ?>
					<div class="form-group">
					    <label for="faq"><?php echo $jkl["hd56"];?></label>
						<input type="number" min="0" name="credits" class="form-control" value="<?php if (isset($_REQUEST["credits"])) { echo $_REQUEST["credits"]; } else { echo "0";}?>">
					</div>
					<div class="form-group">
					    <label for="faq"><?php echo $jkl["g278"];?></label>
						<input type="text" name="faq" class="form-control" value="<?php if (isset($_REQUEST["faq"])) echo $_REQUEST["faq"];?>">
					</div>
					<?php } ?>

					<label><?php echo $jkl["hd18"];?></label>
					<div class="form-check form-check-radio">
			        	<label class="form-check-label">
			          		<input class="form-check-input" type="radio" name="jak_guesta" value="0"<?php if (isset($_REQUEST["jak_guesta"]) && $_REQUEST["jak_guesta"] == 1) echo " checked";?>>
			          		<span class="form-check-sign"></span>
			          		<?php echo $jkl["g19"];?>
			       	 	</label>
			      	</div>
				    <div class="form-check form-check-radio">
				        <label class="form-check-label">
				        	<input class="form-check-input" type="radio" name="jak_guesta" value="1"<?php if (isset($_REQUEST["jak_guesta"]) && $_REQUEST["jak_guesta"] == 0) echo " checked";?>>
				          	<span class="form-check-sign"></span>
				          	<?php echo $jkl["g18"];?>
				    	</label>
				    </div>

					<div class="form-group">
					    <label for="description"><?php echo $jkl["g52"];?></label>
						<textarea name="description" class="form-control" rows="3"><?php if (isset($_REQUEST["description"])) echo $_REQUEST["description"];?></textarea>
					</div>

        </div>
					
				<div class="card-footer">
					<button type="submit" name="insert_department" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
				</div>

			</form>
	</div>
	<?php } else { ?>
	<div class="alert alert-danger"><?php echo $jkl['i6'];?></div>
	<?php } ?>
</div>	
</div>

</div>

<?php include_once 'footer.php';?>