<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.2                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('config.php')) die('rest_api config.php not exist');
require_once 'config.php';

$userid = $loginhash = $ticketid = $answerid = $editticket = $editanswer = $deleteticket = $deleteanswer = $subject = $message = $searchticket = "";
$readticket = false;
if (isset($_REQUEST['userid']) && !empty($_REQUEST['userid']) && is_numeric($_REQUEST['userid'])) $userid = $_REQUEST['userid'];
if (isset($_REQUEST['loginhash']) && !empty($_REQUEST['loginhash'])) $loginhash = $_REQUEST['loginhash'];
if (isset($_REQUEST['ticketid']) && !empty($_REQUEST['ticketid'])) $ticketid = $_REQUEST['ticketid'];
if (isset($_REQUEST['answerid']) && !empty($_REQUEST['answerid'])) $answerid = $_REQUEST['answerid'];
if (isset($_REQUEST['readticket']) && !empty($_REQUEST['readticket'])) $readticket = $_REQUEST['readticket'];
if (isset($_REQUEST['editticket']) && !empty($_REQUEST['editticket'])) $editticket = $_REQUEST['editticket'];
if (isset($_REQUEST['editanswer']) && !empty($_REQUEST['editanswer'])) $editanswer = $_REQUEST['editanswer'];
if (isset($_REQUEST['deleteticket']) && !empty($_REQUEST['deleteticket'])) $deleteticket = $_REQUEST['deleteticket'];
if (isset($_REQUEST['deleteanswer']) && !empty($_REQUEST['deleteanswer'])) $deleteanswer = $_REQUEST['deleteanswer'];
if (isset($_REQUEST['subject']) && !empty($_REQUEST['subject'])) $subject = $_REQUEST['subject'];
if (isset($_REQUEST['message']) && !empty($_REQUEST['message'])) $message = $_REQUEST['message'];
if (isset($_REQUEST['message']) && !empty($_REQUEST['message'])) $message = $_REQUEST['message'];
if (isset($_REQUEST['searchticket']) && !empty($_REQUEST['searchticket'])) $searchticket = $_REQUEST['searchticket'];

if (!empty($userid) && !empty($loginhash)) {

	// Let's check if we are logged in
	$usr = $jakuserlogin->jakCheckrestlogged($userid, $loginhash);

	if ($usr) {

		// Select the user fields
		$jakuser = new JAK_user($usr);
		// Only the SuperAdmin in the config file see everything
		if ($jakuser->jakSuperadminaccess($userid)) {
			define('JAK_SUPERADMINACCESS', true);
		} else {
			define('JAK_SUPERADMINACCESS', false);
		}

		$USER_LANGUAGE = strtolower($jakuser->getVar("language"));

		// Import the language file
		if ($USER_LANGUAGE && file_exists(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$USER_LANGUAGE.'.php')) {
		    include_once APP_PATH.JAK_OPERATOR_LOC.'/lang/'.$USER_LANGUAGE.'.php';
		    $lang = $USER_LANGUAGE;
		} else {
		    include_once(APP_PATH.JAK_OPERATOR_LOC.'/lang/'.JAK_LANG.'.php');
		    $lang = JAK_LANG;
		}

		// Read the ticket
		if ($searchticket && !empty($searchticket)) {

			// let's go through the tables
			$filtered = filter_var($searchticket, FILTER_SANITIZE_STRING);
		    $keyword = strtolower($filtered);

		    $seachresult = array();

		    if (is_numeric($jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
		    	$seachresult = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.email", "support_tickets.name", "support_tickets.initiated", "support_tickets.updated", "support_tickets.ended", "support_tickets.private", "support_tickets.status", "support_departments.title(department)"], ["AND" => ["OR" => ["support_tickets.subject[~]" => $keyword, "support_tickets.content[~]" => $keyword, "support_tickets.name[~]" => $keyword], "support_tickets.depid" => $jakuser->getVar("support_dep")], "ORDER" => ["support_tickets.updated" => "DESC"], "LIMIT" => 10]);
			} elseif (!((boolean)$jakuser->getVar("support_dep")) && $jakuser->getVar("support_dep") != 0) {
				$seachresult = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.email", "support_tickets.name", "support_tickets.initiated", "support_tickets.updated", "support_tickets.ended", "support_tickets.private", "support_tickets.status", "support_departments.title(department)"], ["AND" => ["OR" => ["support_tickets.subject[~]" => $keyword, "support_tickets.content[~]" => $keyword, "support_tickets.name[~]" => $keyword], "support_tickets.depid" => [$jakuser->getVar("support_dep")]], "ORDER" => ["support_tickets.updated" => "DESC"], "LIMIT" => 10]);
			} else {
				$seachresult = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.email", "support_tickets.name", "support_tickets.initiated", "support_tickets.updated", "support_tickets.ended", "support_tickets.private", "support_tickets.status", "support_departments.title(department)"], ["OR" => ["support_tickets.subject[~]" => $keyword, "support_tickets.content[~]" => $keyword, "support_tickets.name[~]" => $keyword], "ORDER" => ["support_tickets.updated" => "DESC"], "LIMIT" => 10]);
			}

			if (isset($seachresult) && !empty($seachresult)) {

				// Take the chat go to chat
				die(json_encode(array('status' => true, 'task' => "search", 'searchresult' => $seachresult)));

			} else {

				// There is no data with this ticket
				die(json_encode(array('status' => false, 'task' => "search", 'errorcode' => 9)));
			}

		}

		// Read the ticket
		if ($readticket && !empty($ticketid) && is_numeric($ticketid)) {

				$JAK_FORM_DATA = $jakdb->get("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.depid", "support_tickets.operatorid", "support_tickets.subject", "support_tickets.content", "support_tickets.clientid", "support_tickets.ip", "support_tickets.referrer", "support_tickets.notes", "support_tickets.private", "support_tickets.status", "support_tickets.attachments", "support_tickets.initiated", "support_tickets.ended", "support_tickets.updated", "support_tickets.priorityid", "support_tickets.toptionid", "support_departments.title", "clients.name", "clients.email", "clients.support_dep", "clients.credits", "clients.paid_until"], ["support_tickets.id" => $ticketid]);

				// Get all operators
			    $OPERATOR_ALL = $jakdb->select("user", ["id", "name", "email"], ["OR #andclause" => ["AND #the first condition" => ["id" => [JAK_SUPERADMIN]], "AND #the second condition" => ["permissions[~]" => "support", "support_dep" => [0, $JAK_FORM_DATA["depid"]], "access" => 1]], "ORDER" => ["name" => "ASC"]]);

			    if ($jakuser->getVar("support_dep") == 0) {
			        $DEPARTMENTS_ALL = $jakdb->select("support_departments", ["id", "title"], ["ORDER" => ["dorder" => "ASC"]]);
			    } else {
			        $DEPARTMENTS_ALL = $jakdb->select("support_departments", ["id", "title"], ["id" => [$jakuser->getVar("support_dep")], "ORDER" => ["dorder" => "ASC"]]);
			    }

			    // Get all priorities
			    $PRIORITY_ALL = $jakdb->select("ticketpriority", "*", ["depid" => [0, $JAK_FORM_DATA["depid"]]]);
			    // Get all options
			    $TOPTIONS_ALL = $jakdb->select("ticketoptions", "*", ["depid" => [0, $JAK_FORM_DATA["depid"]]]);

			    // Get the ticket Answers
			    $JAK_ANSWER_DATA = $jakdb->select("ticket_answers", ["[>]user" => ["operatorid" => "id"], "[>]clients" => ["clientid" => "id"]], ["ticket_answers.id", "ticket_answers.content", "ticket_answers.lastedit", "ticket_answers.sent", "user.id(oid)", "user.name(oname)", "clients.id(cid)", "clients.name(cname)"], ["ticket_answers.ticketid" => $ticketid, "ORDER" => ["ticket_answers.sent" => "DESC"]]);

			    // Get the standard support responses
			    $JAK_RESPONSE_DATA = array();
			    // Standard Message
			    $JAK_RESPONSE_DATA[] = array("message" => 0, "title" => $jkl["g7"]);
			    if (isset($HD_RESPONSEST) && is_array($HD_RESPONSEST)) {
			        
			        // get the responses from the file specific for this client
			        foreach($HD_RESPONSEST as $r) {
			        
			        	if ($r["depid"] == 0 || $r["depid"] == $JAK_FORM_DATA["depid"]) {
			        
			            	$phold = array("%operator%","%client%","%email%");
			            	$replace   = array($jakuser->getVar("name"), $JAK_FORM_DATA["name"], JAK_EMAIL);
			            	$message = str_replace($phold, $replace, $r["message"]);
			            
			            	$JAK_RESPONSE_DATA[] = array("message" => base64_encode($message), "title" => $r["title"]);
			          	}
			        }
			    }

			    // Get the custom fields if any
			    $fields = array();
			    $custom_fields = array();
			    if ($JAK_FORM_DATA["depid"] != 0) {
		            $depid = [0, $JAK_FORM_DATA["depid"]];
		        } else {
		        	$depid = $JAK_FORM_DATA["depid"];
		        }
		        $formfields = $jakdb->select('customfields', "*", ["AND" => ["fieldlocation" => 2, "depid" => $depid], "ORDER" => ["dorder" => "ASC"]]);

		        // Get the correct language
		        $usrlang = $jakuser->getVar("language");
			    if (!empty($usrlang) && $usrlang != JAK_LANG) {
			        $translations = $jakdb->select('translations', ["customfieldid", "title", "description"], ["AND" => ["lang" => $usrlang, "customfieldid[!]" => 0]]);
			    }

			    // Custom fields
			    $fieldoptions = array();
			    if (isset($formfields) && !empty($formfields)) {

			    	$JAK_CUSTOM_FIELD_DATA = $jakdb->get("support_tickets", "*", ["id" => $ticketid]);

			        foreach ($formfields as $v) {


			            if ($v["fieldtype"] == 2 || $v["fieldtype"] == 3 || $v["fieldtype"] == 4) {
			                $fieldoptions = explode(",", $v["field_html"]);
			                // Set translation to false because it does not exist
			                $tl = false;
			            }

			            // Get the translation
			            if (isset($translations) && !empty($translations)) foreach ($translations as $t) {
			                if ($t["customfieldid"] == $v["id"]) {
			                    $v["title"] = $t["title"];
			                    if ($v["fieldtype"] == 2 || $v["fieldtype"] == 3 || $v["fieldtype"] == 4) {
			                        $fieldoptionstrans = explode(",", $t["description"]);
			                        $fieldoptions = array_combine($fieldoptions, $fieldoptionstrans);
			                        // Set translation to true because it does exist
			                        $tl = true;
			                    }
			                }
			            }

			            $fields[] = array("id" => $v["id"], "type" => $v["fieldtype"], "title" => $v["title"], "slug" => $v["val_slug"], "value" => $JAK_CUSTOM_FIELD_DATA[$v["val_slug"]], "options" => $fieldoptions);
			        }
			    }

			    if (!empty($fields)) $custom_fields = $fields;

			    // Get the attachments if any
			    $JAK_TICKET_FILES = array();
			    $JAK_FILES_PATH = BASE_URL.JAK_FILES_DIRECTORY.'/support/'.$ticketid;
			    if ($JAK_FORM_DATA["attachments"] != 0) {
			    	$JAK_TICKET_FILES = jak_get_files(APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$ticketid);
			   	}

			   	$ticket = array("id" => $JAK_FORM_DATA["id"], "subject" => $JAK_FORM_DATA["subject"], "content" => $JAK_FORM_DATA["content"], "depid" => $JAK_FORM_DATA["depid"], "operatorid" => $JAK_FORM_DATA["operatorid"], "clientid" => $JAK_FORM_DATA["clientid"], "private" => $JAK_FORM_DATA["private"], "status" => $JAK_FORM_DATA["status"], "notes" => $JAK_FORM_DATA["notes"], "ip" => $JAK_FORM_DATA["ip"], "referrer" => $JAK_FORM_DATA["referrer"], "attachments" => $JAK_FORM_DATA["attachments"], "initiated" => $JAK_FORM_DATA["initiated"], "ended" => $JAK_FORM_DATA["ended"], "updated" => $JAK_FORM_DATA["updated"], "priorityid" => $JAK_FORM_DATA["priorityid"], "toptionid" => $JAK_FORM_DATA["toptionid"], "department" => $JAK_FORM_DATA["title"], "clientname" => $JAK_FORM_DATA["name"], "clientemail" => $JAK_FORM_DATA["email"], "clientdep" => $JAK_FORM_DATA["support_dep"], "clientcredit" => $JAK_FORM_DATA["credits"], "clientpaid" => $JAK_FORM_DATA["paid_until"], "answers" => $JAK_ANSWER_DATA, "opall" => $OPERATOR_ALL, "depall" => $DEPARTMENTS_ALL, "priorityall" => $PRIORITY_ALL, "optionsall" => $TOPTIONS_ALL, "responseall" => $JAK_RESPONSE_DATA, "customfields" => $custom_fields, "files" => $JAK_TICKET_FILES, "filespath" => $JAK_FILES_PATH);

			if (isset($ticket) && !empty($ticket)) {

				// Take the chat go to chat
				die(json_encode(array('status' => true, 'ticket' => $ticket)));

			} else {

				// There is no data with this ticket
				die(json_encode(array('status' => false, 'task' => "ticket", 'errorcode' => 9)));
			}
		}

		// Edit ticket
		if ($editticket && !empty($ticketid) && is_numeric($ticketid)) {

			if (jak_row_exist($ticketid, "support_tickets")) {

				$subjectf = filter_var($subject, FILTER_SANITIZE_STRING);
				$contentf = jak_clean_safe_userpost($message);

	      		// Update the ticket
	      		$jakdb->update("support_tickets", ["subject" => $subjectf, "content" => $contentf], ["id" => $ticketid]);

				// Deny transfer stay in queue
				die(json_encode(array('status' => true, 'task' => "editticket")));

			} else {
				die(json_encode(array('status' => false, 'task' => "editticket", 'errorcode' => 7)));
			}

		}

		// Edit answer
		if ($editanswer && !empty($ticketid) && is_numeric($ticketid) && is_numeric($answerid)) {

			if (jak_row_exist($ticketid, "support_tickets") && jak_row_exist($answerid, "ticket_answers")) {

				$contentf = jak_clean_safe_userpost($message);

	      		// Update the answer
	      		$jakdb->update("ticket_answers", ["content" => $contentf], ["id" => $answerid]);

				// Deny transfer stay in queue
				die(json_encode(array('status' => true, 'task' => "editanswer")));

			} else {
				die(json_encode(array('status' => false, 'task' => "editanswer", 'errorcode' => 7)));
			}

		}

		// Delete answer
		if ($deleteanswer && !empty($ticketid) && is_numeric($ticketid) && is_numeric($answerid)) {

			if (jak_row_exist($ticketid, "support_tickets") && jak_row_exist($answerid, "ticket_answers")) {

	      		// Delete the answer
	      		$jakdb->delete("ticket_answers", ["AND" => ["id" => $answerid, "ticketid" => $ticketid]]);

				// Deny transfer stay in queue
				die(json_encode(array('status' => true, 'task' => "deleteanswer")));

			} else {
				die(json_encode(array('status' => false, 'task' => "deleteanswer", 'errorcode' => 7)));
			}

		}

		// Delete ticket
		if ($deleteticket && !empty($ticketid) && is_numeric($ticketid)) {

			if (JAK_SUPERADMINACCESS) {

				// Delete the ticket
                  $jakdb->delete("support_tickets", ["id" => $ticketid]);

                  // Delete the answer
                  $jakdb->delete("ticket_answers", ["ticketid" => $ticketid]);

                  // Delete all attachments
                  $targetPath = APP_PATH.JAK_FILES_DIRECTORY.'/support/'.$ticketid.'/';
                  $removedouble =  str_replace("//","/",$targetPath);
                  foreach(glob($removedouble.'*.*') as $jak_unlink) {
                    // Delete all files
                    @unlink($jak_unlink);    
                  }
                  // Delete the folder
                  @rmdir($targetPath);

				// Deny transfer stay in queue
				die(json_encode(array('status' => true, 'task' => "deleteticket")));

			} else {
				die(json_encode(array('status' => false, 'task' => "deleteticket", 'errorcode' => 7)));
			}

		}


	} else {
		die(json_encode(array('status' => false, 'errorcode' => 1, 'errorcode' => false)));
	}
}

die(json_encode(array('status' => false, 'errorcode' => 7, 'errorcode' => false)));
?>