<?php
/*
Plugin Name: FB chat plus
Description: Unofficial Facebook Chat Plugin with more powerful settings.
Author: Concentric Digital
Author URI: https://concentricdigital.com.au
Version: 0.1
Text Domain: fb-chat-plus
Domain Path: /languages/
*/

/*
* Copyright (C) 2020-present, Concentric Digital Pty Ltd.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/

class FB_Chat_Plus {
  function __construct() {
    if (!class_exists('Facebook_Messenger_Customer_Chat')){
      add_action( 'admin_notices', array( $this,'display_dependency_error_notice' ));
    }else{
      include( plugin_dir_path( __FILE__ ) . 'options.php' );
      add_action( 'wp_enqueue_scripts', array($this,'fbcp_enqueue_scripts'));
    }
    add_filter( 'plugin_action_links',
      array( $this, 'fbcp_plugin_action_links'), 10, 2 );
    add_filter( 'plugin_row_meta',
      array( $this, 'fbcp_register_plugin_links'), 10, 2 );
    add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ));
  }

  function fbcp_enqueue_scripts(){
    wp_register_script('js_cookie','https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js');
    wp_register_script('fbcp_main_script', plugin_dir_url( __FILE__ ).'script.js',array( 'js_cookie','jquery-effects-shake' ));
    wp_enqueue_script('fbcp_main_script');
    $options = get_option( 'fbcp_options' );
    wp_localize_script( 'fbcp_main_script', 'fbcp_variables',
      array(
        'autoOpen' => true,
        'autoOpenConversationEnabled' => true,
        'autoOpenConversationOnceOnly' => $options['auto_show_only_once'],
        'autoOpenbyDelay' => $options['auto_show_by_delay'],
        'OpenDelay' => $options['auto_show_delay']*1000,
        'shakeConversationEnabled' => $options['auto_show_shake']
      ));
  }

  function display_dependency_error_notice(){
    printf(
      '<div class="notice notice-error"><p>%s<a href="%s">%s</a></p></div>',
      esc_html__('Chat plus requires the Facebook messenger customer chat plugin to work properly, please install "The Official Facebook Chat Plugin" and activate the official plugin first, ', 'fb-chat-plus'),
      'plugin-install.php?s=the%20official%20facebook%20chat%20plugin&tab=search&type=term',
      esc_html__( 'get the official plugin here.', 'fb-chat-plus' )
    );
  }

  function fbcp_plugin_action_links( $links, $file ) {
    $settings_url = 'admin.php?page=messenger-customer-chat-plugin';
    if ( current_user_can( 'manage_options' ) ) {
      $base = plugin_basename(__FILE__);
      if ( $file == $base ) {
        $settings_link = sprintf(
          '<a href="%s">%s</a>',
          $settings_url,
          esc_html__( 'Settings', 'fb-chat-plus' )
        );
        array_unshift( $links, $settings_link );

      }
    }
    return $links;
  }

  function fbcp_register_plugin_links( $links, $file ) {
    $settings_url = 'admin.php?page=messenger-customer-chat-plugin';
    $base = plugin_basename(__FILE__);
    if ( $file == $base ) {
      if ( current_user_can( 'manage_options' ) ) {
        $links[] = sprintf(
          '<a href="%s">%s</a>',
          $settings_url,
          esc_html__( 'Settings', 'fb-chat-plus' )
        );
      }
      $links[] =
        sprintf(
          '<a href="%s">%s</a>',
          esc_url( 'https://wordpress.org/plugins/fb-chat-plus/#faq' ),
          esc_html__( 'FAQ', 'fb-chat-plus' )
        );
      $links[] =
        sprintf(
          '<a href="%s">%s</a>',
          esc_url( 'https://wordpress.org/support/plugin/fb-chat-plus/' ),
          esc_html__( 'Support', 'fb-chat-plus' )
        );
    }
    return $links;
  }

  function load_plugin_textdomain() {
    load_plugin_textdomain( 'fb-chat-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }
}

new FB_Chat_Plus();
?>
