<?php
/*
Plugin Name: FB chat plus
Description: Unofficial Facebook Chat Plugin with more powerful settings.
Author: Facebook
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
    include( plugin_dir_path( __FILE__ ) . 'options.php' );
    add_filter( 'plugin_action_links',
      array( $this, 'fbcp_plugin_action_links'), 10, 2 );
    add_filter( 'plugin_row_meta',
      array( $this, 'fbcp_register_plugin_links'), 10, 2 );
    add_action( 'plugins_loaded', 'load_plugin_textdomain' );
    add_action( 'plugins_loaded', array( 'FB_Chat_Plus','check_dependency' ) );
  }

  function check_dependency() {
    if( !class_exists( 'Facebook_Messenger_Customer_Chat' ) ) {
      add_action( 'admin_notices', array( 'FB_Chat_Plus','plugin_dependency_fail_notice' ));
    }
  }

  function plugin_dependency_fail_notice(){
    ?>
    <div class="notice notice-error">
      <?php
      sprintf(
          '<p>%s<a href="%s">%s></a></p>',
          esc_html__('Chat plus requires the Facebook messenger customer chat plugin to work properly, please install and activate the official plugin first', 'fb-chat-plus'),
          'plugin-install.php?s=the%20official%20facebook%20chat%20plugin&tab=search&type=term',
          esc_html__( 'get', 'fb-chat-plus' )
        )
         ?>
    </div>
    <?php
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
