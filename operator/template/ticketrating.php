<div class="modal-header">
	<h4 class="modal-title"><?php echo $jkl["stat_s12"];?></h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">
    <div class="box box-primary">
    	<div class="box-header">
    		<h3 class="box-title"><?php echo $rowt["subject"];?></h3>
    	</div><!-- /.box-header -->
    	<div class="box-body">
			<div class="padded-box">
				<div class="row">
					<div class="col-md-6">
						<h6><?php echo $jkl['u'];?></h6>
						<?php echo $row['name'];?>
					</div>
					<div class="col-md-6">
						<h6><?php echo $jkl['u1'];?></h6>
						<?php echo $row['email'];?>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<h6><?php echo $jkl['g85'];?></h6>
						<p><?php echo $row['vote'];?>/5</p>
					</div>
					<div class="col-md-6">
						<h6><?php echo $jkl['stat_s12'];?></h6>
						<p><?php echo $row['comment'];?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $jkl["g180"];?></button>
</div>