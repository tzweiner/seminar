<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'seminarwordpress');

/** MySQL database username */
define('DB_USER', 'seminarwordpress');

/** MySQL database password */
define('DB_PASSWORD', 'Sem!n4r06');

/** MySQL hostname */
define('DB_HOST', 'seminarwordpress.db.8564458.hostedresource.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
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
define('AUTH_KEY',         'fSY8hp(Dad[pFV/Scb5|<5j}.MCH~X~k{g:PTQzh-mkSV$97Xx)%#a#WoL;bJo#+');
define('SECURE_AUTH_KEY',  '/1h!E%am|`gii<+/rZ2*64|i`iwuaof[##<{u5iKe%Mq4_2dV%uxOaOih37Ege6*');
define('LOGGED_IN_KEY',    '6BcSd;1nGZ?IC3EPg)dHV*b)&>-fLTPzgmW*;I+WU9,q<pt~0|5kw-D$2+%y2%sn');
define('NONCE_KEY',        '+|o1R9+Ml&>-[![P`qM8D?o[.#6Gl(+N&|bU$HFhHY9,W$NP1xuh7Dp4. +N5t[Z');
define('AUTH_SALT',        'g-/?hN%D7!9T^=A0:+dT`K+|kAy%vZC0tGNv?iy{``+qPa1Ly^@D(s&WK9i*`BGC');
define('SECURE_AUTH_SALT', 'TUau.-f)Z;rC[_b|MPz]I9@Zr-zzRipZJ`bAL!E<QRm+%X2qW7/Xf+|NJq0/t2iK');
define('LOGGED_IN_SALT',   '|mGNUTHuf`fXTvLL&d*TrMn)EiiD7|w~w6(/8/WMzAcqPL6 -GgdH54z7kjl]wyW');
define('NONCE_SALT',       'SFR-cs0bH;g6~C[O lWl{y^pmUM-?$z,@jZ2c{Aq+k`3K!B(c|D)?-IaU[tv{z|K');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
