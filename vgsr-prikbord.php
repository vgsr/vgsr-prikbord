<?php

/**
 * The VGSR Ab-actiaal Prikbord Plugin
 *
 * @package VGSR Ab-actiaal Prikbord
 * @subpackage Main
 */

/**
 * Plugin Name:       VGSR Ab-actiaal Prikbord
 * Description:       Het ab-actiaal prikbord voor intern gebruik. Vereist VGSR.
 * Plugin URI:        https://github.com/vgsr/vgsr-prikbord
 * Version:           1.0.3
 * Author:            Laurens Offereins
 * Author URI:        https://github.com/lmoffereins
 * Text Domain:       vgsr-prikbord
 * Domain Path:       /languages/
 * GitHub Plugin URI: vgsr/vgsr-prikbord
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'VGSR_Prikbord' ) ) :
/**
 * Main Plugin Class
 *
 * @since 1.0.0
 */
final class VGSR_Prikbord {

	/**
	 * Setup and return the singleton pattern
	 *
	 * @since 1.0.0
	 *
	 * @uses VGSR_Prikbord::setup_globals()
	 * @uses VGSR_Prikbord::includes()
	 * @uses VGSR_Prikbord::setup_actions()
	 * @return The single VGSR_Prikbord
	 */
	public static function instance() {

		// Store instance locally
		static $instance = null;

		if ( null === $instance ) {
			$instance = new VGSR_Prikbord;
			$instance->setup_globals();
			$instance->includes();
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Prevent the plugin class from being loaded more than once
	 */
	private function __construct() { /* Nothing to do */ }

	/** Private Methods *******************************************************/

	/**
	 * Setup class defaults
	 *
	 * @since 1.0.0
	 */
	private function setup_globals() {

		/** Versions **********************************************************/

		$this->version      = '1.0.2';

		/** Paths *************************************************************/

		// Setup some base path and URL information
		$this->file         = __FILE__;
		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url ( $this->file );

		// Includes
		$this->includes_dir = trailingslashit( $this->plugin_dir . 'includes' );
		$this->includes_url = trailingslashit( $this->plugin_url . 'includes' );

		// Languages
		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );

		/** Users *************************************************************/

		$this->post_type_id = apply_filters( 'vgsr_prikbord_post_type', 'vgsr_prikbord' );
	}

	/**
	 * Include the required files
	 *
	 * @since 1.1.0
	 */
	private function includes() {

		/** Core **************************************************************/

		require( $this->includes_dir . 'actions.php'        );
		require( $this->includes_dir . 'functions.php'      );
		require( $this->includes_dir . 'prikbord-items.php' );
		require( $this->includes_dir . 'sub-actions.php'    );

		/** Admin *************************************************************/

		if ( is_admin() ) {
			require( $this->includes_dir . 'admin.php' );
		}
	}

	/**
	 * Setup default hooks and actions
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'vgsr_prikbord_activation'   );
		add_action( 'deactivate_' . $this->basename, 'vgsr_prikbord_deactivation' );

		// Register prikbord items
		add_action( 'vgsr_init',   array( $this, 'register_post_type' ), 11 );
		add_action( 'parse_query', array( $this, 'parse_query'        )     );

		// Append attachments to post content
		add_filter( 'the_content', array( $this, 'append_attachments' ) );
	}

	/** Public Methods ********************************************************/

	/**
	 * Register the prikbord post type
	 *
	 * Hide prikbord items for non-vgsr users.
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {

		// Check user status
		$access = vgsr_prikbord_check_access();

		// Register the post type
		register_post_type(
			vgsr_prikbord_get_item_post_type(),
			array(
				'labels'              => vgsr_prikbord_get_item_post_type_labels(),
				'supports'            => vgsr_prikbord_get_item_post_type_supports(),
				'description'         => __( 'VGSR Prikbord Items', 'vgsr-prikbord' ),
				'capabilities'        => vgsr_prikbord_get_item_post_type_caps(),
				'capability_type'     => array( 'vgsr_prikbord_item', 'vgsr_prikbord_items' ),
				'hierarchical'        => false,
				'public'              => $access,
				'has_archive'         => true,
				'rewrite'             => vgsr_prikbord_get_item_post_type_rewrite(),
				'query_var'           => true,
				'exclude_from_search' => ! $access,
				'show_ui'             => current_user_can( 'vgsr_prikbord_item_admin' ),
				'show_in_nav_menus'   => $access,
				'menu_icon'           => 'dashicons-pressthis',
				'vgsr'                => true // VGSR exclusive post type
			)
		);
	}

	/**
	 * Add checks for plugin conditions to parse_query action
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Query $posts_query
	 */
	public function parse_query( $posts_query ) {

		// Bail when this is not the main loop
		if ( ! $posts_query->is_main_query() )
			return;

		// Bail when filters are suppressed on this query
		if ( true === $posts_query->get( 'suppress_filters' ) )
			return;

		// Bail when in admin
		if ( is_admin() )
			return;

		/**
		 * Find out whether this is still a Prikbord request, even though the post type
		 * was defined as non-public. In that case, WP couldn't match the query vars. This
		 * way we force WP to 404, and not default to the blog index when nothing matched.
		 */
		$post_type_object = get_post_type_object( vgsr_prikbord_get_item_post_type() );
		$wp_query_vars    = wp_parse_args( $GLOBALS['wp']->matched_query, array( 'post_type' => false, $post_type_object->query_var => false ) );
		$is_prikbord      = $post_type_object->name === $wp_query_vars['post_type'] || ! empty( $wp_query_vars[ $post_type_object->query_var ] );

		/**
		 * 404 and bail when the user has no access.
		 */
		if ( $is_prikbord && ! vgsr_prikbord_check_access() ) {
			$posts_query->set_404();
			return;
		}
	}

	/** Template **************************************************************/

	/**
	 * Append list of attachments to the post content body
	 *
	 * @since 1.0.0
	 *
	 * @uses apply_filters() Calls 'vgsr_prikbord_item_attachment'
	 * @uses apply_filters() Calls 'vgsr_prikbord_attachment_item'
	 * @uses apply_filters() Calls 'vgsr_prikbord_items_title'
	 *
	 * @param string $content The post content
	 * @return string Content
	 */
	public function append_attachments( $content ) {

		// Bail when this is not a Prikbord Item
		if ( ! $post = vgsr_prikbord_get_item() )
			return $content;

		// Get all the Item's attachments. `false` means all mime-types
		$attachments = get_attached_media( false, $post );
		$atts_list   = array();

		// Walk attachments
		foreach ( $attachments as $attachment ) {

			// Get file
			$file_path = get_attached_file( $attachment->ID );

			// Skip when the file does not exist
			if ( ! file_exists( $file_path ) )
				continue;

			// Get file details
			$file_ext  = pathinfo( $file_path, PATHINFO_EXTENSION );
			$file_size = size_format( filesize( $file_path ) );

			// Get the title once
			$title = get_the_title( $attachment->ID );

			// Setup list item as titled link to file
			$li = sprintf( '<a href="%s" title="%s" target="_blank">%s</a>',
				esc_url( wp_get_attachment_url( $attachment->ID ) ),
				sprintf( __( 'View &#8220;%s&#8221;', 'vgsr-prikbord' ), $title ),
				$title . sprintf( ' (%s%s)', $file_ext, ! empty( $file_size ) ? ", $file_size" : '' )
			);

			// Enable item filtering
			$atts_list[ $attachment->ID ] = apply_filters( 'vgsr_prikbord_item_attachment', $li, $attachment );
		}

		// Start list output
		$list_title = ! empty( $atts_list ) && ! empty( $content ) ? __( 'Attachments', 'vgsr-prikbord' ) : '';
		$output     = apply_filters( 'vgsr_prikbord_items_title', $list_title, count( $atts_list ) );

		// Having attachments
		if ( $atts_list ) {

			// Wrap list title
			if ( ! empty( $ouput ) ) {
				$output = '<h3>' . $output . "</h3>\n";
			}

			// Setup attachment list
			$output .= '<ul class="attachments prikbord-items">';
			foreach ( $atts_list as $attachment_id => $li ) {
				$output .= apply_filters( 'vgsr_prikbord_attachment_item', '<li>' . $li . '</li>', $attachment_id );
			}
			$output .= '</ul>';

		// Without attachments
		} else {
			$output .= '<p>' . __( 'This prikbord item has no attachments.', 'vgsr-prikbord' ) . '</p>';
		}

		// Append created attachment list
		$content .= $output;

		return $content;
	}
}

/**
 * Return single instance of the plugin's main class
 *
 * @since 1.0.0
 *
 * @return VGSR_Prikbord
 */
function vgsr_prikbord() {
	return VGSR_Prikbord::instance();
}

// Initiate plugin on load
vgsr_prikbord();

endif; // class_exists
