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
 * Get real-time platform config.
 *
 * @since Live Chat X (2.4.0)
 * @return array|bool
 */
function fn_lcx_get_realtime_config( $db = 'firebase', $token = true )
{
	$email = trim( lcx_get_option( 'realtime_firebase', 'email' ) );
	$apiKey = trim( lcx_get_option( 'realtime_firebase', 'apiKey' ) );
	$projectId = trim( lcx_get_option( 'realtime_firebase', 'projectId' ) );

	if( !empty( $apiKey ) and !empty( $projectId ) ) {
		$output = array(
			'email' => $email,
			'apiKey' => $apiKey,
			'authDomain' => $projectId . '.firebaseapp.com',
			'databaseURL' => 'https://' . $projectId . '.firebaseio.com',
			'projectId' => $projectId,
			'storageBucket' => $projectId . '.appspot.com'
		);

		if( $token ) {
			$output['token'] = LiveChatX()->get_token();
		}

		return $output;
	}

	return null;
}