<?php

/**
 * The VGSR Ab-actiaal Prikbord Plugin
 *
 * @package VGSR Ab-actiaal Prikbord
 * @subpackage Main
 */

/**
 * Plugin Name: VGSR Ab-actiaal Prikbord
 * Description: Het ab-actiaal prikbord voor intern gebruik. Vereist VGSR.
 * Plugin URI:  https://github.com/vgsr/vgsr-prikbord
 * Version:     1.0.0
 * Author:      Laurens Offereins
 * Author URI:  https://github.com/lmoffereins
 * Text Domain: vgsr-prikbord
 * Domain Path: /languages/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'VGSR_Prikbord' ) ) :
/**
 * Main Plugin Class
 *
 * @since 1.0.0
 */
class VGSR_Prikbord {

	/**
	 * The prikbord post type name
	 * @var string
	 */
	private $post_type_id;

	/**
	 * Setup class structure
	 *
	 * @since 1.0.0
	 *
	 * @uses VGSR_Prikbord::setup_globals()
	 * @uses VGSR_Prikbord::setup_actions()
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/** Private Methods *******************************************************/

	/**
	 * Setup class defaults
	 *
	 * @since 1.0.0
	 */
	private function setup_globals() {

		/** Versions **********************************************************/

		$this->version    = '1.0.0';

		/** Paths *************************************************************/

		// Setup some base path and URL information
		$this->file       = __FILE__;
		$this->basename   = plugin_basename( $this->file );
		$this->plugin_dir = plugin_dir_path( $this->file );
		$this->plugin_url = plugin_dir_url ( $this->file );

		// Languages
		$this->lang_dir   = trailingslashit( $this->plugin_dir . 'languages' );

		/** Users *************************************************************/

		$this->post_type_id = apply_filters( 'vgsr_prikbord_post_type', 'vgsr_prikbord' );
	}

	/**
	 * Setup default hooks and actions
	 *
	 * @since 1.0.0
	 *
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {
		add_action( 'vgsr_init', array( $this, 'register_post_type' ) );
	}

	/** Public Methods ********************************************************/

	/**
	 * Return the prikbord post type
	 *
	 * @since 1.0.0
	 *
	 * @return string Prikbord post type name
	 */
	public function get_post_type() {
		return $this->post_type_id;
	}

	/**
	 * Register the prikbord post type
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {
		register_post_type( $this->get_post_type(), array(
			'labels' => array(
				'name' => __( 'Prikbord Items', 'vgsr-prikbord' ),
			),
			'public' => is_user_vgsr() // Hide prikbord for non-vgsr
		) );
	}

}

/**
 * Setup prikbord plugin
 *
 * @since 1.0.0
 *
 * @uses VGSR_Prikbord
 */
function vgsr_prikbord() {

	// Bail if VGSR is not active
	if ( ! function_exists( 'vgsr' ) )
		return;

	new VGSR_Prikbord;
}

add_action( 'plugins_loaded', 'vgsr_prikbord' );

endif; // class_exists
