<?php

namespace Rsu\ContactForm\DbWriter;


/**
 * Class DbWriter
 * @package Rsu\ContactForm\DbWriter
 */
class DbWriterLogger implements LoggerInterface
{
    const FORM_NAME = 'Order form';

    const TABLE_SUBMIT_TIME = 'cf7dbplugin_st';
    const TABLE_SUBMITS = 'cf7dbplugin_submits';

    /**
     * @param array $data
     * @return boolean
     */
    public static function log($data)
    {
        $time = microtime(true);
        self::logToSubmitTimeTable($time);
        self::logToSubmitsTable($data, $time);
        return true;
    }

    private static function logToSubmitTimeTable($time)
    {
        global $wpdb;

        $wpdb->insert( self::submitTimeTable(), [
            'submit_time' => $time
        ]);
    }

    private static function logToSubmitsTable($data, $time)
    {
        global $wpdb;

        $submitsTable = self::submitsTable();

        $wpdb->insert( $submitsTable, [
            'submit_time' => $time,
            'form_name' => self::FORM_NAME,
            'field_name' => 'Mail',
            'field_value' => '',
            'field_order' => 0,
            'file' => null
        ]);

        foreach ($data as $idx => $arrValue) {

            $caption = key($arrValue);
            $value = $arrValue[$caption];

            $wpdb->insert( $submitsTable, [
                'submit_time' => $time,
                'form_name' => self::FORM_NAME,
                'field_name' => $caption,
                'field_value' => $value,
                'field_order' => ($idx + 1),
                'file' => null
            ]);
        }

        $wpdb->insert( $submitsTable, [
            'submit_time' => $time,
            'form_name' => self::FORM_NAME,
            'field_name' => 'Submitted From',
            'field_value' => self::userIpAddress(),
            'field_order' => 10000,
            'file' => null
        ]);
    }

    private static function userIpAddress() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    private static function submitTimeTable()
    {
        global $wpdb;
        return $wpdb->base_prefix . self::TABLE_SUBMIT_TIME;
    }

    private static function submitsTable()
    {
        global $wpdb;
        $tableName = 'cf7dbplugin_submits';
        return $wpdb->base_prefix . self::TABLE_SUBMITS;
    }
}