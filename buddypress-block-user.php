<?php
/*
    Plugin Name: BuddyPress Block User
    Plugin URI: https://www.upwork.com/freelancers/~0170bd24e40bc723c6
    Description: Allows users to block another users.
    Author: Andrij Tkachenko
    Version: 0.1
    Author URI: https://www.upwork.com/freelancers/~0170bd24e40bc723c6
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'BPBlockUser_DIR', dirname( __FILE__ ) );

//include main plugin file
require_once BPBlockUser_DIR .'/includes/class-BPBlockUser.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-BPBlockUser-activator.php
 */
function activate_bp_block_user() {
    require_once BPBlockUser_DIR . '/includes/class-BPBlockUser-activator.php';
    BP_Block_User_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_bp_block_user' );


//start plugin
$plugin = new BP_Block_User();