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
 * Get widget content.
 *
 * @since Live Chat X (2.4.0)
 *
 * @param array $data Options for the function.
 * @return string|null Rendered content of the widget.
 */
function fn_lcx_api_render_widget( $data = array() ) 
{
	$v = LCX_VERSION;
	$dev_mode = lcx_get_option( 'advanced', 'secure_dev_mode' );
	$lcx_custom_uri = fn_lcx_get_upload_dir_var( 'baseurl', '/lcx' );
	
	$dev_mode = ( !empty( $dev_mode ) ) ? true : false;

	// Update version for developing mode
	if( $dev_mode ) {
		$v .= '-' . time();
	}

	// Get template messages
	$msgs = fn_lcx_get_template_msgs();

	// Get template arguments
	$template_args = apply_filters( 'lcx_skin_args', array(
		'css' => array( $lcx_custom_uri . '/app.css?ver=' . $v ),
		'msg' => $msgs
	));


	if( $dev_mode ) {
		$js = array(
			LCX_URL . '/assets/js/library/firebase.js?v=' . $v,
			LCX_URL . '/assets/js/src/events.js?v=' . $v,
			LCX_URL . '/assets/js/src/frontend.js?v=' . $v
		);
		
	} else {
		$js = array(
			LCX_URL . '/assets/js/screets.chat.min.js?v=' . $v
		);
	}

	// Get default style and script file paths
	$css = apply_filters( 'lcx_skin_css_files', array(
		$lcx_custom_uri . '/app-iframe.css?ver=' . $v
	));
	$js = apply_filters( 'lcx_skin_js_files', $js );

	$userid = null;
	$is_operator = current_user_can( 'lcx_chat_with_visitors' );
	$is_logged = is_user_logged_in();
	$realtime_db = 'firebase';

	// Logged in user
	if( $is_logged ) {
		$userid = 'u' . get_current_user_id();
		$user_type = ( $is_operator ) ? 'operator' : 'member';
	
	// Just anonymous visitor
	} else {
		$user_type = 'webVisitor';
	}

	return array(
		'js' => $js,
		'css' => $css,
		'opts' => apply_filters( 'lcx_app_opts', array(
			'db' => fn_lcx_get_realtime_config( $realtime_db, $is_logged ),
			'userid' => $userid,
			'userType' => $user_type,
			'anonymousImage' => fn_lcx_get_anonymous_img(),
			'companyName' => lcx_get_option( 'site', 'info_name' ),
			'companyURL' => lcx_get_option( 'site', 'info_url' ),
			'companyLogo' => lcx_get_option( 'site', 'info_logo' ),
			'ajax' => array(
				'nonce' => wp_create_nonce( LiveChatX_AJAX::NONCE ),
				'url' => LiveChatX()->ajax_url()
			),
			'pluginURL' => LCX_URL
		)),
		'html' => fn_lcx_get_template( 'widget', $template_args )
	);
}
add_action( 'rest_api_init', function () {
	register_rest_route( 'screetslcx/v1', '/widget/render', array(
		'methods' => 'POST',
		'callback' => 'fn_lcx_api_render_widget',
	));
});


/**
 * Grab loader options.
 *
 * @since Live Chat X (2.4.0)
 *
 * @param array $data Options for the function.
 * @return string|null Loader options.
 */
/*function fn_lcx_get_loader_opts( $data = array() )
{
	$lcx_url = fn_lcx_get_upload_dir_var( 'baseurl', '/lcx' );
	$ver = time();

	return apply_filters( 'lcx_skin_loader_opts', array(
		'pluginURL' => esc_url( LCX_URL ),
		'cssFiles' => array(),
		'jsFiles' => array()
	));
	
}
add_action( 'rest_api_init', function () {
  register_rest_route( 'screetslcx/v1', '/loader/options', array(
	'methods' => 'GET',
	'callback' => 'fn_lcx_get_loader_opts',
  ) );
});*/

