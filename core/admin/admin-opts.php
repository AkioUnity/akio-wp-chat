<?php
/**
 * SCREETS © 2018
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 *
 * @package LiveChatX
 * @author Screets
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Admin options.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Admin_Opts {

	/**
	 * Constructor.
	 */
	function __construct() {

        // 
		// Scripts
        //
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-autocomplete' ); 
        wp_enqueue_script( 'wp-color-picker' ); 
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_media();

		wp_register_script(
            'lcx-admin-opts',
            LCX_URL . '/assets/js/admin/admin-opts.js',
            array( 'wp-color-picker' ),
            LCX_VERSION
        );
        wp_enqueue_script( 'lcx-admin-opts' );

        // 
        // Styles
        //
		wp_register_style(
			'lcx-admin-opts',
			LCX_URL . '/assets/css/admin/admin-opts.css',
			null,
			LCX_VERSION
		);
		wp_enqueue_style( 'lcx-admin-opts' );

        wp_enqueue_script(
            'iris',
            admin_url( 'js/iris.min.js' ),
            array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
            false,
            1
        );

	}

}

new LiveChatX_Admin_Opts();