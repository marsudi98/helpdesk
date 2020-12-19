<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<?php if (JAK_FAQ_A && isset($allfaq) && is_array($allfaq)){?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
        if ($t["cmsid"] == 8) {
          echo '<div data-editable data-name="faq-title-8">'.$t["title"].'</div>';
          echo '<div data-editable data-name="faq-text-8">'.$t["description"].'</div>';
        }
      } 
      ?>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-8 ml-auto mr-auto">
      <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
        if ($t["cmsid"] == 21) {
          echo '<div data-editable data-name="title-21">'.$t["title"].'</div>';
          echo '<div data-editable data-name="text-21">'.$t["description"].'</div>';
        }
      } ?>
      <?php if ($errors) { ?>
        <div class="alert alert-danger">
          <?php if (isset($errors["e"])) echo $errors["e"];
          if (isset($errors["e1"])) echo $errors["e1"];?>
        </div>
      <?php } ?>
      <form class="jak_form" method="post" action="<?php echo JAK_rewrite::jakParseurl(JAK_SEARCH_URL);?>">
        <div class="row">
          <div class="col-md-10">
            <div class="form-group">
              <input type="text" name="smart_search" id="smart_search" class="form-control" placeholder="<?php echo $jkl['hd'];?>" autocomplete="off">
            </div>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary btn-search" type="submit"><i class="fa fa-search"></i></button>
          </div>
        </div>
        <input type="hidden" name="search_now" value="1">
      </form>
    </div>
  </div>
</div>

<?php if (isset($nav) && is_array($nav)) { ?>
  <div class="container my-3">
    <div class="row">
      <div class="col-md-8 ml-auto mr-auto text-center">
        <ul class="nav nav-pills nav-pills-primary">
          <li class="nav-item">
            <a class="nav-link<?php if (empty($page1)) echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL);?>"><?php echo $jkl['hd122'];?></a>
          </li>
          <?php foreach($nav as $n) { ?>
            <li class="nav-item">
              <a class="nav-link<?php if ($page2 == $n["idcat"]) echo ' active';?>" href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'c', $n["idcat"], JAK_rewrite::jakCleanurl($n["titlecat"]));?>"><?php echo $n["titlecat"];?></a>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
<?php } ?>

<div class="card-columns">
  <?php foreach($allfaq as $faq) {
    $faqparseurl = JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $faq["id"], JAK_rewrite::jakCleanurl($faq["title"]));
    ?>
    <div class="card<?php echo (!empty($faq["class"]) && $faq["class"] != "white" ? ' bg-'.$faq["class"] : '');?>">
      <div class="card-body">
        <a href="<?php echo $faqparseurl;?>">
          <h3 class="card-title"><?php echo $faq["title"];?></h3>
        </a>
        <p><?php echo jak_cut_text($faq["content"], 200, '...');?></p>
      </div>
      <div class="card-footer justify-content-center">
        <div class="author">
          <a href="<?php echo $faqparseurl;?>"><?php echo $jkl["hd1"];?></a>
        </div>
        <div class="stats ml-auto">
          <i class="material-icons">schedule</i> <?php echo JAK_base::jakTimesince($faq['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
  if ($t["cmsid"] == 19) {
    echo '<div data-editable data-name="title-19">'.$t["title"].'</div>';
    echo '<div data-editable data-name="text-19">'.$t["description"].'</div>';
  }
} ?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php if ($JAK_PAGINATE) echo $JAK_PAGINATE;?>
      <?php if (isset($_SESSION["webembed"]) && $page1 == 'c') { ?>
        <p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL);?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i></a></p>
      <?php } ?>
    </div>
  </div>
</div>
<?php } include_once APP_PATH.'template/modern/tplblocks/footer.php';?>