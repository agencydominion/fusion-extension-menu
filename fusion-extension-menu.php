<?php
/**
 * @package Fusion_Extension_Menu
 */
/**
 * Plugin Name: Fusion : Extension - Menu
 * Plugin URI: http://fusion.1867dev.com
 * Description: Menu Extension Package for Fusion.
 * Version: 1.0.2
 * Author: Agency Dominion
 * Author URI: http://agencydominion.com
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
		load_plugin_textdomain( 'fusion-extension-menu', false, plugin_dir_url( __FILE__ ) . 'languages' );
		
		// Enqueue admin scripts and styles
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'));
		
		// Enqueue front end scripts and styles
		add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts_styles'));	
		
		// Plugin activation / deactivation hooks
		register_activation_hook( __FILE__, array($this, 'plugin_activated') );
		register_deactivation_hook( __FILE__, array($this, 'plugin_deactivated') );
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
		wp_register_script( 'fsn_menu', plugin_dir_url( __FILE__ ) . 'includes/js/fusion-extension-menu.js', array('jquery'), '1.0.0', true );
		wp_enqueue_style( 'fsn_menu', plugin_dir_url( __FILE__ ) . 'includes/css/fusion-extension-menu.css', false, '1.0.0' );
	}
	
}

$fsn_extension_menu = new FusionExtensionMenu();

//EXTENSIONS

//menu
require_once('includes/extensions/menu.php');

?>