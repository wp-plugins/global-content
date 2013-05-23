<?php
/**
 * Plugin Name: Global Content
 * Plugin URI: http://wordpress.org/extend/plugins/global-content/
 * Description: Define only once some global content and reuse easily in templates and posts/pages as many times as required.
 * Version: 1.0
 * Author: BusinessBox
 * Author URI: http://www.businessbox.com.au
 * License: A "Slug" license name e.g. GPL2
*/

/* INCLUDES */

require_once 'global-content-config.php';


/* HOOKS */

register_activation_hook( __FILE__, 'gcbb_install_db' );
register_activation_hook( __FILE__, 'gcbb_install_data' );
add_action( 'admin_menu', 'gcbb_register_menu_page' );
add_action('admin_init', 'gcbb_add_css_js');
add_filter('the_content', 'gcbb_filter_content');

/* FUNCTIONS */

function gcbb_register_menu_page()
{
	add_menu_page( 'Global Content by BusinessBox', 'Global Content', 'level_10', 'global-content/global-content-admin-list.php', '', plugins_url( 'global-content/global-content-menu.png' ), 26 );
	$slug = add_submenu_page( 'global-content/global-content-admin-list.php', 'Global Content by BusinessBox', 'All Variables', 'level_10', 'global-content/global-content-admin-list.php', '');
	add_submenu_page( $slug, 'Global Content by BusinessBox', 'Edit', 'level_10', 'global-content/global-content-admin-edit.php', '');
	add_submenu_page( 'global-content/global-content-admin-list.php', 'Global Content by BusinessBox', 'Add New', 'level_10', 'global-content/global-content-admin-new.php', '');
}

function gcbb_add_css_js()
{
	wp_enqueue_script('post');
	wp_register_style('global_content_css', plugins_url('global_content.css', __FILE__) );
	wp_enqueue_style( 'global_content_css');
}

function gcbb_get($variable, $debug = false)
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . GCBB_DB_TABLE;
	$query = "SELECT content FROM $table_name WHERE name = '$variable'";
	
	$result = $wpdb->get_row($query, ARRAY_A);
	
	if(isset($result['content']))
		return $result['content'];
	else if($debug)
		return "GCBB: $variable NOT FOUND";
	else
		return NULL;
}

function gcbb_filter_content($content = '')
{
	$variables = array();
	$pattern = '/'.sprintf(addcslashes(GCBB_TAG_PATTERN, '\^$.[]|()?*+{}-'), '([a-z\-]+)').'/';
	preg_match_all($pattern, $content, $variables);
	
	while($variable = array_pop($variables[1]))
	{
		$pattern = '/'.sprintf(addcslashes(GCBB_TAG_PATTERN, '\^$.[]|()?*+{}-'), $variable).'/';
		$content = preg_replace($pattern, gcbb_get($variable), $content);
	}
	
	return $content;
}

function gcbb_install_db()
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . GCBB_DB_TABLE;
	
	$sql = "CREATE TABLE $table_name (
	ID int(11) NOT NULL AUTO_INCREMENT,
	time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	name tinytext NOT NULL,
	content text NOT NULL,
	author bigint(20) DEFAULT 0 NOT NULL,
	UNIQUE KEY id (id)
	);";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function gcbb_install_data() 
{
	global $wpdb;
	
	$current_user = wp_get_current_user();
	$table_name = $wpdb->prefix . GCBB_DB_TABLE;
	
	$name = "test";
	$content = "Congratulations, you successfully installed Global Content by BusinessBox!";

	$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $name, 'content' => $content, 'author' => $current_user->ID ) );
}