<?php
/*
Plugin Name: Test Plugin Update
Plugin URI: http://www.c-metric.com
Description: Test plugin updates
Version: 2.0
Author: Rupesh jorkar
Author URI: http://www.c-metric.com
*/

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
	//add the new or existing column 
	update_db();
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
?>