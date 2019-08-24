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
 *
 * Application class. Initialized in both front-end and back-end.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_App extends LiveChatX_Abstract {

	/**
	 * Construction.
	 *
	 * @access public
	 */
	public function __construct()
    {
		$this->addAction( 'init', 'init', 0 );

	}
	
	/**
	 * Initializing.
	 *
	 * @access public
	 */
	public function init()
    {
		/**
		 * Register short codes.
		 */
		add_shortcode( 'livechatx', array( $this, 'sc_livechatx' ) );

		// Enable shortcodes on text widget
		add_filter( 'widget_text', 'do_shortcode' );

        // Register post types
        $this->register_post_types();

        // Register taxonomies
        $this->register_taxonomies();

        // Logged in user actions
        if( is_user_logged_in() )
        {
            $this->logged_user();
        }
	}

    /**
     * Action short code.
     *
     * @access public
     */
    public function sc_livechatx( $atts, $content = '' )
    {
        $value = '';
        $atts = shortcode_atts( array(
            'type' => 'openPopup',
            'name' => '', // popup name
            'msg' => '', // chat message
            'class' => ''
        ), $atts, 'livechatx' );

        if( empty( $atts['type'] ) ) return;

        if( !empty( $atts['name'] ) )
            $value = 'data-value="' . $atts['name'] . '"';

        if( !empty( $atts['msg'] ) )
            $value = 'data-value="' . $atts['msg'] . '"';

        return '<a href="#" class="lcx-action '.$atts['class'].'" data-action="'.$atts['type'].'" ' . $value . '>' . $content . '</a>';
    }

    /**
     * Register post types.
     *
     * @access public
     */
    public function register_post_types()
    {

        // Auto-messages post-type
        $labels = array(
            'name'                => _x( 'Auto-messages', 'Post Type General Name', 'lcx' ),
            'singular_name'       => _x( 'Auto-message', 'Post Type Singular Name', 'lcx' ),
            'menu_name'           => __( 'Auto-messages', 'lcx' ),
            'parent_item_colon'   => __( 'Parent item:', 'lcx' ),
            'all_items'           => __( 'All', 'lcx' ),
            'view_item'           => __( 'View', 'lcx' ),
            'add_new_item'        => __( 'Add new', 'lcx' ),
            'add_new'             => __( 'Add new', 'lcx' ),
            'edit_item'           => __( 'Edit', 'lcx' ),
            'update_item'         => __( 'Update', 'lcx' ),
            'search_items'        => __( 'Search', 'lcx' ),
            'not_found'           => __( 'Not found', 'lcx' ),
            'not_found_in_trash'  => __( 'Not found', 'lcx' ),
        );
        $args = array(
            'labels'              => $labels,
            'supports'            => array('title'),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => 'livechatx',
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'menu_icon'           => '',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'rewrite'             => false,
            'capability_type'     => 'page'
        );
        register_post_type( 'lcx_auto_msgs', $args );

    }

    /**
     * Register taxonomies.
     *
     * @access public
     */
    public function register_taxonomies()
    {

        /*// Custom pages
        $labels = array(
            'name'                       => _x( 'Where to show up?', 'Taxonomy General Name', 'lcx' ),
            'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'lcx' ),
            'menu_name'                  => __( 'Location', 'lcx' ),
            'all_items'                  => __( 'All', 'lcx' ),
            'parent_item'                => __( 'Parent item', 'lcx' ),
            'parent_item_colon'          => __( 'Parent item:', 'lcx' ),
            'new_item_name'              => __( 'Add new', 'lcx' ),
            'add_new_item'               => __( 'Add new', 'lcx' ),
            'edit_item'                  => __( 'Edit', 'lcx' ),
            'update_item'                => __( 'Update', 'lcx' ),
            'view_item'                  => __( 'View', 'lcx' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'lcx' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'lcx' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'lcx' ),
            'popular_items'              => __( 'Popular items', 'lcx' ),
            'search_items'               => __( 'Search', 'lcx' ),
            'not_found'                  => __( 'Not found', 'lcx' ),
            'no_terms'                   => __( 'Not found', 'lcx' ),
            'items_list'                 => __( 'Items list', 'lcx' ),
            'items_list_navigation'      => __( 'Items list navigation', 'lcx' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => false,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false
        );
        register_taxonomy( 
            'lcx_page', 
            apply_filters( 'lcx_page_object_types', array( 'lcx_custom_form' ) ), 
            $args
        );

        // Support categories
        $labels = array(
            'name'                       => _x( 'Support Categories', 'Taxonomy General Name', 'lcx' ),
            'singular_name'              => _x( 'Support Category', 'Taxonomy Singular Name', 'lcx' ),
            'menu_name'                  => __( 'Support Category', 'lcx' ),
            'all_items'                  => __( 'All', 'lcx' ),
            'parent_item'                => __( 'Parent item', 'lcx' ),
            'parent_item_colon'          => __( 'Parent item:', 'lcx' ),
            'new_item_name'              => __( 'Add new', 'lcx' ),
            'add_new_item'               => __( 'Add new', 'lcx' ),
            'edit_item'                  => __( 'Edit', 'lcx' ),
            'update_item'                => __( 'Update', 'lcx' ),
            'view_item'                  => __( 'View', 'lcx' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'lcx' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'lcx' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'lcx' ),
            'popular_items'              => __( 'Popular items', 'lcx' ),
            'search_items'               => __( 'Search', 'lcx' ),
            'not_found'                  => __( 'Not found', 'lcx' ),
            'no_terms'                   => __( 'Not found', 'lcx' ),
            'items_list'                 => __( 'Items list', 'lcx' ),
            'items_list_navigation'      => __( 'Items list navigation', 'lcx' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false
        );
        register_taxonomy( 
            'lcx_support_category', 
            apply_filters( 'lcx_support_category_object_types', array( 'lcx_custom_form' ) ), 
            $args
        );*/
    }

    /**
     * Logged in user actions.
     *
     * @access public
     */
    public function logged_user()
    {

        // Modify admin bar
        if( !is_admin() && current_user_can( 'lcx_chat_with_visitors' ) )
        {
            $this->addAction( 'admin_bar_menu', '__setup_admin_bar', 9000 );
        }
    }

     function __setup_admin_bar( $wp_admin_bar )
     {

        // Add chat console
        $args = array(
            'id'    => 'lcx_chat_console',
            'title' => __( 'Chat Console', 'lcx' ),
            'href'  => admin_url( 'admin.php?page=livechatx' ),
            'meta'  => array( 'class' => 'lcx-bar-chat-console' ),
            'parent' => 'site-name'
        );
        $wp_admin_bar->add_node( $args );
    }
}

