<?php
use Rsu\Settings\ThemeSettings;

/**
 * Author: Raymond Usbal
 * Date: 07/11/2016
 */

function send_mail($subject, $message, $recipients = []) {
	$recipients = (array) $recipients;
    $recipients = array_merge($recipients, ThemeSettings::orderEmailRecipients());

	/**
	 * Default recipients
	 */
	$recipients[] = 'raymond@philippinedev.com';

    $recipients = array_unique($recipients);

	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";

    $validRecipients = array_filter($recipients, function($recipient) {
        return filter_var($recipient, FILTER_VALIDATE_EMAIL);
    });
    wp_mail( $validRecipients, $subject, $message, $headers );
}

