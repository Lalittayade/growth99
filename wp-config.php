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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'growth99' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'VANsdxs{2STUVk-3g[lM}bBH3s;Kj!=`6z:=sHz9nrbd b/@$43#yyfzdW8|phJa' );
define( 'SECURE_AUTH_KEY',  'iS}H,0!<Tol4RiK|T[3)[<vp/bLU:TDeln1Bw%nXAXSB[xc*M9g?r$g?5ww]:8hZ' );
define( 'LOGGED_IN_KEY',    '1L/gA)l@aN IKVf_K,z/Qg<j[`z|cJd?5+QQIvg(0TcO[ykPx2fr5q+{-Aq`9*=F' );
define( 'NONCE_KEY',        'M?HY79&Q}kJW_A]a.gKag<:9nu.=ShGp_-Nz*uCMU1$BbfRT{u6JAlUCs;@:!tD*' );
define( 'AUTH_SALT',        '#VT*y])zXwZ3pUIgiMGx7N*9{XN1amcRuNR/aHO8:FLWq^c@04N}NNGFVs=~Z(=^' );
define( 'SECURE_AUTH_SALT', 'XE(DvZG |#e,o9Cu3}-scF-6 +I^=Cas>m:~uM;0r7n/GPXWxqL! ,W6+S++Mp=~' );
define( 'LOGGED_IN_SALT',   'KV/LuAa|tnCOH^D.v`lciEJli),`xF (O8ty>u|@j1Z3Bw;ercrgOO>P v M_]`?' );
define( 'NONCE_SALT',       'z&C5r~M-(BRyY)1TH0_;r$s`KP,ox[Fey7bFhM/[p4<I;Q+d?MDME$-:7aX;TcZh' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
