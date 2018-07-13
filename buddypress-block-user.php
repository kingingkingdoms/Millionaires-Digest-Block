<?php
/*
Plugin Name: Millionaire's Digest Block  
Description: Allow users to block and unblock users and accounts.
Version: 1.0.0
Author: K&L (Founder of the Millionaire's Digest)
Author URI: https://millionairedigest.com/

*/

//If this file is called directly, abort.
if( ! defined( 'WPINC' ) ) {
	die;
}
define( 'BPBlockUser_DIR', dirname( __FILE__ ) );

//Include main plugin file
require_once BPBlockUser_DIR .'/includes/class-BPBlockUser.php';

/* The code that runs during plugin activation. This action is documented in includes/class-BPBlockUser-activator.php */
function activate_bp_block_user() {
    require_once BPBlockUser_DIR . '/includes/class-BPBlockUser-activator.php';
    BP_Block_User_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_bp_block_user' );

//Start plugin
$plugin = new BP_Block_User();
