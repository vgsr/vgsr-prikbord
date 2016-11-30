<?php

/**
 * VGSR Prikbord Actions
 *
 * @package VGSR Prikbord
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( is_admin() ) {
	add_action( 'vgsr_admin_init', 'vgsr_prikbord_admin' );
}
