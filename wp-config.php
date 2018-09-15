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
define('DB_NAME', 'trunghao_wp199');

/** MySQL database username */
define('DB_USER', 'trunghao_wp199');

/** MySQL database password */
define('DB_PASSWORD', '27U-SpR!o4');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'hsjsdw1fgjcdemcevo0cuveawev1eczw2hamkxrqqmyv58z7me68pmfkgjkqrd5q');
define('SECURE_AUTH_KEY',  '7vaaivrong1llp6o3mwvxc3hvaxcv583vedh23s7d6xcnjyp1vpgmir6lglzu1lo');
define('LOGGED_IN_KEY',    'pmjwsoblhyvpkzjoznsxar0pinu6uovlzr0mkunnkbhbfo8r5eilohzk5q6wbpul');
define('NONCE_KEY',        'ihnd5xshsdfwgwv8tfj8rdzebac3kjy5d6epy3vgsnytsltfk1wkawnabxleouwy');
define('AUTH_SALT',        'vjguwrzyzfznjhde2qw7i3ahtqgrnd3pyqaw61wx4g6i6d9zfgbqspnpt4uyyd0v');
define('SECURE_AUTH_SALT', '0jwptylnpo5ezafgtmm9zqzmq67ujrmrsbqwkyecd1qaggmwywsaaiqsrqvspru8');
define('LOGGED_IN_SALT',   'mlc6zdama8ieq6bwptf1fayumrjoqosrhqw868rqx8ifvl8uvvqpmmwfddvctecq');
define('NONCE_SALT',       'ddxxb16rtlrkwdqccg2kyoron4ihadvg92vixaosbxmslci9j1vqbfdptamaz4nn');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp5i_';

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
define( 'WP_MEMORY_LIMIT', '128M' );
define( 'WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
