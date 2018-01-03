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

	add_action( 'init',                        __NAMESPACE__ . '\\CPT\\register_address' );
	add_action( 'init',                        __NAMESPACE__ . '\\CPT\\register_taxonomies' );
	add_action( 'save_post',                   __NAMESPACE__ . '\\CPT\\save_old_address', 10, 2 );
	add_action( 'cmb2_init',                   __NAMESPACE__ . '\\CMB2\\address_meta' );
	add_action( 'cmb2_init',                   __NAMESPACE__ . '\\CMB2\\past_addresses' );
	add_action( 'cmb2_init',                   __NAMESPACE__ . '\\CMB2\\special_dates' );
	add_action( 'cmb2_render_address_history', __NAMESPACE__ . '\\CMB2\\cmb2_render_address_history', 10, 3 );
	add_action( 'admin_menu',                  __NAMESPACE__ . '\\Admin\\remove_cpt_menu' );
	add_action( 'admin_menu',                  __NAMESPACE__ . '\\Admin\\add_menus' );
	add_action( 'parent_file',                 __NAMESPACE__ . '\\Admin\\taxonomy_parent' );

	add_filter( 'enter_title_here',            __NAMESPACE__ . '\\CPT\\change_title_placeholder' );
	add_filter( '_wp_post_revision_fields',    __NAMESPACE__ . '\\CPT\\old_address_revision_fields' );
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

/**
 * Helper function to get an ID.
 *
 * @since  0.2.1
 * @param  integer $post_id A post ID. If passed, we just make sure it's an int.
 * @return mixed            Either a WP_Error (if no ID could be found) or the post ID.
 */
function get_post_id( $post_id = 0 ) {
	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_id || ! is_int( $post_id ) ) {
		return new \WP_Error( 'no_IDea', esc_html__( 'No Address ID found', 'address-book' ) );
	}

	return $post_id;
}
