<?php

// FAQ is turned on in general
if (JAK_FAQ_A) {

// Client Access
if (JAK_CLIENTID) {
// All access
  if ($jakclient->getVar("faq_cat") == 0) {
    $newfaqart = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
      "ORDER" => ["faq_article.dorder" => "DESC"],
      "LIMIT" => JAK_FAQ_HOME
    ]);
// Only for certain categories
  } else {
    $newfaqart = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.catid" => [$jakclient->getVar("faq_cat")], "faq_article.active" => 1],
      "ORDER" => ["faq_article.dorder" => "DESC"],
      "LIMIT" => JAK_FAQ_HOME
    ]);
  }
// Can see all active articles
} elseif (JAK_USERID) {
  $newfaqart = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
    "ORDER" => ["faq_article.dorder" => "DESC"],
    "LIMIT" => JAK_FAQ_HOME
  ]);
// Can see categories for guests
} else {
  $newfaqart = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.title(titlecat)"], ["AND" => ["faq_categories.guesta" => 1, "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
    "ORDER" => ["faq_article.dorder" => "DESC"],
    "LIMIT" => JAK_FAQ_HOME
  ]);
}

}

if (isset($newfaqart) && !empty($newfaqart)){?>

<div class="row">
  <div class="col-md-12">
    <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
      if ($t["cmsid"] == 7) {
        echo '<div data-editable data-name="faqnew-title-7">'.$t["title"].'</div>';
        echo '<div data-editable data-name="faqnew-text-7">'.$t["description"].'</div>';
      }
    }
    ?>
  </div>
</div>

<div class="row">
  <?php foreach($newfaqart as $faqn) {
    $faqparseurl = JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $faqn["id"], JAK_rewrite::jakCleanurl($faqn["title"]));
    ?>
    <div class="col-lg-4 col-md-6">
      <div class="card card-blog">
        <div class="card-header card-header-<?php echo $faqn["class"];?>">
          <div class="row">
            <div class="col-2">
              <span class="fa-stack fa-lg">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-lightbulb-o fa-stack-1x"></i>
              </span>
            </div>
            <div class="col-10">
              <h4 class="mb-0"><a href="<?php echo $faqparseurl;?>"><?php echo $faqn["title"];?></a></h4>
              <p class="text-muted mb-0"><?php echo $faqn["titlecat"];?></p>
            </div>
          </div>
        </div>
        <div class="card-body">
          <p class="card-text"><?php echo jak_cut_text($faqn["content"], 200, '...');?></p>
        </div>
        <div class="card-footer justify-content-center">
          <a href="<?php echo $faqparseurl;?>"><?php echo $jkl["hd1"];?></a>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
<?php } ?>