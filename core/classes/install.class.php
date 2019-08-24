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
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * LiveChatX_Install Class.
 */
class LiveChatX_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 100 );
	}


	/**
	 * Check the plugin version and run the updater if it is required.
	 *
	 */
	public static function check_version() {

		$current = get_option( 'lcx_version' );

		// -1: downgrade, 0: same version, 1: update, 2: new install
		$compare = ( empty( $current ) ) ? 2 : version_compare( LCX_VERSION, $current );

		if( ! defined( 'IFRAME_REQUEST' ) ) {

			// Upgrade from 2.5.2 or lower to new versions
			if( version_compare( $current, '2.5.2' ) <= 0 ) {
				self::upgrade( '2.5.2' );
			} 
			
			if( $compare > 0 ) {
				self::install( $compare );
				do_action( 'lcx_updated' );
			}
		}

	}

	/**
	 * Install and update the plugin.
	 */
	public static function install( $compare ) {
		global $wpdb;

		if ( ! defined( 'LCX_INSTALLING' ) ) {
			define( 'LCX_INSTALLING', true );
		}

		$app = new LiveChatX_App();
		$app->register_post_types();
		$app->register_taxonomies();

		// Create database tables
		// self::create_tables();

		// Setup the plugin basics
		self::setup();

		// Queue upgrades/setup wizard
		$current_version = get_option( 'lcx_version', null );

		// Update the plugin version
		self::update_version();

		// Flush rules after install
		flush_rewrite_rules();

		// Trigger action
		do_action( 'lcx_installed' );

	}

	/**
	 * Setup the plugin.
	 */
	private static function setup() {

		// Remove old roles
		remove_role( 'cx_op' );

		// Get roles
		$admin_role = get_role( 'administrator' );

		// Add capabilities to administrators
		$admin_role->add_cap( 'lcx_admin' );
		$admin_role->add_cap( 'lcx_chat_with_visitors' );

		if( ! current_user_can( 'manage_options' ) )
			$admin_role->add_cap( 'manage_options' );

		// 
		// Update options.
		// 
		$defaults = LiveChatX()->optsFW->get_options();
		$last_options = get_option( LCX_SLUG . '_settings' );

		$last_options = !empty( $last_options ) ? $last_options : array();
		$design_options = array();

		foreach( $defaults as $k => $v ) {
			if( !isset( $last_options[ $k ] ) )
				$last_options[ $k ] = $v;

			if( substr( $k, 0, 7 ) === 'design_' )
				$design_options[ substr( $k, 7 ) ] = $v;
		}

		update_option( LCX_SLUG . '_settings', $last_options );

		// Compile application CSS file
		fn_lcx_compile_app_css( $design_options );

		
		// Insert default widget pages
		/*if( !term_exists( 'contact', 'lcx_page' ) ) {
			$id = wp_insert_term( 
				'Contact Form', 
				'lcx_page', 
				array( 'slug' => 'contact' )
			);
		}*/
	}

	/**
	 * Update the plugin version to the current one.
	 */
	private static function update_version() {
		delete_option( 'lcx_version' );
		add_option( 'lcx_version', LCX_VERSION );
	}

	/**
	 * Upgrade from older versions.
	 */
	private static function upgrade( $last_highest ) {
		
		// 
		// Upgrade from 2.5.2 and older versions.
		// 
		if( $last_highest === '2.5.2' ) {

			$last_options = get_option( LCX_SLUG . '_options' );

			// Remove problematic field
			if( !empty( $last_options['msgs_popup_closing_msg'] ) )
				unset( $last_options['msgs_popup_closing_msg'] );

			// Remove some options for better experience in new one
			if( !empty( $last_options['design_ui_popup_width'] ) )
				unset( $last_options['design_ui_popup_width'] );

			// Update "lcx_settings"
			if( !empty( $last_options ) ) {
				update_option( LCX_SLUG . '_settings', $last_options );
			}
			
			// Delete old options data..
			delete_option( LCX_SLUG . '_options' );
		}


	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 */
	/*private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::get_schema() );

	}
*/
	/**
	 * Get database table schema.
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}
		return "
";
	}

}

LiveChatX_Install::init();