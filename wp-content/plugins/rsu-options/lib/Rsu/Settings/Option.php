<?php

namespace Rsu\Settings;


class Option
{
    protected static $settings;

    public static function get_fields()
    {
        if (! isset(self::$settings)) {
            self::$settings = get_option('rsu_theme_settings');
        }
        return self::$settings;
    }

    public static function get($key)
    {
        if (! isset(self::$settings)) {
            self::$settings = get_option('rsu_theme_settings');
        }
        return self::$settings[$key];
    }

    public static function get_image($key, $size = null)
    {
        if (is_null($size)) {
            $width = self::get('header_logo_width') ?: 175;
            $height = self::get('header_logo_height') ?: 175;
            $size = [$width, $height];
        }
        return wp_get_attachment_image( self::get($key), $size );
    }

    public static function get_csv_lines($key)
    {
        $data = [];
        $csvString = self::get($key);

        if ($csvString) {
            $csvLines = explode("\n", $csvString);
            foreach ($csvLines as $csvLine) {
                $data[] = array_map('trim', explode(',', $csvLine));
            }
        }

        return json_encode($data);
    }

    public static function companyNameDesc()
    {
        return self::implode(' | ', ['company_name', 'company_description']);
    }

    public static function implode($glue, $keys)
    {
        return implode(
            $glue,
            array_map(function($key){
                return self::get($key);
            }, $keys)
        );
    }
}