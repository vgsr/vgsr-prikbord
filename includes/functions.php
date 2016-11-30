<?php

/**
 * VGSR Prikbord Functions
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
 * @return string Prikbord post type name
 */
public function vgsr_prikbord_get_item_post_type() {
	return vgsr_prikbord()->post_type_id;
}

/**
 * Return the labels for the Prikbord Item post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'vgsr_prikbord_get_item_post_type_labels'
 * @return array Prikbord Item post type labels
 */
public function vgsr_prikbord_get_item_post_type_labels() {
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
