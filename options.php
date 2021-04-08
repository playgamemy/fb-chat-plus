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

//initialise settings api
add_action('admin_init', function(){
  register_setting( 'fbcp_settings', 'fbcp_options');
  add_settings_section( 
    'fbcp_settings_auto_show_section', 
    __('Auto Show','fb-chat-plus'),
    'fbcp_settings_auto_show_section_cb',
    'fbcp_settings',
  );

  add_settings_field( 
    'fbcp_settings_auto_show_field',
    __('Auto show conversation after delay?','fb-chat-plus'),
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
    __('Shake the conversation when shown?','fb-chat-plus'),
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
    __('Time to delay before showing the conversation (in seconds)','fb-chat-plus'),
    'fbcp_setting_auto_show_delay_cb',
    'fbcp_settings',
    'fbcp_settings_auto_show_section',
  );

  add_settings_field( 
    'fbcp_settings_auto_show_only_once_field',
    __('Auto show conversation only once per session?','fb-chat-plus'),
    'fbcp_setting_auto_show_only_once_cb',
    'fbcp_settings',
    'fbcp_settings_auto_show_section',
    array(
      'type' => 'boolean',
      'default' => True
    )
  );
});



//Settings callback function
function fbcp_settings_auto_show_section_cb() {
  //silence is gold
}

function fbcp_setting_auto_show_by_delay_cb() {
  $options = get_option( 'fbcp_options' ); 
	if($options['auto_show_by_delay']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='fbcp_auto_show_checkbox' name='fbcp_options[auto_show_by_delay]' type='checkbox' />";
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
	if($options['auto_show_only_once']) { $checked = ' checked="checked" '; }
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
    <form action='options.php' method='post'>

        <h2>Chat Plus Settings Admin Page</h2>

        <?php
        settings_fields( 'fbcp_settings' );
        do_settings_sections( 'fbcp_settings' );
        submit_button();
        ?>

    </form>
  <?php
}
?>
