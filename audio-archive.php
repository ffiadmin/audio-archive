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
			echo "<script>
    jQuery(document).ready(function() {
        new Highcharts.Chart({
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
		
		var colors = Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
		    return {
		        radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
		        stops: [
		            [0, color],
		            [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
		        ]
		    };
		});
		var categories = ['MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera'],
		name = 'Browser brands',
		data = [{
				y: 55.11,
				color: colors[0],
				drilldown: {
					name: 'MSIE versions',
					categories: ['MSIE 6.0', 'MSIE 7.0', 'MSIE 8.0', 'MSIE 9.0'],
					data: [10.85, 7.35, 33.06, 2.81],
					color: colors[0]
				}
			}, {
				y: 21.63,
				color: colors[1],
				drilldown: {
					name: 'Firefox versions',
					categories: ['Firefox 2.0', 'Firefox 3.0', 'Firefox 3.5', 'Firefox 3.6', 'Firefox 4.0'],
					data: [0.20, 0.83, 1.58, 13.12, 5.43],
					color: colors[1]
				}
			}, {
				y: 11.94,
				color: colors[2],
				drilldown: {
					name: 'Chrome versions',
					categories: ['Chrome 5.0', 'Chrome 6.0', 'Chrome 7.0', 'Chrome 8.0', 'Chrome 9.0',
						'Chrome 10.0', 'Chrome 11.0', 'Chrome 12.0'],
					data: [0.12, 0.19, 0.12, 0.36, 0.32, 9.91, 0.50, 0.22],
					color: colors[2]
				}
			}, {
				y: 7.15,
				color: colors[3],
				drilldown: {
					name: 'Safari versions',
					categories: ['Safari 5.0', 'Safari 4.0', 'Safari Win 5.0', 'Safari 4.1', 'Safari/Maxthon',
						'Safari 3.1', 'Safari 4.1'],
					data: [4.55, 1.42, 0.23, 0.21, 0.20, 0.19, 0.14],
					color: colors[3]
				}
			}, {
				y: 2.14,
				color: colors[4],
				drilldown: {
					name: 'Opera versions',
					categories: ['Opera 9.x', 'Opera 10.x', 'Opera 11.x'],
					data: [ 0.12, 0.37, 1.65],
					color: colors[4]
				}
			}];


	// Build the data arrays
	var browserData = [];
	var versionsData = [];
	for (var i = 0; i < data.length; i++) {

		// add browser data
		browserData.push({
			name: categories[i],
			y: data[i].y,
			color: data[i].color
		});

		// add version data
		for (var j = 0; j < data[i].drilldown.data.length; j++) {
			var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
			versionsData.push({
				name: data[i].drilldown.categories[j],
				y: data[i].drilldown.data[j],
				color: Highcharts.Color(data[i].color).brighten(brightness).get()
			});
		}
	}

	// Create the chart
	new Highcharts.Chart({
		chart: {
			renderTo: 'pie',
			type: 'pie'
		},
		title: {
			text: 'Browser market share, April, 2011'
		},
		yAxis: {
			title: {
				text: 'Total percent market share'
			}
		},
		plotOptions: {
			pie: {
				shadow: false
			}
		},
		tooltip: {
			valueSuffix: '%'
		},
		series: [{
			name: 'Browsers',
			data: browserData,
			size: '60%',
			dataLabels: {
				formatter: function() {
					return this.y > 5 ? this.point.name : null;
				},
				color: 'white',
				distance: -30
			}
		}, {
			name: 'Versions',
			data: versionsData,
			innerSize: '60%',
			dataLabels: {
				formatter: function() {
					// display only if larger than 1
					return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
				}
			}
		}]
	});
	
	
	// Build the chart
	new Highcharts.Chart({
		chart: {
			renderTo: 'dvs'
		},
		title: {
			text: 'Downloads vs. Streams'
		},
		tooltip: {
			pointFormat: '<b>{point.percentage}%</b>',
			percentageDecimals: 1
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					formatter: function() {
						var percent = this.percentage;
						
						return '<b>'+ this.point.name +'</b>: '+ percent.toFixed(1) +' %';
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Downloads vs. Streams',
			data: [
				['Downloads', 45.0],
				['Streams', 55.0]
			]
		}]
	});
});
    
</script>
			
<div class=\"wrap\">
<div id=\"icon-upload\" class=\"icon32\"><br></div>
<h2>Statistics</h2>

<div id=\"container\" style=\"min-width: 400px; height: 400px; margin: 0 auto\"></div>
<div id=\"pie\" style=\"min-width: 400px; height: 400px; margin: 0 auto\"></div>
<div id=\"dvs\" style=\"min-width: 400px; height: 400px; margin: 0 auto\"></div>
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