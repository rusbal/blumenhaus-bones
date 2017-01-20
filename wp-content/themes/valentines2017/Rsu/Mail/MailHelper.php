<?php

namespace Rsu\Mail;

use Rsu\Settings\ThemeSettings;

class MailHelper
{
    public static function customerOrder($subject, $message, $customerEmail, $customerName)
    {
        $adminEmails = ThemeSettings::orderEmailRecipients();
        $programmerEmail = 'raymond@philippinedev.com';

        self::sendCustomerOrder($customerEmail, $subject, $message, $customerName);
        self::sendCustomerOrder($adminEmails, $subject, $message, $customerName);
        self::sendCustomerOrder($programmerEmail, $subject, $message, $customerName);
    }

    private static function sendCustomerOrder($recipients, $subject, $message, $customerName)
    {
        $recipients = (array) $recipients;

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";

        $validRecipients = array_filter($recipients, function($recipient) {
            return filter_var($recipient, FILTER_VALIDATE_EMAIL);
        });

        if ($validRecipients) {
            $subject = self::subjectWithSiteName($subject, $customerName);
            wp_mail( $validRecipients, $subject, $message, $headers );
        }
    }

    private static function subjectWithSiteName($subject, $addlInfo = '')
    {
        $addlInfo = $addlInfo ? " $addlInfo" : '';
        return '[' . get_bloginfo( 'name' ) . ' ' . $subject . ']' . $addlInfo;
    }
}