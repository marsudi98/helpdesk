<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
				if ($t["cmsid"] == 20) {
					echo '<div data-editable data-name="title-20">'.$t["title"].'</div>';
					echo '<div data-editable data-name="text-20">'.$t["description"].'</div>';
				}
			} ?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8 ml-auto mr-auto">
			<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
				if ($t["cmsid"] == 21) {
					echo '<div data-editable data-name="title-21">'.$t["title"].'</div>';
					echo '<div data-editable data-name="text-21">'.$t["description"].'</div>';
				}
			} ?>
			<?php if ($errors) { ?>
				<div class="alert alert-danger">
					<?php if (isset($errors["e"])) echo $errors["e"];
					if (isset($errors["e1"])) echo $errors["e1"];?>
				</div>
			<?php } ?>
			<form class="jak_form" method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_SEARCH_URL);?>">
				<div class="row">
					<div class="col-md-10">
						<div class="form-group">
							<input type="text" name="smart_search" id="smart_search" class="form-control" placeholder="<?php echo $jkl['hd'];?>" autocomplete="off">
						</div>
					</div>
					<div class="col-md-2">
						<button class="btn btn-primary btn-search" type="submit"><i class="fa fa-search"></i></button>
					</div>
				</div>
				<input type="hidden" name="search_now" value="1">
			</form>
		</div>
	</div>
</div>

<?php if (!empty($SearchInput)) { if (isset($searchresult) && !empty($searchresult)) { ?>
<div class="card-columns">
	<?php foreach ($searchresult as $s) {
		$faqparseurl = JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $s["id"], JAK_rewrite::jakCleanurl($s["title"]));
		?>
		<div class="card card-blog<?php echo (!empty($s["class"]) && $s["class"] != "white" ? ' bg-'.$s["class"] : '');?>">
			<div class="card-body">
				<a href="<?php echo $faqparseurl;?>">
					<h3 class="card-title"><?php echo $s["title"];?></h3>
				</a>
				<p><?php echo jak_cut_text($s["content"], 200, '...');?></p>
			</div>
			<div class="card-footer justify-content-center">
				<div class="author">
					<a href="<?php echo $faqparseurl;?>"><?php echo $jkl["hd1"];?></a>
				</div>
				<div class="stats ml-auto">
					<i class="material-icons">schedule</i> <?php echo JAK_base::jakTimesince($s['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<?php } else { ?>
<div class="container">
	<div class="col-12">
		<div class="alert alert-info">
			<?php echo $jkl['hd4'];?>
		</div>
	</div>
</div>
<?php } } else { ?>
<div class="container">
	<div class="col-12">
		<div class="alert alert-rose">
			<?php echo $jkl['hd5'];?>
		</div>
	</div>
</div>
<?php } ?>


<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>