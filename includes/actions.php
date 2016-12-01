<?php

/**
 * VGSR Prikbord Actions
 *
 * @package VGSR Prikbord
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Utility *******************************************************************/

add_action( 'vgsr_prikbord_activation',   'vgsr_prikbord_delete_rewrite_rules' );
add_action( 'vgsr_prikbord_deactivation', 'vgsr_prikbord_delete_rewrite_rules' );

/** Admin *********************************************************************/

if ( is_admin() ) {
	add_action( 'vgsr_admin_init', 'vgsr_prikbord_admin' );
}
