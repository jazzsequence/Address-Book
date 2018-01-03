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

/**
 * Add the past email and mailing address meta fields to address revisions.
 *
 * @param  array $fields The array of meta fields saved.
 * @return array         The updated array of meta fields saved.
 * @since  0.1
 */
function old_address_revision_fields( $fields ) {
	return array_merge( $fields, [
		'_ab_mailing_address' => esc_html__( 'Mailing Address', 'address-book' ),
		'_ab_email'           => esc_html__( 'Email Address', 'address-book' ),
		'_ab_phone'           => esc_html__( 'Phone Number', 'address-book' ),
	] );
}

/**
 * Save the old address metadata to the revision.
 *
 * @param  int    $post_id The post ID.
 * @param  object $post    The post object.
 * @link   https://johnblackbourn.com/post-meta-revisions-wordpress/
 * @since  0.1
 */
function save_old_address( $post_id, $post ) {
	$parent_id = wp_is_post_revision( $post_id );

	if ( $parent_id ) {
		$parent      = get_post( $parent_id );
		$meta_values = [
			'_ab_mailing_address' => get_post_meta( $parent->ID, '_ab_mailing_address', true ),
			'_ab_email'           => get_post_meta( $parent->ID, '_ab_email', true ),
			'_ab_phone'           => get_post_meta( $parent->ID, '_ab_phone', true ),
		];

		foreach ( $meta_values as $key => $value ) {
			// Save post meta if there is a value and it's not the same as what's already saved.
			if ( false !== $value ) {
				add_metadata( 'post', $post_id, $key, $value );
			}
		}
	}
}

/**
 * Insert some default relationships.
 *
 * @since 0.2.2
 */
function insert_relationships() {
	wp_insert_term( __( 'Family', 'address-book' ), 'relationship', [
		'slug'        => 'family',
		'description' => __( 'People who are related by blood or marriage.', 'address-book' ),
	] );

	wp_insert_term( __( 'Friend', 'address-book' ), 'relationship', [
		'slug'        => 'friend',
		'description' => __( 'People who are close but not blood related.', 'address-book' ),
	] );

	wp_insert_term( __( 'Acquaintance', 'address-book' ), 'relationship', [
		'slug'        => 'acquaintance',
		'description' => __( 'Friends of friends and people who are not close.', 'address-book' ),
	] );
}

/**
 * Remove the action row links for built-in taxonomies.
 *
 * @since  0.2.2
 * @param  array  $actions The array of term actions.
 * @param  object $tag     The taxonomy term object.
 * @return array           Filtered array of actions.
 */
function remove_relationship_row_actions( $actions, $tag ) {
	if ( in_array( $tag->slug, [ 'family', 'friend', 'acquaintance' ] ) ) {
		unset( $actions['edit'] );
		unset( $actions['inline hide-if-no-js'] );
		unset( $actions['delete'] );
	}

	return $actions;
}
