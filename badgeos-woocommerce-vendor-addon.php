<?php
/**
 * Plugin Name: BadgeOS WooCommerce Vendor Addon
 * Plugin URI: http://www.northnetmedia.com/
 * Description: This BadgeOS/WooCommerce add-on allows you create vendors, add likes and award achievements
 * Tags: badgeos
 * Author: Northnet Media
 * Version: 1.0
 * Author URI: https://northnetmedia.com/
 * License: GNU AGPL
 * Text Domain: badgeos-community
 **/

 
/**
 * Our main plugin instantiation class
 *
 * This contains important things that our relevant to
 * our add-on running correctly. Things like registering
 * custom post types, taxonomies, posts-to-posts
 * relationships, and the like.
 *
 * @since 1.0.0
 */
class BadgeOS_VendorAddon {

	/**
	 * Get everything running.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// Define plugin constants
		$this->basename       = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url  = plugins_url( dirname( $this->basename ) );

		// Load translations
		load_plugin_textdomain( 'badgeos-vendor-addon', false, dirname( $this->basename ) . '/languages' );

		// Run our activation and deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// If BadgeOS is unavailable, deactivate our plugin
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );

		// Include our other plugin files
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts') );
		add_action('wp_ajax_nopriv_add_vendor_likes', 'add_vendor_likes');
		add_action('wp_ajax_add_vendor_likes', 'add_vendor_likes');
		add_action( 'admin_enqueue_scripts', array( $this,'selectively_enqueue_admin_script') );
		
		
		add_action('wp_ajax_nopriv_add_vendor_followers', 'add_vendor_followers');
		add_action('wp_ajax_add_vendor_followers', 'add_vendor_followers');

		add_action( 'init', 'vendor_init'  );
		
		add_action( 'add_meta_boxes', array( $this,'register_vendor_meta_boxes')  );

		add_filter( 'archive_template', array( $this, 'get_vendor_archive_page')  ) ;
		add_filter( 'single_template', array( $this, 'get_single_vendor_page'));
		
		if ( ! defined('VENDOR_PROD_PLACEHOLDER')){
			define('VENDOR_PROD_PLACEHOLDER', '/wp-content/uploads/2020/08/placeholder.png'); 
		}

	} /* __construct() */


	/**
	 * Include our plugin dependencies
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// If BadgeOS is available...
		if ( $this->meets_requirements() ) {

			require_once( $this->directory_path . '/includes/rules-engine.php' );
			require_once( $this->directory_path . '/includes/vendor.php' );
			require_once( $this->directory_path . '/includes/shortcodes.php' );

		}

	} /* includes() */

	/**
	 * Activation hook for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// If BadgeOS is available, run our activation functions
		if ( $this->meets_requirements() ) {

			// Do some activation things
			//
			
		}

	} /* activate() */

	/**
	 * Deactivation hook for the plugin.
	 *
	 * Note: this plugin may auto-deactivate due
	 * to $this->maybe_disable_plugin()
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Do some deactivation things.

	} /* deactivate() */

	/**
	 * Check if BadgeOS is available
	 *
	 * @since  1.0.0
	 * @return bool True if BadgeOS is available, false otherwise
	 */
	public static function meets_requirements() {

		if ( class_exists('BadgeOS') && class_exists('WooCommerce') )
			return true;
		else
            return false;
        

	} /* meets_requirements() */
	public function scripts() {

		wp_enqueue_style( 'add-vendor-addon', plugin_dir_url( __FILE__ ) . 'css/style.css', '',  filemtime(plugin_dir_path( __FILE__ ) . 'css/style.css'));
		wp_enqueue_script( 'add-vendor-addon', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'add-vendor-addon', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array( 'jquery' ), null, true );
	
		wp_localize_script( 'add-vendor-addon', 'settings', array(
			'ajaxurl'    => admin_url( 'admin-ajax.php' )
		) );
	}
	/**
	 * Enqueue a script in the WordPress admin on edit.php.
	 *
	 * @param int $hook Hook suffix for the current admin page.
	 */
	public function selectively_enqueue_admin_script( $hook ) {
		if ( 'post.php' != $hook ) {
			return;
		}
		wp_register_script( 'vendor_script', plugin_dir_url( __FILE__ ).'/js/vendor_script.js', array(), '1.0' );

		wp_localize_script( 'vendor_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

		wp_enqueue_script( 'vendor_script' );

	}
		
	/**
	 * Register Metaboxes
	 */
	public function register_vendor_meta_boxes() {
		add_meta_box( 'vendor_details', __( 'Vendor Details', 'textdomain' ), 'show_vendors_list_in_product', 'product', 'side'); 
	}
	
	public function get_vendor_archive_page( $archive_template ) {
		global $post;   
		if ( is_post_type_archive ( 'vendor' ) ) {
			 $archive_template = dirname( __FILE__ ) . '/templates/archive-vendor.php';
		}
		return $archive_template;
   	}
   public function get_single_vendor_page($single_template) {
		global $post;   
		if ( is_singular ( 'vendor' ) ) {
			$single_template = dirname( __FILE__ ) . '/templates/single-vendor.php';
		}
		return $single_template;
   }
	/**
	 * Potentially output a custom error message and deactivate
	 * this plugin, if we don't meet requriements.
	 *
	 * This fires on admin_notices.
	 *
	 * @since 1.0.0
	 */
	public function maybe_disable_plugin() {

		if ( ! $this->meets_requirements() ) {
			// Display our error
			echo '<div id="message" class="error">';
			echo '<p>' . sprintf( __( 'BadgeOS vendor Addon requires BadgeOS and WooCommerce and has been <a href="%s">deactivated</a>. Please install and activate BadgeOS and WooCommerce. Then reactivate this plugin.', 'badgeos-bvendorAddon' ), admin_url( 'plugins.php' ) ) . '</p>';
			echo '</div>';

			// Deactivate our plugin
			deactivate_plugins( $this->basename );
		}

	} /* maybe_disable_plugin() */

} /* BadgeOS_vendorAddon */

// Instantiate our class to a global variable that we can access elsewhere
$GLOBALS['BadgeOS_VendorAddon'] = new BadgeOS_VendorAddon();
