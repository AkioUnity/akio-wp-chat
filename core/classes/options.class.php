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


class LCX_Options extends LiveChatX_Abstract  {
    
    private $framework;

    function __construct() {

        add_action( 'admin_menu', array( $this, 'init' ), 200 );

        $this->framework = new LCX_Options_FW( LCX_PATH .'/core/options.php', LCX_SLUG );

        add_filter( $this->framework->get_option_group() .'_settings_validate', array(&$this, 'validate') );
    }

    public function init() {    
        $this->framework->add_settings_page( array(
            'parent_slug' => 'livechatx',
            'page_title'  => __( 'Options', 'lcx' ),
            'capability'  => 'lcx_admin',
            'menu_title'  => __( 'Options', 'lcx' ),
            'page_content' => array( $this, 'render' )
        ) );
        
    }

    public function render() {
        global $wpdb;
       ?>
        <div class="wrap">
            <img src="<?php echo LCX_URL; ?>/assets/img/logo-200x.png" alt="" class="lcx-icon">
            <h1><?php echo sprintf( __( '%s Options', 'lcx' ), LCX_NAME ); ?></h1>

            <p class="lcx-desc description"><strong class="lcx-edition"><?php echo LCX_EDITION; ?> Edition</strong> <span class="lcx-highlight"><?php echo LCX_VERSION; ?></span> &nbsp;-&nbsp; <a href="<?php echo LCX_CHANGELOG_URL; ?>" class="action-button" target="_blank">Changelog &raquo;</a></p>

            <?php
            // Output your settings form
            $this->framework->settings();
            ?>
        </div>
        <?php
        
    }

    public function get_options() {
        return $this->framework->get_settings();
    }


    function validate( $input ) {

        // 
        // Update user capabilities
        // 
        if ( ! function_exists( 'get_editable_roles' ) ) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        $roles = get_editable_roles();
        $lcx_caps = fn_lcx_get_capabilities();

        foreach( $roles as $role_name => $role_info ) {
            $role = get_role( $role_name );

            // First, remove all the plugin capabilities
            foreach( $lcx_caps as $capid => $name ) {
                $role->remove_cap( $capid );
                $role->remove_cap( 'manage_options' );
            }

            // Add chosen capabilities
            if( !empty( $_POST['op_caps'][$role_name] ) ) {
                foreach( $_POST['op_caps'][$role_name] as $capid ) {
                    $role->add_cap( $capid );

                    if( $capid === 'lcx_admin' )
                        $role->add_cap( 'manage_options' );
                }
            }

            // In any case, keep admins to manage plugin options
            if( $role_name == 'administrator' ) {
                $role->add_cap( 'manage_options' );
                $role->add_cap( 'lcx_admin' );
            }
        }

        // 
        // Compile application CSS file
        //
        fn_lcx_compile_app_css();

        return $input;
        
    }
}