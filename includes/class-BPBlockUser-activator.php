<?php
/* Activatin plugin */
class BP_Block_User_Activator {
	public static function activate() {
		global $wpdb;
		$query = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bp_block_user` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
		`blocked_id` int(11) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($query);
	}
}
