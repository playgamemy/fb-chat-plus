<?php /*
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

//Add metabox using Class
/**
 * Register a meta box using a class.
 */
class FB_Chat_Plus_Custom_Meta_Box {
 
  /**
   * Constructor.
   */
  public function __construct() {
      if ( is_admin() ) {
          add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
          add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
      }

  }

  /**
   * Meta box initialization.
   */
  public function init_metabox() {
      add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
      add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
  }

  /**
   * Adds the meta box.
   */
  public function add_metabox() {
      add_meta_box(
          'fbcp-meta-box',
          __( 'Disable Facebook Chat', 'chat-plus' ),
          array( $this, 'render_metabox' ),
          'post',
          'side',
          'default'
      );

  }

  /**
   * Renders the meta box.
   */
  public function render_metabox( $post ) {
    // Add nonce for security and authentication.
    wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );

    // Retrieve an existing value from the database.
		$disable_chat = get_post_meta( $post->ID, 'fbcp_disable_chat', true );
    // Set default values.
    if( empty( $disable_chat ) ){
      $disable_chat = false;
    }
    if($disable_chat){$checked = ' checked="checked" ';}
    // Form fields.
		echo '<table class="form-table">';
		echo '	<tr>';
		echo '		<th><label for="disable_chat" class="disable_chat_label">' . __( 'Disable FB Chat', 'chat-plus' ) . '</label></th>';
		echo '		<td>';
		echo "      <input ".$checked." id='fbcp_disable_chat' name='fbcp_disable_chat' type='checkbox' />";
		echo '		</td>';
		echo '	</tr>';
		echo '</table>';
  }

  /**
   * Handles saving the meta box.
   *
   * @param int     $post_id Post ID.
   * @param WP_Post $post    Post object.
   * @return null
   */
  public function save_metabox( $post_id, $post ) {
      // Add nonce for security and authentication.
      $nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
      $nonce_action = 'custom_nonce_action';

      // Check if nonce is valid.
      if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
          return;
      }

      // Check if user has permissions to save data.
      if ( ! current_user_can( 'edit_post', $post_id ) ) {
          return;
      }

      // Check if not an autosave.
      if ( wp_is_post_autosave( $post_id ) ) {
          return;
      }

      // Check if not a revision.
      if ( wp_is_post_revision( $post_id ) ) {
          return;
      }

      // Sanitize user input.
		$disable_chat = isset( $_POST[ 'fbcp_disable_chat' ] ) ? true : false ;

		// Update the meta field in the database.
		update_post_meta( $post_id, 'fbcp_disable_chat', $disable_chat);
  }
}

new FB_Chat_Plus_Custom_Meta_Box();
?>