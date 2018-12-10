<?php
/**
 * Plugin Name: Address Book
 * Plugin URI: https://github.com/jazzsequence/Address-Book
 * Description: A WordPress plugin for storing and maintaining addresses.
 * Author: Chris Reynolds
 * Author URI: https://chrisreynolds.io
 * License: GPLv3
 * Version: 0.2.3
 *
 * @package AddressBook
 */

namespace AddressBook;

/**
 * Move everything into an init function.
 */
function init() {
	// Load extended cpts if it hasn't been loaded yet.
	if ( ! function_exists( 'register_extended_post_type' ) ) {
		require_once __DIR__ . '/vendor/johnbillion/extended-cpts/extended-cpts.php';
	}

	require_once __DIR__ . '/inc/namespace.php';
	require_once __DIR__ . '/inc/cpt/namespace.php';
	require_once __DIR__ . '/inc/cpt/cmb.php';
	require_once __DIR__ . '/inc/admin/namespace.php';

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
}

// Kick it off.
init();
