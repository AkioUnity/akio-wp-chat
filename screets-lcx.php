<?php
/*
  Plugin Name: Screets Live Chat
  Plugin URI: https://screets.io
  Description: A beautiful, intelligent <strong>chat tool</strong>.
  Version: 2.8.5
  Author: Screets
  Author URI: https://screets.io
  Requires at least: 5.0
  Tested up to: 5.2.2
  Text Domain: lcx
  Domain Path: /languages
*/

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
 * @link https://screets.com
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main LiveChatX Class.
 *
 * @class LiveChatX
 */
final class LiveChatX 
{
	/**
	 * Plugin version.
	 *
	 * @var string
	 * @since Live Chat X (2.4.0)
	 */
	public $version = '2.8.5';

	/**
	 * Database version.
	 *
	 * @var int
	 * @since Live Chat X (2.4.0)
	 */
	public $db_version = 4;

	/**
	 * Real-time database version (Firebase).
	 *
	 * @var string
	 * @since Live Chat X (2.4.0)
	 */
	public $firebase_version = '4.13.0';

	/**
	 * Screets API version.
	 *
	 * @var string
	 * @since Live Chat X (2.4.0)
	 */
	public $api_version = '1';

	/**
	 * Plugin name.
	 *
	 * @var string
	 * @since Live Chat X (2.4.0)
	 */
	public $name = 'Live Chat';

	/**
	 * Plugin edition.
	 *
	 * @var string
	 * @since Live Chat X (2.4.0)
	 */
	public $edition = 'WordPress';

	/**
	 * Plugin website URL.
	 *
	 * @var string
	 * @since Live Chat X (2.4.0)
	 */
	public $url = 'https://screets.com/chat';

	/**
	 * Options data.
	 *
	 * @var array
	 * @since Live Chat X (2.4.0)
	 */
	public $opts;

	/**
	 * Options framework.
	 *
	 * @var object
	 * @since Live Chat X (2.4.0)
	 */
	public $optsFW;

	/**
	 * Application class.
	 *
	 * @var LiveChatX_App
	 * @since Live Chat X (2.4.0)
	 */
	public $app;

	/**
	 * The single instance of the class.
	 *
	 * @var LiveChatX
	 * @since Live Chat X (2.4.0)
	 */
	protected static $_instance = null;

	/**
	 * Main LiveChatX Instance.
	 *
	 * Ensures only one instance of LiveChatX is loaded or can be loaded.
	 *
	 * @static
	 * @see LiveChatX()
	 * @return LiveChatX - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'No way...', '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, 'No way...', '1.0' );
	}

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		if( !defined( 'LCX_NAME' ) ) {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			// Load application class.
			$this->app = new LiveChatX_App();

			// Initialize options framework.
			$this->optsFW = new LCX_Options();

			// Get options
			$this->opts = get_option( LCX_SLUG . '_settings' );

			// Install the plugin
			register_activation_hook( __FILE__, array( 'LiveChatX_Install', 'install' ) );

			// Loaded action
			do_action( 'lcx_loaded' );

		}
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 10 );
		add_action( 'plugins_loaded', array( 'LiveChatX_AJAX', 'register' ) );
	}

	/**
	 * Define the plugin constants.
	 */
	private function define_constants() {

		// Some useful constants
		define( 'LCX_NAME', 'Screets ' . $this->name );
		define( 'LCX_SNAME', $this->name );
		define( 'LCX_EDITION', $this->edition );
		define( 'LCX_VERSION', $this->version );
		define( 'LCX_API_VERSION', $this->api_version );
		define( 'LCX_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'LCX_CHANGELOG_URL', 'https://screets.com/livechat/changelog.php' );
		define( 'LCX_URL', plugin_dir_url( dirname( __FILE__ ) ) . 'akio-wp-chat' );
		define( 'LCX_SLUG', 'lcx' );
		
		define( 'LCX_DB_VERSION', $this->db_version );
		define( 'LCX_FIREBASE_VERSION', $this->firebase_version );
	}

	/**
	 * Init the plugin when WordPress initializes.
	 *
	 * @access public
	 * @return void
	 */
	function init() {
		// Before init action
		do_action( 'before_lcx_init' );
		
		// Set up localization
		$this->load_plugin_textdomain();

		// Initialization action
		do_action( 'lcx_init' );

	}

	/**
	 * Include required core files.
	 *
	 * @access public
	 * @return void
	 */
	public function includes() {

		// Functions
		require_once( 'core/functions/common.php' );
		require_once( 'core/functions/templates.php' );
		require_once( 'core/functions/users.php' );
		require_once( 'core/functions/forms.php' );
		require_once( 'core/functions/chats.php' );
		require_once( 'core/functions/realtime.php' );
		require_once( 'core/functions/deprecated.php' );

		// Classes
		require_once( 'core/classes/abstract.class.php' );
		require_once( 'core/library/options-fw/options-fw.php' );
		require_once( 'core/classes/options.class.php' );
		require_once( 'core/classes/template.class.php' );
		require_once( 'core/classes/app.class.php' );
		require_once( 'core/classes/ajax.class.php' );
		require_once( 'core/classes/install.class.php' );

		// Back-end includes
		if( $this->is_request( 'admin' ) && !$this->is_request( 'ajax' ) ) {
			require_once( 'core/classes/admin.class.php' );
			require_once( 'core/classes/nav-menus.class.php' );
		}
		// Front-end includes
		if( $this->is_request( 'frontend' ) && !$this->is_request( 'ajax' ) ) {
			require_once( 'core/functions/restapi.php' );
			require_once( 'core/functions/widget.php' );
			require_once( 'core/classes/frontend.class.php' );
		}
	}

	/**
	 * Get options.
	 *
	 * @access public
	 * @return string
	 */
	public function opts() {
		return $this->opts;
	}

	/**
	 * Get Ajax URL.
	 *
	 * @access public
	 * @return string
	 */
	public function ajax_url() {
		$ajax_url = admin_url( 'admin-ajax.php', 'relative' );

		$current_lang = apply_filters( 'wpml_current_language', NULL );
		if( $current_lang )
			$ajax_url = add_query_arg( 'wpml_lang', $current_lang, $ajax_url );

		return $ajax_url;

	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template url.
	 *
	 * @return string
	 */
	public function template_url() {
		return apply_filters( 'lcx_template_url', LCX_URL . '/core/templates/basic' );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'lcx_template_path', LCX_PATH . '/core/templates/basic' );
	}

	/**
	 * Localization.
	 *
	 * @access public
	 * @return void
	 */
	function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), LCX_SLUG );

		load_textdomain( LCX_SLUG, WP_LANG_DIR . '/' . LCX_SLUG . '/' . LCX_SLUG . '-' . $locale . '.mo' );
		load_plugin_textdomain( LCX_SLUG, false, LCX_PATH . '/languages/' );

		// 
		// Register translatable messages.
		// 
		$strs = lcx_get_option_group( 'msgs' );

		// Register strings to WPML 3.2+
		if( has_action( 'wpml_register_single_string' ) && !empty( $strs ) ) {
			foreach( $strs as $k => $v ) {
				do_action( 'wpml_register_single_string', LCX_NAME, $k, $v );
			}
		}

		// Register strings to Polylang
		/*if( function_exists( 'pll_register_string' ) ) {
			foreach( $this->__opts as $id => $opt ) {
				$string = $this->opts->getOption( $id );
				pll_register_string( $opt['name'], $string, LCX_NAME, $opt['multiline'] );
			}
		}*/

	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin.
	 *
	 * @return bool
	 */
	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Get real-time token for logged-in users only.
	 *
	 * @since Live Chat X (2.4.0)
	 */
	public function get_token() {

		// Only for logged in users!
		if( !is_user_logged_in() ) {
			return false;
		}

		return require LCX_PATH . '/core/admin/get-token.php';

	}
}

/**
 * Main instance of LiveChatX.
 *
 * Returns the main instance of LiveChatX to prevent the need to use globals.
 *
 * @since  Live Chat X (2.4.0)
 * @return LiveChatX
 */
function LiveChatX() {
	return LiveChatX::instance();
}

// Init the plugin class
$GLOBALS['LiveChatX'] = LiveChatX();

/**
 * Check new plugin updates from Screets server.
 */
if( is_admin() ) {
	global $pagenow;

	if( !empty( $pagenow ) ) {

		switch( $pagenow ) {
			case 'plugin-install.php':
			case 'update-core.php':
			case 'plugins.php':
			case 'admin-ajax.php':
			case 'update.php':

				require LCX_PATH . '/core/library/update-checker/plugin-update-checker.php';
				$checker = Puc_v4_Factory::buildUpdateChecker(
					'https://support.screets.io/updates/wp/?action=get_metadata&slug=screets-' . LCX_SLUG,
					__FILE__,
					'screets-' . LCX_SLUG
				);
				$checker->addQueryArgFilter( '_fn_lcx_filter_updates' );

			break;

		}

	}

	/**
	 * Filter updates with API key.
	 *
	 * @since Live Chat X (2.4.0)
	 * @return array Query arguments
	 */
	function _fn_lcx_filter_updates( $query_args ) {
		global $wp_version;

		$api_key = lcx_get_option( 'general', 'license_api' );

		$query_args['api'] = ( !empty( $api_key ) ) ? $api_key : null;
		$query_args['domain'] = fn_lcx_get_pure_domain( fn_lcx_get_current_url() );
		$query_args['wp'] = $wp_version;
		$query_args['php'] = phpversion();

		return $query_args;
	}
	
}