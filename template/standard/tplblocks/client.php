<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<div class="content-login-block">
  <div class="container">
    <div class="row justify-content-md-center">
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
		<p><a href="javascript:void(0)" class="btn btn-block btn-danger lost-pwd"><i class="fa fa-key"></i> <?php echo $jkl["hd19"];?></a></p>
		<?php } } ?>
		<?php if (isset($errorsl)) { ?><div class="alert alert-danger"><?php echo $errorsl["recaptcha"];?></div><?php } ?>
		<form id="login_form" method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>">
		  <div class="form-group">
		    <input type="text" name="email" class="form-control<?php if (isset($ErrLogin)) echo " is-invalid";?>" id="email" placeholder="<?php echo $jkl["g5"];?>">
		  </div>
		  <div class="form-group">
		    <input type="password" name="password" class="form-control<?php if (isset($ErrLogin)) echo " is-invalid";?>" id="password" placeholder="<?php echo $jkl["g77"];?>">
		  </div>

		  <?php if (!empty(JAK_RECAP_CLIENT)) { ?>
          <p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
          <?php } ?>

		  <input type="hidden" name="action" value="login">
		  <button type="submit" name="logID" class="btn btn-block btn-success"><?php echo $jkl["hd18"];?> <span class="rocket-sprite"></span></button>

		  <div class="form-check">
		    <label class="form-check-label">
		      <input class="form-check-input" type="checkbox" name="lcookies"> <?php echo $jkl["hd20"];?>
		    </label>
		  </div>
		</form>

		</div>

		<div class="forgotP">
		<h4><?php echo $jkl["hd21"];?></h4>
		<?php if (isset($errorfp)) { ?><div class="alert alert-danger"><?php echo $errorfp["e"];?></div><?php } ?>
		<form role="form" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>" method="post">
		<div class="form-group">
			<label for="email" class="sr-only"><?php echo $jkl["l5"];?></label>
		  	<input type="text" name="lsE" class="form-control<?php if (isset($errorfp)) echo " is-invalid";?>" id="email" placeholder="<?php echo $jkl["g5"];?>">
		</div>

		<?php if (!empty(JAK_RECAP_CLIENT)) { ?>
        <p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
        <?php } ?>

		<button type="submit" name="forgotP" class="btn btn-info btn-block"><?php echo $jkl["hd22"];?></button>
		<input type="hidden" name="action" value="forgot-password">
		</form>
		<hr>
		<p><a href="javascript:void(0)" class="btn btn-block btn-warning lost-pwd"><i class="fa fa-lightbulb-o"></i> <?php echo $jkl["hd23"];?></a></p>
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
            <div class="form-group">
              <input type="text" name="name" class="form-control<?php if (isset($errorsreg["e"])) echo " is-invalid";?>" id="name" placeholder="<?php echo $jkl["g4"];?>" value="<?php if (isset($_REQUEST["name"])) echo $_REQUEST["name"];?>">
            </div>
            <div class="form-group">
              <input type="text" name="email" class="form-control<?php if (isset($errorsreg["e1"])) echo " is-invalid";?>" id="email" placeholder="<?php echo $jkl["g5"];?>" value="<?php if (isset($_REQUEST["email"])) echo $_REQUEST["email"];?>">
            </div>
            <?php echo $custom_fields;?>

            <?php if (!empty(JAK_RECAP_CLIENT)) { ?>
            <p><div class="g-recaptcha" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div></p>
            <?php } ?>

            <input type="hidden" name="action" value="register">
            <button type="submit" name="registerID" class="btn btn-block btn-success"><?php echo $jkl["hd30"];?> <span class="rocket-sprite"></span></button>

          </form>
            
          </div>

        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>