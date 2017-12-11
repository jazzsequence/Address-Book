<?php
/**
 * Plugin Name: Address Book
 * Plugin URI: https://github.com/jazzsequence/Address-Book
 * Description: A WordPress plugin for storing and maintaining addresses.
 * Author: Chris Reynolds
 * Author URI: https://chrisreynolds.io
 * License: GPLv2
 *
 * @package AddressBook
 */

namespace AddressBook;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
