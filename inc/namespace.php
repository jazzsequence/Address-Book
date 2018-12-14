<?php
/**
 * Address Book
 *
 * @package AddressBook
 */

namespace AddressBook;

use AddressBook\CMB2;

/**
 * Bootstrap the plugin.
 *
 * Registers actions and filter required to run the plugin.
 */
function bootstrap() {
	spl_autoload_register( __NAMESPACE__ . '\\autoload' );

	add_action( 'save_post', __NAMESPACE__ . '\\flush_cached_addresses' );
	add_action( 'init', __NAMESPACE__ . '\\CPT\\register_address' );
	add_action( 'init', __NAMESPACE__ . '\\CPT\\register_taxonomies' );
	add_action( 'init', __NAMESPACE__ . '\\CPT\\insert_relationships' );
	add_action( 'save_post', __NAMESPACE__ . '\\CPT\\save_old_address', 10, 2 );
	add_action( 'cmb2_init', __NAMESPACE__ . '\\CMB2\\address_meta' );
	add_action( 'cmb2_init', __NAMESPACE__ . '\\CMB2\\past_addresses' );
	add_action( 'cmb2_init', __NAMESPACE__ . '\\CMB2\\special_dates' );
	add_action( 'cmb2_render_address_history', __NAMESPACE__ . '\\CMB2\\cmb2_render_address_history', 10, 3 );
	add_action( 'admin_menu', __NAMESPACE__ . '\\Admin\\remove_cpt_menu' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\Admin\\add_menus' );
	add_action( 'parent_file', __NAMESPACE__ . '\\Admin\\taxonomy_parent' );

	add_filter( 'enter_title_here', __NAMESPACE__ . '\\CPT\\change_title_placeholder' );
	add_filter( '_wp_post_revision_fields', __NAMESPACE__ . '\\CPT\\old_address_revision_fields' );
	add_filter( 'relationship_row_actions', __NAMESPACE__ . '\\CPT\\remove_relationship_row_actions', 10, 2 );
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

/**
 * Handle the query to pull addresses and sort by relationship and family.
 *
 * @since  0.2.2
 * @param  int  $numposts The number of posts to query. -1 pulls all posts.
 * @param  bool $inactive Whether to include inactive addresses.
 * @return object         The WP_Query object.
 */
function address_query( $numposts = -1, $inactive = false ) {
	$args = [
		'post_type'      => 'ab_address',
		'nopaging'       => true,
		'posts_per_page' => $numposts,
		'no_found_rows'  => true,
	];

	// Include inactive posts if $inactive is true.
	if ( $inactive ) {
		$args['meta_query'] = [
			[
				'key'     => 'inactive',
				'compare' => '!=',
				'value'   => '',
			],
		];
	}

	$address_query = new \WP_Query( $args );

	return $address_query;
}

/**
 * Remove the inactive addresses and return a filtered, sorted (by family and relationship) list of addresses.
 *
 * @since  0.2.1
 * @param  int  $numposts The number of posts to query. -1 pulls all posts.
 * @param  bool $inactive Whether to include inactive addresses.
 * @return array The array of addresses.
 */
function get_addresses( $numposts = -1, $inactive = false ) {
	$addresses = wp_cache_get( 'full_address_list', 'address_book' );

	if ( ! $addresses && $numposts < 0 ) {
		$addresses = address_query()->posts;
		wp_cache_set( 'full_address_list', $addresses, 'address_book', DAY_IN_SECONDS );
	}

	foreach ( $addresses as $index => $address ) {
		if ( CMB2\is_inactive( $address->ID ) ) {
			unset( $addresses[ $index ] );
		}
	}

	return $addresses;
}

/**
 * Delete cached address list on save post.
 *
 * @since  0.2.2
 * @param  int $post_id The post ID.
 * @return void
 */
function flush_cached_addresses( $post_id ) {
	// Don't flush for revisions.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	wp_cache_delete( 'full_address_list', 'address_book' );
}
