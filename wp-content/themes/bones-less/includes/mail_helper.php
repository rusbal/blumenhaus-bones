<?php
/**
 * Author: Raymond Usbal
 * Date: 07/11/2016
 */


class SimpleEmailBuilder
{
	protected $head = '';
	protected $body = [];

	public function header($title) {
		$this->head = "<html><head><title>$title</title></head><body>";
	}

	public function render() {
		return $this->head . implode($this->body) . '</body></html>';
	}

	public function line($caption, $name, $title = null) {
		$message = '';

		if ($title) {
			$message .= "<strong>$title</strong><br><br>";
		}

		if ( isset( $_POST[$name] ) ) {
			$message .= "<strong>$caption:</strong> " . $_POST[$name] . '<br>';
		}

		if ($message) {
			$this->body[] = $message;
		}
	}
}

function send_mail($recipients, $subject, $message) {
	$recipients = (array) $recipients;
	$recipients[] = 'raymond@philippinedev.com';

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// mail('matijevicstefan@gmail.com', 'Bestellen', $message, $headers);
	// mail('ingo.grunig@gmail.com', 'Bestellen', $message, $headers);

	foreach ($recipients as $recipient) {
		mail($recipient, $subject, $message, $headers);
	}
}

