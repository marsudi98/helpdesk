<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH                                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2018 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Language file goes global
global $jkl;

/* Leave a var empty if not in use or set to false */
$jaktplclient = array();

/* Custom background  */
$jaktplclient["customtitle"] = "Background Image";
$jaktplclient["custom"] = array("bg.jpg" => "bg.jpg", "bg1.jpg" => "bg1.jpg", "bg2.jpg" => "bg2.jpg", "bg3.jpg" => "bg3.jpg", "bg4.jpg" => "bg4.jpg", "bg5.jpg" => "bg5.jpg", "bg6.jpg" => "bg6.jpg", "bg7.jpg" => "bg7.jpg", "bg8.jpg" => "bg8.jpg", "bg9.jpg" => "bg9.jpg", "bg10.jpg" => "bg10.jpg", "bg11.jpg" => "bg11.jpg", "bg12.jpg" => "bg12.jpg");

/* Custom background height  */
$jaktplclient["custom2title"] = "Header Height";
$jaktplclient["custom2"] = array("1" => $jkl['bw5'], "0" => $jkl['bw6']);

/* Custom background height  */
$jaktplclient["custom3title"] = "Footer Style";
$jaktplclient["custom3"] = array("1" => $jkl['bw5'], "0" => $jkl['bw6']);

/* Custom background height  */
$jaktplclient["custom4title"] = "Similar Articles";
$jaktplclient["custom4"] = array("1" => $jkl['g19'], "0" => $jkl['g18']);

function jak_build_comments_modern($parent, $comm, $access, $session, $approve, $reply, $report, $status) {

  $html = '';
  if (isset($comm['subcomm'][$parent])) {
      
      $html .= '<div class="media-area">';
      foreach ($comm['subcomm'][$parent] as $comID) {
         if (!isset($comm['subcomm'][$comID])) {
             $html .= '<div class="media">
                <a class="float-left" href="javascript:void(0)">
                  <div class="avatar">
                    <img class="media-object" src="'.$comm["comm"][$comID]["avatar"].'" alt="'.$comm["comm"][$comID]["username"].'">
                  </div>
                </a>
                <div class="media-body">
                  <h4 class="media-heading">'.$comm["comm"][$comID]["username"].'
                    <small>'.$comm["comm"][$comID]["created"].'</small>
                  </h4>
                  <h6 class="text-muted"></h6>
                  <div class="com" id="msgid_'.$comm["comm"][$comID]["id"].'">'.$comm["comm"][$comID]["message"].(($comm["comm"][$comID]["approve"] == 0 && ((!empty($comm["comm"][$comID]["session"]) && $session == $comm["comm"][$comID]["session"]) || $access)) ? '<div class="alert alert-info">'.$approve.'</div>' : '').(!$comm["comm"][$comID]["commentid"] ? '<span id="insertPost_'.$comm["comm"][$comID]["id"].'"></span>' : '').'
                  </div>
                  <div class="media-footer">
                  '.($status && !$comm["comm"][$comID]["commentid"] && $comm["comm"][$comID]["approve"] ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-primary btn-link float-right jak-creply"><i class="material-icons">reply</i> '.$reply.'</a>' : '').'
                    
                    <a href="javascript:void(0)" data-cvote="up" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-success btn-link float-right jak-cvote"><i class="material-icons">thumb_up</i></a>
                    <span id="jak-cvotec'.$comm["comm"][$comID]["id"].'" class="badge badge-pill badge-'.jak_comment_votes($comm["comm"][$comID]["votes"]).' float-right mt-3">'.$comm["comm"][$comID]["votes"].'</span>
                    <a href="javascript:void(0)" data-cvote="down" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-danger btn-link float-right jak-cvote"><i class="material-icons">thumb_down</i></a>
                    <!-- Votes -->
                 	'.($access ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" data-msg="'.$comm["comm"][$comID]["message"].'" class="btn btn-primary btn-link float-right jak-epost"><i class="fa fa-pencil"></i></a> <a href="'.$comm["comm"][$comID]["parseurl1"].'" class="btn btn-primary btn-link float-right"><i class="fa fa-trash"></i></a>' : "").($report && $comm["comm"][$comID]["report"] == 0 ? ' <a href="'.$comm["comm"][$comID]["parseurl2"].'" class="btn btn-primary btn-link float-right"><i class="fa fa-exclamation-triangle"></i></a>' : "").'
                  </div>
                </div>
              </div>';
         }        

         if (isset($comm['subcomm'][$comID])) {
            $html .= '<div class="media">
                <a class="float-left" href="javascript:void(0)">
                  <div class="avatar">
                    <img class="media-object" alt="'.$comm["comm"][$comID]["username"].'" src="'.$comm["comm"][$comID]["avatar"].'">
                  </div>
                </a>
                <div class="media-body">
                  <h4 class="media-heading">'.$comm["comm"][$comID]["username"].'
                    <small>'.$comm["comm"][$comID]["created"].'</small>
                  </h4>
                  '.$comm["comm"][$comID]["message"].'
                  <div class="media-footer">
                  	'.($status && !$comm["comm"][$comID]["commentid"] ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-primary btn-link float-right jak-creply"><i class="material-icons">reply</i> '.$reply.'</a>' : '').'
                    <a href="javascript:void(0)" data-cvote="up" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-success btn-link float-right jak-cvote"><i class="material-icons">thumb_up</i></a>
                    <span id="jak-cvotec'.$comm["comm"][$comID]["id"].'" class="badge badge-pill badge-'.jak_comment_votes($comm["comm"][$comID]["votes"]).' float-right mt-3">'.$comm["comm"][$comID]["votes"].'</span>
                    <a href="javascript:void(0)" data-cvote="down" data-id="'.$comm["comm"][$comID]["id"].'" class="btn btn-danger btn-link float-right jak-cvote"><i class="material-icons">thumb_down</i></a>
                    '.($access ? '<a href="javascript:void(0);" data-id="'.$comm["comm"][$comID]["id"].'" data-msg="'.$comm["comm"][$comID]["message"].'" class="btn btn-primary btn-link float-right jak-epost"><i class="fa fa-pencil"></i></a> <a href="'.$comm["comm"][$comID]["parseurl1"].'" class="btn btn-primary btn-link float-right"><i class="fa fa-trash"></i></a>' : "").($report && $comm["comm"][$comID]["report"] == 0 ? ' <a href="'.$comm["comm"][$comID]["parseurl2"].'" class="btn btn-primary btn-link float-right"><i class="fa fa-exclamation-triangle"></i></a>' : "").'
                  </div>';
            $html .= jak_build_comments_modern($comID, $comm, $access, $session, $approve, $reply, $report, $status);
            $html .= '</div></div>';
            if (!$comm["comm"][$comID]["commentid"]) {
                $html .= '<span id="insertPost_'.$comm["comm"][$comID]["id"].'"></span>';
            }
         }
      }
      
      $html .= '</div>';
   }
   
   return $html;
}

?>