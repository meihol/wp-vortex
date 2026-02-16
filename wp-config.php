<?php
define( 'WP_CACHE', true );
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
define( 'DB_NAME', 'u731710703_voRtXDbsD' );

/** Database username */
define( 'DB_USER', 'u731710703_voRtXDbsU' );

/** Database password */
define( 'DB_PASSWORD', 'IVSX@=Oo+t*9' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          'PiRQCf`EbTz8Wjk [(l<U7=eb(VH4<f>hn{6b:ET[E6,N)6T=Z#?*Z8qrFJB~1=$' );
define( 'SECURE_AUTH_KEY',   '}B+TyHsVq$5U|28jp}w.ZX,Ep<2?/nkUvlnh1zrb(lqZ37/A(wBIxLLG_1%~1UMz' );
define( 'LOGGED_IN_KEY',     'J*U5_cN)Cvbt{_?Zw&WR]|~!y<;XP^_vOwT9$ZdIb0cUoT$FoK]?=A(F4v&kfFTg' );
define( 'NONCE_KEY',         '+G`hp::~>%D>@ !e!vVaM[dECgBpkZ]=GaB1fnr_VM1vp)3xf.`u{%K!ar};c(<s' );
define( 'AUTH_SALT',         'dhhEyDXgfjs; 7@HA^k=:DC11dJS{Pw![[dk8dOkk!^AaPU0tfx>/7zSi@zjT%fM' );
define( 'SECURE_AUTH_SALT',  ',#Oxf[;<SL+lcy 56@kTjih>ZXZGB/RT6G^,CeZBl[O#iyU@L8x~*`3v7On0*by*' );
define( 'LOGGED_IN_SALT',    'ZEw6Gk(,D/2XMwN^6ta/.${9y&&z6dbp i*,Ppgj{?$JuCu#l)AM^mQacnR1w]_c' );
define( 'NONCE_SALT',        '+)Em|~#I;Ivsq(#@*ybv]<?WqU[kgC-5(e0QR}CdLA?rN=Vdnmn `6A%k$&gLnOZ' );
define( 'WP_CACHE_KEY_SALT', '{jxYb>*q_QH$_)Jd.yv4L5hWjqS+Ogkk+W!&!T3%Q5QB*Dud{AmQ*h<45^p(pumL' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISABLE_WP_CRON' , true );


/* Add any custom values between this line and the "stop editing" line. */



define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', true );
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISABLE_WP_CRON' , true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
