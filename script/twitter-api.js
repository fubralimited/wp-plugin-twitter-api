/**
 * WP Twitter API AJAX functionality
 *
 * @package  Twitter API
 * @author   Neil Sweeney <neil@wolfiezero.com>
 * @since    1.1.0
 */

jQuery(document).ready( function ($) {

    var data = {
            action:  'twitter_api_query',
            uri:     wp_ajax_twitterapi.uri,
            param:   wp_ajax_twitterapi.param,
            is_ajax: true
        };

    $.post(
        wp_ajax_twitterapi.ajax_url,
        data,
        function (r) {

        	var twitter 	 = $.parseJSON(r),
        		count_tweets = twitter.length,
        		tweet        = {},
        		html         = '';

			html += '<section class="twitter box box-aside-item">';
				html += '<header class="twitter-header box-pad-txt">';
					html += '<h2 class="twitter-header-name">';
						html += twitter[0].user.name;
					html += '</h2>';
					html += '<p class="twitter-header-screenname"><a href="https://twitter.com/' + twitter[0].user.screen_name + '" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @' + twitter[0].user.screen_name + '</a></p>';
					html += '<figure class="twitter-header-avatar"><img src="' + twitter[0].user.profile_image_url + '" width="50"></figure>';
				html += '</header>';
				html += '<ul class="twitter-tweets">';

				for ( var i = 0; i < count_tweets; i++ ) {

					tweet = twitter[i];

					html += '<li class="twitter-tweet">';
						html += tweet.text;
						html += '<a href="http://twitter.com/' + tweet.user.screen_name + '/status/' + tweet.id + '" rel="nofollow external" class="twitter-tweet-meta">' + tweet.created_at + '</a>';
					html += '</li>';

				}

				html += '</ul>';
			html += '</section>';

			$('#widget-twitter').html(html);

        }
    );

});
