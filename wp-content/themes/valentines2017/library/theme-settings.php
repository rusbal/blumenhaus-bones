<?php
use Rsu\Settings\ThemeSettings;
use Rsu\Settings\ThemeSettingsTab;

/**
 * Setting Form Assets
 */
function blu_admin_settings_enqueue() {
    $path = get_template_directory_uri() . '/includes/Rsu/Settings/assets';

    wp_enqueue_style('wp_theme_settings', $path . '/css/theme-settings.css');
    wp_register_script('wp_theme_settings', $path . '/js/theme-settings.js', array('jquery'));
    wp_enqueue_script('wp_theme_settings');
}

/**
 * Setting Fields
 */

$theme_settings_tabs = [
    'order'   => ['text' => 'Order', 'dashicon' => 'dashicons-clipboard' ],
    'general' => ['text' => 'General', 'dashicon' => 'dashicons-admin-generic' ],
    'contact' => ['text' => 'Contact', 'dashicon' => 'dashicons-phone' ],
    'map'     => ['text' => 'Map', 'dashicon' => 'dashicons-pressthis' ],
    'apikeys' => ['text' => 'API Keys', 'dashicon' => 'dashicons-admin-generic' ],
];

$theme_settings = [
    'tabs' => $theme_settings_tabs,

    // 'general'       => array('description' => 'A custom WordPress class for creating theme settings page'),

    'settingsID'    => 'blu_settings',

    'settingFields' => [
        'order' => [
            [
                'name' => 'blu_settings_order_email_recipients',
                'placeholder' => 'Comma-separated for multiple emails'
            ],
        ],
        'general' => [
            ['name' => 'blu_settings_company_name'],
            ['name' => 'blu_settings_address_line_1'],
            ['name' => 'blu_settings_address_line_2'],
            ['name' => 'blu_settings_country'],
        ],
        'contact' => [
            ['name' => 'blu_settings_email'],
            ['name' => 'blu_settings_telephone'],
            ['name' => 'blu_settings_fax'],
        ],
        'map' => [
            ['name' => 'blu_settings_latitude'],
            ['name' => 'blu_settings_longitude'],
        ],
        'apikeys' => [
            ['name' => 'blu_settings_google_api_key'],
        ],
    ],
];

new ThemeSettingsTab(
    new ThemeSettings($theme_settings),
    array_keys($theme_settings_tabs)
);
