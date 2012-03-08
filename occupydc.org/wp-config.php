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
 
 define('WP_SITEURL', 'http://localhost:8080/occupydc.org');
define('WP_HOME', 'http://localhost:8080/occupydc.org'); 

define('RELOCATE',true);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'main');

/** MySQL database username */
define('DB_USER', 'admin');

/** MySQL database password */
define('DB_PASSWORD', '448807');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '!Q;<1yt$EAz_+VRA^EJ+j{$w,8,~V1^UmGb6VoKi7ElhYLWzT%$={;3]!Td9m@^)');
define('SECURE_AUTH_KEY',  'PoaiOW!Vuwn+2>oy=,0Tc]i&UmY%%uG^2h5_H}%7`6CPyqd3XyF) F6V6RH_Dc9t');
define('LOGGED_IN_KEY',    'kJaFOq}Ytk~t_`;qkoTsAX:LP!03(E0Bu5l(+<tox8#x1SZu?U7}KBlJJw^p8jJ^');
define('NONCE_KEY',        '7;AS;ZfGat*TaS7)3c>_$qdw1lvn@.kt]=tqxZKY^NG<(B.WAO]@9Efc.P(`9=z-');
define('AUTH_SALT',        '`X0 HGhWzd~u|Qbg~9FG99FW?y]g*LhSwotV^}[%Q8OX~3$ kSOVEiaB8Ixe}?aT');
define('SECURE_AUTH_SALT', '|w|MW[dsXrUz?~0[!E+hVcAZZU }qomH*RESN4<0/OddPDx2v Ws?s9aRCQ[|qlq');
define('LOGGED_IN_SALT',   ' Sg!_:V.`G$l`_EU`pKZ1F }<aLouRF@M]x_II.d!U1.FMJ&G0)ucR(@bC0u6I4X');
define('NONCE_SALT',       'c!VwFwS3;D8{A-G~QZ5%[H(5W14Z7}]hq[Jh@uishF#Sp]A eo$F_oo6ds>if^<U');

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
