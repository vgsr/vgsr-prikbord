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
 * Return the prikbord item post type
 *
 * @since 1.1.0
 *
 * @return string Prikbord post type name
 */
public function vgsr_prikbord_get_item_post_type() {
	return vgsr_prikbord()->post_type_id;
}
