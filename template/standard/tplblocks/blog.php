<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>

<?php if (JAK_BLOG_A && isset($jak_blogs) && is_array($jak_blogs)){?>

<div class="content-blog-block">
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
<div class="row">
<div class="col-md-9">
<?php foreach($jak_blogs as $bl) {
  $blogparseurl = JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $bl["id"], JAK_rewrite::jakCleanurl($bl["title"]));
?>

<div class="card mb-5">
    <?php if ($bl["previmg"]) { ?><a href="<?php echo $blogparseurl;?>"><img class="card-img-top img-fluid" src="<?php echo BASE_URL.$bl["previmg"];?>" alt="<?php echo $bl["title"];?>"></a><?php } ?>
    <div class="card-body">
        <p class="text-muted"><?php echo JAK_base::jakTimesince($bl['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
        <h4 class="card-title"><a href="<?php echo $blogparseurl;?>"><?php echo $bl["title"];?></a></h4>
        <hr>
        <p class="card-text"><?php echo jak_cut_text($bl["content"], 200, '...');?></p>
        <p class="mb-0"><a href="<?php echo $blogparseurl;?>" class="text-danger"><?php echo $jkl["hd1"];?></a></p>
    </div>
</div>

<?php } ?>
<?php if ($JAK_PAGINATE) echo $JAK_PAGINATE;?> 
</div>
<div class="col-md-3">
  <?php if (isset($jak_comments) && !empty($jak_comments)) { ?>
  <h4><?php echo $jkl['hd64'];?></h4>
  <ul class="list-unstyled new-comments mt-3">
    <?php foreach ($jak_comments as $c) { ?>
      <li>
        <dl class="row">
        <dt class="col-sm-1"><i class="fa fa-chevron-right"></i></dt>
        <dd class="col-sm-10">
         <p class="mb-0"><a href="<?php echo JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $c["id"], JAK_rewrite::jakCleanurl($c["title"]));?>" class="text-danger"><?php echo jak_cut_text($c["message"], 20, '...');?></a></p><p class="text-muted mb-0"><?php echo JAK_base::jakTimesince($c['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
        </dd>
        </dl>
        <hr>
      </li>
    <?php } ?>
  </ul>
  <?php } ?>
</div>
</div>

</div>
</div>
<?php } else { ?>
<div class="content-blog-block">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-info">
        <?php echo $jkl['hd17'];?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>