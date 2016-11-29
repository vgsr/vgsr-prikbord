<?php

/**
 * VGSR Prikbord Sub-Actions
 *
 * @package VGSR Prikbord
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Run dedicated activation hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'vgsr_prikbord_activation'
 */
function vgsr_prikbord_activation() {
	do_action( 'vgsr_prikbord_activation' );
}

/**
 * Run dedicated deactivation hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'vgsr_prikbord_deactivation'
 */
function vgsr_prikbord_deactivation() {
	do_action( 'vgsr_prikbord_deactivation' );
}

/**
 * Run dedicated init hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'vgsr_prikbord_init'
 */
function vgsr_prikbord_init() {
	do_action( 'vgsr_prikbord_init' );
}

/**
 * Run dedicated admin init hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'vgsr_prikbord_admin_init'
 */
function vgsr_prikbord_admin_init() {
	do_action( 'vgsr_prikbord_admin_init' );
}
