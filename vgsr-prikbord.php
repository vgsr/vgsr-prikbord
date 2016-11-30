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
		require( $this->includes_dir . 'actions.php'     );
		require( $this->includes_dir . 'functions.php'   );
		require( $this->includes_dir . 'sub-actions.php' );
	}

	/**
	 * Setup default hooks and actions
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		// Bail when VGSR is not active
		if ( ! function_exists( 'vgsr' ) )
			return;

		// Fetch post type for later use
		$post_type = vgsr_prikbord_get_item_post_type();

		// Register prikbord items
		add_action( 'init',        array( $this, 'register_post_type' ), 11 );
		add_action( 'parse_query', array( $this, 'parse_query'        )     );

		// Append attachments to post content
		add_filter( 'the_content', array( $this, 'append_attachments' ) );

		// Admin columns
		add_action( 'vgsr_admin_head',                         array( $this, 'print_admin_scripts' )        );
		add_action( "manage_{$post_type}_posts_columns",       array( $this, 'add_columns'         )        );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'add_column_content'  ), 10, 2 );
	}

	/**
	 * Run logic on plugin activation
	 *
	 * @since 1.0.0
	 */
	public static function activation() {

		// Instatiate plugin once
		$prikbord = new VGSR_Prikbord;

		// Trigger registering our post type
		$prikbord->register_post_type();

		// Clear permalinks after post type registration
		flush_rewrite_rules();

		do_action( 'vgsr_prikbord_activation' );
	}

	/**
	 * Run logic on plugin deactivation
	 *
	 * @since 1.0.0
	 */
	public static function deactivation() {

		// Clear permalinks without our post type
		flush_rewrite_rules();

		do_action( 'vgsr_prikbord_deactivation' );
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
		$access = is_user_vgsr();

		// Register the post type
		register_post_type(
			vgsr_prikbord_get_item_post_type(),
			array(
				'labels'              => vgsr_prikbord_get_item_post_type_labels(),
				'rewrite'             => array( 'slug' => 'prikbord', 'with_front' => false ),
				'supports'            => array( 'title', 'editor' ),
				'description'         => __( 'VGSR Prikbord Items', 'vgsr-prikbord' ),
				'has_archive'         => 'prikbord',
				'exclude_from_search' => ! $access,
				'publicly_queryable'  => $access,
				'show_in_nav_menus'   => true,
				'public'              => $access, // Hide prikbord for non-vgsr
				'show_ui'             => true,
				'can_export'          => true,
				'hierarchical'        => false,
				'query_var'           => true,
				'menu_icon'           => 'dashicons-pressthis',
				'vgsr'                => true // VGSR exclusive post type
			)
		);
	}

	/**
	 * Add checks for plugin conditions to parse_query action
	 *
	 * @since 1.0.4
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
		if ( $is_prikbord && ! is_user_vgsr() ) {
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
	 * @param string $content The post content
	 * @return string Content
	 */
	public function append_attachments( $content ) {
		$post = get_post();

		// Bail if this is not a prikbord item
		if ( vgsr_prikbord_get_item_post_type() != $post->post_type )
			return $content;

		// Get all attachments. 'false' means all mime-types
		$attachments = get_attached_media( false );
		$counter = 0;

		// Having attachments
		if ( ! empty( $attachments ) ) {

			// Setup list title
			$list_title = '<h3>' . __( 'Attachments', 'vgsr-prikbord' ) . '</h3>';

			// Start list
			$list = '<ul class="attachments prikbord-items">';

			// Walk all attachments
			foreach ( $attachments as $attachment ) {

				// Get file
				$file_path = get_attached_file( $attachment->ID );

				// Check for existence
				if ( ! file_exists( $file_path ) )
					continue;

				// Get file details
				$file_ext  = pathinfo( $file_path, PATHINFO_EXTENSION );
				$file_size = size_format( filesize( $file_path ) );

				// Get the title once
				$title = get_the_title( $attachment->ID );

				// Setup list item as titled link to file
				$item = sprintf( '<li><a href="%s" title="%s" target="_blank">%s</a></li>',
					esc_url( wp_get_attachment_url( $attachment->ID ) ),
					sprintf( __( 'View &#8220;%s&#8221;', 'vgsr-prikbord' ), $title ),
					$title . sprintf( ' (%s%s)', $file_ext, ! empty( $file_size ) ? ", $file_size" : '' )
				);

				// Increment
				$counter++;

				// Enable item filtering
				$list .= apply_filters( 'vgsr_prikbord_attachment_item', $item, $attachment );
			}

			// Close list
			$list .= '</ul>';
		}

		// Without attachments
		if ( empty( $attachments ) || empty( $counter ) ) {
			$list_title = '';
			$list       = '<p>' . __( 'This prikbord item has no attachments.', 'vgsr-prikbord' ) . '</p>';
		}

		return $content . apply_filters( 'vgsr_prikbord_items_title', $list_title, $counter ) . $list;
	}

	/**
	 * Return the post attachment count
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @return int Post attachment count
	 */
	public function get_post_attachments_count( $post_id ) {
		global $wpdb;

		// Ensure we have a valid post ID
		if ( ! is_numeric( $post_id ) ) {
			global $post;

			if ( ! isset( $post ) ) {
				return 0;
			} else {
				$post_id = $post->ID;
			}
		} else {
			$post_id = (int) $post_id;
		}

		// Query post attachment count
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_parent = %d", 'attachment', $post_id ) );

		return apply_filters( 'vgsr_prikbord_get_post_attachment_count', $count, $post_id );
	}

	/** Admin *****************************************************************/

	/**
	 * Output some prikbord admin specific styles
	 *
	 * @since 1.0.0
	 */
	public function print_admin_scripts() {

		// Bail if we're not on a prikbord admin screen
		if ( ! isset( get_current_screen()->post_type ) || vgsr_prikbord_get_item_post_type() != get_current_screen()->post_type )
			return;

		?>

		<style type="text/css">
			.fixed .column-attachments {
				width: 12%;
			}
		</style>

		<?php
	}

	/**
	 * Add columns to prikbord admin edit screen
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Columns
	 * @return array Columns
	 */
	public function add_columns( $columns ) {

		// Add attachments admin column
		$columns['attachments'] = __( 'Attachments', 'vgsr-prikbord' );

		return $columns;
	}

	/**
	 * Display custom column content for prikbord admin edit screen
	 *
	 * @since 1.0.0
	 *
	 * @param string $column Column name
	 * @param WP_Post $post Post object
	 */
	public function add_column_content( $column, $post_id ) {

		// This is the attachments column
		if ( 'attachments' == $column ) {
			echo $this->get_post_attachments_count( $post_id );
		}
	}
}

// Flush rewrite rules on (de)activation
register_activation_hook(   __FILE__, array( 'VGSR_Prikbord', 'activation'   ) );
register_deactivation_hook( __FILE__, array( 'VGSR_Prikbord', 'deactivation' ) );

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
