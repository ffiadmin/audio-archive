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
		add_action("admin_init", "FFI\\AAM\\add_highcharts");
		
		function register_custom_menu_page() {
			global $menu;
   			global $submenu;
			
			add_menu_page("Statistics", "Archive Manager", "update_core", "custompage", "FFI\\AAM\\custom_menu_page");
			add_submenu_page("custompage", "Update Metadata", "Update Metadata", "update_core", "customstats", "FFI\\AAM\\custom_menu_page");
			
			$submenu['custompage'][0][0] = "Statistics";
		}
		
		function custom_menu_page() {
			echo "<script type=\"text/javascript\">
    var chart;
    jQuery(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'spline'
            },
			credits: {
				enabled: false
			},
			exporting: {
				buttons: {
					exportButton: {
						enabled: false,
					},
					printButton: {
						enabled: false
					}
				}
			},
			legend: {
				enabled: false
			},
            title: {
                text: 'Daily Hits'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            yAxis: {
				min: 0,
                title: {
                    text: null
                }
            },
            tooltip: {
				crosshairs: true,
				shared: true
            },
            series: [{
                name: 'Total',
                data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
            }, {
                name: 'Downloads',
                data: [0, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
            }, {
                name: 'Streams',
                data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
            }]
        });
    });
</script>
			
<div class=\"wrap\">
<div id=\"icon-upload\" class=\"icon32\"><br></div>
<h2>Statistics</h2>

<div id=\"container\" style=\"min-width: 400px; height: 400px; margin: 0 auto\"></div>
</div>";
		}
	
		function add_highcharts() {
			wp_register_script("highcharts", "//cdnjs.cloudflare.com/ajax/libs/highcharts/2.3.5/highcharts.js", array("jquery"));
			wp_enqueue_script("highcharts");
			
			wp_register_script("highcharts-exporting", "//cdnjs.cloudflare.com/ajax/libs/highcharts/2.3.5/modules/exporting.js", array("highcharts"));
			wp_enqueue_script("highcharts-exporting");
		}
		
		
	}
?>