<?php 

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://naimbhuiya.devshark.net
 * @since             1.0.0
 * @package           Devshark_Frontend
 *
 * @wordpress-plugin
 * Plugin Name:       Devshark Frontend
 * Plugin URI:        https://devshark-backend-and-frontend.devshark.net
 * Description:       This is a plugin for the DevShark frontend for handling  
 * Version:           1.0.0
 * Author:            Naim Bhuiya
 * Author URI:        https://naimbhuiya.devshark.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       devshark-frontend
 * Domain Path:       /languages
 */

/**
 *   import the files 
 */


define("DEVSHARK_FRONTEND_TEXTDOMAIN" , "devshark-frontend");
define("DEVSHARK_FRONTEND_PREFIX" , "devshark-frontend");

$devshark_api = get_option( DEVSHARK_FRONTEND_PREFIX . '-api-url', '' );
$devshark_api_key = get_option( DEVSHARK_FRONTEND_PREFIX . '-api-key', '' );

// Define devshark api for access globaly 
define( 'DEVSHARK_API_URL' , $devshark_api );

// Define devshark api key for access globaly 
define( 'DEVSHARK_API_KEY' , $devshark_api_key );

require_once(plugin_dir_path( __FILE__ ) . 'admin/admin.php');
require_once(plugin_dir_path( __FILE__ ) . 'src/assets.php');
require_once(plugin_dir_path( __FILE__ ) . 'src/portfolio.php');