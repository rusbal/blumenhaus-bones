<?php

namespace Rsu\Settings;


/**
 * Class Register
 * @package Rsu\Settings
 *
 * http://www.wp-load.com/register-settings-api/usage/field-types/
Field types

Standard html form field types.

Checkbox
Multiselect
Radio
Select
Text, email, password etc
Textarea
Button

javascript - Non standard types made width javascript.

Colorpicker
Datepicker
Image
File
Tinymce

not - Not field types but solutions.

oEmbed
Post object / url
Shortcode
 */
class Register
{
    protected $plugin;

    public function __construct($pluginSettings) {
        $this->plugin = $pluginSettings;

        add_filter('register_settings_api', array( $this, 'settings_array' ) );
    }

    function settings_array( $options_page ) {
        $options_page[$this->plugin->plugin_name] = [
            'menu_title' => $this->plugin['name'],
            'page_title' => $this->plugin['name'],
            'option_name' => $this->plugin['plugin_name'],
            'tabs' => $this->plugin['tabs'],
        ];
        return $options_page;
    }
}