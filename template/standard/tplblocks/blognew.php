<?php

if (JAK_BLOG_A) {
  $newblogarticle = $jakdb->select("blog", ["id", "title", "content", "previmg"], ["AND" => ["lang" => $BT_LANGUAGE, "active" => 1], "ORDER" => ["dorder" => "DESC"], "LIMIT" => JAK_BLOG_HOME]);
}

if (isset($newblogarticle) && !empty($newblogarticle)){?>
<div class="content-blog-block">
<div class="container">
<div class="row">
  <div class="col">
<?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
  if ($t["cmsid"] == 5) {
    echo '<div data-editable data-name="title-5">'.$t["title"].'</div>';
    echo '<div data-editable data-name="text-5">'.$t["description"].'</div>';
  }
} ?>
</div>
</div>
<div class="row">
<div class="col">
<div class="card-deck">
<?php foreach($newblogarticle as $bl) {
  $blogparseurl = JAK_rewrite::jakParseurl(JAK_BLOG_URL, 'a', $bl["id"], JAK_rewrite::jakCleanurl($bl["title"]));
?>

<div class="card">
    <a href="<?php echo $blogparseurl;?>"><img class="card-img-top img-fluid" src="<?php echo BASE_URL.$bl["previmg"];?>" alt="<?php echo $bl["title"];?>"></a>
    <div class="card-body">
        <h4 class="card-title"><a href="<?php echo $blogparseurl;?>"><?php echo $bl["title"];?></a></h4>
        <p class="card-text"><?php echo jak_cut_text($bl["content"], 200, '...');?></p>
        <p class="mb-0"><a href="<?php echo $blogparseurl;?>" class="btn btn-success btn-green"><?php echo $jkl["hd1"];?></a></p>
    </div>
</div>

<?php } ?>
</div>
</div>
</div>

</div>
</div>
<?php } ?>