<?php 

// Check if no user is logged.
if (!JAK_USERISLOGGED && JAK_REGISTER) {

// Get the custom fields
$custom_fields = jak_get_custom_fields(false, 1, false, $BT_LANGUAGE, false, false, false, true);

?>

<div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">

   <div class="card card-login card-hidden">
    <div class="card-header card-header-success text-center">
     <h4 class="card-title"><?php echo $jkl['hd24'];?></h4>
    </div>

   <div class="card-body">

    <form method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>">

      <span class="bmd-form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="material-icons">face</i>
            </span>
          </div>
          <input type="text" name="name" class="form-control" id="name" placeholder="<?php echo $jkl["g4"];?>">
        </div>
      </span>

      <span class="bmd-form-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="material-icons">mail</i>
            </span>
          </div>
          <input type="text" name="email" class="form-control" id="email" placeholder="<?php echo $jkl["g5"];?>">
        </div>
      </span>
      <?php echo $custom_fields;?>

      <?php if (!empty(JAK_RECAP_CLIENT)) { ?>
        <div class="g-recaptcha my-3 ml-3" data-sitekey="<?php echo JAK_RECAP_CLIENT;?>"></div>
      <?php } ?>

      <input type="hidden" name="action" value="register">
    </div>
    <div class="card-footer justify-content-center">
      <button type="submit" name="registerID" class="btn btn-rose btn-round"><?php echo $jkl["hd30"];?></button>
    </div>


  </form>

</div>

</div>
<?php } ?>