<?php
/**
 * Twitter API class
 *
 * @package  Twitter API
 * @author   Neil Sweeney <neil@wolfiezero.com>
 */
class TwitterAPI {

    // == Variables ===========================================================

    /**
     * Instance of this class
     * 
     * @since  1.0.0
     * 
     * @var    object
     */
    private static $_instance = null;

    /**
     * Display debug info
     *
     * @since  1.0.0
     *
     * @var    boolean
     */
    private static $_debug = false;

    /**
     * Root URL
     *
     * @since  1.0.0
     *
     * @var    string
     */
    private static $_root = 'https://api.twitter.com/1.1/';


    /**
     * Initialize the plugin by setting localization, filters, and 
     * administration functions.
     * @since  1.0.0
     */
    private function __construct () {

        // Load plugin text domain
        //add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        // Add the options page and menu item.
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        // Load admin style sheet and JavaScript.
        //add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        //add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // Load public-facing style sheet and JavaScript.
        //add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

    }


    /**
     * Register and enqueues public-facing JavaScript files.
     * @since  1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script('twitterplatform', 'http://platform.twitter.com/widgets.js', false, false, true);
   
    }


    /**
     * Creates or returns an instance of this class.
     * 
     * @since   1.0.0
     * 
     * @return  object  A single instance of this class.
     */
    public function get_instance () {

        return null == self::$_instance ? new self : self::$_instance;
    
    }


    /**
     * Fired when the plugin is activated.
     * 
     * @since  1.0.0
     * 
     * @param  boolean  $network_wide  True if WPMU superadmin uses "Network
     *                                 Activate" action, false if WPMU is 
     *                                 disabled or plugin is activated on an 
     *                                 individual blog.
     */
    public static function activate ($network_wide) {

        global $wpdb;

        // WP Options
        add_option(TAPI_SLUG.'_consumer_key', '');
        add_option(TAPI_SLUG.'_consumer_secret', '');
        add_option(TAPI_SLUG.'_oauth_access_token', '');
        add_option(TAPI_SLUG.'_oauth_access_token_secret', '');
        add_option(TAPI_SLUG.'_use_cache', 'Y');
        add_option(TAPI_SLUG.'_expiration_time', 15);

        // Database
        $sql = '
            CREATE TABLE `'.TAPI_TABLE_CACHE.'` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `uri` varchar(255) NOT NULL DEFAULT \'\',
                `data` text NOT NULL,
                `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uri` (`uri`),
                KEY `cache_reference` (`uri`)
            )
        ';
        $success = $wpdb->query($sql);

        return $success;

    }


    /**
     * Fired when the plugin is deactivated.
     * 
     * @since  1.0.0
     * 
     * @param  boolean  $network_wide  True if WPMU superadmin uses "Network
     *                                 Deactivate" action, false if WPMU is 
     *                                 disabled or plugin is deactivated on an
     *                                 individual blog.
     */
    public static function deactivate ($network_wide) {

        global $wpdb;

        // WP Options
        delete_option(TAPI_SLUG.'_consumer_key');
        delete_option(TAPI_SLUG.'_consumer_secret');
        delete_option(TAPI_SLUG.'_oauth_access_token');
        delete_option(TAPI_SLUG.'_oauth_access_token_secret');
        delete_option(TAPI_SLUG.'_expiration_time');
        delete_option(TAPI_SLUG.'_use_cache');

        // Database
        $sql     = 'drop table `'.TAPI_TABLE_CACHE.'`;';
        $success = $wpdb->query($sql);

        return $success;

    }


    /**
     * Register the administration menu for this plugin into the WordPress
     * Dashboard menu.
     * 
     * @since  1.0.0
     */
    public function add_plugin_admin_menu() {

        add_options_page(
            'Twitter API',
            'Twitter API',
            'edit_plugins',
            TAPI_SLUG.'_settings',
            array($this, 'display_plugin_admin_page')
        );

    }


    /**
     * Render the settings page for this plugin.
     * @since  1.0.0
     */
    public function display_plugin_admin_page() {

        include_once('view/settings.php');

    }


    // ========================================================================


    public function query ($uri, $param='') {

        $twitter   = null;
        $data      = null;
        $use_cache = get_option(TAPI_SLUG.'_use_cache');

        // May seem a bit odd, but it allows users to not repeat the same URL
        // in their code multiple times, so the plugin adds it on for them. It
        // just presumes they passed the full URL and we just remove it and 
        // then add it on to keep things uniform. Also, we are only working
        // with version 1.1 of the API at the moment.
        $root     = TwitterAPI::$_root;
        $url      = $root.str_replace($root, '', $uri);

        // Start the query with a `?` like all good querystrings should!
        $query = '?';

        // We allow the use of an array or string so process the string if need 
        // be.
        if ( is_array($param) ) {
            // Sort and uniform all the data for caching reference
            ksort($param);
            foreach ( $param as $key => $value ) {
                $query .= strtolower($key).'='.strtolower($value).'&';
            }
        } else {
            $query = ltrim($param, '?'); // Just in case they added a `?`
        }

        // Remove the last `&` if there is one
        $query = rtrim($query, '&');

        if ( $use_cache === 'Y' ) {        
            // Check if we got the data
            $data = TwitterAPI::_check_cache($uri.$query);
        }

        if ( !$data ) {

            // Grab the users API keys from WP (provided they have added them to
            // the settings page).
            $settings = array(
                'consumer_key'              => get_option(TAPI_SLUG.'_consumer_key'),
                'consumer_secret'           => get_option(TAPI_SLUG.'_consumer_secret'),
                'oauth_access_token'        => get_option(TAPI_SLUG.'_oauth_access_token'),
                'oauth_access_token_secret' => get_option(TAPI_SLUG.'_oauth_access_token_secret')
            );

            // Now to query Twitter API
            $request_method = 'GET';
            $twitter = new TwitterAPIExchange($settings);

            $data = $twitter
                        ->setGetfield($query)
                        ->buildOauth($url, $request_method)
                        ->performRequest();

            if ( $use_cache === 'Y' ) {
                TwitterAPI::_add_cache($uri.$query, $data);
            }

        }

        $data = json_decode($data);

        if ( $data && count($data) > 0 )
            $twitter = $data;

        return $twitter;

    }


    /**
     * Sets the data given into the cache database
     * 
     * @since   1.0.0
     * 
     * @param   string   $uri      Reference URI for cache
     * @param   string   $data     Results data from Twitter
     * 
     * @return  boolean  $results  True upon query success
     */
    public function _add_cache ($uri, $data) {

        global $wpdb;

        $serialized = base64_encode(serialize($data));

        $sql = '
            INSERT INTO
                `'.TAPI_TABLE_CACHE.'`
                (uri,data)
            VALUES
                (\''.$uri.'\',\''.$serialized.'\')
                
            ON DUPLICATE KEY

            UPDATE
                data = \''.$serialized.'\',
                date = CURRENT_TIMESTAMP
        ';

        //echo '<pre>'; print_r($sql); echo '</pre>';

        $success = $wpdb->query($sql);
        return $success;

    }


    /**
     * Checks to see if there is a cache of data
     * 
     * @since   1.0.0
     * 
     * @param   string  $uri  URI reference for the query
     * 
     * @return  mixed         Cached results if found and in date, otherwise false
     */
    public function _check_cache ($uri) {

        global $wpdb;

        $result = false;
        $sql = '
            SELECT
                data,
                date
            FROM
                `'.TAPI_TABLE_CACHE.'`
            WHERE
                uri = \''.$uri.'\'
        ';

        $data = $wpdb->get_row($sql);

        if ( !isset($data->date) || TwitterAPI::_is_expired($data->date) ) {
            if ( TwitterAPI::$_debug ) print_r('Twitter API: Using live data');
            return false;
        } else {
            if ( TwitterAPI::$_debug ) print_r('Twitter API: Using cached data');
            return unserialize(base64_decode($data->data));
        }

    }


    /**
     * Checks if the cached version is expired
     * 
     * @since   1.0.0
     * 
     * @param   string  $saved_time  Time the cached version was saved
     * 
     * @return  boolean              True if expired false if still good to eat
     */
    public function _is_expired ($saved_time) {

        $minutes = get_option(TAPI_SLUG.'_expiration_time');

        $saved_time = strtotime($saved_time);
        $expiration = $saved_time + ($minutes*60);

        if (time() > $expiration) {
            return true;
        } else {
            return false;
        }


    }


    /**
     * Finds any links, @s or #s and coverts them to a link
     * 
     * @since   1.0.0
     * 
     * @param   string  $tweet  Raw tweet with no HTML
     * 
     * @return  string          Tweet with symbols converted to links
     */
    public function make_links ($tweet) {

        // Link
        $tweet = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" rel=\"external nofollow\" class=\"twitter-link\">\\2</a>", $tweet);
        $tweet = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" rel=\"external nofollow\" class=\"twitter-link\">\\2</a>", $tweet);

        // User
        $tweet = preg_replace("/@(\w+)/", "<a href=\"https://twitter.com/intent/user?screen_name=\\1\" rel=\"external nofollow\" class=\"twitter-at\">@\\1</a>", $tweet);

        // Hash
        $tweet = preg_replace("/#(\w+)/", "<a href=\"https://twitter.com/search?q=%23\\1\" rel=\"external nofollow\" class=\"twitter-hash\">#\\1</a>", $tweet);
        
        return $tweet;
    
    }


    /**
     * Take the values from the settings admin panel and save them
     * 
     * @since   1.0.0
     */
    public function update_settings () {

        if ( !empty($_POST) || wp_verify_nonce($_POST['update'],'update_twitter_api') ) {
            
            $consumer_key              = trim($_POST['consumer_key']);
            $consumer_secret           = trim($_POST['consumer_secret']);
            $oauth_access_token        = trim($_POST['oauth_access_token']);
            $oauth_access_token_secret = trim($_POST['oauth_access_token_secret']);
            $use_cache                 = trim($_POST['use_cache']);
            $expiration_time           = intval($_POST['expiration_time']);

            if ($consumer_key == '' || $consumer_secret == '' || $oauth_access_token == '' || $oauth_access_token_secret == '') {
                // Although we show this error message we will update to the
                // new given value
                echo '<div class="error settings-error"><p>All keys and tokens must be filled in to query Twitter</p></div>';
            }


            if ( $use_cache != 'Y' && $use_cache != 'N' ) {
                $use_cache = 'Y';
            }

            update_option(TAPI_SLUG.'_consumer_key', $consumer_key);
            update_option(TAPI_SLUG.'_consumer_secret', $consumer_secret);
            update_option(TAPI_SLUG.'_oauth_access_token', $oauth_access_token);
            update_option(TAPI_SLUG.'_oauth_access_token_secret', $oauth_access_token_secret);
            update_option(TAPI_SLUG.'_use_cache', $use_cache);
            update_option(TAPI_SLUG.'_expiration_time', $expiration_time);

            echo '<div class="updated settings-error"><p><strong>Settings saved.</strong></p></div>';

        } elseif ( !wp_verify_nonce($_POST['update'],'update_twitter_api') ) {

            die('Nonce failed. Good bye.');

        }

    }
 
} 