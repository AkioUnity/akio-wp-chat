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
 * Get ajax data without script wrapper.
 *
 * @since Live Chat X (2.6.0)
 * @return string
 */
function fn_lcx_get_ajax()
{	
	$ajax = json_encode( array(
		'uri' => LiveChatX()->ajax_url(),
		'nonce' => wp_create_nonce( LiveChatX_AJAX::NONCE )
	));
	return <<<EOT
var lcxAJAX = $ajax;
EOT;
}

/**
 * Get embedding JavaScript code without "script" wrapper.
 *
 * @since Live Chat X (2.4.0)
 * @return string
 */
function fn_lcx_get_snippet( $crossDomain = false )
{	
	$opts_json = '';
	$siteurl = get_site_url();

	$apiOpts = array(
		'uri' => apply_filters( 'lcx_skin_loader_uri', esc_url_raw( LCX_URL ) ),
		'root' => esc_url_raw( rest_url() ),
		'v' => LCX_API_VERSION
	);

	$apiOpts = json_encode( $apiOpts );

	$js = <<<EOT
var lcxAPI = $apiOpts;
(function() {
	var d = document;
	window.addEventListener( "load", function() {
		var b = d.createElement("script");
		b.src = lcxAPI.uri + '/assets/js/loader.js';
		b.async = !0;
		d.head.appendChild(b);
	}, !1);
})();
EOT;

	$js = str_replace( 
		array( "\n","\t", ' = ' ),	
		array( '', '', '=' ), 
		$js
	);
	
	return "$opts_json $js";
	
}

/**
 * Get widget content.
 *
 * @since Live Chat X (2.6.0)
 * @return array
 */
function fn_lcx_get_widget_content()
{
	$assets = array();
	$user = wp_get_current_user();
	$basics = lcx_get_option( 'general', 'display_basics' );
	$msgs = lcx_get_option_group( 'msgs', true );
	$chats = lcx_get_option_group( 'chats' );
	$design = lcx_get_option_group( 'design' );
	$site = lcx_get_option_group( 'site' );
	$upload_uri = fn_lcx_get_upload_dir_var( 'baseurl', '/lcx' );
	$upload_path = fn_lcx_get_upload_dir_var( 'basedir', '/lcx' );

	// Is developing mode?
	$dev_mode = lcx_get_option( 'advanced', 'secure_dev_mode' );
	
	// Get right version
	$version = empty( $dev_mode ) ? LCX_VERSION : time();

	// CSS files
	$assets[] = array( 'type' => 'css', 'href' => $upload_uri . '/app-iframe.css?v=' . $version );

	// Prepare basics
	$basics = empty( $basics ) ? array() : $basics;

	// Additional strings
	$msgs['collector_postcard_online'] = fn_lcx_get_response_time( 'online', $msgs['collector_postcard'] );
	$msgs['collector_postcard_offline'] = fn_lcx_get_response_time( 'offline', $msgs['collector_postcard'] );

	// IE 11
	/*if( strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false )
		$assets[] = array( 'type' => 'js', 'src' => LCX_URL . '/assets/js/ie.js?v=' . $version );*/

	// JS files in production mode
	if( empty( $dev_mode ) ) {
		$assets[] = array( 'type' => 'js', 'src' => LCX_URL . '/assets/js/screets.chat.min.js?v=' . $version );

	// JS files in development mode
	} else {
		$assets[] = array( 'type' => 'js', 'src' => 'https://www.gstatic.com/firebasejs/' . LCX_FIREBASE_VERSION . '/firebase-app.js?v=' . $version );
		$assets[] = array( 'type' => 'js', 'src' => 'https://www.gstatic.com/firebasejs/' . LCX_FIREBASE_VERSION . '/firebase-auth.js?v=' . $version );
		$assets[] = array( 'type' => 'js', 'src' => 'https://www.gstatic.com/firebasejs/' . LCX_FIREBASE_VERSION . '/firebase-database.js?v=' . $version );
		$assets[] = array( 'type' => 'js', 'src' => LCX_URL . '/assets/js/src/events.js?v=' . $version );
		$assets[] = array( 'type' => 'js', 'src' => LCX_URL . '/assets/js/src/frontend.js?v=' . $version );
	}

	return array(
		'assets' => $assets,
		'extraAssets' => apply_filters( 'lcx_widget_assets', array() ),
		'iframe' => fn_lcx_get_template( 'widget', array(
			'css' => array( $upload_path . '/app.css' ),
			'msg' => $msgs,
			'design' => $design,
			'chats' => $chats,
			'site' => lcx_get_option_group( 'site' )
		)),
		'opts' => apply_filters( 'lcx_frontend_app_opts',
			array(
				'db' => fn_lcx_get_realtime_config(),
				'ajax' => array(
					'nonce' => wp_create_nonce( LiveChatX_AJAX::NONCE ),
					'url' => LiveChatX()->ajax_url()
				),
				'user' => fn_lcx_get_user_data(),
				'autoinit' => true,
				'mobileBreakpoint' => 450,
				'collectorReqs' => @$chats['offline_coll_req_fields'],
				'offlineInit' => @$chats['offline_initResponse'],
				'allowedPopups' => array( 'cnv', 'online' ),
				'anonymousImage' => fn_lcx_get_anonymous_img(),
				'systemImage' => LCX_URL . '/assets/img/logo-120x.png',
				'companyName' => $site['info_name'],
				'companyURL' => lcx_get_option( 'site', 'info_url' ),
				'companyLogo' => $site['info_logo'],
				'dateFormat' => $chats['time_dateFormat'],
				'hideOffline' => in_array( 'hideOffline', $basics ),
				'disableLookingAt' => in_array( 'disableLookingAt', $basics ),

				'_pluginurl' => LCX_URL,
				'_optsurl' => admin_url( 'admin.php?page=options-general.php%3Fpage%3Dlcx_opts' )
			)
		),
		'strings' => apply_filters( 'lcx_frontend_app_strings', $msgs )
	);

}