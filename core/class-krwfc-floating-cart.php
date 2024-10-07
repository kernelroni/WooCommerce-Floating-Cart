<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This is the main class that is responsible for registering
 * the core functions, including the files and setting up all features. 
 * 
 * To add a new class, here's what you need to do: 
 * 1. Add your new class within the following folder: core/includes/classes
 * 2. Create a new variable you want to assign the class to (as e.g. public $helpers)
 * 3. Assign the class within the instance() function ( as e.g. self::$instance->helpers = new Woocommerce_Floating_Cart_Helpers();)
 * 4. Register the class you added to core/includes/classes within the includes() function
 * 
 * HELPER COMMENT END
 */

if ( ! class_exists( 'Krwfc_Floating_Cart' ) ) :

	/**
	 * Main Woocommerce_Floating_Cart Class.
	 *
	 * @package		KRWFC
	 * @subpackage	Classes/Woocommerce_Floating_Cart
	 * @since		1.0.0
	 * @author		Roni Das
	 */
	final class Krwfc_Floating_Cart {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Woocommerce_Floating_Cart
		 */
		private static $instance;

		/**
		 * KRWFC helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Woocommerce_Floating_Cart_Helpers
		 */
		public $helpers;

		/**
		 * KRWFC settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Woocommerce_Floating_Cart_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			//_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'new-woocommerce-floating-cart' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			//_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'new-woocommerce-floating-cart' ), '1.0.0' );
		}

		/**
		 * Main Woocommerce_Floating_Cart Instance.
		 *
		 * Insures that only one instance of Woocommerce_Floating_Cart exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Woocommerce_Floating_Cart	The one true Woocommerce_Floating_Cart
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Krwfc_Floating_Cart ) ) {
				self::$instance					= new Krwfc_Floating_Cart;
				//self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Krwfc_Floating_Cart_Helpers();
				self::$instance->settings		= new krwfc_Floating_Cart_Settings();

				//Fire the plugin logic
				new Woocommerce_Floating_Cart_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'KRWFC/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once KRWFC_PLUGIN_DIR . 'core/includes/classes/class-krwfc-floating-cart-helpers.php';
			require_once KRWFC_PLUGIN_DIR . 'core/includes/classes/class-krwfc-floating-cart-settings.php';

			require_once KRWFC_PLUGIN_DIR . 'core/includes/classes/class-krwfc-floating-cart-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			//load_plugin_textdomain( 'new-woocommerce-floating-cart', FALSE, dirname( plugin_basename( KRWFC_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.