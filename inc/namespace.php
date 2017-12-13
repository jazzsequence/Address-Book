<?php
/**
 * Address Book
 *
 * @package AddressBook
 */

namespace AddressBook;

/**
 * Bootstrap the plugin.
 *
 * Registers actions and filter required to run the plugin.
 */
function bootstrap() {
	spl_autoload_register( __NAMESPACE__ . '\\autoload' );

	add_action( 'init',             __NAMESPACE__ . '\\CPT\\register_address' );
	add_action( 'init',             __NAMESPACE__ . '\\CPT\\register_taxonomies' );
	add_action( 'save_post',        __NAMESPACE__ . '\\CPT\\save_old_address', 10, 2 );
	add_filter( '_wp_post_revision_fields', __NAMESPACE__ . '\\CPT\\old_address_revision_fields' );
	add_action( 'cmb2_init',        __NAMESPACE__ . '\\CMB2\\address_meta' );
	add_action( 'cmb2_init',        __NAMESPACE__ . '\\CMB2\\past_addresses' );
	add_action( 'cmb2_init',        __NAMESPACE__ . '\\CMB2\\special_dates' );
	add_action( 'cmb2_render_address_history', __NAMESPACE__ . '\\CMB2\\cmb2_render_address_history', 10, 3 );
	add_filter( 'enter_title_here', __NAMESPACE__ . '\\CPT\\change_title_placeholder' );
}

/**
 * Autoload classes for this namespace.
 *
 * @param string $class Class name.
 */
function autoload( $class ) {
	if ( strpos( $class, __NAMESPACE__ . '\\' ) !== 0 ) {
		return;
	}

	$relative = strtolower( substr( $class, strlen( __NAMESPACE__ . '\\' ) ) );
	$parts = explode( '\\', $relative );
	$final = array_pop( $parts );
	array_push( $parts, 'class-' . $final . '.php' );
	$path = __DIR__ . '/' . implode( '/', $parts );

	require $path;
}
