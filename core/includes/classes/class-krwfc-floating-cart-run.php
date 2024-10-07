<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This class is used to bring your plugin to life. 
 * All the other registered classed bring features which are
 * controlled and managed by this class.
 * 
 * Within the add_hooks() function, you can register all of 
 * your WordPress related actions and filters as followed:
 * 
 * add_action( 'my_action_hook_to_call', array( $this, 'the_action_hook_callback', 10, 1 ) );
 * or
 * add_filter( 'my_filter_hook_to_call', array( $this, 'the_filter_hook_callback', 10, 1 ) );
 * or
 * add_shortcode( 'my_shortcode_tag', array( $this, 'the_shortcode_callback', 10 ) );
 * 
 * Once added, you can create the callback function, within this class, as followed: 
 * 
 * public function the_action_hook_callback( $some_variable ){}
 * or
 * public function the_filter_hook_callback( $some_variable ){}
 * or
 * public function the_shortcode_callback( $attributes = array(), $content = '' ){}
 * 
 * 
 * HELPER COMMENT END
 */

/**
 * Class Woocommerce_Floating_Cart_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		KRWFC
 * @subpackage	Classes/Woocommerce_Floating_Cart_Run
 * @author		Roni Das
 * @since		1.0.0
 */
class Woocommerce_Floating_Cart_Run{

	private $woocommerce_plugin_path = null;

	/**
	 * Our Woocommerce_Floating_Cart_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->woocommerce_plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
		$this->add_hooks();
		
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){



		register_activation_hook(KRWFC_PLUGIN_FILE, array( $this, 'krwfc_floating_cart_activate' ) );

		// run only if woocommerce plugin is active.
		if (in_array( $this->woocommerce_plugin_path, wp_get_active_and_valid_plugins())){

			add_action( 'plugin_action_links_' . KRWFC_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
	
			add_action( 'wp_footer', array( $this, 'krwfc_add_cart_wrapper' ) );
	
			add_action("wp_ajax_krwfc_get_cart", array( $this, 'krwfc_get_cart' ), 20);
			add_action("wp_ajax_nopriv_krwfc_get_cart", array( $this, 'krwfc_get_cart' ), 20);
	
	
			add_action("wp_ajax_krwfc_remove_cart_item", array( $this, 'krwfc_remove_cart_item' ), 20);
			add_action("wp_ajax_nopriv_krwfc_remove_cart_item", array( $this, 'krwfc_remove_cart_item' ), 20);
		}
	



	
	
	}

	function krwfc_floating_cart_activate() {
		// run the plugin code if woocommerce exist and active
		if (!in_array( $this->woocommerce_plugin_path, wp_get_active_and_valid_plugins())){
			wp_die(esc_html('Sorry, but New Woocommerce Floating Cart requires WooCommerce to be installed and active. <br><a href="') . esc_url(admin_url('plugins.php')) . esc_html('">&laquo; Return to Plugins</a>'));			
		}		
	}


	public function krwfc_get_cart(){

		$data = [];
		$data['total_products'] = WC()->cart->get_cart_contents_count();
		$data['total_amount'] = WC()->cart->get_cart_contents_total();
		$data['products'] = $this->get_cart_products();
		$data['currency_symbol'] = get_woocommerce_currency_symbol();
		echo wp_json_encode($data, true);


		wp_die();

	}

	public function get_cart_products(){

		$products = [];
		$cartItems = WC()->cart->get_cart();
		if($cartItems){
			foreach($cartItems as $item => $values) { 

				//print_r($values);
				// $_product =  wc_get_product( $values->product_id); 
				$_product = $values['data'];
				$titie = $_product->get_title();

				$product = [];
				$product['title'] = $titie;
				$product['product_id'] = $values['product_id'];
				$product['url'] = get_permalink( $product['product_id'] );
				$product['quantity'] = $values['quantity'];
				$product['price'] = get_woocommerce_currency_symbol() . number_format($values['line_total'],2);
				$product['sale_price'] = $_product->get_sale_price();
				$product['regular_price'] = $_product->get_regular_price();
				$product['images'] = wp_get_attachment_url( $_product->get_image_id() );
				$product['categories'] = wc_get_product_category_list($product['product_id']);
				$product['sku'] = $_product->get_sku();
				$product['currency'] = html_entity_decode(get_woocommerce_currency_symbol());

				if(intval($product['sale_price']) && intval($product['regular_price']) && intval($product['regular_price']) > intval($product['sale_price'])){
					$product['price_saved'] = (intval($product['regular_price']) - intval($product['sale_price'])) * intval($product['quantity']);
				}else{
					$product['price_saved'] = 0;
				}
				

				$products[] = $product;
			} 
		}

		return $products;

	}

	public function krwfc_remove_cart_item() {

		//print_r($_POST);
		$nonce = check_ajax_referer("krwfc_get_cart", "nonce");
		if(!$nonce){
			echo esc_html("Invalid nonce"); // Product not found in the cart
			wp_die();
		}

		$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
		// Get the current cart items
		$cart = WC()->cart->get_cart();
	
		// Loop through each cart item
		foreach ($cart as $cart_item_key => $cart_item) {

			// Check if the product ID matches
			if ($cart_item['product_id'] == $product_id) {
				// Remove the item from the cart
				WC()->cart->remove_cart_item($cart_item_key);
				echo esc_html("Removed"); // Successfully removed
				wp_die();
			}
		}

		echo esc_html("Not Removed"); // Product not found in the cart
		wp_die();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links ) {

		$links['our_shop'] = sprintf( '<a href="%s" title="Contact Me" style="font-weight:700;">%s</a>', 'https://github.com/kernelroni', __( 'Contact Me', 'new_woocommerce-floating-cart' ) );

		return $links;
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		wp_enqueue_style( 'krwfc-styles', KRWFC_PLUGIN_URL . 'assets/css/krwfc.css', array(), KRWFC_VERSION, 'all' );
		wp_enqueue_script( 'krwfc-scripts', KRWFC_PLUGIN_URL . 'assets/js/krwfc.js', array('jquery'), KRWFC_VERSION, false );
		// in page js
		wp_localize_script( 'krwfc-scripts', 'krwfc_var', array(
			'plugin_name'   	=> __( 'Floating Cart for Woocommerce' ),
			'ajax_url' 	=> admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( "krwfc_get_cart" ),
		));
		
	}

	public function krwfc_add_cart_wrapper(){
		
		if (!is_cart()){
			include_once KRWFC_PLUGIN_DIR . 'html/cart.php';
		}
		
	}

}
