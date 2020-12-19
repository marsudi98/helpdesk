<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.3.3                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2018 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('JAK_PREVENT_ACCESS')) die('No direct access!');

// FAQ is turned off go back to home
if (!JAK_FAQ_A) jak_redirect(BASE_URL);

// Get the important database table
$jaktable2 = 'faq_article';
$jaktable3 = 'faq_categories';

// Reset some vars
$getTotal = 0;

// We edit some client details
if ($page1 == "a" && isset($page2) && is_numeric($page2) && jak_row_exist($page2, $jaktable2)) {

  // Get the data
  $JAK_FORM_DATA = jak_get_data_front($page2, $jaktable2);

  // Get the title
  $titlearray = explode(" ", $JAK_FORM_DATA["title"], 5);

  // Check permissions depend on the login status
  if (JAK_CLIENTID) {
    if ($jakclient->getVar("faq_cat") == 0) {

        // Categories
        $allcategories = $jakdb->select($jaktable3, ["id", "title"], ["active" => 1]);

        // Similar
        $similarart = $jakdb->select($jaktable2, ["id", "title"], ["AND" => ["id[!]" => $page2, "active" => 1, "content[~]" => $titlearray], "LIMIT" => 5]);

        // Page Navigation
        $nextp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[>]" => $page2, "active" => 1], "ORDER" => ["id" => "ASC"]]);
        $prevp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[<]" => $page2, "active" => 1], "ORDER" => ["id" => "DESC"]]);

    } else {

      if (!$jakdb->has($jaktable3, ["OR" => ["guesta" => 1, "id" => [$jakclient->getVar("faq_cat")]]])) jak_redirect(JAK_rewrite::jakParseurl(JAK_FAQ_URL));

        // Categories
        $allcategories = $jakdb->select($jaktable3, ["id", "title"], ["AND" => ["id" => [$jakclient->getVar("faq_cat")], "active" => 1]]);

        // Similar
        $similarart = $jakdb->select($jaktable2, ["id", "title"], ["AND" => ["id[!]" => $page2, "catid" => [$jakclient->getVar("faq_cat")], "active" => 1, "content[~]" => $titlearray], "LIMIT" => 5]);

        // Page Navigation
        $nextp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[>]" => $page2, "catid" => [$jakclient->getVar("faq_cat")], "active" => 1], "ORDER" => ["id" => "ASC"]]);
        $prevp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[<]" => $page2, "catid" => [$jakclient->getVar("faq_cat")], "active" => 1], "ORDER" => ["id" => "DESC"]]);
    }
  } elseif (JAK_USERID) {
    if ($jakuser->getVar("faq_cat") == 0 || JAK_SUPERADMINACCESS) {

      // Categories
      $allcategories = $jakdb->select($jaktable3, ["id", "title"], ["active" => 1]);

      // Similar
      $similarart = $jakdb->select($jaktable2, ["id", "title"], ["AND" => ["id[!]" => $page2, "active" => 1, "content[~]" => $titlearray], "LIMIT" => 5]);

      // Page Navigation
      $nextp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[>]" => $page2, "active" => 1], "ORDER" => ["id" => "ASC"]]);
      $prevp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[<]" => $page2, "active" => 1], "ORDER" => ["id" => "DESC"]]);

    } else {
      if (!$jakdb->has($jaktable3, ["OR" => ["guesta" => 1, "id" => [$jakuser->getVar("faq_cat")]]])) jak_redirect(JAK_rewrite::jakParseurl(JAK_FAQ_URL));

        // Categories
        $allcategories = $jakdb->select($jaktable3, ["id", "title"], ["AND" => ["catid" => [$jakuser->getVar("faq_cat")], "active" => 1]]);

        // Similar
        $similarart = $jakdb->select($jaktable2, ["id", "title"], ["AND" => ["id[!]" => $page2, "catid" => [$jakuser->getVar("faq_cat")], "active" => 1, "content[~]" => $titlearray], "LIMIT" => 5]);

        // Page Navigation
        $nextp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[>]" => $page2, "catid" => [$jakuser->getVar("faq_cat")], "active" => 1], "ORDER" => ["id" => "ASC"]]);
        $prevp = $jakdb->get($jaktable2, ["id", "title"], ["AND" => ["id[<]" => $page2, "catid" => [$jakuser->getVar("faq_cat")], "active" => 1], "ORDER" => ["id" => "DESC"]]);
    }
  } else {

    if (!$jakdb->has($jaktable3, ["guesta" => 1])) jak_redirect(JAK_rewrite::jakParseurl(JAK_FAQ_URL));

      // Categories
      $allcategories = $jakdb->select($jaktable3, ["id", "title"], ["AND" => ["guesta" => 1, "active" => 1]]);

      // Similar
      $similarart = $jakdb->select($jaktable2, ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.title"], ["AND" => ["id[!]" => $page2, "faq_categories.guesta" => 1, "faq_article.active" => 1, "faq_article.content[~]" => $titlearray], "LIMIT" => 5]);

      // Page Navigation
      $nextp = $jakdb->get("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.title"], ["AND" => ["faq_categories.guesta" => 1, "faq_article.id[>]" => $page2, "faq_article.active" => 1], "ORDER" => ["faq_article.id" => "ASC"]]);
      $prevp = $jakdb->get("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.title"], ["AND" => ["faq_categories.guesta" => 1, "faq_article.id[<]" => $page2, "faq_article.active" => 1], "ORDER" => ["faq_article.id" => "DESC"]]);
  }

  // Page Nav
  $JAK_NAV_NEXT = $JAK_NAV_NEXT_TITLE = $JAK_NAV_PREV = $JAK_NAV_PREV_TITLE = "";
  if (isset($nextp) && !empty($nextp)) {
    $JAK_NAV_NEXT = JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $nextp['id'], JAK_rewrite::jakCleanurl($nextp['title']));
    $JAK_NAV_NEXT_TITLE = $nextp['title'];
  }
  if (isset($prevp) && !empty($prevp)) {
    $JAK_NAV_PREV = JAK_rewrite::jakParseurl(JAK_FAQ_URL, 'a', $prevp['id'], JAK_rewrite::jakCleanurl($prevp['title']));
    $JAK_NAV_PREV_TITLE = $prevp['title'];
  }

  // finally get the category
  if (isset($HD_FAQ_CATEGORIES) && !empty($HD_FAQ_CATEGORIES)) foreach ($HD_FAQ_CATEGORIES as $ct) {
    if ($ct["id"] == $JAK_FORM_DATA["catid"]) $incat = $ct["title"];
  }

  // Include the javascript file for results
  $js_file_footer = 'js_faqart.php';

  // Load the template
  include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/faqart.php';

// We need only the categories
} elseif ($page1 == "c" && isset($page2) && is_numeric($page2) && jak_row_exist($page2, $jaktable3)) {

  // include the class
  include_once(APP_PATH.'class/class.paginator.php');

  // Client Access
  if (JAK_CLIENTID) {
    // All access
    if ($jakclient->getVar("faq_cat") == 0) {

      // Get the total
      $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["faq_article.catid" => $page2, "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1]]);

      if ($getTotal != 0) {
        
        // Paginator
        $pages = new JAK_Paginator;
        $pages->items_total = $getTotal;
        $pages->mid_range = 10;
        $pages->items_per_page = 10;
        $pages->jak_get_page = $page1;
        $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
        $pages->paginate();
        $JAK_PAGINATE = $pages->display_pages();

        // Get the result
        $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.catid" => $page2, "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
          ]);

      }

    // Only for certain categories
    } else {

      // Get the total
      $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["faq_article.catid" => $page2, "faq_article.lang" => $BT_LANGUAGE, "faq_article.catid" => [$jakclient->getVar("faq_cat")], "faq_article.active" => 1]]);

      if ($getTotal != 0) {
        
        // Paginator
        $pages = new JAK_Paginator;
        $pages->items_total = $getTotal;
        $pages->mid_range = 10;
        $pages->items_per_page = 10;
        $pages->jak_get_page = $page1;
        $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
        $pages->paginate();
        $JAK_PAGINATE = $pages->display_pages();

        // Get the result
        $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.catid" => $page2, "faq_article.lang" => $BT_LANGUAGE, "faq_article.catid" => [$jakclient->getVar("faq_cat")], "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
          ]);

      }

    }
  // Can see all active articles
  } elseif (JAK_USERID) {

    // Get the total
    $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["faq_article.catid" => $page2, "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1]]);

    if ($getTotal != 0) {
        
      // Paginator
      $pages = new JAK_Paginator;
      $pages->items_total = $getTotal;
      $pages->mid_range = 10;
      $pages->items_per_page = 10;
      $pages->jak_get_page = $page1;
      $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
      $pages->paginate();
      $JAK_PAGINATE = $pages->display_pages();

      // Get the result
      $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.catid" => $page2, "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
        ]);

    }
  // Can see categories for guests
  } else {

    // Get the total
    $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["faq_article.catid" => $page2, "faq_categories.guesta" => 1, "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1]]);

    if ($getTotal != 0) {
        
      // Paginator
      $pages = new JAK_Paginator;
      $pages->items_total = $getTotal;
      $pages->mid_range = 10;
      $pages->items_per_page = 10;
      $pages->jak_get_page = $page1;
      $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
      $pages->paginate();
      $JAK_PAGINATE = $pages->display_pages();

      // Get the result
      $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.catid" => $page2, "faq_categories.guesta" => 1, "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
        ]);

    }
  }

  $nav = array();
  if (isset($allfaq) && !empty($allfaq)) foreach ($allfaq as $val) {
      $nav[$val["idcat"]] = array("idcat" => $val["idcat"], "titlecat" => $val["titlecat"]);
  }

  // Load the template
  include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/faq.php';

} else {

  // include the class
  include_once(APP_PATH.'class/class.paginator.php');

  // Client Access
  if (JAK_CLIENTID) {
    // All access
    if ($jakclient->getVar("faq_cat") == 0) {

      // Get the total
      $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1]]);

      if ($getTotal != 0) {
        
        // Paginator
        $pages = new JAK_Paginator;
        $pages->items_total = $getTotal;
        $pages->mid_range = 10;
        $pages->items_per_page = 10;
        $pages->jak_get_page = $page1;
        $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
        $pages->paginate();
        $JAK_PAGINATE = $pages->display_pages();

        // Get the result
        $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
          ]);

      }

    // Only for certain categories
    } else {

      // Get the total
      $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.catid" => [$jakclient->getVar("faq_cat")], "faq_article.active" => 1]]);

      if ($getTotal != 0) {
        
        // Paginator
        $pages = new JAK_Paginator;
        $pages->items_total = $getTotal;
        $pages->mid_range = 10;
        $pages->items_per_page = 10;
        $pages->jak_get_page = $page1;
        $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
        $pages->paginate();
        $JAK_PAGINATE = $pages->display_pages();

        // Get the result
        $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.catid" => [$jakclient->getVar("faq_cat")], "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
          ]);

      }

    }
  // Can see all active articles
  } elseif (JAK_USERID) {

    // Get the total
    $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1]]);

    if ($getTotal != 0) {
        
      // Paginator
      $pages = new JAK_Paginator;
      $pages->items_total = $getTotal;
      $pages->mid_range = 10;
      $pages->items_per_page = 10;
      $pages->jak_get_page = $page1;
      $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
      $pages->paginate();
      $JAK_PAGINATE = $pages->display_pages();

      // Get the result
      $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
        ]);

    }
  // Can see categories for guests
  } else {

    // Get the total
    $getTotal = $jakdb->count("faq_article", ["[>]faq_categories" => ["catid" => "id"]], "faq_article.id", ["AND" => ["OR" => ["faq_categories.guesta" => 1, "faq_article.catid" => 0], "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1]]);

    if ($getTotal != 0) {
        
      // Paginator
      $pages = new JAK_Paginator;
      $pages->items_total = $getTotal;
      $pages->mid_range = 10;
      $pages->items_per_page = 10;
      $pages->jak_get_page = $page1;
      $pages->jak_where = JAK_rewrite::jakParseurl(JAK_FAQ_URL);
      $pages->paginate();
      $JAK_PAGINATE = $pages->display_pages();

      // Get the result
      $allfaq = $jakdb->select("faq_article", ["[>]faq_categories" => ["catid" => "id"]], ["faq_article.id", "faq_article.lang", "faq_article.title", "faq_article.content", "faq_categories.class", "faq_categories.id(idcat)", "faq_categories.title(titlecat)"], ["AND" => ["OR" => ["faq_categories.guesta" => 1, "faq_article.catid" => 0], "faq_article.lang" => $BT_LANGUAGE, "faq_article.active" => 1],
          "ORDER" => ["faq_article.dorder" => "DESC"],
          "LIMIT" => $pages->limit
        ]);

    }
  }

  $nav = array();
  if (isset($allfaq) && !empty($allfaq)) foreach ($allfaq as $val) {
      $nav[$val["idcat"]] = array("idcat" => $val["idcat"], "titlecat" => $val["titlecat"]);
  }

  // Load the template
  include_once APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/faq.php';
}
?>