<?php if (!JAK_USERISLOGGED) { ?>
<div class="content-login-block">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-md-6 col-sm-10">

        <div class="login-wrapper">

          <div class="login-title">
          	<h2><?php echo $jkl['hd18'];?></h2>
          </div>

          <div class="form-signin">

          <div class="loginF">
          <form method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>">
            <div class="form-group">
              <input type="text" name="email" class="form-control" id="email" placeholder="<?php echo $jkl["g5"];?>">
            </div>
            <div class="form-group">
              <input type="password" name="password" class="form-control" id="password" placeholder="<?php echo $jkl["g77"];?>">
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

          </div>

        </div>

      </div>
    </div>
  </div>
</div>
<?php } else { ?>

<?php } ?>