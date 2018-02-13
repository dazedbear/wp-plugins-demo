<?php
/*
Plugin Name:  WP Hooks Demo Plugin
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description:  Basic WordPress Plugin for Using Custom Hooks
Version:      20160911
Author:       WP QDS
Author URI:   https://developer.wordpress.org/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /languages
*/


// 負責打 API 並回傳部分資料
function fetch_movie_title() {    
    $res = wp_remote_get('https://cloud.culture.tw/frontsite/trans/SearchShowAction.do?method=doFindTypeJ&category=8');
    $body = json_decode(wp_remote_retrieve_body( $res ));
    $des = $body[0]->title;

    // Action return 無法被 do_action 接收到，只能夠 echo
    // echo $des;
    
    // Filter return 可以被 apply_filters 接收到
    return $des;
}

/* Action 版本 */
// add_action('get_movie_title', 'fetch_movie_title');

/* Filter 版本 */
add_filter('get_movie_title', 'fetch_movie_title');