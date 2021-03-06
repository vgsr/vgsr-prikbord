<?php

/**
 * VGSR Prikbord Item Fucntions
 *
 * @package VGSR Prikbord
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Type *****************************************************************/

/**
 * Return the Prikbord Item post type
 *
 * @since 1.1.0
 *
 * @return string Prikbord Item post type name
 */
function vgsr_prikbord_get_item_post_type() {
	return vgsr_prikbord()->post_type_id;
}

/**
 * Return the labels for the Prikbord Item post type
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'vgsr_prikbord_get_item_post_type_labels'
 * @return array Prikbord Item post type labels
 */
function vgsr_prikbord_get_item_post_type_labels() {
	return apply_filters( 'vgsr_prikbord_get_item_post_type_labels', array(
		'name'                  => __( 'Prikbord Items',                 'vgsr-prikbord' ),
		'menu_name'             => __( 'Prikbord',                       'vgsr-prikbord' ),
		'singular_name'         => __( 'Prikbord Item',                  'vgsr-prikbord' ),
		'all_items'             => __( 'All Prikbord Items',             'vgsr-prikbord' ),
		'add_new'               => __( 'New Prikbord Item',              'vgsr-prikbord' ),
		'add_new_item'          => __( 'Create New Prikbord Item',       'vgsr-prikbord' ),
		'edit'                  => __( 'Edit',                           'vgsr-prikbord' ),
		'edit_item'             => __( 'Edit Prikbord Item',             'vgsr-prikbord' ),
		'new_item'              => __( 'New Prikbord Item',              'vgsr-prikbord' ),
		'view'                  => __( 'View Prikbord Item',             'vgsr-prikbord' ),
		'view_item'             => __( 'View Prikbord Item',             'vgsr-prikbord' ),
		'view_items'            => __( 'View Prikbord Items',            'vgsr-prikbord' ), // Since WP 4.7
		'search_items'          => __( 'Search Prikbord Items',          'vgsr-prikbord' ),
		'not_found'             => __( 'No items found',                 'vgsr-prikbord' ),
		'not_found_in_trash'    => __( 'No items found in Trash',        'vgsr-prikbord' ),
		'insert_into_item'      => __( 'Insert into item',               'vgsr-prikbord' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item',          'vgsr-prikbord' ),
		'filter_items_list'     => __( 'Filter items list',              'vgsr-prikbord' ),
		'items_list_navigation' => __( 'Prikbord Items list navigation', 'vgsr-prikbord' ),
		'items_list'            => __( 'Prikbord Items list',            'vgsr-prikbord' ),
	) );
}

/**
 * Return the Prikbord Item post type rewrite settings
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'vgsr_prikbord_get_item_post_type_rewrite'
 * @return array Prikbord Item post type support
 */
function vgsr_prikbord_get_item_post_type_rewrite() {
	return apply_filters( 'vgsr_prikbord_get_item_post_type_rewrite', array(
		'slug'       => vgsr_prikbord_get_item_slug(),
		'with_front' => false
	) );
}

/**
 * Return an array of features the Prikbord Item post type supports
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'vgsr_prikbord_get_item_post_type_supports'
 * @return array Prikbord Item post type support
 */
function vgsr_prikbord_get_item_post_type_supports() {
	return apply_filters( 'vgsr_prikbord_get_item_post_type_supports', array(
		'title',
		'editor'
	) );
}

/**
 * Return the capability mappings for the Prikbord Item post type
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'vgsr_prikbord_get_item_post_type_caps'
 * @return array Prikbord Item post type caps
 */
function vgsr_prikbord_get_item_post_type_caps() {
	return apply_filters( 'vgsr_prikbord_get_item_post_type_caps', array(
		'edit_post'           => 'edit_vgsr_prikbord_item',
		'edit_posts'          => 'edit_vgsr_prikbord_items',
		'edit_others_posts'   => 'edit_others_vgsr_prikbord_items',
		'publish_posts'       => 'publish_vgsr_prikbord_items',
		'read_private_posts'  => 'read_private_vgsr_prikbord_items',
		'delete_post'         => 'delete_vgsr_prikbord_item',
		'delete_posts'        => 'delete_vgsr_prikbord_items',
		'delete_others_posts' => 'delete_others_vgsr_prikbord_items'
	) );
}

/** Template ******************************************************************/

/**
 * Retreive a Prikbord Item post object
 *
 * @since 1.1.0
 *
 * @param WP_Post|int $item Optional. Defaults to the current post.
 * @return WP_Post|bool Post object when found, False otherwise.
 */
function vgsr_prikbord_get_item( $item = 0 ) {

	// Get the post
	$item = get_post( $item );

	// Verify Prikbord Item post type
	if ( ! $item || vgsr_prikbord_get_item_post_type() != $item->post_type ) {
		$item = false;
	}

	return $item;
}

/**
 * Output the Prikbord Item's attachment count
 *
 * @since 1.1.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 */
function vgsr_prikbord_item_attachments_count( $post = 0 ) {
	echo vgsr_prikbord_get_item_attachments_count( $post );
}

/**
 * Return the Prikbord Item's attachment count
 *
 * @since 1.1.0
 *
 * @uses WPDB $wpdb
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return int Post attachment count
 */
function vgsr_prikbord_get_item_attachments_count( $post = 0 ) {
	global $wpdb;

	// Bail when the post does not exist
	if ( ! $post = vgsr_prikbord_get_item( $post ) )
		return 0;

	// Query post attachment count
	$sql   = $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_parent = %d", 'attachment', $post->ID );
	$count = (int) $wpdb->get_var( $sql );

	return apply_filters( 'vgsr_prikbord_get_item_attachment_count', $count, $post );
}
