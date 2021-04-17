<?php
/*
Plugin Name: Chat Plus - Unofficial addon for Customer Chat 
Description: Unofficial Addon for Facebook Customer Chat. Added useful functions including disable chat in some pages, css class for CTA button to show chat, auto show chat after delay, shake conversation to get attention, etc.
Author: Concentric Digital
Author URI: https://concentricdigital.com.au
Version: 0.1
Text Domain: chat-plus
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
    add_action('init', array($this,'init'));  
    add_filter( 'plugin_action_links',
      array( $this, 'fbcp_plugin_action_links'), 10, 2 );
    add_filter( 'plugin_row_meta',
      array( $this, 'fbcp_register_plugin_links'), 10, 2 );
    add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ));
    add_action( 'init', array($this, 'register_shortcode'));
    add_action( 'wp_enqueue_scripts', array($this, 'fbcp_add_styles'),1000);
  }

  function check_chat_disabled(){
    if (!is_admin()&&get_post_meta(get_queried_object_id(), 'fbcp_disable_chat',true)){
      remove_action( 'wp_enqueue_scripts', array($this,'fbcp_enqueue_scripts'));
      $this->remove_filters_for_anonymous_class( 'wp_footer', 'Facebook_Messenger_Customer_Chat','fbmcc_inject_messenger' );
    }
  }

  function fbcp_enqueue_scripts(){
    wp_register_script('js_cookie',plugin_dir_url( __FILE__ ).'scripts/js.cookie.min.js');
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
      esc_html__('Chat plus requires the Facebook messenger customer chat plugin to work properly, please install "The Official Facebook Chat Plugin" and activate the official plugin first, ', 'chat-plus'),
      'plugin-install.php?s=the%20official%20facebook%20chat%20plugin&tab=search&type=term',
      esc_html__( 'get the official plugin here.', 'chat-plus' )
    );
  }

  function fbcp_plugin_action_links( $links, $file ) {
    $settings_url = 'admin.php?page=chat-plus-setting';
    if ( current_user_can( 'manage_options' ) ) {
      $base = plugin_basename(__FILE__);
      if ( $file == $base ) {
        $settings_link = sprintf(
          '<a href="%s">%s</a>',
          $settings_url,
          esc_html__( 'Settings', 'chat-plus' )
        );
        array_unshift( $links, $settings_link );

      }
    }
    return $links;
  }

  function fbcp_register_plugin_links( $links, $file ) {
    $settings_url = 'admin.php?page=chat-plus-setting';
    $base = plugin_basename(__FILE__);
    if ( $file == $base ) {
      if ( current_user_can( 'manage_options' ) ) {
        $links[] = sprintf(
          '<a href="%s">%s</a>',
          $settings_url,
          esc_html__( 'Settings', 'chat-plus' )
        );
      }
      $links[] =
        sprintf(
          '<a href="%s">%s</a>',
          esc_url( 'https://wordpress.org/plugins/chat-plus/#faq' ),
          esc_html__( 'FAQ', 'chat-plus' )
        );
      $links[] =
        sprintf(
          '<a href="%s">%s</a>',
          esc_url( 'https://wordpress.org/support/plugin/chat-plus/' ),
          esc_html__( 'Support', 'chat-plus' )
        );
    }
    return $links;
  }

  function load_plugin_textdomain() {
    load_plugin_textdomain( 'chat-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }
  
  //add style
  function fbcp_add_styles() {
    wp_enqueue_style(
      'fbcp-styles',
      plugins_url( '/style.css', __FILE__ ),
      false,
      '1.0',
      'all'
    );
  }

  function register_shortcode() {
    add_shortcode( 'fbcp_cta_button', array($this,'fbcp_cta_button_shortcode_cb' ));
  }

  function fbcp_cta_button_shortcode_cb(){
    $string = '<button class="fbcp-cta-button"><a class="fbcp-messenger-logo fbcp-open-chat" href="/"><img src="'.plugin_dir_url(__FILE__).'imgs/Logo_Messenger.png"> Chat with us</a></button>';
    return $string;
  }
  
  function init() {
     //check if dependency satisfied
     if (!class_exists('Facebook_Messenger_Customer_Chat')){
      add_action( 'admin_notices', array( $this,'display_dependency_error_notice' ));
    }else{
      include( plugin_dir_path( __FILE__ ) . 'options.php' );
      include( plugin_dir_path( __FILE__ ) . 'metabox.php' );
      add_action( 'wp_enqueue_scripts', array($this,'fbcp_enqueue_scripts'));
      add_action( 'get_header', array( $this, 'check_chat_disabled' ));
    }
  }

  /***Helper Function***/
  function remove_filters_for_anonymous_class( $hook_name = '', $class_name = '', $method_name = '', $priority = 10 ) {
	global $wp_filter;

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class, class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
				// Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
				if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
					unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
				} else {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}

	}

	return false;
}
}

$fbcp_class = new FB_Chat_Plus();
?>
