<?php
/**
 * Woocommerce Floating Cart
 *
 * @package       KRWFC
 * @author        Roni Das
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Woocommerce Floating Cart
 * Plugin URI:    https://www.linkedin.com/in/kernelronifullstackdeveloper/
 * Description:   Floating Cart in every page.
 * Version:       1.0.0
 * Author:        Roni Das
 * Author URI:    https://www.linkedin.com/in/kernelronifullstackdeveloper/
 * Text Domain:   woocommerce-floating-cart
 * Domain Path:   /languages
 */

// plugin-boilerplate generated on : https://pluginplate.com/plugin-boilerplate/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 * 
 * The function KRWFC() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
define( 'KRWFC_NAME',			'Woocommerce Floating Cart' );

// Plugin version
define( 'KRWFC_VERSION',		'1.0.0' );

// Plugin Root File
define( 'KRWFC_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'KRWFC_PLUGIN_BASE',	plugin_basename( KRWFC_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'KRWFC_PLUGIN_DIR',	plugin_dir_path( KRWFC_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'KRWFC_PLUGIN_URL',	plugin_dir_url( KRWFC_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once KRWFC_PLUGIN_DIR . 'core/class-woocommerce-floating-cart.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Roni Das
 * @since   1.0.0
 * @return  object|Woocommerce_Floating_Cart
 */
function KRWFC() {
	
	return Woocommerce_Floating_Cart::instance();
}

KRWFC();
