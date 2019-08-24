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
 * Front-end class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Frontend extends LiveChatX_Abstract
{	

	public $display = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		
		if( ! LiveChatX()->is_request( 'ajax' ) ) {

			// Don't load application on IE browsers
			if( !empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$ua = htmlentities( $_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8' );
				if( preg_match( '~MSIE|Internet Explorer~i', $ua ) || ( strpos( $ua, 'Trident/7.0' ) !== false && strpos( $ua, 'rv:11.0') !== false ) ) {
					return;
				}
			}

			// Actions
			$this->addAction( 'wp_enqueue_scripts', 'isDisplay', 100 );
			// $this->addAction( 'wp_enqueue_scripts', 'assets', 110 );
			$this->addAction( 'wp_footer', 'render', 120 );

		}
	}

	/**
	 * Check display options.
	 *
	 * @since LiveChatX (1.0)
	 * @return void
	 */
	public function isDisplay() {

		$db = lcx_get_option( 'realtime', 'firebase_apiKey' );
		$basics = lcx_get_option( 'general', 'display_basics' );
		$display_type = lcx_get_option( 'general', 'display_type' );
		$exceptPages = lcx_get_option( 'general', 'display_except_pages' );
		$blog = lcx_get_option( 'general', 'display_blog' );

		if( empty( $db ) ) {
			$this->display = false;
			return;
		}

		// Show up for WP users only
		if( !empty( $basics ) && in_array( 'showWPusers', $basics ) ) {
			if( ! is_user_logged_in() )
				return;
		}


		// Show except
		switch( $display_type ) {

			case 'show':
				if( !empty( $exceptPages ) ) {
					if( !is_page( $exceptPages ) ) {
						$this->display = true;
					}
				} else {
					$this->display = true;
				}

				// Force to hide blog-related pages
				if( $blog == 'hide' ) {
					if( fn_lcx_is_blog_page() )
						$this->display = false;
				}

				break;

			case 'hide':
				if( !empty( $exceptPages ) ) {
					if( is_page( $exceptPages ) ) {
						$this->display = true;
					}
				}

				// Force to show in blog-related pages
				if( $blog == 'show' ) {
					if( fn_lcx_is_blog_page() )
						$this->display = true;
				}
				break;
		}

		// 
		// Check basic display settings.
		// 
		if( !empty( $basics ) ) {

			if( $this->display === true ) {

				// Hide on mobile devices.
	 			if( in_array( 'hideMobile', $basics ) ) {
					if( fn_lcx_is_mobile() ) {
						$this->display = false;
					}
				}

				// Hide on SSL pages.
	 			if( in_array( 'hideSSL', $basics ) ) {
					if( is_ssl() ) {
						$this->display = false;
					}
				}

			}
		}

		// 
		// Check for homepage.
		// 
		if( is_home() || is_front_page() ) {
			$home = lcx_get_option( 'general', 'display_home' );

			switch( $home ) {
				case 'show':
					$this->display = true;
					break;
				case 'hide':
					$this->display = false;
					break;
			}
		}

		do_action( 'lcx_check_visibility', $this->display );
	}

	/**
	 * Load css/js files.
	 *
	 * @access public
	 */
	public function assets() {

		if( !$this->display )
			return;

		$upload_uri = fn_lcx_get_upload_dir_var( 'baseurl', '/lcx' );

		wp_register_style(
			'lcx-app',
			$upload_uri . '/app.css', 
			null,
			LCX_VERSION
		);
		wp_enqueue_style( 'lcx-app' );

		// Is developing mode?
		$dev_mode = lcx_get_option( 'advanced', 'secure_dev_mode' );

		// 
		// Production mode
		// 
		if( empty( $dev_mode ) ) {
			
			wp_register_script(
				'lcx-app',
				LCX_URL . '/assets/js/screets.chat.ie11.min.js',
				null,
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-app' );

		// 
		// Developer mode
		// 
		} else {

			wp_register_script( 
				'firebase',
				'https://www.gstatic.com/firebasejs/' . LCX_FIREBASE_VERSION . '/firebase.js',
				 null,
				 LCX_VERSION
			);
			wp_enqueue_script( 'firebase' );
			
			wp_register_script(
				'lcx-events',
				LCX_URL . '/assets/js/src/events.js',
				null,
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-events' );

			wp_register_script(
				'lcx-app',
				LCX_URL . '/assets/js/src/app.js',
				array( 'firebase', 'lcx-events' ),
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-app' );

			wp_register_script(
				'lcx-db',
				LCX_URL . '/assets/js/src/db.firebase.js',
				array( 'lcx-app' ),
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-db' );

			wp_register_script(
				'lcx-frontend',
				LCX_URL . '/assets/js/src/frontend.js',
				array( 'lcx-db' ),
				LCX_VERSION
			);
			wp_enqueue_script( 'lcx-frontend' );
		}

	}

	/**
	 * Render widget.
	 *
	 * @access public
	 */
	public function render() {

		if( !$this->display )
			return;

		$ajax =  fn_lcx_get_ajax();
		$snippet = fn_lcx_get_snippet( false );

		echo "<script>$ajax\n$snippet</script>";

	}

}

return new LiveChatX_Frontend();