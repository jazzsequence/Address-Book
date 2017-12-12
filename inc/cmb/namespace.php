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
		] );

		$cmb->add_field( [
			'name'       => __( 'Old addresses', 'address-book' ),
			'id'         => $prefix . 'old_addresses',
			'type'       => 'address_history',
		] );
	}
}

function cmb2_render_address_history( $field, $escaped_value, $object_id ) {
	$revisions = wp_get_post_revisions( $object_id );
	$current_email = get_post_meta( $object_id, '_ab_email', true );
	$current_address = get_post_meta( $object_id, '_ab_mailing_address', true );
	$old_emails = $current_email ? [ $current_email ] : [];
	$old_addresses = $current_address ? [ $current_address ] : [];
	foreach ( $revisions as $post ) {
		$email = get_post_meta( $post->ID, '_ab_email', true );
		if ( $email && empty( $old_emails || ! in_array( $email, $old_emails ) ) ) {
			$old_emails[] = $email;
		}
		unset( $old_emails[ $current_email ] );
	}

	foreach ( $revisions as $post ) {
		$address = get_post_meta( $post->ID, '_ab_mailing_address', true );
		if ( $address && empty( $old_addresses ) || ! in_array( $address, $old_addresses ) ) {
			$old_addresses[] = $address;
		}
		unset( $old_addresses[ $current_address ] );
	}
var_dump($old_addresses); var_dump($old_emails);
	if ( ! empty( $old_emails ) ) {
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