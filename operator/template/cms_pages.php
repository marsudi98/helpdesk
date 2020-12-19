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
                  <i class="fa fa-file"></i>
                </div>
              </div>
              <div class="col-7 text-right">
                <h3 class="info-title"><?php echo count($PAGES_ALL);?></h3>
                <h6 class="stats-title"><?php echo $jkl["stat_s54"];?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-footer">
        <div class="stats">
          <i class="fa fa-history"></i> <?php echo $jkl["stat_s54"];?>
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
    <a class="nav-link" href="<?php echo JAK_rewrite::jakParseurl('answers');?>"><?php echo $jkl["m20"];?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl('answers', 'cms');?>"><?php echo $jkl["hd126"];?></a>
  </li>
</ul>
</p>

<div class="row">
<div class="col-md-4">

	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-pencil"></i> <?php echo $jkl["hd128"];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php if (isset($errors["e"])) echo $errors["e"];
					  if (isset($errors["e1"])) echo $errors["e1"];
					  if (isset($errors["e2"])) echo $errors["e2"];?></div>
				<?php } ?>
				
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					
					<div class="form-group">
					    <label for="title"><?php echo $jkl["g16"];?></label>
						<input type="text" maxlength="255" name="title" id="title" class="form-control<?php if (isset($errors["e"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["title"])) echo $_REQUEST["title"];?>">
					</div>

					<div class="form-group">
						<label for="url_slug"><?php echo $jkl["hd130"];?></label>
						<input type="text" name="url_slug" id="url_slug" class="form-control<?php if (isset($errors["e1"])) echo " is-invalid";?>" value="<?php if (isset($_REQUEST["url_slug"])) echo $_REQUEST["url_slug"];?>">
					</div>

					<div class="form-group">
						<p><label for="jak_prepage"><?php echo $jkl["hd212"];?></label></p>
						<select name="jak_prepage" class="selectpicker">
							<option value="0"><?php echo $jkl["hd213"];?></option>
							<option value="<?php echo JAK_CLIENT_URL;?>"<?php if (isset($_REQUEST["jak_prepage"]) && $_REQUEST["jak_prepage"] == JAK_CLIENT_URL) echo ' selected';?>><?php echo JAK_CLIENT_URL;?></option>
							<option value="<?php echo JAK_SEARCH_URL;?>"<?php if (isset($_REQUEST["jak_prepage"]) && $_REQUEST["jak_prepage"] == JAK_SEARCH_URL) echo ' selected';?>><?php echo JAK_SEARCH_URL;?></option>
							<option value="<?php echo JAK_BLOG_URL;?>"<?php if (isset($_REQUEST["jak_prepage"]) && $_REQUEST["jak_prepage"] == JAK_BLOG_URL) echo ' selected';?>><?php echo JAK_BLOG_URL;?></option>
							<option value="<?php echo JAK_FAQ_URL;?>"<?php if (isset($_REQUEST["jak_prepage"]) && $_REQUEST["jak_prepage"] == JAK_FAQ_URL) echo ' selected';?>><?php echo JAK_FAQ_URL;?></option>
							<option value="<?php echo JAK_SUPPORT_URL;?>"<?php if (isset($_REQUEST["jak_prepage"]) && $_REQUEST["jak_prepage"] == JAK_SUPPORT_URL) echo ' selected';?>><?php echo JAK_SUPPORT_URL;?></option>
						</select>
					</div>
					
					<div class="form-group">
						<p><label for="jak_lang"><?php echo $jkl["g22"];?></label></p>
						<select name="jak_lang" class="selectpicker">
						<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if (JAK_LANG == $lf) { ?> selected="selected"<?php } ?>><?php echo ucwords($lf);?></option><?php } ?>
						</select>
					</div>
					
					<div class="form-group">
					    <label for="content"><?php echo $jkl["g321"];?></label>
						<textarea name="content" id="content" class="form-control<?php if (isset($errors["e2"])) echo " is-invalid";?>" rows="5"><?php if (isset($_REQUEST["content"])) echo $_REQUEST["content"];?></textarea>
					</div>
					
					<div class="form-actions">
						<button type="submit" name="insert_page" class="btn btn-primary"><?php echo $jkl["g38"];?></button>
					</div>

				</form>
		</div>
		
	</div>
</div>
<div class="col-md-8">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-file-alt"></i> <?php echo $jkl["hd127"];?></h3>
	  	</div><!-- /.box-header -->
	  	<div class="card-body">
			<div class="table-responsive">
			<table class="table table-striped">
			<thead>
			<tr>
			<th>#</th>
			<th><?php echo $jkl["g16"];?></th>
			<th><?php echo $jkl["hd130"];?></th>
			<th><?php echo $jkl["g22"];?></th>
			<th><?php echo $jkl["g102"];?></th>
			<th><?php echo $jkl["g101"];?></th>
			<th><?php echo $jkl["g47"];?></th>
			<th><?php echo $jkl["g48"];?></th>
			</tr>
			</thead>
			<?php if (isset($PAGES_ALL) && is_array($PAGES_ALL)) foreach($PAGES_ALL as $v) { ?>
			<tr>
			<td><?php echo $v["id"];?></td>
			<td class="title"><a href="<?php echo JAK_rewrite::jakParseurl('answers', 'cms', 'edit', $v["id"]);?>"><?php echo $v["title"];?></a></td>
			<td><?php echo $v["url_slug"];?></td>
			<td><?php echo $v["lang"];?></td>
			<td><?php echo $v["dorder"];?></td>
			<td><a class="btn btn-secondary btn-sm" href="<?php echo JAK_rewrite::jakParseurl('answers', 'cms', 'lock', $v["id"]);?>"><?php echo ($v["active"] == 1 ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>');?></a></td>
			<td><a class="btn btn-secondary btn-sm" href="<?php echo JAK_rewrite::jakParseurl('answers', 'cms', 'edit', $v["id"]);?>"><i class="fa fa-pencil"></i></a></td>
			<td><a class="btn btn-danger btn-sm btn-confirm" href="<?php echo JAK_rewrite::jakParseurl('answers', 'cms', 'delete', $v["id"]);?>" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e31"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></a></td>
			</tr>
			<?php } ?>
			</table>
			</div>

		</div>
	</div>
</div>		
</div>

</div>

<?php include_once 'footer.php';?>