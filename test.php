<?php
/*
Plugin Name: Test Plugin Update
Plugin URI: http://www.c-metric.com
Description: Test plugin updates
Version: 2.1
Author: Rupesh jorkar
Author URI: http://www.c-metric.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
 exit;
}

define('CUSTPLGNAME','test-plugin-update');
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/rupesh-cmetric/Test-Plugin-Update',
	__FILE__,
	'test-plugin-update'
);


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
	set_transient( 'wp_upe_activated', 1 );
}

function  update_db(){
	global $wpdb;
	$table_name = $wpdb->prefix . "user_details";
	//column name and data type for
	$colums_details = array(
		'city' => 'varchar(255)',
		'country' => 'varchar(255)',
		'state' => 'varchar(255)',
		'created_at' => 'DATETIME',
		'updated_at' => 'DATETIME'		
	);
	foreach($colums_details as $column=>$value){
		//get column exist or not
		$checkcolumn = $wpdb->get_row("Show columns from $table_name like '$column'",ARRAY_A);
		if(empty($checkcolumn)){
			//if column not exist than add into table
			$wpdb->query("ALTER TABLE $table_name ADD $column $value");
		}
	}
}

function wp_upe_upgrade_completed( $upgrader_object, $options ) {
 // The path to our plugin's main file
 $our_plugin = plugin_basename( __FILE__ );
 // If an update has taken place and the updated type is plugins and the plugins element exists
 if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
  // Iterate through the plugins being updated and check if ours is there
  foreach( $options['plugins'] as $plugin ) {
   if( $plugin == $our_plugin ) {
    // Set a transient to record that our plugin has just been updated
	update_db();
    set_transient( 'wp_upe_updated', 1 );
   }
  }
 }
}
add_action( 'upgrader_process_complete', 'wp_upe_upgrade_completed', 10, 2 );


function wp_upe_display_update_notice() {
 // Check the transient to see if we've just updated the plugin
 if( get_transient( 'wp_upe_updated' ) ) {
  echo '<div class="notice notice-success">' . __( 'Thanks for updating', 'wp-upe' ) . '</div>';
  delete_transient( 'wp_upe_updated' );
 }
}
add_action( 'admin_notices', 'wp_upe_display_update_notice' );



function wp_upe_display_install_notice() {
 // Check the transient to see if we've just activated the plugin
 if( get_transient( 'wp_upe_activated' ) ) {
  echo '<div class="notice notice-success">' . __( 'Thanks for installing', 'wp-upe' ) . '</div>';
  // Delete the transient so we don't keep displaying the activation message
 delete_transient( 'wp_upe_activated' );
 }
}
add_action( 'admin_notices', 'wp_upe_display_install_notice' );

?>