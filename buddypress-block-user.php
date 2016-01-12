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

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-BPBlockUser-activator.php
 */
function activate_bp_block_user() {
    $plugin_dir = plugin_dir_path( __FILE__ );

    require_once $plugin_dir . 'includes/class-BPBlockUser-activator.php';
    BP_Block_User_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_bp_block_user' );

/**
 * The code that runs during plugin deactivation.
function deactivate_wp_habit_builder() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-WPHabbitBuilder-deactivator.php';
    WP_Habbit_Builder_Deactivator::deactivate();
}
 */