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