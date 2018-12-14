# Address Book #
**Contributors:**      Chris Reynolds
**Requires at least:** 4.4
**Tested up to:**      4.9.1
**Stable tag:**        0.3
**License:**           GPLv3
**License URI:**       http://www.gnu.org/licenses/gpl-3.0.html

## Description ##

A WordPress plugin for storing and maintaining addresses.

## Installation ##

### Manual Installation ###

1. Upload the entire `/address-book` directory to the `/wp-content/plugins/` directory.
2. Activate Address Book through the 'Plugins' menu in WordPress.

## Changelog ##

### 0.3.1 ###
* Remove check for CMB2 class (not needed, CMB2 handles it internally).

### 0.3 ###
* Using composer to require extended cpts and cmb2.
* Moved requires to an init function.

### 0.2.2 ###
* Added new helper function to handle `WP_Query` so we aren't using `get_posts`
* Updated `get_addresses` so it works like `get_posts`
* Added meta query for inactive addresses
* Added default relationships and removed row actions so they are harder to edit
* Added caching for full address lists

### 0.2.1 ###
* Added admin page for custom address list
* Moved CMB2 stuff into CPT directory
* Added generic and CMB2 helper functions

### 0.2 ###
* Update license to GPL3

### 0.1.2 ###
* Added phone number field and custom sanitization for phone numbers
* Added support for phone number revisions
* Added support for special dates

### 0.1.1 ###
* Refined the handling of address meta revisions
* Added new Past Addresses meta box
* Made more generic CMB2 helper functions

### 0.1 ###
* First release
* Adds initial plugin framework
* Added post type, taxonomies, and initial metaboxes
* Added support for revisions and post meta revision support
* Changed the default "Enter title here" placeholder text

## Upgrade Notice ##

### 0.1 ###
First Release
