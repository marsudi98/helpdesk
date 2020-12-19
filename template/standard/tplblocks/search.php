<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-faq-search">
	<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-md-6 col-sm-10">
				<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
                        if ($t["cmsid"] == 1) {
                            echo '<div data-editable data-name="title-1">'.$t["title"].'</div>';
                            echo '<div data-editable data-name="text-1">'.$t["description"].'</div>';
                        }
                    } ?>
                <?php if ($errors) { ?>
				<div class="alert alert-danger">
				<?php if (isset($errors["e"])) echo $errors["e"];
					  if (isset($errors["e1"])) echo $errors["e1"];?>
				</div>
				<?php } ?>
				<form class="jak_form" method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_SEARCH_URL);?>">
				<div class="input-group">
			      <input type="text" name="smart_search" id="smart_search" class="form-control" placeholder="<?php echo $jkl['hd'];?>">
			      <span class="input-group-btn">
			        <button class="btn btn-success btn-search" type="submit"><i class="fa fa-search"></i></button>
			      </span>
			    </div>
			    <input type="hidden" name="search_now" value="1">
			    </form>
			</div>
		</div>
	</div>
</div>

<div class="content-search-result">
	<div class="container">
		<div class="row">
			<?php if (!empty($SearchInput)) { ?>
			<?php if (isset($searchresult) && !empty($searchresult)) foreach ($searchresult as $s) { ?>
			<div class="col-md-4 col-12">
			<div class="card custom-card-<?php echo $s["class"];?> mb-3">
				  <div class="card-header">
				  	<div class="row">
				        <div class="col-2">
				          <span class="fa-stack fa-lg">
				            <i class="fa fa-circle fa-stack-2x"></i>
				            <i class="fa fa-lightbulb-o fa-stack-1x fa-inverse"></i>
				          </span>
				        </div>
				        <div class="col-10">
				          <h4 class="mb-0"><a href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $s["id"], JAK_rewrite::jakCleanurl($s["title"]));?>"><?php echo $s["title"];?></a></h4>
				          <p class="text-muted mb-0"><?php echo $s["titlecat"];?></p>
				        </div>
				      </div>
				  </div>
				  <div class="card-body">
				    <p class="card-text"><?php echo jak_cut_text($s["content"], 200, '...');?></p>
				  </div>
				  <div class="card-footer">
				  	<a href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $s["id"], JAK_rewrite::jakCleanurl($s["title"]));?>"><?php echo $jkl["hd1"];?><span class="pull-right"><i class="fa fa-arrow-circle-o-right"></i></span></a>
				  </div>
				</div>
			</div>
			<?php } else { ?>
			<div class="col">
				<div class="alert alert-info">
					<?php echo $jkl['hd4'];?>
				</div>
			</div>
			<?php } } else { ?>
			<div class="col">
				<div class="alert alert-info">
					<?php echo $jkl['hd5'];?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>