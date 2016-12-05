<?php

namespace Rsu\ContactForm\DbWriter;


/**
 * Interface LoggerInterface
 * @package Rsu\ContactForm\DbWriter
 */
interface LoggerInterface
{
    /**
     * @param array $data
     * @return boolean
     */
    public static function log($data);
}