<?php
/**
 * @package Fusion_Extension_Menu
 */
/**
 * Plugin Name: Fusion : Extension - Menu
 * Plugin URI: http://www.agencydominion.com/fusion/
 * Description: Menu Extension Package for Fusion.
 * Version: 1.1.20
 * Author: Agency Dominion
 * Author URI: http://agencydominion.com
 * Text Domain: fusion-extension-menu
 * Domain Path: /languages/
 * License: GPL2
 */

/**
 * FusionExtensionMenu class.
 *
 * Class for initializing an instance of the Fusion Menu Extension.
 *
 * @since 1.0.0
 */


class FusionExtensionMenu	{
	public function __construct() {

		// Initialize the language files
		add_action('plugins_loaded', array($this, 'load_textdomain'));

		// Enqueue admin scripts and styles
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'));

		// Enqueue front end scripts and styles
		add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts_styles'));

	}

	/**
	 * Load Textdomain
	 *
	 * @since 1.1.11
	 *
	 */

	public function load_textdomain() {
		load_plugin_textdomain( 'fusion-extension-menu', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Enqueue JavaScript and CSS on Admin pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current admin page.
	 */

	public function admin_enqueue_scripts_styles($hook_suffix) {
		global $post;

		$options = get_option('fsn_options');
		$fsn_post_types = !empty($options['fsn_post_types']) ? $options['fsn_post_types'] : '';

		// Editor scripts and styles
		if ( ($hook_suffix == 'post.php' || $hook_suffix == 'post-new.php') && (!empty($fsn_post_types) && is_array($fsn_post_types) && in_array($post->post_type, $fsn_post_types)) ) {
			wp_enqueue_script( 'fsn_menu_admin', plugin_dir_url( __FILE__ ) . 'includes/js/fusion-extension-menu-admin.js', array('jquery'), '1.0.0', true );
			wp_localize_script( 'fsn_menu_admin', 'fsnExtMenuJS', array(
					'fsnEditMenuNonce' => wp_create_nonce('fsn-admin-edit-menu')
				)
			);
			//add translation strings to script
			$translation_array = array(
				'error' => __('Oops, something went wrong. Please reload the page and try again.','fusion-extension-menu'),
				'layout_change' => __('Changing the Menu Layout will erase the current Menu. Continue?','fusion-extension-menu')
			);
			wp_localize_script('fsn_menu_admin', 'fsnExtMenuL10n', $translation_array);
		}
	}

	/**
	 * Enqueue JavaScript and CSS on Front End pages.
	 *
	 * @since 1.0.0
	 *
	 */

	public function front_enqueue_scripts_styles() {
		wp_register_script('bootstrap_hover_dropdown', plugin_dir_url( __FILE__ ) . 'includes/js/bootstrap-hover-dropdown.min.js', array('jquery'), '2.1.3', true);
		//plugin
		wp_register_script( 'fsn_menu', plugin_dir_url( __FILE__ ) . 'includes/js/fusion-extension-menu.js', array('jquery','fsn_core','bootstrap_hover_dropdown'), '1.1.16', true );
		wp_enqueue_style( 'fsn_menu', plugin_dir_url( __FILE__ ) . 'includes/css/fusion-extension-menu.css', false, '1.0.0' );
	}

}

$fsn_extension_menu = new FusionExtensionMenu();

//EXTENSIONS

//menu
require_once('includes/extensions/menu.php');

?>
