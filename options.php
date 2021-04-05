<?php
/*
* Copyright (C) 2017-present, Facebook, Inc.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/

// Settings page
add_action( 'admin_menu', function() {

  add_menu_page(
    'Chat Plug Plugin settings',
    'Chat Plus',
    'manage_options',
    'chat-plus-setting',
    'fbcp_admin_page_contents',
    'dashicons-microphone'
  );
});

add_action( 'admin_enqueue_scripts', 'fbmcc_add_styles' );
add_action( 'admin_enqueue_scripts', 'fmcc_localize_ajax' );

add_action( 'wp_ajax_fbmcc_update_options', 'fbmcc_update_options');

function fbmcc_update_options() {

  if ( current_user_can( 'manage_options' ) ) {
    check_ajax_referer( 'update_fmcc_code' );
    update_option( 'fbmcc_pageID', fbmcc_sanitize_page_id($_POST['pageID']));
    update_option( 'fbmcc_locale', fbmcc_sanitize_locale($_POST['locale']));
  }
  wp_die();
}

function fbmcc_sanitize_page_id($input) {
  if ( preg_match('/^\d+$/', $input) ) {
    return $input;
  } else {
    return '';
  }
}

function fbmcc_sanitize_locale($input) {
  if ( preg_match('/^[A-Za-z_]{4,5}$/', $input) ){
    return $input;
  } else {
    return '';
  }
}

function fbmcc_add_styles() {
  wp_enqueue_style(
    'fbmcc-admin-styles',
    plugins_url( '/settings.css', __FILE__ ),
    false,
    '1.0',
    'all'
  );
}

function fmcc_localize_ajax() {

  if ( current_user_can( 'manage_options' ) ) {
    $ajax_object = array(
      'nonce' => wp_create_nonce( 'update_fmcc_code' )
    );

    wp_register_script( 'code_script',
      plugin_dir_url( __FILE__ ) . 'script.js' );
    wp_localize_script( 'code_script', 'ajax_object', $ajax_object );
    wp_enqueue_script( 'code_script' );
  }

}

function fbcp_admin_page_contents() {
  ?>
		<h1>
			<?php esc_html_e( 'Welcome to my custom admin page.', 'my-plugin-textdomain' );
      ?>
		</h1>
	<?php 
}
?>
