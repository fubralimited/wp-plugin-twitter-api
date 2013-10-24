<?php
/**
 * @package  Twitter API
 * @author   Neil Sweeney <neil@wolfiezero.com>
 * @license  GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Twitter API
 * Description: Provides easy access to the Twitter API
 * Version:     1.1.1
 * Author:      Neil Sweeney
 * Author URI:  http://wolfiezero.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( !defined('WPINC') ) die;

// Request the config file.
require_once plugin_dir_path( __FILE__ ) . 'config.php';

// Request the Twitter API library
require_once plugin_dir_path( __FILE__ ) . 'lib/TwitterAPIExchange.php';

// Request the class file (where the work is done).
require_once plugin_dir_path( __FILE__ ) . 'class-twitter-api.php';

// Register hooks that are fired when the plugin is activated, deactivated, and
// uninstalled, respectively.
register_activation_hook( __FILE__, array( 'TwitterAPI', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TwitterAPI', 'deactivate' ) );

// AJAX function access
// ajax for logged in users
add_action( 'wp_ajax_twitter_api_query', array( 'TwitterAPI', 'ajax_query' ) );
// ajax for not logged in users
add_action( 'wp_ajax_nopriv_twitter_api_query', array( 'TwitterAPI', 'ajax_query' ) );

// Create the class instance.
add_action( 'plugins_loaded', array( 'TwitterAPI', 'get_instance' ) );