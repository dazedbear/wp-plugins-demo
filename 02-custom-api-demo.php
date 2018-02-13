<?php
/*
Plugin Name:  WP Custom API Demo Plugin
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description:  Basic WordPress API
Version:      20160911
Author:       WP QDS
Domain Path:  /languages
*/


/**
 *  Controller，定義 Route & Model(Endpoint) 
 */
class Custom_API_Demo_Route extends WP_REST_Controller {
  public function register_routes() {
    $version = '1';
    $namespace = 'custom-api-demo/v' . $version;
    $base = '/';

    register_rest_route( $namespace, '/postlist', array(
      'methods' => 'GET',
      'callback' => array($this, 'get_all_posts'),
    ) );
  
    register_rest_route( $namespace, '/movietitle', array(
      'methods' => 'GET',
      'callback' => array($this, 'fetch_movie_title'),
    ) );
  
    register_rest_route( $namespace, '/private-data', array(
      'methods'  => 'GET',
      'permission_callback' => array($this, 'get_private_data_permissions_check'),  // 比下方的 main callback 早執行
      'callback' => array($this, 'get_private_data'),
      'args' => array(
        'token' => array(
          'required' => true
        )
      )
    ) );
  }

  // 取得 WordPress 內部的文章
  public function get_all_posts( $data ) {
    $posts = get_posts();
    return empty( $posts ) ? null : $posts;
  }

  // 取得外部 Open Data
  public function fetch_movie_title() {    
    $res = wp_remote_get('https://cloud.culture.tw/frontsite/trans/SearchShowAction.do?method=doFindTypeJ&category=8');
    $body = json_decode(wp_remote_retrieve_body( $res ));
    return empty( $body[0]->title ) ? 'Movie Not Found' : $body[0]->title;
  }

  // 取得機敏資料
  public function get_private_data() {
    return rest_ensure_response( 'This is private data.' );
  }

  // 檢查權限 & token
  public function get_private_data_permissions_check( WP_REST_Request $request ) {
    // 簡單的檢查 token 是否相同
    $token = $request['token'];
    if ( $token === 'thisistesttoken' ) {
      return true;
    }

    // 驗證使用者是否有編輯文章權限 
    // if ( current_user_can( 'edit_posts' ) ) {
    //   return true;
    // } 
    
    return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view private data.', 'my-text-domain' ), array( 'status' => 401 ) );
  }
}

/**
 *  註冊 Route
 */
add_action( 'rest_api_init', function () {
  $controller = new Custom_API_Demo_Route();
  $controller->register_routes();
} );