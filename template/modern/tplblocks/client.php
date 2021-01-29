<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<div class="container">
<div class="row">
	<div class="col-md-6 col-sm-10 mb-3">

		<?php if (isset($_SESSION["webembed"])) { ?>
			<p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL);?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i></a></p>
		<?php } ?>

		<div class="login-wrapper">

			<div class="login-title">
				<h2><?php echo $jkl['hd18'];?></h2>
			</div>

			<div class="form-signin">

				<div class="loginF">
					<?php if (isset($ErrLogin)) { ?>
						<?php if (defined('JAK_API_PROFILE') && !empty(JAK_API_PROFILE)) { ?>
							<p><a href="<?php echo JAK_API_PROFILE;?>" class="btn btn-block btn-danger"><i class="fa fa-key"></i> <?php echo $jkl["hd19"];?></a></p>
						<?php } else { ?>
							<p><a href="javascript:void(0)" class="btn-link lost-pwd"><i class="fa fa-key"></i> <?php echo $jkl["hd19"];?></a></p>
						<?php } } ?>
						<?php if (isset($errorsl)) { ?><div class="alert alert-danger"><?php echo $errorsl["recaptcha"];?></div><?php } ?>
						<form id="login_form" method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>">
							<div class="form-group<?php if (isset($ErrLogin)) echo "";?>">
								<label for="email" class="bmd-label-floating"><?php echo $jkl["g5"];?></label>
								<input type="text" name="email" class="form-control" id="email">
							</div>
							<div class="form-group<?php if (isset($ErrLogin)) echo "";?>">
								<label for="password" class="bmd-label-floating"><?php echo $jkl["g77"];?></label>
								<input type="password" name="password" class="form-control" id="password">
							</div>

							<?php if (!empty(JAK_RECAP_CLIENT)) { ?>
								<p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
							<?php } ?>

							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="lcookies" checked>
									<span class="form-check-sign">
										<span class="check"></span>
									</span>
									<?php echo $jkl["hd20"];?>
								</label>
							</div>

							<input type="hidden" name="action" value="login">
							<button type="submit" name="logID" class="btn btn-rose btn-round"><?php echo $jkl["hd18"];?></button>

						</form>

					</div>

					<div class="forgotP">
						<h4><?php echo $jkl["hd21"];?></h4>
						<?php if (isset($errorfp)) { ?><div class="alert alert-danger"><?php echo $errorfp["e"];?></div><?php } ?>
						<form role="form" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>" method="post">
							<div class="form-group<?php if (isset($errorfp)) echo " has-danger";?>">
								<label for="emailr" class="bmd-label-floating"><?php echo $jkl["g5"];?></label>
								<input type="text" name="lsE" class="form-control" id="emailr">
							</div>

							<?php if (!empty(JAK_RECAP_CLIENT)) { ?>
								<p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
							<?php } ?>

							<button type="submit" name="forgotP" class="btn btn-rose btn-round"><?php echo $jkl["hd22"];?></button>
							<input type="hidden" name="action" value="forgot-password">
						</form>
						<!-- <hr> -->
						<p><a href="javascript:void(0)" class="btn btn-default btn-round lost-pwd"><i class="fa fa-lightbulb-o"></i> <?php echo $jkl["hd23"];?></a></p>
					</div>
					
				</div>

			</div>

		</div>
		<div class="col-md-6 col-sm-10">
			<?php if (JAK_REGISTER) { ?>
				<div class="login-wrapper">

					<div class="register-title">
						<h2><?php echo $jkl['hd24'];?></h2>
					</div>
					
					<div class="form-signin">
						<?php if ($errorsreg) { ?>
							<div class="alert alert-danger">
								<?php if (isset($errorsreg) && !empty($errorsreg)) foreach ($errorsreg as $e) { echo $e; }?>
							</div>
						<?php } ?>
						<form method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>">
							<div class="form-group<?php if (isset($errorsreg["e"])) echo " has-danger";?>">
								<label for="name" class="bmd-label-floating"><?php echo $jkl["g4"];?></label>
								<input type="text" name="name" class="form-control" id="name" value="<?php if (isset($_REQUEST["name"])) echo $_REQUEST["name"];?>">
							</div>
							<div class="form-group<?php if (isset($errorsreg["e1"])) echo " has-danger";?>">
								<label for="emailreg" class="bmd-label-floating"><?php echo $jkl["g5"];?></label>
								<input type="text" name="email" class="form-control" id="emailreg" value="<?php if (isset($_REQUEST["email"])) echo $_REQUEST["email"];?>">
							</div>
							<?php echo $custom_fields;?>

							<?php if (!empty(JAK_RECAP_CLIENT)) { ?>
								<p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
							<?php } ?>
							<p><a href="javascript:void(0)" class="btn-link lost-pwd"><i class="fa fa-key"></i> <?php echo $jkl["hd19"];?></a></p>
							<input type="hidden" name="action" value="register">
							<button type="submit" name="registerID" class="btn btn-rose btn-round"><?php echo $jkl["hd30"];?></button>

						</form>
						
					</div>

				</div>
			<?php } ?>
		</div>
	</div>
</div>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>