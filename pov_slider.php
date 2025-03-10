<?php
/*
Plugin Name: POV Sliders
Plugin URI: https://github.com/theREDspace/pov_slider
Description: A collection of sliders for featuring content on WordPress pages
Version: 1.0.4
Author: Luke DeWitt
Author URI: http://www.whatadewitt.ca
Author Email: luke.dewitt@theredspace.com
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class POVSlider {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// Register admin styles and scripts
		add_action( 'admin_print_styles-appearance_page_pov_slider_featured_homepage', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_print_scripts-appearance_page_pov_slider_featured_homepage', array( $this, 'register_admin_scripts' ) );	

	    add_action('admin_menu', array( $this, 'pov_slider_register_homepage_slider_page' ) );
	    add_action('wp_ajax_pov_slider_homepage_slider_search', array( $this, 'pov_slider_homepage_slider_search' ) );
	} // end constructor

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {
		$domain = 'pov-slider';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	} // end plugin_textdomain

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
		wp_enqueue_style( 'pov-slider-admin-styles', plugins_url( 'pov_slider/css/admin.css' ) );
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {
		wp_enqueue_script( 'pov-slider-admin-script', plugins_url( 'pov_slider/js/admin.js' ), array('jquery', 'jquery-ui-sortable') );
	} // end register_admin_scripts


	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	function pov_slider_register_homepage_slider_page() {
		require_once('views/homepage_slider.php');
		add_submenu_page( 'themes.php', 'POV Slider', 'POV Slider', 'manage_options', 'pov_slider_featured_homepage', 'pov_slider_homepage_slider_page' ); 
	}

	function pov_slider_homepage_slider_search() {

		$args = array ( 
			'posts_per_page' => -1,
			'post_type' => 'any',
			'post_status' => 'publish',
			's' => $_POST['s'] 
		);
		
		$posts_query = new WP_Query($args);
		$return_data = array();	
		
		while ( $posts_query->have_posts() ) : $posts_query->the_post();
			$p = get_post_type_object(get_post_type())->labels->singular_name;
			array_push($return_data, array( "id" => get_the_ID(), "title" => get_the_title(), "type" => $p ));
		endwhile;
		
		wp_reset_postdata();
		wp_reset_query();
		
		echo json_encode($return_data);
		die();
	}

} // end class

$pov_slider = new POVSlider();

function pov_slider_get_featured_posts() {
	return get_option('pov_slider_featured_posts');
}