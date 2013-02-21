<?php
/*
Plugin Name: Audio Archive Manager
Plugin URI: http://forwardfour.com/archive-manager
Description: This is a plugin which will extract the list of MP3s from a particular directory, parse the metadata for each of these MP3s, and publically display them in a formatted list by month and year. This plugin will also track the number of hits each MP3 has recieved and display this data in the administration section.
Version: 1.0
Author: Oliver Spryn
Author URI: http://forwardfour.com/
License: MIT
*/

	namespace FFI\AAM;

//Create plugin-specific global definitions
	define("FILE", __FILE__);
	define("PATH", plugin_dir_path(__FILE__));
	define("REAL_ADDR", get_site_url() . "/wp-content/plugins/audio-archive/");
	define("FAKE_ADDR", get_site_url() . "/audio-archive/");
	define("URL_ACTIVATE", "audio-archive");
	error_reporting(E_ALL);
	
//Register installation and uninstallation hooks
	require_once(PATH . "includes/Hook_Manager.php");
	$hook = new Hook_Manager();
	
	register_activation_hook(__FILE__, array(&$hook, "activationHandler"));
	register_uninstall_hook(__FILE__, array(&$hook, "uninstallHandler"));

//Instantiate the Interception_Manager
	if(!is_admin()) {
		require_once(PATH . "includes/Interception_Manager.php");
		new Interception_Manager();
	} else {
		add_action("admin_menu", "FFI\\AAM\\register_custom_menu_page");
		
		function register_custom_menu_page() {
			add_menu_page("Archive Manager", "Archive Manager", "update_core", "custompage", "FFI\\AAM\\custom_menu_page");
		}
		
		function custom_menu_page() {
			echo "WHOA! You're here!";
		}
	}
?>