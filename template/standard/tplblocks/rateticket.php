<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-ticket">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2">

				<h3><?php echo $row["subject"];?></h3>

				<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
					<div class="form-group">
						<label class="control-label" for="vote5"><?php echo $jkl["g29"];?></label>
						<div id="star-container">
							<i class="fa fa-star fa-2x star star-checked" id="star-1"></i>
							<i class="fa fa-star fa-2x star star-checked" id="star-2"></i>
							<i class="fa fa-star fa-2x star star-checked" id="star-3"></i>
							<i class="fa fa-star fa-2x star star-checked" id="star-4"></i>
							<i class="fa fa-star fa-2x star star-checked" id="star-5"></i>
						</div>
						<input type="hidden" name="fbvote" id="fbvote" value="5">
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label" for="name"><?php echo $jkl["g4"];?></label>
								<input type="text" name="name" id="name" class="form-control" value="<?php echo $row["name"];?>">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label" for="email"><?php echo $jkl["g5"];?></label>
								<input type="email" name="email" id="email" class="form-control" value="<?php echo $row["email"];?>">
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label" for="feedback"><?php echo $jkl["g24"];?></label>
						<textarea name="message" id="feedback" rows="5" class="form-control"></textarea>
					</div>

					<div class="form-actions">
						<button type="submit" class="btn btn-primary btn-block ls-submit"><?php echo $jkl["g25"];?></button>
					</div>
					<input type="hidden" name="action" value="support_rating">
					<input type="hidden" name="ticketid" value="<?php echo $row["id"];?>">
				</form>
			</div>
		</div>
	</div>
</div>

<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>


