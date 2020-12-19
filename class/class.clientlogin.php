<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.5                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

class JAK_clientlogin {

	protected $name = '', $pass = '', $time = '';
	var $email;     //Username given on sign-up
	
	public function __construct() {
	    $this->email = '';
	}
	   
	function jakChecklogged() {
	
	    /* Check if user has been remembered */
	    if (isset($_COOKIE['jak_lcpc_cookname']) && isset($_COOKIE['jak_lcpc_cookid'])) {
	        $_SESSION['jak_lcpc_email'] = $_COOKIE['jak_lcpc_cookname'];
	        $_SESSION['jak_lcpc_idhash'] = $_COOKIE['jak_lcpc_cookid'];
	    }
	
	    /* Username and idhash have been set */
	    if (isset($_SESSION['jak_lcpc_email']) && isset($_SESSION['jak_lcpc_idhash']) && $_SESSION['jak_lcpc_email'] != $this->email) {
	        /* Confirm that email and userid are valid */
	        if (!JAK_clientlogin::jakConfirmidhash($_SESSION['jak_lcpc_email'], $_SESSION['jak_lcpc_idhash'])) {
	        	/* Variables are incorrect, user not logged in */
	            unset($_SESSION['jak_lcpc_email']);
	            unset($_SESSION['jak_lcpc_idhash']);
	            
	            return false;
	        }
	         
	        // Return the user data
	        return JAK_clientlogin::jakUserinfo($_SESSION['jak_lcpc_email']);

	    /* User not logged in */
	    } else {
	    	return false;
	    }
	}

	function jakCheckrestlogged($userid, $hash) {
	
	    /* UserID and Hash have been set */
	    global $jakdb;
	    $datauinfo = $jakdb->get("clients", "*", ["AND" => ["id" => $userid, "idhash" => $hash]]);
	    if (isset($datauinfo) && !empty($datauinfo)) {

	        // Return the user data
	        return $datauinfo;

	    /* User not logged in */
	    } else {
	    	return false;
	    }
	}
	
	public static function jakCheckuserdata($email, $pass) {
	
		// The new password encrypt with hash_hmac
		$passcrypt = hash_hmac('sha256', $pass, DB_PASS_HASH);
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
	
		global $jakdb;
		$datausr = $jakdb->get("clients", "id", ["AND" => ["email" => $email, "password" => $passcrypt, "access" => 1]]);
		if ($datausr) {
			return $datausr;
		} else {
			return false;
		}
			
	}
	
	public static function jakLogin($email, $pass, $remember) {
		
		// The new password encrypt with hash_hmac
		$passcrypt = hash_hmac('sha256', $pass, DB_PASS_HASH);
	
		global $jakdb;
		
		// Get the stuff out the database
		$datausr = $jakdb->get("clients", ["idhash", "logins"], ["AND" => ["email" => $email, "password" => $passcrypt]]);
		
		if ($datausr['logins'] % 10 == 0) {
		
			// Generate new idhash
			$nidhash = JAK_clientlogin::generateRandID();
			
		} else {
		
			if (isset($datausr['idhash']) && !empty($datausr['idhash']) && $datausr['idhash'] != "NULL") { 
		
				// Take old idhash
				$nidhash = $datausr['idhash'];
			
			} else {
			
				// Generate new idhash
				$nidhash = JAK_clientlogin::generateRandID();
			
			}
		
		}
		
		// Set session in database
		$jakdb->update("clients", ["session" => session_id(), "idhash" => $nidhash, "logins[+]" => 1, "forgot" => 0, "lastactivity" => time()], ["AND" => ["email" => $email, "password" => $passcrypt, "access" => 1]]);
		
		$_SESSION['jak_lcpc_email'] = $email;
		$_SESSION['jak_lcpc_idhash'] = $nidhash;
		
		// Check if cookies are set previous (wrongly) and delete
		if (isset($_COOKIE['jak_lcpc_cookname']) || isset($_COOKIE['jak_lcpc_cookid'])) {
			JAK_base::jakCookie('jak_lcpc_cookname', $email, JAK_COOKIE_TIME, JAK_COOKIE_PATH);
			JAK_base::jakCookie('jak_lcpc_cookid', $nidhash, JAK_COOKIE_TIME, JAK_COOKIE_PATH);
		}
		
		// Now check if remember is selected and set cookies new...
		if ($remember) {
			JAK_base::jakCookie('jak_lcpc_cookname', $email, JAK_COOKIE_TIME, JAK_COOKIE_PATH);
			JAK_base::jakCookie('jak_lcpc_cookid', $nidhash, JAK_COOKIE_TIME, JAK_COOKIE_PATH);
		}
		
	}
	
	public static function jakConfirmidhash($email, $idhash) {
	
		global $jakdb;
		
		if (isset($email) && !empty($email)) {
		
		    $datausr = $jakdb->get("clients", "idhash", ["AND" => ["email" => $email, "access" => 1]]);
		    
		    if ($datausr) {
		    
		    	$datausr = stripslashes($datausr);
		    	$idhash = stripslashes($idhash);
		    			    	
		    	/* Validate that userid is correct */
		    	if(!is_null($datausr) && $idhash == $datausr) {
		    		return true; //Success! Username and idhash confirmed
		    	}

		    }
		        
		}
	
		return false;
			
	}
	
	public static function jakUserinfo($email) {
	
			global $jakdb;
			$datauinfo = $jakdb->get("clients", "*", ["AND" => ["email" => $email, "access" => 1]]);
			if ($datauinfo) {
			   return $datauinfo;
			} else {
				return false;
			}
			
	}
	
	public static function jakUpdatelastactivity($clientid) {
	
			global $jakdb;
			if (is_numeric($clientid)) $jakdb->update("clients", ["lastactivity" => time()], ["id" => $clientid]);
			
	}
	
	public static function jakForgotpassword($email, $time) {
	
			global $jakdb;
			if ($jakdb->has("clients", ["AND" => ["email" => $email, "access" => 1]])) {
				if ($time != 0) $jakdb->update("clients", ["forgot" => $time], ["email" => $email]);
			    return true;
			} else {
			    return false;
			}
			
	}
	
	public static function jakForgotactive($forgotid) {
	
			global $jakdb;
			if ($jakdb->has("clients", ["AND" => ["forgot" => $forgotid, "access" => 1]])) {
			    return true;
			} else
			    return false;
			
	}
	
	public static function jakForgotcheckuser($email, $forgotid) {
	
			global $jakdb;
			if ($jakdb->has("clients", ["AND" => ["email" => $email, "forgot" => $forgotid, "access" => 1]])) {
			    return true;
			} else
			    return false;
			
	}
	
	public static function jakLogout($userid) {
	
			global $jakdb;
			
			// Delete cookies from this page
			JAK_base::jakCookie('jak_lcpc_cookname', '', -JAK_COOKIE_TIME, JAK_COOKIE_PATH);
			JAK_base::jakCookie('jak_lcpc_cookid', '', -JAK_COOKIE_TIME, JAK_COOKIE_PATH);
			
			// Update Database to session NULL
			$jakdb->update("clients", ["session" => $jakdb->raw("NULL"), "idhash" => $jakdb->raw("NULL"), "available" => 0], ["id" => $userid]);
			
			// Unset the main sessions
			unset($_SESSION['jak_lcpc_email']);
			unset($_SESSION['jak_lcpc_idhash']);
			unset($_SESSION['jak_lcpc_lang']);
			
			// Destroy session and generate new one for that user
			session_destroy();
			session_start();
			session_regenerate_id();
			
	}

	public static function jakLogoutRest($userid) {
	
			global $jakdb;
			
			// Update Database to session NULL
			$jakdb->update("clients", ["session" => $jakdb->raw("NULL"), "idhash" => $jakdb->raw("NULL"), "available" => 0], ["id" => $userid]);
			
	}
	
	public static function generateRandStr($length) {
	   $randstr = "";
	   for($i=0; $i<$length; $i++){
	      $randnum = mt_rand(0,61);
	      if($randnum < 10){
	         $randstr .= chr($randnum+48);
	      }else if($randnum < 36){
	         $randstr .= chr($randnum+55);
	      }else{
	         $randstr .= chr($randnum+61);
	      }
	   }
	   return $randstr;
	}
	
	private static function generateRandID() {
	   return md5(JAK_clientlogin::generateRandStr(16));
	}
}
?>