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

//Create plugin-specific global definitions
	define("FFI_AAM_REAL_ADDR", get_site_url() . "/wp-content/plugins/audio-archive/");
	define("FFI_AAM_FAKE_ADDR", get_site_url() . "/audio-archive/");
	
//Register installation and uninstallation hooks
	require_once(FFI_AAM_REAL_ADDR . "includes/FFI_AAM_Hook_Manager.php");
	new FFI_AAM_Hook_Manager("FFI_AAM_Installer", "FFI_AAM_Uninstaller");
	

?>