<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
 //Added by WP-Cache Manager
define( 'WPCACHEHOME', 'wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
/** The name of the database for WordPress */
define('DB_NAME', 'divino_db');
/** MySQL database username */
define('DB_USER', 'divino_admin');
/** MySQL database password */
define('DB_PASSWORD', 'Adm1n.2018');
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
/*
define('AUTH_KEY',         'P!3S*K;_3wPMp>aki7cm8TwhiS_^mu<\`K>Vo6R1lFuYqNShaN5sz6g/R_3NHGUG2Vq@vbXSI#*3?4m');
define('SECURE_AUTH_KEY',  'qFRrMPfCK_s*Gu-$uKcq6\`WS/\`s|P8m(Tkqtgc)~P7_JAdnBu-uHW88LS2~SZxkX(_*');
define('LOGGED_IN_KEY',    '6c:GMwhnP9!uYB693N#yUKt9-eh7TDv@pu;8p#EXDP$5;eqX70A*$gTZ)xkqW?SOqL6MUyaAZo)X');
define('NONCE_KEY',        ')1)@syLB6dY!8bG8nG/\`0R6@t;aR-B*yjq4ldIYo-r?AjG=jnB!Xh2<@XzGF_Y@j^TpArrw|YOnRtkvc');
define('AUTH_SALT',        'Kg0Er6ajd68LMCg6|3d_?u01mh?e6b;<Q24<ON^9s*|L08waP9Ua=F|M$@goP_)9bW;y~yw');
define('SECURE_AUTH_SALT', '<S(FLiwukz*mqrNA#@VSE=Y?kI?^VDXUZQD>\`4n=q2ObYZQVbUoKJAFb;SIZ2jLN|7P;XwhL:3@z(T');
define('LOGGED_IN_SALT',   '_ixMnZ5B|DRtm*pE)Lp#X8RV~jf\`=ebg)d;|0q@Um1(X>\`ugXjrIWRNYyGW#5uVQ\`l!');
define('NONCE_SALT',       'IPiPup401VzBGw>OdJ-14#$;Akw=yI)iFD_xjr=j_9By<x-9KRLIbUue2D5PAA:Kr6^PByK-llWmyLUITkI');
*/
define('AUTH_KEY',         '-,U0u`9B(&K[>BVa{ rw|-t$-G6UVu{9E%*pw0Lf1Sjj3;pi/2]^X#Nwm*d:@;c3');
define('SECURE_AUTH_KEY',  '~{6Ie!i4.~.yJXBpGyMBxGI|F(@5Q.u^7^ISP6<|0Llc+r-D0A/&]X1QUPMSmahx');
define('LOGGED_IN_KEY',    'E8g$S<HB/m^XGR[DaZN~&ou?^DDxB![zfpOWaqnyQjQI+2@Yj*`googOKhN#pI*R');
define('NONCE_KEY',        '6poD0t[Yf+p|9WIWlQgv-[eY*l(>nPD?RW9y2bO4KUx>5SBM{0N/XNL99$M+6Ole');
define('AUTH_SALT',        '%i>Tuk[Fr3;!#Yo3+n.@=@+(u^^TZeM_;&Gn{8(mq4w.J`(^H>)Tl<gy|]HC]~f8');
define('SECURE_AUTH_SALT', 'eFrpt#]U-TaH8R/(,?*MPMM3H_3#/hyhKXyQch$m8M<]Q=Vlc}>=g%)><v6_]$#7');
define('LOGGED_IN_SALT',   '+|sxTggWp,hQ2 .gdpeAj~u4j~=((3Y|@+`|p8ZPuWA|-d/J|$>JJBltgd0id84^');
define('NONCE_SALT',       '?}&pt*jajL:% {TzvYE+s+7I~(I(4.!~G)vD|3[9DrbLFqS^Z&0Cp/`!KCiaM(vr');
/**#@-*/
define('AUTOSAVE_INTERVAL', 600 );
define('WP_POST_REVISIONS', 1);
define( 'WP_CRON_LOCK_TIMEOUT', 120 );
define( 'WP_AUTO_UPDATE_CORE', true );
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
// Enable WP_DEBUG mode
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );