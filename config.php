<?php
/**
 * Config
 * ===============================================================================
 * @package   WPTwitterAPI
 * @since     1.0.0
 */

global $wpdb;


/**
 * Plugin version, used for cache-busting of style and script file references.
 * @since  1.0.0
 */
define('TAPI_VERSION', '1.0.0');


/**
 * Unique identifier for your plugin.
 * Use this value (not the variable name) as the text domain when
 * internationalizing strings of text. It should match the Text Domain file
 * header in the main plugin file.
 * @since  1.0.0
 */
define('TAPI_SLUG', 'twitter_api');


/**
 * What version is Twitter's API
 * @since  1.0.0
 */
define('TAPI_TWITTER_VERSION', '1.1');


define('TAPI_TABLE_CACHE',  $wpdb->get_blog_prefix().TAPI_SLUG.'_cache');