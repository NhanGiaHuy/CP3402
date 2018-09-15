<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage Getenberg integration
 * @category inserting into posts
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2018-04-22
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

function wpbc_gutenberg_block_booking() {

	if ( function_exists( 'register_block_type' ) ) {
		wp_register_script( 'gutenberg-wpbc-booking', wpbc_plugin_url( '/js/wpbc-gutenberg.js' ), array(
			'wp-blocks',
			'wp-element'
		) );

		register_block_type( 'gutenberg-wpbc/booking', array(
			'editor_script' => 'gutenberg-wpbc-booking'
		) );
	}
}
add_action( 'init', 'wpbc_gutenberg_block_booking' );