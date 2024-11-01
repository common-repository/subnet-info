<?php

/**
 * @link              https://www.yellownote.nl
 * @since             1.0.0
 * @package           Subnet_info
 *
 * @wordpress-plugin
 * Plugin Name:       Subnet Info
 * Plugin URI:        https://wordpress.org/plugins/search/subnetinfo/
 * Description:       Provides detailed information about the IP adress and subnet using a shortcode.
 * Version:           1.0.1
 * Author:            Cris van Geel
 * Author URI:        https://www.yellownote.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       subnetinfo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'SUBNETINFO_VERSION', '1.0.1' );


class subnetinfo {
	

	//magic function (triggered on initialization)
	public function __construct(){

		register_activation_hook(__FILE__, array($this,'plugin_activate')); //activate hook
		register_deactivation_hook(__FILE__, array($this,'plugin_deactivate')); //deactivate hook

	}
	
}


//include shortcodes
include(plugin_dir_path(__FILE__) . 'inc/class_subnetinfo_shortcode.php');	

//Include CSS 
add_action('wp_enqueue_scripts', 'subnetinfo_callback');

//Callback 
	function subnetinfo_callback() {
		wp_register_style('subnetinfo', plugins_url('css/style.css', __FILE__));
		wp_enqueue_style('subnetinfo');
    
	}

			

