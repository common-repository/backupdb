<?php
/*
Plugin Name: BackupDB
Plugin URI: http://www.neosmart.de
Description: Erstellen und Einspielen von MySQL-Dumps per AJAX.
Version: 0.1.1
Author: neosmart GmbH
Author URI: http://www.neosmart.de
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined('ABSPATH'))exit();

function bdb__admin_init(){
	wp_register_script('backupdb-main', plugins_url('/js/backupdb.js', __FILE__ ));
	wp_register_style('backupdb-main', plugins_url('/css/backupdb.css', __FILE__ ));
	
	if (!file_exists(get_home_path().'wp-content/backups/')) {
		mkdir(get_home_path().'wp-content/backups/', 0755, true);
	}
}

function bdb__admin_menu(){
	$page_hook_suffix = add_submenu_page('tools.php', 'BackupDB', 'BackupDB', 'manage_options', 'backupdb', 'bdb__display_page');
	add_action('admin_print_scripts-'.$page_hook_suffix, 'bdb__admin_scripts');
}

function bdb__admin_scripts(){
	wp_enqueue_script('backupdb-main');
	wp_enqueue_style('backupdb-main');
}

function bdb__display_page(){
	bdb__define_vars();
	require_once 'templates/page.php';
}

function bdb__define_vars(){
	if(!defined('BACKUPDB_AJAX'))define('BACKUPDB_AJAX', plugins_url('/ajax.php', __FILE__ ));
	if(!defined('BACKUPDB_TITLE'))define('BACKUPDB_TITLE', 'BackupDB');
}

function bdb_boot_session() {
	if(!session_id())session_start();
}

require_once 'includes/functions.php';

add_action('wp_loaded','bdb_boot_session');
add_action('admin_init','bdb__admin_init');
add_action('admin_menu','bdb__admin_menu');
add_action('wp_ajax_bdb_get_files', 'bdb_get_files');
add_action('wp_ajax_bdb_create_mysql_dump', 'bdb_create_mysql_dump');
add_action('wp_ajax_bdb_load_mysql_dump', 'bdb_load_mysql_dump');