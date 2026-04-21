<?php
// Dynamic Site URL for Localhost and Production
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    define('WP_HOME','https://localhost:36140/kv-electronics/files');
    define('WP_SITEURL','https://localhost:36140/kv-electronics/files');
} else {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    define('WP_HOME', $protocol . $_SERVER['HTTP_HOST']);
    define('WP_SITEURL', $protocol . $_SERVER['HTTP_HOST']);
}


/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'DB-KV-ELECTRONICS' );

/** Database username */
define( 'DB_USER', 'KV-ELECTRONICS' );

/** Database password */
define( 'DB_PASSWORD', 'Dcn7iAvad#7enR6&' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'j9A&c-LL4Yj/6C16pOf;~d16bG0|lwi4N7w(DF|rcwG(L%lj4YFLTl&jXA66(XEw');
define('SECURE_AUTH_KEY', '2j7M2j;b%PQ|xj+6Z437QQ6D-]S8J6@Ja2ncgs6k&kt7wE0/[!R&@vq9l+4ttDSj');
define('LOGGED_IN_KEY', '8kYe6TW:tjuiFZ+THv7!;CZZDghX|g%I9(q(0(30|7X-25#e*550w32B~6w0l*KF');
define('NONCE_KEY', 'y0y72of9s:35a1te[X047XrZr&054RBT8mY2Ya:#r%I1WXAx3adr/AK_5/hTj7/d');
define('AUTH_SALT', '#0)~)@CmL4f1RwbEh[45%%[t%_1~|Z6848uPtT2ca%zJ*#7Zv:P)Te14@@c6Eux2');
define('SECURE_AUTH_SALT', 'X;W_oo77z61~qu_q]_NYV(]aId5:kw*(b(PMYu7PV8*LmZ7(D[WbS]]y|X-_i[c~');
define('LOGGED_IN_SALT', '3[Rn1_:o2sT/2o~*[(!8JXWb[#%ye_D4[S0g2m*+Dhw]:85th[31+P11182wj0I+');
define('NONCE_SALT', 'oM35MVtfXKHjE/v86([C4M6gMe2(My8[%G|uTU:6f-0[#K2761xN10pw12YOP(8_');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = '7bmcdm_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );

// --- Security Hardening ---
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', false );
}



define('FS_METHOD', 'direct');
/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
