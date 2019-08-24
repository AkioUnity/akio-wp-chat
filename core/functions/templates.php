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
 * Get a template content.
 *
 * @since Live Chat X (2.4.0)
 * @return bool.
 */
function fn_lcx_get_template( $file, $args = array() )
{
    $template = new LiveChatX_Template();

    if( !empty( $args ) ) {
        foreach( $args as $k=>$v ) {
            $template->{$k} = $v;
        }
    }
    
    // Current template path
    $path = LiveChatX()->template_path() . '/' . $file . '.php';

    // Set default template path
    if( !file_exists( $path ) ) {
        $path = LCX_PATH . '/core/templates/basic/' . $file . '.php';
    }

    return $template->render( $path );
}

/**
 * Compile application SCSS file to CSS.
 *
 * @since Live Chat X (2.4.0)
 * @return bool.
 */
function fn_lcx_compile_app_css( $compile_data = array() ) 
{
    $_GLOBALS['lcx_compile_data'] = $compile_data;

    include LCX_PATH . '/core/admin/compile-scss.php';   
}

/**
 * Get anonymous image url.
 *
 * @since Live Chat X (2.4.0)
 * @return string
 */
function fn_lcx_get_anonymous_img() {
    return apply_filters( 'lcx_anonymous_img_url', LCX_URL . '/assets/img/anonymous.png' );
}
/**
 * Get response time by HTML.
 *
 * @since Live Chat X (2.4.0)
 * @return bool|string
 */
function fn_lcx_get_response_time( $status, $str, $ignoreIfNoTimeSet = true )
{
    $response_time = lcx_get_option( 'chats', "response_replies_{$status}" );

    // Return false if no response time set.
    if( $ignoreIfNoTimeSet && $response_time == 'none' )
        return false;

    $time_str = lcx__( 'msgs', 'response_time_' . $response_time );

    return str_replace( '{time}', "<span class=\"lcx-time\">$time_str</span>", $str );
}

/**
 * Get countries as select list.
 *
 * @since 3.0.0
 * @return string List of countries.
 */
function fn_lcx_countries_list( $default = '', $show_phone_codes = true ) {

    $output = '';
    $pcode = '';
    $phone_codes = include( LCX_PATH . '/data/phone-codes.php' );

    foreach( include( LCX_PATH . '/data/countries.php' ) as $code => $name ) {
        $pcode = '';

        if( $show_phone_codes && !empty( $phone_codes[$code] ) ) {
            $prefix = ( $phone_codes[$code][0] == '+' ) ? '' : '+';
            $pcode = ' (' .$prefix . $phone_codes[$code] . ')';
        }
        $selected = selected( $default, $code, 0 );
        $output .= "<option value=\"{$code}\" {$selected}>{$name}{$pcode}</option>";
    }

    return $output;
}