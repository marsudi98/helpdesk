<div class="modal-header">
	<h3 class="modal-title"><?php echo $jkl["hd275"];?></h3>
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

	<div class="jak-thankyou"></div>

	<form class="jak-ajaxform" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">

	<div class="card">
		<div class="card-header">
			<h4 class="card-title"><?php echo $jkl["hd191"];?></h4>
		</div>
		<div class="card-body">
				
			<div class="form-group">
				<label for="ticketid" class="control-label"><?php echo $jkl["hd191"];?></label>
				<input type="text" name="ticketid" id="ticketid" class="form-control">
			</div>
				
			<button type="submit" class="btn btn-primary ls-submit"><?php echo $jkl["hd276"];?></button>

		</div>
	</div>
	
	<div class="card">
		<div class="card-header">
			<h4 class="card-title"><?php echo $jkl["hd277"];?></h4>
		</div>
		<div class="card-body">
			<?php if (isset($CLI_TICKET) && !empty($CLI_TICKET)) { ?>
			<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<th><?php echo $jkl['hd191'];?></th>
					<th><?php echo $jkl['g221'];?></th>
					<th><?php echo $jkl['hd282'];?></th>
					<th><?php echo $jkl['hd252'];?></th>
				</thead>
				<tfoot>
					<th><?php echo $jkl['hd191'];?></th>
					<th><?php echo $jkl['g221'];?></th>
					<th><?php echo $jkl['hd282'];?></th>
					<th><?php echo $jkl['hd252'];?></th>
				</tfoot>
				<tbody>
				<?php foreach ($CLI_TICKET as $v) { ?>
					<tr>
						<td>#<?php echo $v["id"];?></td>
						<td><a href="javascript:void(0)" data-tid="<?php echo $v["id"];?>" class="jak-ticketmerge"><?php echo $v["subject"];?></a></td>
						<td><?php echo JAK_base::jakTimesince($v["initiated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
						<td><?php echo JAK_base::jakTimesince($v["updated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
					</tr>
				<?php } ?>

				</tbody>
			</table>
			</div>
			<?php } else { ?>
				<div class="alert alert-info"><?php echo $jkl['i3'];?></div>
			<?php } ?>
		</div>
	</div>

	</form>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $jkl["g180"];?></button>
</div>

<script type="text/javascript" src="../js/contact.js"></script>

<script type="text/javascript">

	$(document).ready(function() {
        
		$('.jak-ticketmerge').click(function() {
			var ticketid = $(this).data("tid");
			$('#ticketid').val(ticketid);
			$('.jak-ajaxform').submit();

			$('#jakModal').on('hidden.bs.modal', function() {
				location.reload();
			});

		});

		$('.ls-submit').click(function() {

			$('#jakModal').on('hidden.bs.modal', function() {
				ocation.reload();
			});

		});

	});

	ls.main_url = "<?php echo BASE_URL;?>";
	ls.lsrequest_uri = "<?php echo JAK_PARSE_REQUEST;?>";
	ls.ls_submit = "<?php echo $jkl['g4'];?>";
	ls.ls_submitwait = "<?php echo $jkl['g67'];?>";
</script>