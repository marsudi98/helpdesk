<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<?php if (JAK_BLOG_A && isset($jak_blogs) && is_array($jak_blogs)){?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
        if ($t["cmsid"] == 11) {
          echo '<div data-editable data-name="title-11">'.$t["title"].'</div>';
          echo '<div data-editable data-name="text-11">'.$t["description"].'</div>';
        }
      } ?>
    </div>
  </div>
</div>

<div class="card-columns">
  <?php foreach($jak_blogs as $bl) {
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
            <a href="<?php echo $blogparseurl;?>" class="text-primary"><?php echo $jkl["hd1"];?></a>
          </div>
          <div class="stats ml-auto">
            <i class="material-icons">schedule</i> <?php echo JAK_base::jakTimesince($bl['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php if ($JAK_PAGINATE) echo $JAK_PAGINATE;?> 
      </div>
    </div>
  </div>
<?php } else { ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-info">
          <?php echo $jkl['hd17'];?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>