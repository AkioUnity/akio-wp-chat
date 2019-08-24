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
 * Get chat message to initiate chat.
 *
 * @since Live Chat X (2.4.0)
 * @return array
 */
function fn_lcx_get_auto_msg() {

    $auto_msgs = get_posts( array( 'post_type' => 'lcx_auto_msg' ) );

    if( empty( $auto_msgs ) ) {
        return null;
    }

    $data = array();
    foreach( $auto_msgs as $msg ) {
        $rules = get_post_meta( $msg->ID, 'lcx_rules', 1 );

        foreach( $rules as $rule ) {
            
        }
    }
    
}