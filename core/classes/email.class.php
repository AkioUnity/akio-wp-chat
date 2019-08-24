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
 *
 * Application class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Email extends LiveChatX_Abstract {

	protected $type = '';
	protected $content = '';

	/**
	 * Construction.
	 *
	 * @access public
	 */
	public function __construct( $type ) {

		// Update defaults
		$this->type = $type;

	}

	/**
	 * Render the email.
	 *
	 * @access public
	 * @since Live Chat X (2.4.0)
	 * @return bool True if succeed
	 */
	public function render( $title, $content, $signature = '', $preloader_text = '' ) {
		
		// Get site info
		$site_name = lcx_get_option( 'site', 'info_name' );
		$site_url = lcx_get_option( 'site', 'info_url' );
		
		// Create template 
		$args['site_logo'] = lcx_get_option( 'site', 'info_logo' );
		$args['site_name'] = $site_name;
		$args['site_url'] = $site_url;
		$args['logo_width'] = lcx_get_option( 'site', 'info_email_logo_width' );
		$args['color'] = lcx_get_option( 'design', 'color_primary' );
		$args['color2'] = lcx_get_option( 'design', 'color_secondary' );
		$args['offset'] = 30;
		$args['radius'] = lcx_get_option( 'design', 'ui_radius' );
		$args['radius_big'] = lcx_get_option( 'design', 'ui_radius_big' );
		$args['plugin_url'] = LCX_URL;
		$args['social_links'] = fn_lcx_get_social_links();
		
		$args['preloader_text'] = $preloader_text;
		$args['title'] = $title;
		$args['content'] = $this->sanitize_html( $content );
		$args['signature'] = wpautop( $signature );
		$args['footer'] = '&copy; ' . date('Y') . ' ' . $site_name;

		$this->content = fn_lcx_get_template( 'email/' . $this->type, $args );

		return $this->content;

	}

	/**
	 * Send email.
	 *
	 * @access public
	 * @since Live Chat X (2.4.0)
	 * @return bool True if succeed
	 */
	public function send( $subject, $to, $headers ) {
		$subject = apply_filters( "lcx_{$this->type}_email_subject", $subject );

		return wp_mail( $to, $subject, $this->content, $headers );

		/*// SEND NOTIFICATION EMAIL TO ADMINS
		if( $ntf_admins && !empty( $ntf_email ) ) {

			$admin_emails = array_map( 'trim', explode( ',', $ntf_email ) );

			// Include restaurant owner emails
			if( !empty( $restID ) ) {
				$owner_emails = get_post_meta( $restID, 'owner_emails', true );

				if( !empty( $owner_emails ) ) {
					$owner_emails = array_map( 'trim', explode( ',', $owner_emails ) );
					$admin_emails = array_merge( $admin_emails, $owner_emails );
				}
			}

			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$headers[] = 'From: ' . $site_email;
			$to_admin = wp_mail( $admin_emails, $ntf_subject, $ntf_msg, $headers );
		}

		return $to_guest;*/

	}

	/**
	 * Sanitize HTML content.
	 *
	 * @access public
	 * @since Live Chat X (2.4.0)
	 * @return string
	 */
	public function sanitize_html( $html ) {
		return strip_tags( wp_unslash( $html ), '<strong><em><p><a><img><br><ul><ol><li><div><pre><span>' );
	}

}

