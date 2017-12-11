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
		'type'       => 'address',
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

	$cmb = new_cmb2_box( [
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Former Addresses', 'address-book' ),
		'object_types' => [ 'ab_address' ],
	] );
}