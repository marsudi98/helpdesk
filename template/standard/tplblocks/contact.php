<div class="content-contact">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
			  if ($t["cmsid"] == 6) {
			    echo '<div data-editable data-name="title-6">'.$t["title"].'</div>';
			    echo '<div data-editable data-name="text-6">'.$t["description"].'</div>';
			  }
			} ?>
			<div class="jak-thankyou"></div>
			<form class="jak-ajaxform" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
				<div class="form-row">
			    <div class="col">
			    	<div class="form-group">
    					<label for="gname" class="sr-only"><?php echo $jkl["g4"];?></label>
			      		<input type="text" name="gname" id="gname" class="form-control" placeholder="<?php echo $jkl["g4"];?>">
			      	</div>
			    </div>
			    <div class="col">
			    	<div class="form-group">
					    <label for="gemail" class="sr-only"><?php echo $jkl["g5"];?></label>
					  	<input type="text" name="gemail" id="gemail" class="form-control" placeholder="<?php echo $jkl["g5"];?>">
					</div>
			    </div>
			  	</div>
				<div class="form-group">
					<label for="gphone" class="sr-only"><?php echo $jkl["g49"];?></label>
					<input type="text" name="gphone" id="gphone" class="form-control" placeholder="<?php echo $jkl["g49"];?>">
				</div>
			  <div class="form-group">
			    <label for="gmessage" class="sr-only"><?php echo $jkl["g6"];?></label>
			    <textarea class="form-control" name="gmessage" id="gmessage" rows="5" placeholder="<?php echo $jkl["g6"];?>"></textarea>
			  </div>
			  <?php if (!empty(JAK_DSGVO_CONTACT)) { ?>
			  	<div class="form-check">
					<label class="form-check-label">
					<input class="form-check-input" type="checkbox" name="gdsgvo" id="gdsgvo">
						<span class="form-check-sign">
							<span class="check"></span>
						</span>
						<?php echo JAK_DSGVO_CONTACT;?>
					</label>
				</div>
			  <?php } if (!empty(JAK_RECAP_CLIENT)) { ?>
				<p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
				<?php } ?>
			  <p class="mb-0"><button type="submit" class="btn btn-success btn-green ls-submit"><?php echo $jkl['g7'];?></button></p>
			  <input type="hidden" name="send_email" value="1">
			</form>
			</div>
		</div>
	</div>
</div>