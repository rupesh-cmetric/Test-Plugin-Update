<?php
/*
Plugin Name: Test Plugin Update
Plugin URI: http://www.c-metric.com
Description: Test plugin updates
Version: 1.0
Author: Rupesh jorkar
Author URI: http://www.c-metric.com
*/


//plugin activation hook
register_activation_hook( __file__, 'installer' );
function installer(){
	//create the table
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$winners = $wpdb->prefix . 'user_details';
	//Master table
	$sql = "CREATE TABLE IF NOT EXISTS $winners (
		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		username bigint(20) ,
		password varchar(20) ,
		address varchar(255) ,
		created_at DATETIME ,
		updated_at DATETIME 
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

?>