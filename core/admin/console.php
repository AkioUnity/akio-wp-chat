<?php
/**
 * Heino © 2019
 *
 * Heino, All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 *
 * @package LiveChatX
 * @author Akio
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Console class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Console 
{

	/**
	 * Constructor.
	 */
	function __construct() {

		// Include WP media JS libraries
		wp_enqueue_media();

		// 
		// Dependencies
		// 

		wp_register_script( 'firebase', LCX_URL . '/assets/js/library/firebase.js', null, LCX_VERSION );
		wp_register_script( 'trumbowyg', LCX_URL . '/assets/js/library/trumbowyg/trumbowyg.min.js', array( 'jquery', 'firebase' ), LCX_VERSION );
		wp_register_script( 'list', LCX_URL . '/assets/js/library/list.min.js', array( 'firebase' ), LCX_VERSION );

		wp_enqueue_script( 'firebase' );
		wp_enqueue_script( 'trumbowyg' );
		wp_enqueue_script( 'list' );

		// Is developing mode?
		$dev_mode = lcx_get_option( 'advanced', 'secure_dev_mode' );

		// 
		// Production mode
		// 
		if( empty( $dev_mode ) ) {
			
			wp_register_script(
				'lcx-console',
				LCX_URL . '/assets/js/screets.chat.console.min.js',
				array( 'firebase' ),
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-console' );

		// 
		// Developer mode
		//
		} else {

			wp_register_script(
				'lcx-events',
				LCX_URL . '/assets/js/src/events.js',
				null,
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-events' );

			wp_register_script(
				'lonewolf',
				LCX_URL . '/assets/js/src/app.js',
				array( 'firebase', 'lcx-events' ),
				LCX_VERSION
			);
			wp_enqueue_script( 'lonewolf' );

			wp_register_script(
				'lcx-db',
				LCX_URL . '/assets/js/src/db.firebase.js',
				array( 'lonewolf' ),
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-db' );

			wp_register_script(
				'lcx-console',
				LCX_URL . '/assets/js/src/console.js',
				array( 'lcx-db' ),
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-console' );
		}

		/**
		 * Filter for console stylesheet file url
		 */
		$console_css = apply_filters( 'lcx_console_css_url', LCX_URL . '/assets/css/admin/console/console.css' );

		wp_register_style(
			'lcx-admin-console',
			$console_css,
			null,
			LCX_VERSION
		);
		wp_enqueue_style( 'lcx-admin-console' );

	}

}

new LiveChatX_Console();