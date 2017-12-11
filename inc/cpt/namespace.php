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

function cmb2() {
	$prefix = '_ab_';

	$cmb = new_cmb2_box( array(
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Address', 'address-book' ),
		'object_types' => array( 'ab_address' ),
	) );

	$cmb->add_field( array(
		'name'       => __( 'Email address', 'address-book' ),
		'id'         => $prefix . 'email',
		'type'       => 'text_email',
	) );
}
