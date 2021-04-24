<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<?php if (isset($page1) && isset($page2) && $page1 == "pay") { if ($page2 == "success"){?>
<div class="alert alert-success"><?php echo $jkl['hd113'];?></div>
<?php } else { ?>
<div class="alert alert-danger"><?php echo $jkl['hd114'];?></div>
<?php } } ?>

<?php if (isset($allcsupport) && is_array($allcsupport)){?>
  
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
        if ($t["cmsid"] == 12) {
          echo '<div data-editable data-name="title-12">'.$t["title"].'</div>';
          echo '<div data-editable data-name="text-12">'.$t["description"].'</div>';
        }
      } 
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'n');?>" class="btn btn-primary"><i class="fa fa-ticket-alt"></i> <?php echo $jkl['hd47'];?></a></p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-9">
      <div class="table-responsive">
        <table id="sortable-data" class="table">
          <thead class="table-custom-dark">
            <th><?php echo $jkl['hd7'];?></th>
            <th><?php echo $jkl['hd9'];?></th>
            <th><?php echo $jkl['hd6'];?></th>
            <th><?php echo $jkl['hd10'];?></th>
            <th><?php echo $jkl['hd11'].'/'.$jkl['hd12'];?></th>
            <th><?php echo $jkl['hd13'];?></th>
          </thead>
          <?php foreach($allcsupport as $sup) {
            $supparseurl = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $sup["id"], JAK_rewrite::jakCleanurl($sup["subject"]));
            ?>
            <tr>
              <td><a href="<?php echo $supparseurl;?>" class="btn btn-sm btn-rose"><?php echo $sup["subject"];?></a></td>
              <td><?php echo $sup["titledep"];?></td>
              <td class="text-center"><?php echo $sup["id"];?></td>
              <td><?php echo JAK_base::jakTimesince($sup['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
              <td><?php echo ($sup["status"] == 1 ? '<span class="badge badge-info">'.$jkl['hd14'].'</span>' : ($sup["status"] == 2 ? '<span class="badge badge-warning">'.$jkl['hd15'].'</span>' : '<span class="badge badge-success">'.$jkl['hd16'].'</span>'));?>/<span class="badge badge-<?php echo $sup["class"];?>"><?php echo $sup["titleprio"];?></span></td>
              <td><a href="<?php echo $supparseurl;?>" class="btn btn-sm btn-secondary"><?php echo $jkl['hd13'];?></a></td>
            </tr>
          <?php } ?>
        </table>
      </div>
      <?php if ($JAK_PAGINATE) echo $JAK_PAGINATE;?>

      <?php if (isset($allcsupport) && empty($allcsupport)){?>
        <div class="alert alert-info"><?php echo $jkl['hd17'];?></div>
      <?php } ?>

      <?php if (JAK_BILLING_MODE != 0 && isset($allpackages) && !empty($allpackages)) { ?>
        <div class="row">
          <div class="col-md-12">
            <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
              if ($t["cmsid"] == 14) {
                echo '<div data-editable data-name="title-14">'.$t["title"].'</div>';
                echo '<div data-editable data-name="text-14">'.$t["description"].'</div>';
              }
            } 
            ?>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="card-deck">
              <?php foreach($allpackages as $allp) { ?>
                <div class="card">
                  <?php if (!empty($allp["previmg"])) { ?><img class="card-img-top img-fluid" src="<?php echo BASE_URL.$allp["previmg"];?>" alt="<?php echo $allp["title"];?>"><?php } ?>
                  <div class="card-body">
                    <h4 class="card-title"><?php echo $allp["title"];?></h4>
                    <p class="card-text"><?php echo $allp["content"];?></p>
                    <?php if (JAK_BILLING_MODE == 1) { ?>
                      <p class="card-text"><?php echo sprintf($jkl['hd50'], $allp["credits"]);?></p>
                    <?php } elseif (JAK_BILLING_MODE == 2) { ?>
                      <p class="card-text"><?php echo sprintf($jkl['hd51'], $allp["paidtill"]);?></p>
                    <?php } ?>
                    <p class="card-text"><?php echo sprintf($jkl['hd52'], jak_calc_indiv_price($allp["amount"], $jakclient->getVar("custom_price")).' '.$allp["currency"]);?></p>
                  </div>
                  <div class="card-footer">
                    <?php if (JAK_STRIPE_PUBLISH_KEY) { ?>
                      <a href="javascript:void(0)" class="btn btn-success btn-green stripe" data-package="<?php echo $allp["id"];?>" data-amount="<?php echo jak_calc_indiv_price($allp["amount"], $jakclient->getVar("custom_price"));?>" data-currency="<?php echo $allp["currency"];?>" data-title="<?php echo $allp["title"];?>" data-description="<?php echo $allp["content"];?>"><i class="jak-loadbtn"></i> <i class="fa fa-cc-stripe"></i> <?php echo $jkl['hd48'];?></a>
                    <?php } if (JAK_PAYPAL_EMAIL) { ?>
                      <a href="javascript:void(0)" class="btn btn-success btn-green paypal" data-package="<?php echo $allp["id"];?>" data-amount="<?php echo jak_calc_indiv_price($allp["amount"], $jakclient->getVar("custom_price"));?>" data-currency="<?php echo $allp["currency"];?>" data-title="<?php echo $allp["title"];?>" data-description="<?php echo $allp["content"];?>"><i class="jak-loadbtn"></i> <i class="fa fa-paypal"></i> <?php echo $jkl['hd49'];?></a>
                    <?php } if (JAK_TWOCO) { ?>
                      <a href="javascript:void(0)" class="btn btn-success btn-green twoco" data-package="<?php echo $allp["id"];?>" data-amount="<?php echo jak_calc_indiv_price($allp["amount"], $jakclient->getVar("custom_price"));?>" data-currency="<?php echo $allp["currency"];?>" data-title="<?php echo $allp["title"];?>" data-description="<?php echo $allp["content"];?>"><i class="jak-loadbtn"></i> <i class="fa fa-credit-card-alt"></i> <?php echo $jkl['hd129'];?></a>
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
          <input type="hidden" name="stripeToken" id="stripeToken">
          <input type="hidden" name="stripeEmail" id="stripeEmail">
        </div>

      <?php } ?>

      <?php if (JAK_BILLING_MODE != 0 && isset($last5pay) && !empty($last5pay)) { ?>
        <div class="row">
          <div class="col-md-12">
            <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
              if ($t["cmsid"] == 13) {
                echo '<div data-editable data-name="title-13">'.$t["title"].'</div>';
                echo '<div data-editable data-name="text-13">'.$t["description"].'</div>';
              }
            } 
            ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table class="table table-responsive table-light w-100 d-block d-md-table">
              <thead class="table-custom-green">
                <th><?php echo $jkl['hd38'];?></th>
                <th><?php echo $jkl['hd39'];?></th>
                <th><?php echo $jkl['hd41'];?></th>
                <th><?php echo $jkl['hd40'];?></th>
                <th><?php echo $jkl['hd11'];?></th>
              </thead>
              <?php foreach($last5pay as $l5p) { ?>
                <tr>
                  <td><?php echo $l5p["title"];?></td>
                  <td><?php echo $l5p["amount"].' '.$l5p["currency"];?></td>
                  <td><?php echo $l5p["paidhow"];?></td>
                  <td><?php echo JAK_base::jakTimesince($l5p['paidwhen'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
                  <td><?php echo ($l5p["success"] == 0 ? '<span class="badge badge-warning">'.$jkl['hd43'].'</span>' : '<span class="badge badge-success">'.$jkl['hd42'].'</span>');?></td>
                </tr>
              <?php } ?>
            </table>
          </div>
        </div>

      <?php } ?>
    </div>
    <div class="col-md-3">

      <div class="card card-profile">
        <div class="card-header card-header-image">
          <a href="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL, 'edit');?>">
            <img class="img" src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$jakclient->getVar("picture");?>" alt="profile-picture">
          </a>
        </div>
        <div class="card-body">
          <h4 class="card-title"><?php echo $jakclient->getVar("name");?></h4>
          <?php if (JAK_BILLING_MODE == 1 || JAK_BILLING_MODE == 2) { ?>
            <h6 class="card-category text-gray"><?php echo $jkl['hd46'];?></h6>
            <?php if (JAK_BILLING_MODE == 1) { ?>
              <p class="card-text"><?php echo sprintf($jkl['hd44'], $jakclient->getVar("credits"));?></p>
            <?php } elseif (JAK_BILLING_MODE == 2) { ?>
              <p class="card-text"><?php echo sprintf($jkl['hd45'], JAK_base::jakTimesince($jakclient->getVar("paid_until"), JAK_DATEFORMAT, JAK_TIMEFORMAT));?></p>
            <?php } } ?>
            <p class="card-text"><strong><?php echo $jkl['hd56'];?></strong><br><?php echo $jakclient->getVar("chatrequests");?></p>
            <p class="card-text"><strong><?php echo $jkl['hd57'];?></strong><br><?php echo $jakclient->getVar("supportrequests");?></p>
            <p class="card-text"><strong><?php echo $jkl['g47'];?></strong><br><?php echo $jakclient->getVar("email");?></p>
            <p class="card-text"><strong><?php echo $jkl['hd36'];?></strong><br><?php echo JAK_base::jakTimesince($jakclient->getVar("lastactivity"), JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
            <p class="card-text"><strong><?php echo $jkl['hd37'];?></strong><br><?php echo JAK_base::jakTimesince($jakclient->getVar("time"), JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
          </div>
          <div class="card-footer justify-content-center">
            <a href="<?php echo JAK_rewrite::jakParseurl('logout');?>" class="btn btn-primary"><i class="material-icons">power_settings_news</i> <?php echo $jkl['g26'];?></a>
          </div>
        </div>

      </div>
    </div>

  </div>
<?php } ?>

<div id="paypal_form" class="ishidden"></div>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>