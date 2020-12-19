<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<?php if (JAK_FAQ_A && isset($allfaq) && is_array($allfaq)){?>

<div class="content-faq-block">
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
  <?php if (isset($nav) && is_array($nav)){?>
  <div class="row mb-3">
    <div class="col-md-12">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#faqDropdown" aria-controls="faqDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <i class="fa fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="faqDropdown">
          <ul class="navbar-nav">
        <?php foreach($nav as $n) { ?>
        <li class="nav-item">
          <a class="nav-link active" href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'c', $n["idcat"], JAK_rewrite::jakCleanurl($n["titlecat"]));?>"><?php echo $n["titlecat"];?></a>
        </li>
        <?php } ?>
        </ul>
      </div>
      </nav>
    </div>
  </div>
  <?php } ?>
  <div class="row"> 
<?php foreach($allfaq as $faq) {
  $faqparseurl = JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $faq["id"], JAK_rewrite::jakCleanurl($faq["title"]));
?>
<div class="col-md-4 mb-3">
  <div class="card custom-card-<?php echo $faq["class"];?>">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-2">
          <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-lightbulb-o fa-stack-1x fa-inverse"></i>
          </span>
        </div>
        <div class="col-sm-10">
          <h4 class="mb-0"><a href="<?php echo $faqparseurl;?>"><?php echo $faq["title"];?></a></h4>
          <p class="text-muted mb-0"><?php echo $faq["titlecat"];?></p>
        </div>
      </div>
    </div>
    <div class="card-body">
      <p class="card-text"><?php echo jak_cut_text($faq["content"], 200, '...');?></p>
    </div>
    <div class="card-footer">
      <a href="<?php echo $faqparseurl;?>"><?php echo $jkl["hd1"];?><span class="pull-right"><i class="fa fa-arrow-circle-o-right"></i></span></a>
    </div>
  </div>
</div>
<?php } ?>
</div>
<div class="row">
  <div class="col-md-12">
    <?php if ($JAK_PAGINATE) echo $JAK_PAGINATE;?>
    <?php if (isset($_SESSION["webembed"]) && $page1 == 'c') { ?>
      <p><a href="<?php echo JAK_rewrite::jakParseurl(JAK_FAQ_URL);?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i></a></p>
    <?php } ?>
  </div>
</div>

</div>
</div>
<?php } include_once APP_PATH.'template/standard/tplblocks/footer.php';?>