<?php include_once APP_PATH.JAK_OPERATOR_LOC.'/template/header.php';?>

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
                <div class="icon icon-success icon-circle">
                  <i class="fad fa-share-square"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo count($PHPIMAP_ALL);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s51"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-share-square"></i> <?php echo $jkl["stat_s51"];?>
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
                  <i class="fal fa-edit"></i>
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
          <i class="fal fa-edit"></i> <?php echo $jkl["stat_s49"];?>
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
                  <i class="fa fa-rabbit-fast"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo $totalEntries;?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s50"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-rabbit-fast"></i> <?php echo $jkl["stat_s50"];?>
        </div>
      </div>
    </div>
  </div><!-- ./col -->
</div><!-- /.row -->

<div class="alert alert-info">
	<p><?php echo $jkl["hd258"];?></p>
	<i><?php echo APP_PATH.'cron/phpimap.php';?></i>
</div>

<p>
<ul class="nav nav-pills nav-pills-primary">
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings');?>"><?php echo $jkl["hd9"];?></a>
  </li>
  <?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'support');?>"><?php echo $jkl["hd10"];?></a>
  </li>
  <?php } if (jak_get_access("faq", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'faq');?>"><?php echo $jkl["hd11"];?></a>
  </li>
  <?php } if (jak_get_access("blog", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'blog');?>"><?php echo $jkl["hd12"];?></a>
  </li>
  <?php } ?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('settings', 'email');?>"><?php echo $jkl["hd119"];?></a>
  </li>
  <?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)){?>
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('settings', 'phpimap');?>"><?php echo $jkl["hd43"];?></a>
  </li>
  <?php } ?>
</ul>
</p>

<div class="row">
<div class="col-md-8">

	<div class="card box-primary">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-mailbox"></i> <?php echo $jkl['hd47'];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
			
			<?php if (isset($PHPIMAP_ALL) && is_array($PHPIMAP_ALL)) { ?>
			<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI'];?>">
			<?php foreach($PHPIMAP_ALL as $v) { ?>
			<table class="table table-striped table-hover table-responsive w-100 d-block d-md-table">
			<thead>
			<tr>
			<th>#</th>
			<th><?php echo $jkl['hd48'];?></th>
			<th><?php echo $jkl['u2'];?></th>
			<th><?php echo $jkl['g47'];?></th>
			<th><?php echo $jkl["g101"];?></th>
			<th><?php echo $jkl["g48"];?></th>
			</tr>
			</thead>
			<tr>
			<td><?php echo $v["id"];?></td>
			<td class="title"><a href="<?php echo JAK_rewrite::jakParseurl('settings', 'phpimap', 'edit', $v["id"]);?>"><?php echo $v["mailbox"];?></a></td>
			<td class="desc"><?php echo $v["username"];?></td>
			<td><a href="<?php echo JAK_rewrite::jakParseurl('settings', 'phpimap', 'edit', $v["id"]);?>" class="btn btn-sm btn-secondary"><i class="fa fa-pencil"></i></a></td>
			<td><a href="<?php echo JAK_rewrite::jakParseurl('settings', 'phpimap', 'lock', $v["id"]);?>" title="<?php if ($v["active"] == 1) { echo $jkl['g101']; } else { echo $jkl['hd54'];}?>" class="btn btn-sm btn-warning"><i class="fa fa-<?php if ($v["active"] == '1') { ?>check<?php } else { ?>times<?php } ?>"></i></a></td>
			<td><a href="<?php echo JAK_rewrite::jakParseurl('settings', 'phpimap', 'delete', $v["id"]);?>" class="btn btn-sm btn-danger" onclick="if(!confirm('<?php echo $jkl['e30'];?>'))return false;"><i class="fa fa-trash"></i></a></td>
			</tr>
			
			</table>
			<?php } ?>
			</form>
			<?php } else { ?>
			<div class="alert alert-info"><?php echo $jkl['i3'];?></div>
			<?php } ?>
			
		</div>
	</div>
</div>
<div class="col-md-4">
	
	<div class="card box-danger">
		<div class="card-header">
			<h3 class="card-title"><i class="fal fa-mailbox"></i> <?php echo $jkl['hd46'];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"].$errors["e2"];?></div>
				<?php } ?>
				
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group">
					    <p><label for="jak_depid"><?php echo $jkl['g131'];?></label></p>
					    <select name="jak_depid" id="jak_depid" class="selectpicker">
					    <?php foreach ($JAK_DEPARTMENTS as $p) { ;?><option value="<?php echo $p["id"];?>"<?php if (isset($_REQUEST['jak_depid']) && $_REQUEST['jak_depid'] == $p["id"]) { ?> selected="selected"<?php } ?>><?php echo $p["title"];?></option><?php } ?>
					    </select>
					</div>
					<div class="form-group">
					    <label for="mailbox"><?php echo $jkl['hd48'];?></label>
						<input type="text" name="mailbox" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["mailbox"])) echo $_REQUEST["mailbox"];?>">
					</div>
					<div class="form-group">
					    <label for="usrphpimap"><?php echo $jkl['u2'];?></label>
						<input type="text" name="usrphpimap" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["usrphpimap"])) echo $_REQUEST["usrphpimap"];?>">
					</div>
					<div class="form-group">
					    <label for="passphpimap"><?php echo $jkl['u4'];?></label>
						<input type="password" name="passphpimap" class="form-control" value="<?php if (isset($_REQUEST["passphpimap"])) echo $_REQUEST["passphpimap"];?>">
					</div>
					<div class="form-group">
					    <label for="encryption"><?php echo $jkl['hd49'];?></label>
						<input type="text" name="encryption" class="form-control" value="<?php if (isset($_REQUEST["encryption"])) echo $_REQUEST["encryption"];?>">
					</div>
					<div class="form-group">
					    <label for="inbox"><?php echo $jkl['hd50'];?></label>
						<input type="text" name="inbox" class="form-control" value="<?php if (isset($_REQUEST["inbox"])) echo $_REQUEST["inbox"];?>">
					</div>
					<div class="form-group">
					    <label for="email"><?php echo $jkl['u1'];?></label>
						<input type="text" name="email" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["email"])) echo $_REQUEST["email"];?>">
					</div>
					
					<div class="form-actions">
					<button type="submit" name="insert_phpimap" class="btn btn-primary btn-block"><?php echo $jkl["g38"];?></button>
					</div>

				</form>
		</div>
		
	</div>
	</div>
			
</div>

</div>

<?php include_once APP_PATH.JAK_OPERATOR_LOC.'/template/footer.php';?>