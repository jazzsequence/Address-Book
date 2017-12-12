<?php
/**
 * Address Book CMB2
 *
 * @package AddressBook
 */

namespace AddressBook\CMB2;

/**
 * Handle CMB2 fields
 *
 * @since 0.1
 */
function address_meta() {
	$prefix = '_ab_';

	$cmb = new_cmb2_box( [
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Address Information', 'address-book' ),
		'object_types' => [ 'ab_address' ],
		'priority'     => 'high',
	] );

	$cmb->add_field( [
		'name'       => __( 'Email Address', 'address-book' ),
		'id'         => $prefix . 'email',
		'type'       => 'text_email',
	] );

	$cmb->add_field( [
		'name'       => __( 'Mailing Address', 'address-book' ),
		'id'         => $prefix . 'mailing_address',
		'type'       => 'textarea_small',
	] );

	$cmb->add_field( [
		'name'              => __( 'Inactive', 'address-book' ),
		'id'                => $prefix . 'inactive',
		'type'              => 'multicheck',
		'desc'              => __( 'If checked, this address will not appear in address lists nor be exported.', 'address-book' ),
		'options'           => [
			'moved'    => __( 'Moved', 'address-book' ),
			'deceased' => __( 'Deceased', 'address-book' ),
		],
		'select_all_button' => false,
	] );
}

function past_addresses() {
	$prefix = '_ab_';

	if ( ! empty( $_GET['post'] ) && wp_get_post_revisions( wp_unslash( absint( $_GET['post'] ) ) ) ) {
		$cmb = new_cmb2_box( [
			'id'           => $prefix . 'old_addresses',
			'title'        => __( 'Former Addresses', 'address-book' ),
			'object_types' => [ 'ab_address' ],
			'show_names'   => false,
		] );

		$cmb->add_field( [
			'name'       => __( 'Old addresses', 'address-book' ),
			'id'         => $prefix . 'old_addresses',
			'type'       => 'address_history',
		] );
	}
}

function cmb2_render_address_history( $field, $escaped_value, $object_id ) {
	$revisions     = wp_get_post_revisions( $object_id );
	$old_emails    = get_old_meta( '_ab_email', $revisions, $object_id );
	$old_addresses = get_old_meta( '_ab_mailing_address', $revisions, $object_id );

var_dump($old_addresses);
	if ( $old_emails ) {
		echo '<p>';
		echo '<strong>Old email addresses:</strong><br />';
		foreach ( $old_emails as $email ) {
			echo $email . '<br />';
		}
		echo '</p>';
	}

	if ( ! empty( $old_addresses ) ) {
		echo '<p>';
		echo '<strong>Old addresses:</strong><br />';
		foreach ( $old_addresses as $address ) {
			echo wpautop( $address );
		}
		echo '</p>';
	}
}

function get_old_meta( $meta_key, $revisions, $post_id ) {
	$current_thing = get_post_meta( $post_id, $meta_key, true );
	$old_things    = $current_thing ? [ $current_thing ] : false;

	foreach ( $revisions as $post ) {
		$thing = get_post_meta( $post->ID, $meta_key, true );
		if ( $thing && ( empty( $old_things ) || ! in_array( $thing, $old_things ) ) ) {
			$old_things[] = $thing;
		}
	}

	return clean_old_data( $old_things );
}

function clean_old_data( $current_thing, $old_things ) {
	$old_things = array_filter( $old_things );
	$index      = array_search( $current_thing, $old_things );
	if ( false !== $index ) {
		unset( $old_things[ $index ] );
	}

	return $old_things;
}
