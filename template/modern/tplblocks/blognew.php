<?php

if (JAK_BLOG_A) {
$newblogarticle = $jakdb->select("blog", ["id", "title", "content", "previmg", "time"], ["AND" => ["lang" => $BT_LANGUAGE, "active" => 1], "ORDER" => ["dorder" => "DESC"], "LIMIT" => JAK_BLOG_HOME]);
}

if (isset($newblogarticle) && !empty($newblogarticle)){?>
<div class="row">
  <div class="col-12">
    <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
      if ($t["cmsid"] == 5) {
        echo '<div data-editable data-name="title-5">'.$t["title"].'</div>';
        echo '<div data-editable data-name="text-5">'.$t["description"].'</div>';
      }
    } ?>
  </div>
</div>

<div class="card-columns">
  <?php foreach($newblogarticle as $bl) {
    $blogparseurl = JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $bl["id"], JAK_rewrite::jakCleanurl($bl["title"]));
    ?>
    <div class="card card-blog">
      <?php if (!empty($bl["previmg"])) { ?>
        <div class="card-header card-header-image">
          <a href="<?php echo $blogparseurl;?>">
            <img class="card-img-top img-fluid" src="<?php echo BASE_URL.$bl["previmg"];?>" alt="<?php echo $bl["title"];?>">
            <div class="card-title">
              <?php echo $bl["title"];?>
            </div>
          </a>
        </div>
      <?php } ?>
      <div class="card-body">
        <?php if (empty($bl["previmg"])) { ?>
          <a href="<?php echo $blogparseurl;?>">
            <h6 class="card-category text-primary"><?php echo $bl["title"];?></h6>
          </a>
        <?php } ?>

        <p class="card-text"><?php echo jak_cut_text($bl["content"], 200, '...');?></p>
      </div>
      <div class="card-footer justify-content-center">
        <div class="author">
          <a href="<?php echo $blogparseurl;?>" class="btn btn-link btn-primary"><?php echo $jkl["hd1"];?></a>
        </div>
        <div class="stats ml-auto">
          <i class="material-icons">schedule</i> <?php echo JAK_base::jakTimesince($bl['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<?php } ?>