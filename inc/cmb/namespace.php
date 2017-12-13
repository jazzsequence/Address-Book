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

/**
 * Add the past addresses metabox.
 *
 * @since 0.1
 */
function past_addresses() {
	$prefix = '_ab_';

	$cmb = new_cmb2_box( [
		'id'           => $prefix . 'old_addresses',
		'title'        => __( 'Former Addresses', 'address-book' ),
		'object_types' => [ 'ab_address' ],
		'show_names'   => false,
		'show_on_cb'   => __NAMESPACE__ . '\\show_past_addresses',
	] );

	$cmb->add_field( [
		'name'       => __( 'Old addresses', 'address-book' ),
		'id'         => $prefix . 'old_addresses',
		'type'       => 'address_history',
	] );
}

/**
 * Show on callback for address history custom CMB2 field.
 *
 * @return boolean Whether to show the address history or not.
 * @since  0.1.1
 */
function show_past_addresses() {
	if ( empty( $_GET['post'] ) ) {
		return false;
	}

	$post_id = wp_unslash( absint( $_GET['post'] ) );
	$revisions = wp_get_post_revisions( $post_id );

	if ( ! $revisions ) {
		return false;
	}

	if ( 1 > count( get_old_meta( '_ab_email', $revisions, $post_id ) ) && 1 > count( get_old_meta( '_ab_mailing_address', $revisions, $post_id ) ) ) {
		return false;
	}

	return true;
}

/**
 * Address History CMB2 custom field callback.
 *
 * @param  string $field         The CMB2 meta field. Not used by this custom CMB2 field type.
 * @param  string $escaped_value The CMB2 meta value. Not used by this custom CMB2 field type.
 * @param  int    $object_id     The address post ID.
 * @since  0.1
 */
function cmb2_render_address_history( $field, $escaped_value, $object_id ) {
	$revisions     = wp_get_post_revisions( $object_id );

	render_old_emails( get_old_meta( '_ab_email', $revisions, $object_id ) );
	render_old_addresses( get_old_meta( '_ab_mailing_address', $revisions, $object_id ) );
}

/**
 * Gets the meta values for a meta key from post revisions and returns them in an array.
 *
 * @param  string $meta_key  The meta key to get post meta for.
 * @param  array  $revisions An array of revision post objects.
 * @param  int    $post_id   The post ID of the parent post.
 * @return array             An array of meta values for the given meta key.
 * @since  0.1.1
 */
function get_old_meta( $meta_key, $revisions, $post_id ) {
	$current_thing = get_post_meta( $post_id, $meta_key, true );
	$old_things    = $current_thing ? [ $current_thing ] : false;

	foreach ( $revisions as $post ) {
		$thing = get_post_meta( $post->ID, $meta_key, true );
		if ( $thing && ( empty( $old_things ) || ! in_array( $thing, $old_things ) ) ) {
			$old_things[] = $thing;
		}
	}

	return clean_old_data( $current_thing, $old_things );
}

/**
 * Takes an array of meta values from revisions and removes empty values, duplicates, and the current value.
 *
 * @param  string $current_thing The current value.
 * @param  array  $old_things    An array of old values.
 * @return array                 The cleaned array.
 * @since  0.1.1
 */
function clean_old_data( $current_thing, $old_things ) {
	$old_things = array_filter( $old_things );
	$index      = array_search( $current_thing, $old_things );
	if ( false !== $index ) {
		unset( $old_things[ $index ] );
	}

	return $old_things;
}

/**
 * Displays a list of old emails.
 *
 * @param  array $emails An array of old email addresses.
 * @since  0.1.1
 */
function render_old_emails( $emails ) {
	$count = count( $emails );

	// Bail if there aren't any.
	if ( 0 === $count ) {
		return;
	}

	$label = __( 'Past email addresses', 'address-book' );

	ob_start(); ?>
		<div class="old-email-addresses" id="address-<?php the_ID(); ?>-old-emails">
			<label><?php echo esc_html( $label ); ?></label>
			<span class="old-email-addresses-count">
				<?php
				// Translators: %d is the number of old email addresses.
				echo esc_html( sprintf( _n( '%d former email address found.', '%d former email addresses found.', $count, 'address-book' ), $count ) );
				?>
			</span>
			<ul class="email-address-list">
				<?php foreach ( $emails as $email ) : ?>
					<li><?php echo esc_html( $email ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php
	echo wp_kses_post( ob_get_clean() );
}

/**
 * Displays the list of old addresses.
 *
 * @param  array $addresses An array of former addresses.
 * @since  0.1.1
 */
function render_old_addresses( $addresses ) {
	$count = count( $addresses );

	// Bail if there aren't any.
	if ( 0 === $count ) {
		return;
	}

	$label = __( 'Past addresses', 'address-book' );

	ob_start(); ?>
		<div class="old-addresses" id="address-<?php the_ID(); ?>-old-addresses">
			<label><?php echo esc_html( $label ); ?></label>
			<span class="old-addresses-count">
				<?php
				// Translators: %d is the number of old addresses.
				echo esc_html( sprintf( _n( '%d former address found.', '%d former addresses found.', $count, 'address-book' ), $count ) );
				?>
			</span>
			<div class="address-list">
				<?php foreach ( $addresses as $address ) : ?>
					<address>
						<?php echo wp_kses_post( wpautop( $address ) ); ?>
					</address>
				<?php endforeach; ?>
			</div>
		</div>
	<?php
	echo wp_kses_post( ob_get_clean() );
}

/**
 * Sanitize text field for valid phone numbers.
 *
 * @param  string $value The saved value.
 * @return string        The sanitized phone number.
 * @since  0.1.2
 */
function sanitize_phone_number( $value ) {
	return preg_replace( '/[^0-9+( )-]/', '', $value );
}
