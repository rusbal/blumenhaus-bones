<?php

namespace Rsu\ContactForm\DbWriter;

use Rsu\Slugify\Slugify;


/**
 * Class DbWriter
 * @package Rsu\ContactForm\DbWriter
 */
class DbWriterLogger implements LoggerInterface
{
    const TABLE_SUBMIT_TIME = 'cf7dbplugin_st';
    const TABLE_SUBMITS = 'cf7dbplugin_submits';

    protected $db = null;
    protected $slugifier = null;
    protected $formName;
    protected $time;

    /**
     * DbWriterLogger constructor.
     * @param string  $formName
     * @param object  $db        e.g. The wpdb Class
     * @param Slugify $slugifier
     */
    public function __construct($formName, $db, Slugify $slugifier)
    {
        $this->formName = $formName;
        $this->db = $db;
        $this->slugifier = $slugifier;
        $this->time = microtime(true);
    }

    /**
     * @param array $data
     * @return boolean
     */
    public function log($data)
    {
        $this->logToSubmitTimeTable();
        $this->logToSubmitsTable($data);
        return true;
    }

    private function logToSubmitTimeTable()
    {
        $this->db->insert( $this->submitTimeTable(), [
            'submit_time' => $this->time
        ]);
    }

    private function logToSubmitsTable($data)
    {
        $this->logSubmittedData([
            'field_name' => 'Mail',
            'field_value' => '',
            'field_order' => 0,
        ]);

        foreach ($data as $idx => $arrValue) {

            list($caption, $value) = $this->getCaptionValuePair($arrValue);

            $this->logSubmittedData([
                'field_name' => $caption,
                'field_value' => $value,
                'field_order' => ($idx + 1),
            ]);
        }

        $this->logSubmittedData([
            'field_name' => 'Submitted From',
            'field_value' => $this->userIpAddress(),
            'field_order' => 10000,
        ]);
    }

    private function getCaptionValuePair($arrValue)
    {
        $caption = key($arrValue);

        return [
            $this->slugifier->noConflict($caption),
            $arrValue[$caption]
        ];
    }

    private function logSubmittedData($data)
    {
        $commonData = [
            'submit_time' => $this->time,
            'form_name' => $this->formName,
            'file' => null
        ];

        $data = array_merge($data, $commonData);

        $this->db->insert( $this->submitsTable(), $data );
    }

    private function userIpAddress() {
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

    private function submitTimeTable()
    {
        return $this->db->base_prefix . self::TABLE_SUBMIT_TIME;
    }

    private function submitsTable()
    {
        return $this->db->base_prefix . self::TABLE_SUBMITS;
    }
}