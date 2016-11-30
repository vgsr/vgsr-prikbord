<?php

/**
 * VGSR Prikbord Admin Functions
 *
 * @package VGSR Prikbord
 * @subpackage Administration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'VGSR_Prikbord_Admin' ) ) :
/**
 * The VGSR Prikbord Admin class
 *
 * @since 1.1.0
 */
class VGSR_Prikbord_Admin {

	/**
	 * Setup this class
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.1.0
	 */
	private function setup_actions() {

		// Get post type
		$post_type = vgsr_prikbord_get_item_post_type();

		// Admin columns
		add_action( 'vgsr_admin_head',                         array( $this, 'print_admin_scripts' )        );
		add_action( "manage_{$post_type}_posts_columns",       array( $this, 'item_columns'        )        );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'item_column_content' ), 10, 2 );
	}

	/** Public methods **************************************************/

	/**
	 * Return whether we're on an Prikbord Item admin page
	 *
	 * @since 1.1.0
	 *
	 * @return bool Should we bail?
	 */
	public function bail() {

		// Get the screen
		$screen = get_current_screen();

		if ( isset( $screen->post_type ) && vgsr_prikbord_get_item_post_type() === $screen->post_type ) {
			return false;
		}

		return true;
	}

	/**
	 * Output admin scripts
	 *
	 * @since 1.1.0
	 */
	public function print_admin_scripts() {

		// Bail when not administrating Prikbord Items
		if ( $this->bail() )
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
	 * Modify the set of Prikbord Item admin columns
	 *
	 * @since 1.1.0
	 *
	 * @param array $columns Columns
	 * @return array Columns
	 */
	public function item_columns( $columns ) {

		// Attachments column
		$columns['attachments'] = __( 'Attachments', 'vgsr-prikbord' );

		return $columns;
	}

	/**
	 * Output content for Prikbord Item admin columns
	 *
	 * @since 1.1.0
	 *
	 * @param string $column Column name
	 * @param WP_Post $post Post object
	 */
	public function item_column_content( $column, $post_id ) {

		// Display attachments count
		if ( 'attachments' === $column ) {
			echo vgsr_prikbord()->get_post_attachments_count( $post_id );
		}
	}
}

/**
 * Setup the extension logic for BuddyPress
 *
 * @since 1.1.0
 *
 * @uses VGSR_Prikbord_Admin
 */
function vgsr_prikbord_admin() {
	vgsr_prikbord()->admin = new VGSR_Prikbord_Admin;
}

endif; // class_exists
