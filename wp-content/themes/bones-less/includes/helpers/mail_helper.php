<?php
use Rsu\Settings\ThemeSettings;

/**
 * Author: Raymond Usbal
 * Date: 07/11/2016
 */

function send_mail($subject, $message, $recipients = []) {
	$recipients = (array) $recipients;
    $recipients = array_filter($recipients);
    $recipients = array_merge($recipients, ThemeSettings::orderEmailRecipients());

	/**
	 * Default recipients
	 */
//	$recipients[] = 'matijevicstefan@gmail.com';
//	$recipients[] = 'ingo.grunig@gmail.com';
	$recipients[] = 'raymond@philippinedev.com';

    $recipients = array_unique($recipients);

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	foreach ($recipients as $recipient) {
		if(filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
			mail( $recipient, $subject, $message, $headers );
		}
	}
}

