<?php
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
if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], array( 'localhost', '127.0.0.1' ), true ) ) {
	define( 'DB_NAME', 'DB-KV-ELECTRONICS' );
	define( 'DB_USER', 'root' );
	define( 'DB_PASSWORD', '' );
	define( 'DB_HOST', 'localhost:3306' );
	define( 'WP_HOME', 'http://localhost/kv-electronics' );
	define( 'WP_SITEURL', 'http://localhost/kv-electronics' );
	define( 'FS_METHOD', 'direct' );
	define( 'FS_CHMOD_DIR', 0755 );
	define( 'FS_CHMOD_FILE', 0644 );
} else {
	define( 'DB_NAME', 'DB-KV-ELECTRONICS' );
	define( 'DB_USER', 'wp_whicu' );
	define( 'DB_PASSWORD', 'K2xg#$5KgIaU9nw9' );
	define( 'DB_HOST', 'localhost:3306' );
}

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
define('AUTH_KEY', 'y8GbHxAVHAi-fl_kD9YNuZ7ccovu)j4+BC(bE~7H28XQan1(6o4LWe1B;[+cO6W1');
define('SECURE_AUTH_KEY', '8j8UKJZ!mvR&]q#eI-D9qI%7n|k9yk6+d_~IX1nbH&M3d]vc@v6liDHP5H0VFD2Y');
define('LOGGED_IN_KEY', 'zA%dUjb5D8sUKEH!7o(bNkMkVq-6%iy;U;e_657[dEsdXw2C4%DV4LG4qipRv3w9');
define('NONCE_KEY', 'k1T_148fkw!akZ)#37J5472(6:!4m%R#+B1f~@02FP+MzpS(e-6Y*_AgjIKzv5Be');
define('AUTH_SALT', 'E7S#n9SF5A6StY:n#%1cDtq4C0aRgIihY9)2_Q#[-]5:0|+!v;]3]L[qXqT:%KxX');
define('SECURE_AUTH_SALT', '6F@040Tdqti2!~/n/c~kOW_FGk9y37-1@EvFTq4AEPM4p~crq]0T1Rp(:Zjs2Yts');
define('LOGGED_IN_SALT', 'm&7~0S+/@Orj;8wjZu9B3K|-70@%)R668w0!ioV|x)7Y)Q8P9&zwhVZ95C5NapTO');
define('NONCE_SALT', 'A3128K)C4V43b-/%!12vB4Qj(UU(62JOB:9Dw[_(RZ&;x98y4w#@*/GxiASG7e1d');


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

// Security hardening.
define( 'DISALLOW_FILE_EDIT', true );

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
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
