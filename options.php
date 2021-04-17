<?php
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

// Settings page
add_action( 'admin_menu', function() {
  
  add_submenu_page(
    'messenger-customer-chat-pluginm',
    'Plugin settings',
    'Customer Chat',
    'manage_options',
    'messenger-customer-chat-plugin',
    'fbmcc_integration_settings',
  );

  add_submenu_page(
    'messenger-customer-chat-plugin',
    'Chat Plus Plugin settings',
    'Chat Plus Addon',
    'manage_options',
    'chat-plus-setting',
    'fbcp_admin_page_contents',
  );
});

add_action( 'admin_enqueue_scripts', 'fbcp_admin_add_styles' );

//initialise settings api
add_action('admin_init', function(){
  register_setting( 'fbcp_settings', 'fbcp_options');
  add_settings_section( 
    'fbcp_settings_auto_show_section', 
    __('Auto Show','chat-plus'),
    'fbcp_settings_auto_show_section_cb',
    'fbcp_settings',
  );

  add_settings_field( 
    'fbcp_settings_auto_show_field',
    __('Auto show conversation after delay?','chat-plus'),
    'fbcp_setting_auto_show_by_delay_cb',
    'fbcp_settings',
    'fbcp_settings_auto_show_section',
    array(
      'type' => 'boolean',
      'default' => True
    )
  );

  add_settings_field( 
    'fbcp_settings_auto_show_shake_field',
    __('Shake the conversation when shown?','chat-plus'),
    'fbcp_setting_auto_show_shake_cb',
    'fbcp_settings',
    'fbcp_settings_auto_show_section',
    array(
      'type' => 'boolean',
      'default' => True
    )
  );

  add_settings_field( 
    'fbcp_settings_auto_show_delay_field',
    __('Time to delay before showing the conversation (in seconds)','chat-plus'),
    'fbcp_setting_auto_show_delay_cb',
    'fbcp_settings',
    'fbcp_settings_auto_show_section',
  );

  add_settings_field( 
    'fbcp_settings_auto_show_only_once_field',
    __('Auto show conversation only once per session?','chat-plus'),
    'fbcp_setting_auto_show_only_once_cb',
    'fbcp_settings',
    'fbcp_settings_auto_show_section',
    array(
      'type' => 'boolean',
      'default' => True
    )
  );
});

//add style
function fbcp_admin_add_styles() {
  wp_enqueue_style(
    'fbcp-admin-styles',
    plugins_url( '/admin.css', __FILE__ ),
    false,
    '1.0',
    'all'
  );
}
//Settings callback function
function fbcp_settings_auto_show_section_cb() {
  //silence is gold
}

function fbcp_setting_auto_show_by_delay_cb() {
  $options = get_option( 'fbcp_options' ); 
	if($options['auto_show_by_delay']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='fbcp_auto_show_checkbox' name='fbcp_options[auto_show_by_delay]' type='checkbox' value=1/>";
}

function fbcp_setting_auto_show_shake_cb() {
  $options = get_option( 'fbcp_options' );
	if($options['auto_show_shake']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='fbcp_auto_show_shake_checkbox' name='fbcp_options[auto_show_shake]' type='checkbox' />";
}

function fbcp_setting_auto_show_delay_cb() {
  $options = get_option( 'fbcp_options' );
  if(!$options['auto_show_delay']){ $options['auto_show_delay'] = 5;}
	echo "<input id='fbcp_auto_show_delay_input' name='fbcp_options[auto_show_delay]' size='40' type='number' value='{$options['auto_show_delay']}' />";
}

function fbcp_setting_auto_show_only_once_cb() {
  $options = get_option( 'fbcp_options' );
	if($options['auto_show_only_once']) {  $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='fbcp_auto_show_only_once_checkbox' name='fbcp_options[auto_show_only_once]' type='checkbox' />";
}

function fbcp_sanitize_page_id($input) {
  if ( preg_match('/^\d+$/', $input) ) {
    return $input;
  } else {
    return '';
  }
}

function fbcp_sanitize_locale($input) {
  if ( preg_match('/^[A-Za-z_]{4,5}$/', $input) ){
    return $input;
  } else {
    return '';
  }
}

function fbcp_admin_page_contents() {
  ?>
    <div class="fbcp-admin-row">
      <div class="fbcp-admin-column fbcp-admin-column-left">
        <form action='options.php' method='post'>

          <h2>Chat Plus Settings Admin Page</h2>

          <?php
            settings_fields( 'fbcp_settings' );
            do_settings_sections( 'fbcp_settings' );
            submit_button();
          ?>

        </form>
        <h2><u>How to add a CTA button</u></h2>
        <p> Simply add css class "fbcp-open-chat" to the button, conversation will open when clicked</p>
        <p> You can also use shortcode [fbcp-cta-button] to add a "Chat with us" button with messenger logo </p>
        <p> Need help? Check out the <a href=""> FAQ section</a> or submit a ticket in the <a href="">support section</a></p>
        <p><strong> Ready to hire? We can develop custom plugin or theme for your site. Send us an email <a href="mailto:development@concentricdigital.com.au">development@concentricdigital.com.au</a></strong></p>
      </div>
      <div class="fbcp-admin-column fbcp-admin-column-right">
        <div><a class='fbcp-web-banner' href="https://www.elegantthemes.com/affiliates/idevaffiliate.php?id=67377_5_1_18" target="_blank" rel="nofollow"><img style="border:0px" src="https://www.elegantthemes.com/affiliates/media/banners/divi_300x250.jpg" alt="Divi WordPress Theme"></a></div>
        <div><a class='fbcp-web-banner' href="https://siteground.com/wordpress-hosting.htm?afimagecode=afe04a3500301b0438b17066b53b89c6" target="_blank"><img border="0" src="https://uapi.siteground.com/img/affiliate/en/USD/general_EN_USD_wordpress-medium-rectangle-blue.jpg"></a></div>
      </div>
    </div>
  <?php
}
?>
