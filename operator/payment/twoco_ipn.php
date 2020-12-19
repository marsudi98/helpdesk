<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.4.4                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2019 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../../config.php')) die('[ipn.php] db.php not exist');
require_once '../../config.php';

// Include phpmailer
include_once '../../class/PHPMailerAutoload.php';

// Include the paypal library
include_once ('twoco.php');

// Reset vars
$sendmail = 0;

// Create an instance of the authorize.net library
$my2CO = new TwoCo();

// Log the IPN results
$my2CO->ipnLog = TRUE;

// Specify your authorize login and secret
$my2CO->setSecret(JAK_TWOCO_SECRET);

// Enable test mode if needed
// $my2CO->enableTestMode();

// Change for 3.0.3
use JAKWEB\JAKsql;

// Check validity and write down it
if ($my2CO->validateIpn()) {

  $item_name = base64_decode($my2CO->ipnData['custom']);
  $payment_currency = $my2CO->ipnData['tco_currency'];
  $payment_status = $my2CO->ipnData['invoice_status'];
  $txn_id = $my2CO->ipnData['vendor_order_id'];
  $payment_amount = $my2CO->ipnData['total'];
  $payer_email = $my2CO->ipnData['vendor_id'];
  $onumber = base64_decode($my2CO->ipnData['order_number']);

  $cdata = explode(":#:", $item_name);

        // check that payment_amount/payment_currency are correct
        global $jakdb;

        if ($cdata[0] == "cc3/cd3") {

            // Now if we have multi site we have fully automated process
            if (!empty(JAKDB_MAIN_NAME) && JAK_MAIN_LOC) {
                // Database connection to the main site
                $jakdb1 = new JAKsql([
                    // required
                    'database_type' => JAKDB_MAIN_DBTYPE,
                    'database_name' => JAKDB_MAIN_NAME,
                    'server' => JAKDB_MAIN_HOST,
                    'username' => JAKDB_MAIN_USER,
                    'password' => JAKDB_MAIN_PASS,
                    'charset' => 'utf8',
                    'port' => JAKDB_MAIN_PORT,
                    'prefix' => JAKDB_MAIN_PREFIX,
                             
                    // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
                    'option' => [PDO::ATTR_CASE => PDO::CASE_NATURAL]
                ]);

                // We get the settings for the payment
                $sett = array();
                $settings = $jakdb1->select("settings", ["varname", "used_value"]);
                foreach ($settings as $v) {
                    $sett[$v["varname"]] = $v["used_value"]; 
                }

                if (isset($payment_amount) && ($payment_amount == $jakhs['pricemonth'] || $payment_amount == (3*$jakhs['pricemonth']) || $payment_amount == (6*$jakhs['pricemonth']) || $payment_amount == (12*$jakhs['pricemonth']))) {

                    // Get the trial in the correct format $jakwidget['validtill']
                    if ($payment_amount == (12*$jakhs['pricemonth'])) {
                        if (JAK_VALIDTILL > time()) {
                            $paidunix = strtotime("+12 month", JAK_VALIDTILL);
                        } else {
                            $paidunix = strtotime("+12 month");
                        }
                    } elseif ($payment_amount == (6*$jakhs['pricemonth'])) {
                        if (JAK_VALIDTILL > time()) {
                            $paidunix = strtotime("+6 month", JAK_VALIDTILL);
                        } else {
                            $paidunix = strtotime("+6 month");
                        }
                    } elseif ($payment_amount == (3*$jakhs['pricemonth'])) {
                        if (JAK_VALIDTILL > time()) {
                            $paidunix = strtotime("+3 month", JAK_VALIDTILL);
                        } else {
                            $paidunix = strtotime("+3 month");
                        }
                    } else {
                        if (JAK_VALIDTILL > time()) {
                            $paidunix = strtotime("+1 month", JAK_VALIDTILL);
                        } else {
                            $paidunix = strtotime("+1 month");
                        }
                    }

                    // get the nice time
                    $paidtill = date('Y-m-d H:i:s', $paidunix);

                    // Insert into payment
                    $jakdb1->insert("payment_ipn", [ 
                        "userid" => $userid,
                        "status" => $payment_status,
                        "amount" => $payment_amount,
                        "currency" => $payment_currency,
                         "txn_id" => $txn_id,
                        "receiver_email" => $receiver_email,
                        "payer_email" => $payer_email,
                        "paid_with" => "2Checkout for Advanced",
                        "time" => $jakdb->raw("NOW()")]);

                    // We get the user data from the main table
                    $opmain = $jakdb1->get("users", ["id"], ["AND" => ["opid" => $userid, "locationid" => JAK_MAIN_LOC]]);
                
                    // Now make the stuff paid because we received the money.
                    // finally update the main database
                    $jakdb1->update("users", [ 
                        "trial" => "1980-05-06 00:00:00",
                        "paidtill" => $paidtill,
                        "active" => 1,
                        "confirm" => 0], ["AND" => ["opid" => $userid, "locationid" => JAK_MAIN_LOC]]);

                    // Payment details insert
                    $jakdb1->insert("subscriptions", [ 
                        "locationid" => JAK_MAIN_LOC,
                        "userid" => $userid,
                        "amount" => $payment_amount,
                        "currency" => $sett["currency"],
                        "paidfor" => "HD3 Membership",
                        "paidhow" => "2Checkout",
                        "paidwhen" => $jakdb->raw("NOW()"),
                        "paidtill" => $paidtill,
                        "success" => 1]);

                    // Update the advanced access table
                    $jakdb1->update("advaccess", [ 
                        "lastedit" => $jakdb->raw("NOW()"),
                        "paidtill" => $paidtill,
                        "paythanks" => 1], ["AND" => ["opid" => $userid, "id" => $opmain["id"]]]);

                    // Update the time for the user on the custom installation
                    $jakdb->update("settings", ["used_value" => $paidunix], ["varname" => "validtill"]);

                    // Now let us delete the define cache file
                    $cachewidget = APP_PATH.JAK_CACHE_DIRECTORY.'/opcache'.$userid.'.php';
                    if (file_exists($cachewidget)) {
                        unlink($cachewidget);
                    }

                    // log for manual investigation amount is not the same
                    $mail = new PHPMailer(); // defaults to using php "mail()"
                    $mail->SetFrom(JAK_EMAIL);
                    $mail->AddAddress(JAK_EMAIL);
                    $mail->AddReplyTo($payer_email);
                    $mail->Subject = JAK_TITLE.' - PAYPAL Success';
                    $mail->Body = 'There is a new payment for advanced access thru 2Checkout, userid: '.$userid.' - '.$item_name.' - '.$payment_status.' - '.$payment_amount.' - '.$payment_currency.' - '.$txn_id.' - '.$receiver_email.' - '.$payer_email;
                    $mail->Send(); // Send email without any warnings
                    
                } else {
                    
                    // log for manual investigation amount is not the same
                    $mail = new PHPMailer(); // defaults to using php "mail()"
                    $mail->SetFrom(JAK_EMAIL);
                    $mail->AddAddress(JAK_EMAIL);
                    $mail->AddReplyTo($payer_email);
                    $mail->Subject = JAK_TITLE.' - PAYPAL Success, but...';
                    $mail->Body = 'There is a new payment thru 2Checkout for advanced access, userid: '.$userid.' - '.$item_name.' - '.$payment_status.' - '.$payment_amount.' - '.$payment_currency.' - '.$txn_id.' - '.$receiver_email.' - '.$payer_email.' But the amount was paid is not the same amount was ordered, please check in the paypal order details.';
                    $mail->Send(); // Send email without any warnings
                    
                }

            }

        } else {

            // Now make the stuff paid because we received the money.
            $package = $jakdb->get("billing_packages", ["title", "credits", "paidtill", "chat_dep", "support_dep", "faq_cat", "amount"], ["id" => $cdata[2]]);
            // Credit based system
            if (JAK_BILLING_MODE == 1) {

                // Update the credits
                $jakdb->update("clients", ["credits[+]" => $package["credits"]], ["id" => $cdata[1]]);

                // Update the chat departments
                if ($package["chat_dep"] != 0) $jakdb->update("clients", ["chat_dep" => $package["chat_dep"]], ["id" => $cdata[1]]);

                // Update the support departments
                if ($package["support_dep"] != 0) $jakdb->update("clients", ["support_dep" => $package["support_dep"]], ["id" => $cdata[1]]);

                // Update the faq categories
                if ($package["faq_cat"] != 0) $jakdb->update("clients", ["faq_cat" => $package["faq_cat"]], ["id" => $cdata[1]]);

            // Memberschip based system
            } elseif (JAK_BILLING_MODE == 2) {

                // Get the new date
                if (strtotime($jakuser->getVar("paid_until")) > time()) {
                    $paidunix = strtotime($package["paidtill"], strtotime($jakuser->getVar("paid_until")));
                } else {
                    $paidunix = strtotime($package["paidtill"]);
                }
                $paidtill = date('Y-m-d', $paidunix);

                // Update the credits
                $jakdb->update("clients", ["paid_until" => $paidtill], ["id" => $cdata[1]]);

                // Update the chat departments
                if ($package["chat_dep"] != 0) $jakdb->update("clients", ["chat_dep" => $package["chat_dep"]], ["id" => $cdata[1]]);

                // Update the support departments
                if ($package["support_dep"] != 0) $jakdb->update("clients", ["support_dep" => $package["support_dep"]], ["id" => $cdata[1]]);

                // Update the faq categories
                if ($package["faq_cat"] != 0) $jakdb->update("clients", ["faq_cat" => $package["faq_cat"]], ["id" => $cdata[1]]);

            }

            // Payment details insert
            $jakdb->insert("subscriptions", [ 
                "clientid" => $cdata[1],
                "amount" => $payment_amount,
                "paidhow" => "2Checkout",
                "currency" => $payment_currency,
                "package" => $cdata[2],
                "#paidwhen" => "NOW()",
                "success" => 1]);

            // Insert into payment
            $jakdb->insert("payment_ipn", [ 
                "clientid" => $cdata[1],
                "status" => $payment_status,
                "amount" => $payment_amount,
                "currency" => $payment_currency,
                "txn_id" => $txn_id,
                "receiver_email" => $receiver_email,
                "payer_email" => $payer_email,
                "paid_with" => "2Checkout",
                "#time" => "NOW()"]);

        }
                
} else {

    // Write the error into the log file
    file_put_contents('2co.ipn_results.log', "FAILURE\n\n" . $my2CO->ipnData);
         
    // log for manual investigation
    $mail = new PHPMailer(); // defaults to using php "mail()"
    $mail->SetFrom(JAK_EMAIL);
    $mail->AddAddress(JAK_EMAIL);
    $mail->AddReplyTo($payer_email);
    $mail->Subject = JAK_TITLE.' - 2Checkout HTTP error';
    $mail->Body = 'There is an error with 2Checkout, please check with 2Checkout.';
}
?>