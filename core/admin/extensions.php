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
 * Extensions page class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Extensions extends LiveChatX_Abstract {

    /**
     * Constructor.
     */
    function __construct() {

        // 
        // Styles
        //
        wp_register_style(
            'lcx-extensions',
            LCX_URL . '/assets/css/admin/extensions.css',
            null,
            LCX_VERSION
        );
        wp_enqueue_style( 'lcx-extensions' );
        

        if( !empty( $_GET['action'] ) && !empty( $_GET['name'] )) {
            $redirect = admin_url( 'admin.php?page=admin.php?page=lcx_extensions' );

            switch( $_GET['action'] ) {
                case 'activate':
                    activate_plugins( $this->get_plugin_path( $_GET['name'] ), $redirect );
                    break;
                case 'deactivate':
                    deactivate_plugins( $this->get_plugin_path( $_GET['name'] ), $redirect );
                    break;
            }
        }

    }

    function get_plugin_path( $plugin ) {
        if ( ! is_file( $dir = WPMU_PLUGIN_DIR . '/' . $plugin ) ) {
            if ( ! is_file( $dir = WP_PLUGIN_DIR . '/' . $plugin ) )
                $dir = null;
        }

        return $dir;
    }

}

new LiveChatX_Extensions();