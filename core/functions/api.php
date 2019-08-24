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
 * Get api data from Screets server.
 *
 * @since Live Chat X (2.4.0)
 * @return null|array
 */
function fn_lcx_get_api_data( $api = false )
{

    if( empty( $api ) ) {
        return false;
    }

    $data = fn_lcx_curl_get( 'http://api.screets.org/v2/?' );
    
}