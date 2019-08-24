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

require_once LCX_PATH . '/core/library/scss/scss.inc.php';

$settings = @$_POST['lcx_settings'];

use Leafo\ScssPhp\Compiler;
$scss = new Compiler();
$scss->setImportPaths( LCX_PATH . '/assets/css/basic' );

$opts = array(
    'colors_primary' => array( 'primary', '', '#e54045' ),
    'colors_secondary' => array( 'secondary', '', '#2294e3' ),
    'colors_highlight' => array( 'highlightColor', '', '#fffc79' ),
    'ui_radius' => array( 'radius', 'px', '4px' ),
    'ui_radius_big' => array( 'radiusBig', 'px', 8 ),
    'ui_popup_width' => array( 'popupW', 'px', 300 ),
    'ui_starter_size' => array( 'starterSize', 'px', 50 ),
    'ui_offset_x' => array( 'offsetX', 'px', 20 ),
    'ui_offset_y' => array( 'offsetY', 'px', 20 ),
    'ui_starter_icon_size' => array( 'starterIconW', 'px', 30 ),
    'ui_position' => array( 'position', '', 'bottom-right' ),
    'ui_font_family' => array( 'fontfamily', '', '' ),
    'ui_alt_font_family' => array( 'fontfamily2', '', '' ),
);

if( !empty( $_GLOBALS['lcx_compile_data'] ) ) {
    $compile_var = $_GLOBALS['lcx_compile_data'];

    foreach( $opts as $key => $args ) {
        $value = !empty( $compile_var[$key] ) ? $compile_var[$key] : $args[2];

        if( !empty( $value ) )
            $compile_var[$args[0]] = stripslashes( $value ) . $args[1];
    }

} else {
    foreach( $opts as $key => $args ) {
        $value = !empty( $settings[ "design_{$key}" ] ) ? $settings[ "design_{$key}" ] : $args[2];

        if( !empty( $value ) )
            $compile_var[$args[0]] = stripslashes( $value ) . $args[1];
    }
}

// Include assets url if not exists
if( empty( $compile_var['assetsURL'] ) ) {
    $plugin_url = str_replace( array( 'http://', 'https://' ), '//', LCX_URL );
    $compile_var['assetsURL'] = $plugin_url . '/assets';
}

$scss->setVariables( apply_filters( 'lcx_app_sccs_vars', $compile_var ) );
$scss->setFormatter( 'Leafo\ScssPhp\Formatter\Crunched' );

$lcx_dir = fn_lcx_get_upload_dir_var( 'basedir', '/lcx' );

if( !file_exists( $lcx_dir ) ) {
    if( ! mkdir( $lcx_dir, 0777, true ) ) {
        die( 'The directory is not writable: ' . $upload_dir['basedir'] );
    }
}

// Get full scss file url
$app_scc = file_get_contents( apply_filters( 'lcx_app_scss', LCX_PATH . '/assets/css/basic/app.scss' ) );
$iApp_scss = file_get_contents( apply_filters( 'lcx_app_iframe_scss', LCX_PATH . '/assets/css/basic/app-iframe.scss' ) );
$app_css = $lcx_dir . '/app.css';
$iApp_css = $lcx_dir . '/app-iframe.css';

// Create app.css and app-iframe files if not exists
if( !file_exists( $app_css ) || !file_exists( $app_css ) ) {
    $fh = fopen( $app_css, 'w' );
    $fhi = fopen( $iApp_css, 'w' );
}

// Compile now
$app = $scss->compile( $app_scc );
$iApp = $scss->compile( $iApp_scss );

// Get custom CSS
$customCSS = ( !empty( $settings[ 'design_advanced_customCSS' ] ) ) ? $settings[ 'design_advanced_customCSS' ] : '';

// Include custom css code
$app .= ' ' . stripslashes( $customCSS );

file_put_contents( $app_css, $app );
file_put_contents( $iApp_css, $iApp );
