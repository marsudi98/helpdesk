<?php include_once 'header.php';?>

<div class="content">

  <?php if ($stataccess) { ?>

  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="statistics">
                <div class="info">
                  <div class="icon icon-primary">
                    <i class="fa fa-comment"></i>
                  </div>
                  <h3 class="info-title"><?php echo $chatmsgStat;?></h3>
                  <h6 class="stats-title"><?php echo $jkl["stat_s26"];?></h6>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="statistics">
                <div class="info">
                  <div class="icon icon-success">
                    <i class="fa fa-comment-alt-lines"></i>
                  </div>
                  <h3 class="info-title"><?php echo $contactStat;?></h3>
                  <h6 class="stats-title"><?php echo $jkl['stat_s29'];?></h6>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="statistics">
                <div class="info">
                  <div class="icon icon-danger">
                    <i class="fa fa-ticket-alt"></i>
                  </div>
                  <h3 class="info-title"><?php echo $totalTickets;?></h3>
                  <h6 class="stats-title"><?php echo $jkl['hd141'];?></h6>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="statistics">
                <div class="info">
                  <div class="icon icon-info">
                    <i class="fa fa-users"></i>
                  </div>
                  <h3 class="info-title"><?php echo $clientsStat;?></h3>
                  <h6 class="stats-title"><?php echo $jkl['stat_s5'];?></h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php } ?>

<?php if (JAK_HOLIDAY_MODE != 0) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo $jkl["g303"];?> (<?php echo (JAK_HOLIDAY_MODE == 1 ? $jkl["g1"] : $jkl["g305"]);?>)</div>
<?php } ?>

<?php if (isset($approve_comments) && !empty($approve_comments)) { ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-comments"></i> <?php echo $jkl['hd233'];?></h3>
        <div class="card-body">
          <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <th><?php echo $jkl['g16'];?></th>
              <th><?php echo $jkl['g146'];?></th>
              <th><?php echo $jkl['hd234'];?></th>
              <th><?php echo $jkl['g48'];?></th>
            </thead>
              <tbody>
              <?php foreach ($approve_comments as $c) { ?>
                <tr>
                  <td><a href="<?php echo str_replace("operator/", "", JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $c["blogid"], JAK_rewrite::jakCleanurl($c["title"])));?>"><?php echo $c["title"];?></a></td>
                  <td><?php echo $c["message"];?></td>
                  <td><a class="btn btn-sm btn-success" href="<?php echo JAK_rewrite::jakParseurl('blog', 'comment', 'status', $c["id"]);?>"><i class="fa fa-check"></i></a></td>
                  <td><a class="btn btn-sm btn-danger" href="<?php echo JAK_rewrite::jakParseurl('blog', 'comment', 'delete', $c["id"]);?>"><i class="fa fa-trash"></i></a></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?php } ?>

<div class="row">
  <div class="col-lg-4 col-md-6">
    <div class="card card-chart">
      <div class="card-header">
        <h5 class="card-category"><i class="fa fa-users"></i> <?php echo $jkl['stat_s72'];?></h5>
        <h2 class="card-title"><?php echo $visitorStat;?></h2>
      </div>
      <div class="card-body">
        <div id="worldMap" class="map"></div>
        <?php if (isset($ctlres) && !empty($ctlres)) { ?>
        <div class="table-responsive">
          <table class="table">
            <tbody>
              <?php foreach ($ctlres as $u) { ?>
              <tr>
                <td>
                  <img src="<?php echo BASE_URL;?>img/blank.png" class="flag-big flag-<?php echo $u['countrycode'];?>" alt="<?php echo $u['country'];?>">
                </td>
                <td><?php echo $u["country"];?></td>
                <td class="text-right">
                  <?php echo $u["total_country"];?>
                </td>
                <td class="text-right">
                  <?php echo number_format(($u["total_country"] * 100) / $visitorStat, 2);?>%
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="alert alert-info"><?php echo $jkl['i3'];?></div>
      <?php } ?>
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo JAK_rewrite::jakParseurl('uonline');?>"><i class="fa fa-map-marked-alt"></i> <?php echo $jkl['g122'];?></a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6">
    <div class="card card-chart">
      <div class="card-header">
        <h5 class="card-category"><i class="fa fa-ticket-alt"></i> <?php echo $jkl['stat_s73'];?></h5>
        <h2 class="card-title"><?php echo (!empty($openTickets) ? count($openTickets) : 0);?></h2>
      </div>
      <div class="card-body">

        <?php if (isset($openTickets) && !empty($openTickets)) { ?>
        <div class="table-responsive">
          <table class="table">
            <tbody>
              <?php foreach ($openTickets as $t) { ?>
              <tr>
                <td><a href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $t['id']);?>"><?php echo $t["subject"];?></a></td>
                <td class="text-right">
                  <?php echo JAK_base::jakTimesince($t["updated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="alert alert-info"><?php echo $jkl['stat_s74'];?></div>
      <?php } ?>
        
      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo JAK_rewrite::jakParseurl('support');?>"><i class="fa fa-ticket-alt"></i> <?php echo $jkl['hd'];?></a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6">
    <div class="card card-chart">
      <div class="card-header">
        <h5 class="card-category"><i class="fa fa-comments"></i> <?php echo $jkl['stat_s75'];?></h5>
        <h2 class="card-title"><?php echo count($openChats);?></h2>
      </div>
      <div class="card-body">
        
        <?php if (isset($openChats) && !empty($openChats)) { ?>
        <div class="table-responsive">
          <table class="table">
            <tbody>
              <?php foreach ($openChats as $c) { ?>
              <tr>
                <td><a href="<?php echo JAK_rewrite::jakParseurl('live', $c['id']);?>"><?php echo $c["name"];?></a></td>
                <td class="text-right">
                  <?php echo JAK_base::jakTimesince($c["initiated"], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="alert alert-info"><?php echo $jkl['i5'];?></div>
      <?php } ?>

      </div>
      <div class="card-footer">
        <div class="stats">
          <a href="<?php echo JAK_rewrite::jakParseurl('leads');?>"><i class="fa fa-comments"></i> <?php echo $jkl['m1'];?></a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (isset($gcarray) && !empty($gcarray)) { ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-comments"></i> <?php echo $jkl["m29"];?></h3>
      </div><!-- /.box-header -->
      <div class="card-body">
        <?php foreach ($gcarray as $c) { ?>
          <a class="btn btn-primary btn-sm" href="<?php echo str_replace("operator/", "", JAK_rewrite::jakParseurl('groupchat', $c["id"], $c["lang"]));?>" target="_blank"><i class="fa fa-comments"></i> <?php echo $c["title"];?></a>&nbsp;
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<div class="card card-calendar">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-calendar"></i> <?php echo $jkl['hd237'];?></h3>
    <div class="card-body">
      <p><span class="badge badge-primary" style="background-color:#d649d8;border-color:#d649d8"><?php echo $jkl['m31'];?></span> <span class="badge badge-primary" style="background-color:#49abd8;border-color:#49abd8"><?php echo $jkl['m1'];?></span> <span class="badge badge-dark" style="background-color:#d88c49"><?php echo $jkl['m22'];?></span> <span class="badge badge-dark" style="background-color:#71d849"><?php echo $jkl['hd57'];?></span></p>
      <div id="calendar"></div>
    </div>
  </div>
</div>

<div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa fa-download"></i> Downloads and Support</h3>
      <div class="card-body">
        <p>Often on the road, download our native apps for Android and iOS to easily serve your clients outside your office. Push notifications included, just setup your business hours in <a href="<?php echo JAK_rewrite::jakParseurl('users','edit',JAK_USERID);?>">your operator profile</a>. Your Live Chat URL is: <strong><?php echo rtrim(BASE_URL_ORIG,"/");?></strong></p>
        <p>Get your native push notifications token and user key from our <a href="https://www.jakweb.ch/push">Push Server</a>. Use the same login details from when you have purchased a license with us.</p>
      </div>
  </div>
</div>

<!-- Only for super admin access -->
<?php if (JAK_SUPERADMINACCESS && $jakhs['hostactive']) { ?>

<!-- For hosted solutions to inform customer -->
<div class="card">
    <div class="card-header">
      <h3 class="card-title">Welcome <?php echo $jakuser->getVar("username");?></h3>
    </div>
    <div class="card-body">
      <p>Thank you for using our Live Chat solution.</p>
      <div class="alert alert-info" id="membermsg">Your membership is valid until: <span id="memberdate"><?php echo JAK_base::jakTimesince(JAK_VALIDTILL, JAK_DATEFORMAT, JAK_TIMEFORMAT);?></span></div>
      <h3>Available resources</h3>
      <ul>
      	<li>You use <?php echo $totalop;?> operators from <?php echo $jakhs['operators'];?></li>
      	<li>You use <?php echo $totaldep;?> departments from <?php echo $jakhs['departments'];?></li>
      	<li>You use <?php echo $totalwidg;?> chat widgets from <?php echo $jakhs['chatwidgets'];?></li>
      </ul>

      <?php if (JAK_VALIDTILL != 0 && (JAK_VALIDTILL < time())) { ?>

      <div class="alert alert-danger" id="expiredmsg">Your membership has expired, the chat widget will not appear on your website. Please renew your membership with one of the options below.</div>

      <?php } ?>

      <?php if (JAK_VALIDTILL != 0 && JAK_VALIDTILL < strtotime("+30 day") && (!empty($sett["paypal"]) || !empty($sett["stripepublic"]))) { ?>
      	<!-- Extend membership -->
      	<h2>Extend your access to your custom HelpDesk 3 access</h2>
      	<div class="row">
      		<div class="col-3">
      			<select name="month" id="month" class="form-control">
              <option value="<?php echo $jakhs['pricemonth'];?>">1 Month (<?php echo $jakhs['pricemonth'].' '.$sett["currency"];?>)</option>
              <option value="<?php echo (3*$jakhs['pricemonth']);?>">3 Months (<?php echo (3*$jakhs['pricemonth']).' '.$sett["currency"];?>)</option>
              <option value="<?php echo (6*$sett["mo6month"]);?>">6 Months (<?php echo (6*$jakhs['pricemonth']).' '.$sett["currency"];?>)</option>
              <option value="<?php echo (12*$sett["mo12month"]);?>">12 Months (<?php echo (12*$jakhs['pricemonth']).' '.$sett["currency"];?>)</option>
            </select>
      		</div>
      		<div class="col-3">
            <?php if (!empty($sett["paypal"])) { ?><p><a href="javascript:void(0)" class="btn btn-info btn-block btn-pay" id="paypal"><i class="jak-loadbtn"></i> <i class="fa fa-paypal"></i> <?php echo $jkl['g294'];?></a></p><?php } ?>
          </div>
          <div class="col-3">
            <?php if (!empty($sett["stripepublic"])) { ?><p><a href="javascript:void(0)" class="btn btn-info btn-block btn-pay" id="stripe"><i class="jak-loadbtn"></i> <i class="fa fa-cc-stripe"></i> <?php echo $jkl['g293'];?></a></p><?php } ?>
          </div>
          <div class="col-3">
            <?php if (!empty($sett["twoco"])) { ?><p><a href="javascript:void(0)" class="btn btn-info btn-block btn-pay" id="twoco"><i class="jak-loadbtn"></i> <i class="fa fa-credit-card-alt"></i> <?php echo $jkl['hd255'];?></a></p><?php } ?>
          </div>
      	</div>
      	<input type="hidden" name="stripeToken" id="stripeToken">
      	<input type="hidden" name="stripeEmail" id="stripeEmail">
      	<div id="paypal_form" class="hidden"></div>
  </div>
</div>
<?php } } ?>

</div>

<?php include_once 'footer.php';?>