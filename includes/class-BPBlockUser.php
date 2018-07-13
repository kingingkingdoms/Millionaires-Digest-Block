<?php
/* Plugin main functionallity */
class BP_Block_User {
	public $table;
	function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix.'bp_block_user';
		add_action( 'bp_member_header_actions', array( $this, 'block_button' ), 100 );
		add_action( 'bp_init', array( $this, 'block_user_action' ) );
		add_action( 'bp_init', array( $this, 'restrict_blocked' ) );
		add_action( 'messages_message_before_save', array( $this, 'block_message' ) );
	}
	
	/* Restrict blocked users to messaging */
	function block_message( $message_info ) {
		global $bp;
		$recipients = $message_info->recipients;
		foreach ( $recipients as $key => $recipient ) {
			//If admin, skip check
            if( $bp->loggedin_user->is_site_admin == 1 )
				continue;
			//Make sure sender is not trying to send to themselves
			if( $recipient->user_id == $bp->loggedin_user->id ) {
				unset( $message_info->recipients[$key] );
				continue;
			}
			
			//Check if the attempted recipient is blocking user; If we get a match, remove person from recipient list
            if( $this->check_blocking( $bp->loggedin_user->id, $recipient->user_id ) ) {
				if( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
					//If using ajax, then unset
					unset( $message_info->recipients[$key] );
				} else {
					wp_redirect( bp_core_get_userlink( $recipient->user_id, false, true ) );
					exit;
				}
			}
		}
	}
	
	/* Restrict access to profile */
	public function restrict_blocked() {
		global $bp;
		//If site admin, skip check
		if( $bp->loggedin_user->is_site_admin == 1 )
			return;
		if( bp_is_user() && ! bp_is_my_profile() && is_user_logged_in() ) {
			$displayed_id = bp_displayed_user_id();
			$user_id = get_current_user_id();
			if( $this->check_blocking( $user_id, $displayed_id ) ) {
				if( bp_is_current_component( 'blocked' ) ) {
					bp_register_template_stack( 'BP_Block_User::register_template_location' );
					add_filter( 'bp_get_template_part', array( $this, 'replace_template' ), 10, 3 );
					bp_core_load_template( 'members/single/plugins' );
				} else {
					wp_redirect(bp_get_members_component_link('').'blocked/');
					exit;
				}
			}
		}
	}
	
	public static function register_template_location() {
		return BPBlockUser_DIR . '/templates/';
	}
	
	public function replace_template( $templates, $slug, $name ) {
		return array( 'members/single/blocked.php' );
	}

    /* Display blocking and unblocking messages */
	public function block_user_action() {
		global $wpdb;
		
		if( bp_is_user() && ! bp_is_my_profile() && is_user_logged_in() ) {
			if( isset( $_GET['block_user'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'block_user' ) ) {
				$displayed_id = bp_displayed_user_id();
				$user_id = get_current_user_id();
				$wpdb->insert( $this->table, array(
					'blocked_id' => $displayed_id,
					'user_id' => $user_id
				), array( '%d','%d' ) );
				bp_core_add_message( 'User blocked' );
			}
			
			if( isset( $_GET['unblock_user'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'unblock_user' ) ) {
				$displayed_id = bp_displayed_user_id();
				$user_id = get_current_user_id();
				$wpdb->delete($this->table, array(
					'blocked_id' => $displayed_id,
					'user_id' => $user_id
				), array( '%d','%d' ) );
				bp_core_add_message( 'User unblocked' );
			}
		}
	}
	
	/* Display the block button */
	public function block_button() {
		$displayed_id = bp_displayed_user_id();
		$button_args = array(
			'link_text' => 'Block',
			'must_be_logged_in' => true,
			'link_href' => wp_nonce_url( '?block_user', 'block_user' ),
			'component' => 'settings',
			'id' => 'block_user'
		);
		
		if( $this->check_blocking( $displayed_id ) ) {
			$button_args['link_text'] = 'Unblock';
			$button_args['link_href'] = wp_nonce_url( '?unblock_user', 'unblock_user' );
		}
		
		$button = new BP_Button($button_args);
		$button->display();
	}
	
	public function check_blocking( $block_id = false, $user_id = false ) {
		//Stop if user is not logged or empty user
        if( $block_id == false || ! is_user_logged_in() )
			return;
		if( $user_id == false ) $user_id = wp_get_current_user()->ID;
		
		//Stop if user tries to block him or herself
		if( $block_id == $user_id )
			return;
		global $wpdb;
		$blocked = $wpdb->get_var( $wpdb->prepare( "SELECT *
		FROM {$this->table}
		WHERE `user_id` = %d
		AND `blocked_id` = %d
		", $user_id, $block_id
		) );
		
		if( ! $blocked )
			return false;
		return true;
	}
	
	public function block_user( $block_id = false ) {
		//Stop if user is not logged or user is empty
		if( $block_id == false || ! is_user_logged_in() )
			return;
		$user_id = wp_get_current_user()->ID;
		
		//Stop if user tries to block him or herself
		if( $block_id == $user_id )
			return;
		global $wpdb;
		$wpdb->insert( $this->table, array(
			'user_id' => $user_id,
			'block_id' => $block_id
		), array( '%d', '%d' ) );
	}
}
