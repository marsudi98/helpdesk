<div class="col-md-8 ml-auto mr-auto">

	<div class="text-center">
		<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
			if ($t["cmsid"] == 1) {
				echo '<div data-editable data-name="title-1">'.$t["title"].'</div>';
				echo '<div data-editable data-name="text-1">'.$t["description"].'</div>';
			}
		} ?>
	</div>
	<div class="card card-raised card-form-horizontal">
		<div class="card-body">
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
	<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
		if ($t["cmsid"] == 18) {
			echo '<div data-editable data-name="title-18">'.$t["title"].'</div>';
			echo '<div data-editable data-name="text-18">'.$t["description"].'</div>';
		}
	} ?>
</div>