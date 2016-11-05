<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define('DB_NAME', 'bullean_blumen');

/** MySQL database username */
//define('DB_USER', 'bullean_blumen');

/** MySQL database password */
//define('DB_PASSWORD', 'Klapaucius013#');

/** MySQL hostname */
//define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
//define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
//define('DB_COLLATE', '');

define('DB_NAME', 'wp_blumenhaus_live');
define('DB_USER', 'webapps_user');
define('DB_PASSWORD', 'F%j*sem');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '$^/:7]mR@.8TA8V0c@@kOtRcP}e(Cx/lMA- <,b&E x8SYa63c(PhU@nh/5zT_%`');
define('SECURE_AUTH_KEY',  '0Nfl{qX!N>;0](VKpoyGcOn}$/*)7c[:Hl8,6C[m8AM ^lHNV+hr{NL|a^Qa/[mo');
define('LOGGED_IN_KEY',    '0s{%YD0gP;Evvd71agY%aR}Yd|mClghZ<_2*S_/tRHm#N`EzPv@,F%P|u3di},*v');
define('NONCE_KEY',        'CkHsKSr[ aC#B$E-6w-}&K7(O=Q@?sUI:zS7ar g*Emf]J5==#qS (P:ulj%r11<');
define('AUTH_SALT',        'lyp2VL/AJXFOjK&U>Ng#rQh9iV]sBE5o*v{?<8r[/y6`8k[z0gM7S$`6`%>.tq0/');
define('SECURE_AUTH_SALT', '0Uq,NAoKd_+tT/]CA5@f=i7hJgkvMv~OJ;!3UwYnjXB;W~vP5xDfTNfCsNBdJ=o;');
define('LOGGED_IN_SALT',   '/[V1Q0`lfLU*g=PSP,!|W1U<ltAm;-6Jg+J{z5/[:HHn[#YrLO+,`&@vrn{ ?(%%');
define('NONCE_SALT',       '~x-VXYFr4uCeuSw7L_]L+lZji28)]<Aby]<H9cx3a*z?1$YNWU0sMUoe}~BJ.@OG');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
