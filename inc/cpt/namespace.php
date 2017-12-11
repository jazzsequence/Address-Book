<?php
/**
 * Address CPT.
 *
 * @package AddressBook
 */

namespace AddressBook\CPT;

/**
 * Registers the Address post type.
 *
 * @since 0.1
 */
function register_address() {
	register_extended_post_type( 'ab_address', [
			'menu_icon' => 'dashicons-id',
			'supports'  => [ 'title', 'revisions' ],
		], [
			'singular' => esc_html__( 'Address', 'address-book' ),
			'plural'   => esc_html__( 'Addresses', 'address-book' ),
			'slug'     => 'address',
		]
	);
}

/**
 * Registers the Family and Relationship taxonomies.
 *
 * @since 0.1
 */
function register_taxonomies() {
	register_extended_taxonomy( 'ab_family', 'ab_address', [
			// 'meta_box'      => 'simple', // Can use 'radio', 'dropdown', or a callback function.
			'dashboard_glance' => true,   // Show this taxonomy in the 'At a Glance' widget.
			// Custom columns.
			'admin_cols'    => [],
		], [
			'singular'      => esc_html__( 'Family', 'address-book' ),
			'plural'        => esc_html__( 'Families', 'address-book' ),
			'slug'          => 'family',
		]
	);

	register_extended_taxonomy( 'relationship', 'ab_address', [
			'meta_box'      => 'dropdown', // Can use 'radio', 'dropdown', or a callback function.
			'dashboard_glance' => false,   // Show this taxonomy in the 'At a Glance' widget.
			// Custom columns.
			'admin_cols'    => [],
		], [
			'singular'      => esc_html__( 'Relationship', 'address-book' ),
			'plural'        => esc_html__( 'Relationships', 'address-book' ),
			'slug'          => 'relationship',
		]
	);
}

/**
 * Change title placeholder text to "Name" for addresses.
 *
 * @param  string $title The title placeholder.
 * @return string        The updated title placeholder
 * @since  0.1
 */
function change_title_placeholder( $title ) {
	$post_type = get_post_type();

	if ( ! 'ab_address' === $post_type ) {
		return $title;
	}

	return esc_html__( 'Name', 'address-book' );
}


}
