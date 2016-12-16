=== Register Settings API ===
Contributors: Jenst
Tags: settings api, admin, wp-admin, options
Requires at least: 4.0
Tested up to: 4.1
Stable tag: 1.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add settings to your own theme or plugin. As simple as writing an array.

== Description ==

Add settings to your own theme or plugin. As simple as writing an array.

Full documentation at: http://www.wp-load.com/register-settings-api/

= Benefits of the plugin =

*   Work fine as a plugin OR as included to your theme or plugin.
*   Everything you need is in one single file.
*   No more fighting the settings API. Just add your settings and you are done.
*   Tinymce, textarea, checkboxes, radio buttons, url, color, email supported.
*   It will from the array create a menu and a settings page with tabs and fields.

= Full example, placed in functions.php OR your plugin =

`add_filter('register_settings_api', 'settings_array');

function settings_array( $options_page ) {
	$options_page['my-menu-slug'] = array(
		'menu_title' => 'My menu',
		'page_title' => 'My page',
		'option_name' => 'my_options_slug',
		'tabs' => array(
			'first' => array(
				'tab_title' => 'My first tab',
				'fields' => array(
					'my_editor' => array(
						'type' => 'tinymce',
						'title' => 'My editor'
					),
					'my_color' => array(
						'type' => 'color',
						'title' => 'My color'
					)
				),
			),
			'second' => array(
				'tab_title' => 'My second tab',
				'fields' => array(
					'my_textarea' => array(
						'type' => 'textarea',
						'title' => 'My textarea'
					)
				)
			)
		)
	);
	return $options_page;
}`

== Installation ==

To use as a plugin:

1. Upload `register-settings-api` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the array code (described in another tab) in your functions.php file OR in your plugin

To use as a class:

1. Upload `register-settings-api.php` into your theme or plugin and include it
1. Remove the comment on the top and only keep the class. Else it might belive it's a plugin
1. It's recommended to rename the class, the instance and the class_exists class to prevent collision with other plugins
1. Place the array code (described in another tab) in your functions.php file OR in your plugin

== Frequently Asked Questions ==

= Why use this plugin when there are a settings API that work fine? =

Speed. It should take just a few minues to set up a whole lot of settings for your plugin or theme.

= What makes this better than other plugins like this? =

Simplicity. It's one file to include (if not using the plugin directly). There is one array to write.

= I want an advanced settings page not looking like default WordPress style. Is this plugin for me? =

No, probably not. This plugin makes it simple to set up tabs with settings the "default" way, not that much more. It's made for speed not complexity.

= I don't see any tabs. Why not? =

If you only created ONE tab there are no meaning of adding tabs. Then it will not add the tabs. You need two or more tabs to get them visible.

== Screenshots ==

1. Full example. This is what it creates. Much more types than this available.

== Changelog ==

= 1.4 =
* Many bug fixes
* Array values "tab_description", "before_tabs_text" and "after_tabs_text" added
* "button" and "custom" field type added
* "Get" now supports args

= 1.3 =
* Minor bug fixes
* Change color field type to colorpicker to prevent collision with html5 color field.
* Change date field type to datepicker to prevent collision with html5 color field.
* Added color (html5) field type
* Added date (html5)field type

= 1.2 =
* Fixed bug with color field, JS error in some cases

= 1.1 =
* Warnings: More checks, less warnings
* Title: Before it was named "description". It's used in the column to the left
* Description: It's used below the field
* Empty: Add empty option to select boxes
* Default: Add default value when no value is saved
* Size: Added size to textarea, tinymce and multiselect
* Get: Select dynamic values like posts, users, plugins, themes, menus, terms, taxonomies, sidebars, post_types
* New types: image, file, date, multiselect

= 1.0 =
* Initial release