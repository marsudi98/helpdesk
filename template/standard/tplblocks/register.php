<?php 

// Check if no user is logged.
if (!JAK_USERISLOGGED && JAK_REGISTER) {

// Get the custom fields
$custom_fields = jak_get_custom_fields(false, 1, false, $BT_LANGUAGE, false, false, false, true);

?>


<div class="content-login-block">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-md-6 col-sm-10">

        <div class="login-wrapper">

          <div class="register-title">
          	<h2><?php echo $jkl['hd24'];?></h2>
          </div>

          <div class="form-signin">

          <form method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>">
            <div class="form-group">
              <input type="text" name="name" class="form-control" id="name" placeholder="<?php echo $jkl["g4"];?>">
            </div>
            <div class="form-group">
              <input type="text" name="email" class="form-control" id="email" placeholder="<?php echo $jkl["g5"];?>">
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

      </div>
    </div>
  </div>
</div>
<?php } else { ?>

<?php } ?>