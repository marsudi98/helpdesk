<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.4.3                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2019 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

class JAK_comment
{
	private $data = array();
	
	// Get the lang into the class
	
	public function __construct($id, $field, $var, $ptime, $pdate, $admin, $nested = true) {
		/*
		/	The constructor
		*/
		
		global $jakdb;
		$getID = $jakdata = array();
		if ($admin) {
			$result = $jakdb->select("blogcomments", ["[>]user" => ["operatorid" => "id"], "[>]clients" => ["clientid" => "id"]], ["blogcomments.id", "blogcomments.commentid", "blogcomments.message", "blogcomments.votes", "blogcomments.approve", "blogcomments.time", "blogcomments.session", "user.id(oid)", "user.name(oname)", "user.picture(opicture)", "clients.id(cid)", "clients.name(cname)", "clients.picture(cpicture)"], ["blogcomments.".$field => $id]);
		} else {
			$result = $jakdb->select("blogcomments", ["[>]user" => ["operatorid" => "id"], "[>]clients" => ["clientid" => "id"]], ["blogcomments.id", "blogcomments.commentid", "blogcomments.message", "blogcomments.votes", "blogcomments.approve", "blogcomments.time", "blogcomments.session", "user.id(oid)", "user.name(oname)", "user.picture(opicture)", "clients.id(cid)", "clients.name(cname)", "clients.picture(cpicture)"], ["AND" => ["OR" => ["blogcomments.session" => session_id(), "blogcomments.approve" => 1], "blogcomments.".$field => $id]]);
		}

		if (isset($result) && !empty($result)) foreach ($result as $row) {

			if ($row['oid'] && $row['opicture'] && $row['opicture'] != "/standard.jpg") {
				$row['avatar'] = BASE_URL.JAK_FILES_DIRECTORY.$row['opicture'];
			} elseif ($row['cid'] && $row['cpicture'] && $row['cpicture'] != "/standard.jpg") { 
				$row['avatar'] = BASE_URL.JAK_FILES_DIRECTORY.'/'.$row['cpicture'];
			} else {
				$row['avatar'] = BASE_URL.JAK_FILES_DIRECTORY.'/standard.jpg';
			}

			$row['username'] = ($row['oname'] ? $row['oname'] : $row['cname']);
				
			$row['created'] = JAK_base::jakTimesince($row['time'], $ptime, $pdate);

			// Sanitize the message
			$row["message"] = jak_secure_site($row['message']);
				
			// There should be always a varname in categories and check if seo is valid
			$row["parseurl1"] = JAK_rewrite::jakParseurl($var, 'del', $row['id']);
			$row["parseurl2"] = JAK_rewrite::jakParseurl($var, 'report', $row['id']);
				
		    // collect each record into $jakdata
		    $jakdata[] = $row;
		        
		        // Do we have nested comments
		        if ($nested) $getID = $row["id"];
		        
		    }
		    
		// now we go nested because we have a reply
		if ($nested && !empty($getID)) {

			if ($admin) {
				$resnes = $jakdb->select("blogcomments", ["[>]user" => ["operatorid" => "id"], "[>]clients" => ["clientid" => "id"]], ["blogcomments.id", "blogcomments.commentid", "blogcomments.message", "blogcomments.votes", "blogcomments.approve", "blogcomments.time", "blogcomments.session", "user.id(oid)", "user.name(oname)", "user.picture(opicture)", "clients.id(cid)", "clients.name(cname)", "clients.picture(cpicture)"], ["blogcomments.commentid" => [$getID]]);
			} else {
				$resnes = $jakdb->select("blogcomments", ["[>]user" => ["operatorid" => "id"], "[>]clients" => ["clientid" => "id"]], ["blogcomments.id", "blogcomments.commentid", "blogcomments.message", "blogcomments.votes", "blogcomments.approve", "blogcomments.time", "blogcomments.session", "user.id(oid)", "user.name(oname)", "user.picture(opicture)", "clients.id(cid)", "clients.name(cname)", "clients.picture(cpicture)"], ["AND" => ["blogcomments.commentid" => [$getID], "blogcomments.approve" => 1]]);
			}

			if (isset($resnes) && !empty($resnes)) foreach ($resnes as $nes) {

				if ($nes['oid'] && $nes['opicture'] && $nes['opicture'] != "/standard.jpg") {
					$nes['avatar'] = BASE_URL.JAK_FILES_DIRECTORY.$nes['opicture'];
				} elseif ($row['cid'] && $nes['cpicture'] && $nes['cpicture'] != "/standard.jpg") { 
					$nes['avatar'] = BASE_URL.JAK_FILES_DIRECTORY.'/'.$nes['cpicture'];
				} else {
					$nes['avatar'] = BASE_URL.JAK_FILES_DIRECTORY.'/standard.jpg';
				}

				$nes['username'] = ($nes['oname'] ? $nes['oname'] : $nes['cname']);
			
				$nes['created'] = JAK_base::jakTimesince($nes['time'], $ptime, $pdate, $timeago);
					
				// Sanitize the message
				$nes["message"] = jak_secure_site($nes['message']);
					
				// There should be always a varname in categories and check if seo is valid
				$nes["parseurl1"] = JAK_rewrite::jakParseurl($var, 'del', $nes['id']);
				$nes["parseurl2"] = JAK_rewrite::jakParseurl($var, 'report', $nes['id']);
					
			    // collect each record into $jakdata
			    $jakdata[] = $nes;
			        
			}
		}
		
		$this->data = $jakdata;
	}
	
	public function get_comments()
	{
		
		// Setting up an alias, so we don't have to write $this->data every time:
		$d = &$this->data;
		
		return $d;
		
	}
	
	public function get_commentajax($lang) {
		
		foreach($this->data as $d) {
		
		if ($d['oid'] && $d['opicture'] && $d['opicture'] != '/standard.jpg') {
			$avatar = '<img src="'.str_replace("class/", "", BASE_URL).JAK_FILES_DIRECTORY.$d['opicture'].'" alt="avatar">';
		} elseif ($d['cid'] && $d['cpicture'] && $d['cpicture'] != '/standard.jpg') { 
			$avatar = '<img src="'.str_replace("class/", "", BASE_URL).JAK_FILES_DIRECTORY.'/'.$d['cpicture'].'" alt="avatar">';
		} else {
			$avatar = '<img src="'.str_replace("class/", "", BASE_URL).JAK_FILES_DIRECTORY.'/standard.jpg" alt="avatar">';
		}
		
		$approve = "";
		if ($d['approve'] == 0) {
			$approve = '<div class="alert alert-info">'.$lang.'</div>';
		}
		
		return '<div class="comment-wrapper">
			<div class="comment-author">'.$avatar.' <span class="comment-user">'.($d['cname'] ? $d['cname'] : $d['oname']).'</span> <span class="comment-date">'.$d['created'].'</span></div>
			<div class="com">'.stripslashes($d['message']).$approve.'</div>
		</div>';
		
		}
	
	}

	public function get_commentajax_modern($lang) {
		
		foreach($this->data as $d) {
		
		if ($d['oid'] && $d['opicture'] && $d['opicture'] != '/standard.jpg') {
			$avatar = str_replace("class/", "", BASE_URL).JAK_FILES_DIRECTORY.'/operator/'.$d['opicture'];
		} elseif ($d['cid'] && $d['cpicture'] && $d['cpicture'] != '/standard.jpg') { 
			$avatar = str_replace("class/", "", BASE_URL).JAK_FILES_DIRECTORY.'/'.$d['cpicture'];
		} else {
			$avatar = str_replace("class/", "", BASE_URL).JAK_FILES_DIRECTORY.'/standard.jpg';
		}
		
		$approve = "";
		if ($d['approve'] == 0) {
			$approve = '<div class="alert alert-info">'.$lang.'</div>';
		}
		
		return '<div class="media">
                <a class="float-left" href="javascript:void(0)">
                  <div class="avatar">
                    <img class="media-object" src="'.$avatar.'" alt="'.($d['cname'] ? $d['cname'] : $d['oname']).'">
                  </div>
                </a>
                <div class="media-body">
                  <h4 class="media-heading">'.($d['cname'] ? $d['cname'] : $d['oname']).'
                    <small>'.$d['created'].'</small>
                  </h4>
                  <h6 class="text-muted"></h6>
                  '.stripslashes($d['message']).$approve.'
                </div>
              </div>';
		
		}
	
	}
	
	public function get_total() {
		// Setting up an alias, so we don't have to write $this->data every time:
		$d = $this->data;
		
		if ($d) {
		
			foreach($d as $t) {
				$total[] = $t['id'];
			}
		
			// get the total user in one var.
			$total = count($total, COUNT_RECURSIVE);
		
		} else {
		
			$total = 0;
		
		}
		
		return $total;
	}
	
	public static function validate_form(&$arr, $maxpost, $epost, $maxtxt) {
		/*
		/	This method is used to validate the data sent via AJAX.
		/
		/	It return true/false depending on whether the data is valid, and populates
		/	the $arr array passed as a paremter (notice the ampersand above) with
		/	either the valid input data, or the error messages.
		*/
		
		global $jkv;
		$errors = array();
		$data	= array();
		
		// Using the filter with a custom callback function:
		if (!($data['bmessage'] = filter_input(INPUT_POST, 'bmessage', FILTER_CALLBACK, array('options'=>'JAK_comment::validate_text')))) {
			$errors['bmesssage'] = $epost;
		}

		// Subcomments
		if (filter_input(INPUT_POST , 'comanswerid')) {
		   $data['comanswerid'] = filter_input(INPUT_POST , 'comanswerid');
		}

		// Subcomments
		if (filter_input(INPUT_POST , 'editpostid')) {
		   $data['editpostid'] = filter_input(INPUT_POST , 'editpostid');
		}
		
		// Count comment charactars
		if (!empty($maxpost)) {
			$countI = strlen($data['bmessage']);
		
			if ($countI > $maxpost) {
		    	$errors['bmessage'] = $emaxpost.$maxpost.' '.sprintf($maxtxt,$countI);
			}
		}
		
		if (!empty($errors)) {
			
			// If there are errors, copy the $errors array to $arr:
			$arr = $errors;
			return false;
		}
		
		// If the data is valid, sanitize all the data and copy it to $arr:
		
		foreach($data as $k=>$v) {
			$arr[$k] = $v;
		}
		
		return true;
		
	}

	private static function validate_text($str)
	{
		/*
		/	This method is used internally as a FILTER_CALLBACK
		*/
		
		if (mb_strlen($str, 'utf8') < 1) return false;
			
		$str = htmlspecialchars($str);
		
		// Remove the new line characters that are left
		$str = str_replace(array(chr(10), chr(13)), '', $str);

		return $str;
	}

}
?>