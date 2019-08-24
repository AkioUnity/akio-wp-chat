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
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !is_user_logged_in() ) {
    return false;
}

// You can get custom token for a specific user id
$uid = get_transient( 'lcx-custom-token-uid' );


if( !class_exists( '\\Firebase\\JWT\\BeforeValidException' ) )
    require LCX_PATH . '/core/library/firebase/BeforeValidException.php';

if( !class_exists( '\\Firebase\\JWT\\ExpiredException' ) )
    require_once LCX_PATH . '/core/library/firebase/ExpiredException.php';

if( !class_exists( '\\Firebase\\JWT\\SignatureInvalidException' ) )
    require_once LCX_PATH . '/core/library/firebase/SignatureInvalidException.php';

if( !class_exists( '\\Firebase\\JWT\\JWT' ) )
    require_once LCX_PATH . '/core/library/firebase/JWT.php';

use \Firebase\JWT\JWT;

$service_account = lcx_get_option( 'realtime', 'firebase_private_key' );

// Get private key data
if( !empty( $service_account ) ) {
    $service_account = json_decode( $service_account );

    if( empty( $service_account->private_key_id ) ) {
        return false;
    }
} else {
    return false;
}

$service_account_email = $service_account->client_email;
$private_key = $service_account->private_key;

// Get current logged in user's id if no custom user id
if( empty( $uid ) ) { 
    $user = wp_get_current_user();
    $uid = 'u' . $user->ID;
}

// Clean transient now
delete_transient( 'lcx-custom-token-uid' );

$now_seconds = time();
$payload = array(
    'uid' => $uid,
    'iss' => $service_account_email,
    'sub' => $service_account_email,
    'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
    'iat' => $now_seconds,
    'exp' => $now_seconds + ( 60*60 ),   // Maximum expiration time is one hour
    'claims' => array(
        'isOperator' => current_user_can( 'lcx_chat_with_visitors' )
    )
);

return JWT::encode( $payload, $private_key, 'RS256' );