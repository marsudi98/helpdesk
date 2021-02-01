<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.4                   # ||
|| # ----------------------------------------- # ||
|| # Copyright 2017 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Menu builder function, parentId 0 is the root
function jak_build_menu($parent, $menu, $active, $mainclass, $dropdown, $dropclass, $subclass, $admin, $client, $firstli = "", $firsta = "", $from = 0, $to = 0) {
   	$html = '';
   	if (isset($menu['parents'][$parent])) {
   		if (isset($from) && is_numeric($from) && isset($to) && is_numeric($to) && $to != 0) {
   			$mpr = array_slice($menu['parents'][$parent], $from, $to);
   		} else {
   			$mpr = $menu['parents'][$parent];
   		}
      $html .= '<ul class="'.$mainclass.'">';
       foreach ($mpr as $itemId) {
          if (!isset($menu['parents'][$itemId]) && (($menu["items"][$itemId]["access"] == 1 && (!$admin && !$client)) || $menu["items"][$itemId]["access"] == 2) || ($menu["items"][$itemId]["access"] == 3 && ($admin || $client))) {
          	 $html .= '<li'.($firstli ? ' class="'.$firstli.($active == $menu["items"][$itemId]["url_slug"] ? ' active"' : '"') : ($active == $menu["items"][$itemId]["url_slug"] ? ' class="active"' : '')).'><a'.($firsta ? ' class="'.$firsta.'"' : '').' target="'.($menu["items"][$itemId]["url_slug"] == 'mitra-agent' ? '_blank' : '').'" href="'.($menu["items"][$itemId]["url_slug"] == 'mitra-agent' ? 'https://mitraagentjnt.com/' : ( $menu["items"][$itemId]["url_slug"] == 'home' ? 'http://10.20.20.117/jnt_compro/' : ( $menu["items"][$itemId]["url_slug"] ? JAK_rewrite::jakParseurl($menu["items"][$itemId]["url_slug"]) : BASE_URL ))).'">'.$menu["items"][$itemId]["title"].'</a></li>';
          }
          if (isset($menu['parents'][$itemId])) {
             $html .= '<li'.($firstli ? ' class="'.$firstli.($active == $menu["items"][$itemId]["url_slug"] ? ($dropdown ? ' active '.$dropdown.'"' : '"') : ($dropdown ? $dropdown.'"' : '"')) : ($active == $menu["items"][$itemId]["url_slug"] ? ($dropdown ? ' class="active '.$dropdown.'"' : '') : ($dropdown ? ' class="'.$dropdown.'"' : ''))).'><a'.($firsta ? ' class="'.$firsta.'"' : BASE_URL).' href="'.JAK_rewrite::jakParseurl($menu["items"][$itemId]["url_slug"]).'">'.$menu["items"][$itemId]["title"].'</a>';
             $html .= jak_build_menu($itemId, $menu, $active, $dropclass, $subclass, $dropclass, $subclass, $admin);
             $html .= '</li>';
          }
       }

       if ($admin) {
       		$html .= '<li'.($firstli ? ' class="'.$firstli.'"' : '').'><a'.($firsta ? ' class="'.$firsta.'"' : '').' href="'.BASE_URL.'operator/">Operator</a></li>';
       }
       $html .= '</ul>';
   }
   return $html;
}

function produce_replacement($match) {
  $producerName = 'evaluate_'.strtolower($match[1]);
  return function_exists($producerName) ? $producerName() : null;
}

function evaluate_searchblock() {
  global $jkl;
  global $cms_text;
  global $BT_LANGUAGE;
  global $jakclient;
  ob_start(); // Start output buffer capture.
  include(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/searchblock.php'); // Include your template.
  $output = ob_get_contents(); // This contains the output of yourtemplate.php
  // Manipulate $output...
  ob_end_clean(); // Clear the buffer.
  return $output; // Print everything.
}

function evaluate_blognew() {
  global $jkl;
  global $jakdb;
  global $cms_text;
  global $BT_LANGUAGE;
  ob_start(); // Start output buffer capture.
  include(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/blognew.php'); // Include your template.
  $output = ob_get_contents(); // This contains the output of yourtemplate.php
  // Manipulate $output...
  ob_end_clean(); // Clear the buffer.
  return $output; // Print everything.
}

function evaluate_faqnew() {
  global $jkl;
  global $cms_text;
  global $jakdb;
  global $BT_LANGUAGE;
  global $jakclient;
  ob_start(); // Start output buffer capture.
  include(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/faqnew.php'); // Include your template.
  $output = ob_get_contents(); // This contains the output of yourtemplate.php
  // Manipulate $output...
  ob_end_clean(); // Clear the buffer.
  return $output; // Print everything.
}

function evaluate_supportnew() {
  global $jkl;
  global $cms_text;
  global $jakdb;
  global $jakclient;
  global $jakuser;
  ob_start(); // Start output buffer capture.
  include(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/supportnew.php'); // Include your template.
  $output = ob_get_contents(); // This contains the output of yourtemplate.php
  // Manipulate $output...
  ob_end_clean(); // Clear the buffer.
  return $output; // Print everything.
}

function evaluate_contact() {
  global $jkl;
  global $cms_text;
  global $BT_LANGUAGE;
  ob_start(); // Start output buffer capture.
  include(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/contact.php'); // Include your template.
  $output = ob_get_contents(); // This contains the output of yourtemplate.php
  // Manipulate $output...
  ob_end_clean(); // Clear the buffer.
  return $output; // Print everything.
}

function evaluate_login() {
  global $jkl;
  global $cms_text;
  global $BT_LANGUAGE;
  global $jakclient;
  ob_start(); // Start output buffer capture.
  include(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/login.php'); // Include your template.
  $output = ob_get_contents(); // This contains the output of yourtemplate.php
  // Manipulate $output...
  ob_end_clean(); // Clear the buffer.
  return $output; // Print everything.
}

function evaluate_register() {
  global $jkl;
  global $cms_text;
  global $BT_LANGUAGE;
  global $jakclient;
  ob_start(); // Start output buffer capture.
  include(APP_PATH.'template/'.JAK_FRONT_TEMPLATE.'/tplblocks/register.php'); // Include your template.
  $output = ob_get_contents(); // This contains the output of yourtemplate.php
  // Manipulate $output...
  ob_end_clean(); // Clear the buffer.
  return $output; // Print everything.
}

// Get comments votes 
function jak_comment_votes($votes) {
    
    if (isset($votes) && $votes != 0) {
      if ($votes < 0) {
        return 'danger';
      } else {
        return 'success';
      }
    } else {
      return 'default';
    }
}

function jak_build_comments($parent, $comm, $mainclass, $access, $session, $approve, $reply, $report, $status) {

  $html = '';
  if (isset($comm['subcomm'][$parent])) {
      
      $html .= '<ul'.($mainclass ? ' class="'.$mainclass.'"' : "").'>';
      foreach ($comm['subcomm'][$parent] as $comID) {
         if (!isset($comm['subcomm'][$comID])) {
             $html .= '<li><div class="comment-wrapper"><div class="comment-author"><img src="'.$comm["comm"][$comID]["avatar"].'" alt="avatar"> <span class="comment-user">'.$comm["comm"][$comID]["username"].'</span> <span class="comment-date">'.$comm["comm"][$comID]["created"].'</span></div>
                        <div class="com" id="msgid_'.$comm["comm"][$comID]["id"].'">
                            '.$comm["comm"][$comID]["message"].(($comm["comm"][$comID]["approve"] == 0 && !empty($comm["comm"][$comID]["session"]) && $session == $comm["comm"][$comID]["session"]) ? '<div class="alert alert-info">'.$approve.'</div>' : "").'
                        </div>
                        <!-- Comment Controls -->
                        <div class="comment-actions">
                            <a href="javascript:void(0)" data-cvote="up" data-id="'.$comm["comm"][$comID]["id"].'" class="jak-cvote"><i class="fa fa-thumbs-up"></i></a>
                            <a href="javascript:void(0)" data-cvote="down" data-id="'.$comm["comm"][$comID]["id"].'" class="jak-cvote"><i class="fa fa-thumbs-down"></i></a>
                            <!-- Votes -->
                            <span id="jak-cvotec'.$comm["comm"][$comID]["id"].'" class="label label-'.jak_comment_votes($comm["comm"][$comID]["votes"]).'">'.$comm["comm"][$comID]["votes"].'</span>
                            '.($status && !$comm["comm"][$comID]["commentid"] ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-sm btn-primary comment-reply-btn jak-creply"><i class="fa fa-share-alt"></i> '.$reply.'</a>' : '').'
                            '.($access ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" data-msg="'.$comm["comm"][$comID]["message"].'" class="btn btn-secondary btn-sm jak-epost"><i class="fa fa-pencil"></i></a> <a href="'.$comm["comm"][$comID]["parseurl1"].'" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a>' : "").($report && $comm["comm"][$comID]["report"] == 0 ? ' <a href="'.$comm["comm"][$comID]["parseurl2"].'" class="btn btn-sm btn-warning"><i class="fa fa-exclamation-triangle"></i></a>' : "").'
                        </div>
                    </div></li>';
                if (!$comm["comm"][$comID]["commentid"]) {
                    $html .= '<li><ul><li id="insertPost_'.$comm["comm"][$comID]["id"].'"></li></ul></li>';
                }
         }
         if (isset($comm['subcomm'][$comID])) {
            $html .= '<li><div class="comment-wrapper">
                            <div class="comment-author"><img src="'.$comm["comm"][$comID]["avatar"].'" alt="avatar"> <span class="comment-user">'.$comm["comm"][$comID]["username"].'</span> <span class="comment-date">'.$comm["comm"][$comID]["created"].'</span></div>
                            '.(($comm["comm"][$comID]["approve"] == 0 && !empty($comm["comm"][$comID]["session"]) && $session == $comm["comm"][$comID]["session"]) ? '<div class="alert alert-info">'.$approve.'</div>' : "").'
                            <div class="com">
                                '.$comm["comm"][$comID]["message"].'
                            </div>
                            <!-- Comment Controls -->
                            <div class="comment-actions">
                                <a href="javascript:void(0);" data-cvote="up" data-id="'.$comm["comm"][$comID]["id"].'" class="jak-cvote"><i class="fa fa-thumbs-up"></i></a>
                                <a href="javascript:void(0);" data-cvote="down" data-id="'.$comm["comm"][$comID]["id"].'" class="jak-cvote"><i class="fa fa-thumbs-down"></i></a>
                                <!-- Votes -->
                                <span id="jak-cvotec'.$comm["comm"][$comID]["id"].'" class="label label-'.jak_comment_votes($comm["comm"][$comID]["votes"]).'">'.$comm["comm"][$comID]["votes"].'</span>
                                '.($status && !$comm["comm"][$comID]["commentid"] ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-sm btn-primary comment-reply-btn jak-creply"><i class="fa fa-share-alt"></i> '.$reply.'</a>' : '').'
                                '.($access ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" data-msg="'.$comm["comm"][$comID]["message"].'" class="btn btn-secondary btn-sm jak-epost"><i class="fa fa-pencil"></i></a> <a href="'.$comm["comm"][$comID]["parseurl1"].'" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a>' : "").($report && $comm["comm"][$comID]["report"] == 0 ? ' <a href="'.$comm["comm"][$comID]["parseurl2"].'" class="btn btn-sm btn-warning"><i class="fa fa-exclamation-triangle"></i></a>' : "").'
                            </div>
                        </div></li><li>';
            $html .= jak_build_comments($comID, $comm, "", $access, $session, $approve, $reply, $report, $status);
            $html .= '</li>';
            if (!$comm["comm"][$comID]["commentid"]) {
                $html .= '<li><ul><li id="insertPost_'.$comm["comm"][$comID]["id"].'"></li></ul></li>';
            }
         }
      }
      
      $html .= '</ul>';
   }
   
   return $html;
}

function jak_next_page($blogid, $perm, $lang) {

  global $jakdb;
  if ($perm) {
    $result = $jakdb->get("blog", ["id", "title"], ["AND" => ["id[>]" => $blogid, "lang" => $lang, "active" => 1], "ORDER" => ["id" => "ASC"]]);
  } else {
    $result = $jakdb->get("blog", ["id", "title"], ["AND" => ["membersonly" => 0, "id[>]" => $blogid, "lang" => $lang, "active" => 1], "ORDER" => ["id" => "ASC"]]);
  }

  if ($result) {
      return $result;
  } else
      return false;
}

function jak_previous_page($blogid, $perm, $lang) {

  global $jakdb;
  if ($perm) {
    $result = $jakdb->get("blog", ["id", "title"], ["AND" => ["id[<]" => $blogid, "lang" => $lang, "active" => 1], "ORDER" => ["id" => "DESC"]]);
  } else {
    $result = $jakdb->get("blog", ["id", "title"], ["AND" => ["membersonly" => 0, "id[<]" => $blogid, "lang" => $lang, "active" => 1], "ORDER" => ["id" => "DESC"]]);
  }

  if ($result) {
      return $result;
  } else
      return false;

}
?>