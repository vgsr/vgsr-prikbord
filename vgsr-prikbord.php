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
 * Version:           1.0.1
 * Author:            Laurens Offereins
 * Author URI:        https://github.com/lmoffereins
 * Text Domain:       vgsr-prikbord
 * Domain Path:       /languages/
 * GitHub Plugin URI: vgsr/vgsr-prikbord
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

		$this->version    = '1.0.1';

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
	 */
	private function setup_actions() {

		// Fetch post type for later use
		$post_type = $this->get_post_type();

		// Register prikbord items
		add_action( 'init', array( $this, 'register_post_type' ), 11 );

		// Append attachments to post content
		add_filter( 'the_content', array( $this, 'append_attachments' ) );

		// Admin columns
		add_action( 'vgsr_admin_head', array( $this, 'add_admin_styles' ) );
		add_action( "manage_{$post_type}_posts_columns",       array( $this, 'add_admin_column'         )        );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'add_admin_column_content' ), 10, 2 );

		// VGSR-only
		add_filter( 'vgsr_only_is_post_type_markable', array( $this, 'donot_vgsr_only' ), 10, 2 );
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
	 * Hide prikbord items for non-vgsr users.
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {
		register_post_type( $this->get_post_type(), array(
			'labels' => array(
				'name'               => __( 'Prikbord Items',           'vgsr-prikbord' ),
				'menu_name'          => __( 'Prikbord',                 'vgsr-prikbord' ),
				'singular_name'      => __( 'Prikbord Item',            'vgsr-prikbord' ),
				'all_items'          => __( 'All Prikbord Items',       'vgsr-prikbord' ),
				'add_new'            => __( 'New Prikbord Item',        'vgsr-prikbord' ),
				'add_new_item'       => __( 'Create New Prikbord Item', 'vgsr-prikbord' ),
				'edit'               => __( 'Edit',                     'vgsr-prikbord' ),
				'edit_item'          => __( 'Edit Prikbord Item',       'vgsr-prikbord' ),
				'new_item'           => __( 'New Prikbord Item',        'vgsr-prikbord' ),
				'view'               => __( 'View Prikbord Item',       'vgsr-prikbord' ),
				'view_item'          => __( 'View Prikbord Item',       'vgsr-prikbord' ),
				'search_items'       => __( 'Search Prikbord Items',    'vgsr-prikbord' ),
				'not_found'          => __( 'No items found',           'vgsr-prikbord' ),
				'not_found_in_trash' => __( 'No items found in Trash',  'vgsr-prikbord' ),
			),
			'rewrite'             => array( 'slug' => 'prikbord', 'with_front' => false ),
			'supports'            => array( 'title', 'editor' ),
			'description'         => __( 'VGSR Prikbord Items', 'vgsr-prikbord' ),
			// 'menu_position'       => ,
			'has_archive'         => 'prikbord',
			'exclude_from_search' => ! is_user_vgsr(),
			'publicly_queryable'  => is_user_vgsr(),
			'show_in_nav_menus'   => true,
			'public'              => is_user_vgsr(), // Hide prikbord for non-vgsr
			'show_ui'             => true,
			'can_export'          => true,
			'hierarchical'        => false,
			'query_var'           => true,
			'menu_icon'           => 'dashicons-pressthis'
		) );
	}

	/**
	 * Return whether our post type is vgsr-only markable
	 *
	 * @since 1.0.0
	 *
	 * @param bool $markable
	 * @param string $post_type Post type name
	 * @return bool Post type is markable
	 */
	public function donot_vgsr_only( $markable, $post_type ) {

		// Do not enable marking vgsr-only for our post type
		if ( $this->get_post_type() == $post_type )
			$markable = false;

		return $markable;
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
		if ( $this->get_post_type() != $post->post_type )
			return $content;

		// Get all attachments
		$attachments = get_attached_media( false );

		// Setup list title
		$list_title = '<h3>' . __( 'Attachments', 'vgsr-prikbord' ) . '</h3>';
		$list_title = apply_filters( 'vgsr_prikbord_items_title', $list_title );

		// Having attachments
		if ( ! empty( $attachments ) ) {

			// Start list
			$list = '<ul class="attachments prikbord-items">';

			// Walk all attachments
			foreach ( $attachments as $attachment ) {

				// Get the title once
				$title = get_the_title( $attachment->ID );

				// Setup list item as titled link to file
				$item = sprintf( '<li><a href="%s" title="%s">%s</a></li>',
					wp_get_attachment_url( $attachment->ID ),
					sprintf( __( 'View &#8220;%s&#8221;', 'vgsr-prikbord' ), $title ),
					$title
				);

				// Enable item filtering
				$list .= apply_filters( 'vgsr_prikbord_attachment_item', $item, $attachment );
			}

			// Close list
			$list .= '</ul>';

		// Without attachments
		} else {
			$list = '<p>' . __( 'This prikbord item has no attachments.', 'vgsr-prikbord' ) . '</p>';
		}

		return $content . $list_title . $list;
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
	public function add_admin_styles() {

		// Bail if we're not on a prikbord admin screen
		if ( ! isset( get_current_screen()->post_type ) || $this->get_post_type() != get_current_screen()->post_type )
			return; ?>

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
	public function add_admin_column( $columns ) {

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
	public function add_admin_column_content( $column, $post_id ) {
		$content = '';

		// This is the attachments column
		if ( 'attachments' == $column ) {
			$content = $this->get_post_attachments_count( $post_id );
		}

		echo $content;
	}
}

// Flush rewrite rules on (de)activation
register_activation_hook(   __FILE__, array( 'VGSR_Prikbord', 'activation'   ) );
register_deactivation_hook( __FILE__, array( 'VGSR_Prikbord', 'deactivation' ) );

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

// Fire when VGSR is alive
add_action( 'vgsr_ready', 'vgsr_prikbord' );

endif; // class_exists
