<?php
/**
Plugin Name: Theme Options for Register Settings API
   Version: 1.0.0
   Author: Raymond Usbal
   Description: Theme Options Functionality.  This plugin builds on the functionality of
      the plugin "Register Settings API" by Jens Törnell.
   Date: 08/12/2016
*/

require 'vendor/autoload.php';


$deliveryCostTab = [
    'tab_title' => 'Cost',
    'fields' => [
        'card_cost' => [ 'type' => 'text', 'title' => 'Card cost' ],
        'delivery_cost' => [ 'type' => 'textarea', 'title' => 'Delivery CSV' ],
        'delivery_cost_on_request' => [ 'type' => 'text', 'title' => 'Delivery cost on request' ],
    ],
];
$logoTab = [
    'tab_title' => 'Logo',
    'fields' => [
        'header_logo' => [
            'type' => 'image',
            'title' => 'Header Logo',
        ],
        'header_logo_width' => [ 'type' => 'number', 'title' => 'Logo Width' ],
        'header_logo_height' => [ 'type' => 'number', 'title' => 'Logo Height' ],
        'header_banner' => [
            'type' => 'image',
            'title' => 'Header Banner',
        ],
        'description' => [ 'type' => 'tinymce', 'title' => 'Description' ],
    ],
];

$generalTab = [
    'tab_title' => 'General',
    'fields' => [
        'company_name' => [ 'type' => 'text', 'title' => 'Company Name' ],
        'company_description' => [ 'type' => 'text', 'title' => 'Company Description' ],
        'address_line_1' => [ 'type' => 'text', 'title' => 'Address Line 1' ],
        'address_line_2' => [ 'type' => 'text', 'title' => 'Address Line 2' ],
        'telephone' => [ 'type' => 'text', 'title' => 'Telephone' ],
        'fax' => [ 'type' => 'text', 'title' => 'Fax' ],
        'email' => [ 'type' => 'text', 'title' => 'Email' ],
        'rss' => [ 'type' => 'text', 'title' => 'RSS' ],
        'twitter' => [ 'type' => 'text', 'title' => 'Twitter' ],
        'facebook' => [ 'type' => 'text', 'title' => 'Facebook' ],
        'google' => [ 'type' => 'text', 'title' => 'Google' ],
    ],
];

$googleMapsTab = [
    'tab_title' => 'Goggle Maps',
    'fields' => [
        'google_api_key' => [ 'type' => 'text', 'title' => 'Google API Key' ],
        'map_latitude' => [ 'type' => 'text', 'title' => 'Map Latitude' ],
        'map_longitude' => [ 'type' => 'text', 'title' => 'Map Longitude' ],
    ],
];

new Rsu\Settings\Register([
    'plugin_name' => 'rsu_theme_settings',
    'name' => 'Delivery Cost',
    'tabs' => [
        $deliveryCostTab,
//        $logoTab,
//        $generalTab,
        $googleMapsTab,
    ],
]);
