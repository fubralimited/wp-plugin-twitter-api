=== WP Twitter API ===
Contributors: WolfieZero, Fubra
Tags: wac, helper
Requires at least: 3.5.1
Tested up to: 3.6
Stable tag: 1.1.0
License: Copyright 2013 Fubra Limited

A Twitter API library integrated into WordPress


== Description ==

Provides easy access to the Twitter API by removing the complexities of setting
up the oAuth and giving you easy access to the data you want with caching
capabilities.

It also provides use of Twitter's JavaScript widget library

This plugin uses the [Twitter-API-PHP](http://github.com/j7mbo/twitter-api-php) 
wrapper.


== Usage ==

Once activated and credentials are added you can then easily access the RESTful
API by using the following code

    TwitterAPI::query( string $uri [, mixed $param = '' ] );

The `$uri` is just the latter part of the API URL, so if we wanted the user
timeline then we would pass `statuses/user_timeline.json`. You can pass the
full URL for version 1.1 of the API, but it's not required.

The `$param` part is for the querystring and is optional based on the REST API. 
This can be either be a string (minus the prefixed `?`) or an array of items.
The following two are valid...

    // Array of parameters
    $param = array(
        'screen_name' => 'WolfieZero',
        'count' => 5
    );

    // Straight up querystring minus
    $param = 'screen_name=WolfieZero&count=5';

Check Twitter's full [REST API v1.1](https://dev.twitter.com/docs/api/1.1) for 
what you can do.

There is also a `make_links()` function that allows you to take a Tweet and 
covert all the symbols into usable links. These use 
[Web Intents](https://dev.twitter.com/docs/intents) to make interacting with
Twitter that bit better.


== Installation ==

1. Upload this directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the "Twitter API" page under the "Settings" menu
4. Enter your Twitter application credentals (found by going
   [here](https://dev.twitter.com/apps) then clicking the app you want to link)
5. Save and you are ready to go


== Changelog ==

= 1.1.0 =
* Added AJAX functionality

= 1.0.0 =
* Inital release