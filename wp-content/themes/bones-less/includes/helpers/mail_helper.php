<?php
/**
 * Author: Raymond Usbal
 * Date: 07/11/2016
 */

function send_mail($subject, $message, $recipients = []) {
	$recipients = (array) $recipients;

	/**
	 * Default recipients
	 */
//	$recipients[] = 'matijevicstefan@gmail.com';
	$recipients[] = 'ingo.grunig@gmail.com';
	$recipients[] = 'raymond@philippinedev.com';

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	foreach ($recipients as $recipient) {
		mail($recipient, $subject, $message, $headers);
	}
}

