<?php if (!JAK_USERISLOGGED) { ?>
  <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
   <div class="card card-login card-hidden">
    <div class="card-header card-header-primary text-center">
     <h4 class="card-title" style="color:#fff;"><?php echo $jkl['hd18'];?></h4>
    </div>
   <div class="card-body">
    <form method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>">
      <?php 
        	// if (isset($_SESSION['LCRedirect'])) {
          //   jak_redirect($_SESSION['LCRedirect']);
          // } else {
          //   jak_redirect(BASE_URL);
          // }
          
        
    
      ?>
      <span class="bmd-form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="material-icons">alternate_email</i>
            </span>
          </div>
          <input type="text" name="email" class="form-control" id="email" placeholder="<?php echo $jkl["g5"];?>">
        </div>
      </span>

      <span class="bmd-form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="material-icons">lock_outline</i>
            </span>
          </div>
          <input type="password" name="password" class="form-control" id="password" placeholder="<?php echo $jkl["g77"];?>">
        </div>
      </span>

      <?php if (!empty(JAK_RECAP_CLIENT)) { ?>
        <div class="g-recaptcha my-3 ml-3" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div>
      <?php } ?>

      <input type="hidden" name="action" value="login">
    </div>
    <div class="card-footer justify-content-center" style="padding-bottom:5px;">
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="checkbox" name="lcookies" checked>
          <span class="form-check-sign">
            <span class="check"></span>
          </span>
          <?php echo $jkl["hd20"];?>
        </label>
      </div>
      <button type="submit" name="logID" class="btn btn-rose btn-round"><?php echo $jkl["hd18"];?></button>
    </div>
  </form>
  <p class="text-center register-link">Don't have an account?<a href="<?php echo JAK_rewrite::jakParseurl('register');?>" class="btn-link lost-pwd"> Register Now</a></p>     
</div>
</div>
<?php } ?>