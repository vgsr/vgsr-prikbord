<?php

/**
 * VGSR Prikbord Functions
 *
 * @package VGSR Prikbord
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** User **********************************************************************/

/**
 * Return whether the current user has basic access
 *
 * @since 1.1.0
 *
 * @param int $user_id User ID. Optional. Defaults to the current user.
 * @return bool Has the user access?
 */
function vgsr_prikbord_check_access( $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return function_exists( 'vgsr' ) && is_user_vgsr( $user_id );
}

/** Rewrite *******************************************************************/

/**
 * Return the Prikbord Item slug
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'vgsr_prikbord_get_item_slug'
 * @return string Prikbord Item slug
 */
function vgsr_prikbord_get_item_slug() {
	return apply_filters( 'vgsr_prikbord_get_item_slug', 'prikbord' );
}

/** Utility *******************************************************************/

/**
 * Delete a blogs rewrite rules, so that they are automatically rebuilt on
 * the subsequent page load.
 *
 * @since 1.0.0
 */
function vgsr_prikbord_delete_rewrite_rules() {
	delete_option( 'rewrite_rules' );
}
