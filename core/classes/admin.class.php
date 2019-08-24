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
* Admin class.
*
* @since Live Chat X (2.4.0)
*
*/
class LiveChatX_Admin extends LiveChatX_Abstract {

	/**
	* Constructor.
	*/
	public function __construct() {
		$this->addAction( 'current_screen', 'conditional_includes' );
		$this->addAction( 'admin_menu', 'admin_menu', 20, 0 );
		$this->addAction( 'admin_menu', 'admin_menu_last', 200, 0 );
		$this->addAction( 'admin_notices', 'admin_notices' );

		// Disable WP emoji
		$this->addAction( 'init', 'disable_emoji' );
	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		if ( ! $screen = get_current_screen() ) { return; }

		switch ( $screen->id ) {
			case 'toplevel_page_livechatx':
			case 'live-chat_page_admin?page=livechatx':
				include LCX_PATH . '/core/admin/console.php';
				break;

			// The plugin options
			case 'live-chat_page_lcx-settings':
				include LCX_PATH . '/core/admin/admin-opts.php';
				break;

			// Plugins page
			case 'plugins':
				include LCX_PATH . '/core/admin/plugins.php';
				break;

			// Auto-messages
			/*case 'lcx_auto_msgs':
			case 'edit-lcx_auto_msgs':
				include LCX_PATH . '/core/admin/auto-msgs.php';
				break;*/

			// Extensions page
			case 'live-chat_page_admin?page=lcx_extensions':
				include LCX_PATH . '/core/admin/extensions.php';
				break;

			// User profile
			/*case 'user':
			case 'user-edit':
			case 'profile':
				include LCX_PATH . '/core/admin/user-profile.php';
				break;*/
		}
	}

	/**
	 * Show admin notifications.
	 */
	function admin_notices() {
		if ( ! $screen = get_current_screen() ) { return; }

		switch ( $screen->id ) {
			case '':
				break;
		}
	}

	/**
	 * Setup admin menu.
	 */
	function admin_menu() {

		// Live Chat
		add_menu_page(
			LCX_SNAME,
			LCX_SNAME,
			'lcx_chat_with_visitors',
			'livechatx', // menu slug
			array( $this, 'render_console' ), // callback
			'data:image/svg+xml;base64,PHN2ZyAgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgd2lkdGg9Ijg2cHgiIGhlaWdodD0iNjNweCI+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiAgZmlsbD0icmdiKDI1NSwgMjU1LCAyNTUpIiBkPSJNNjguNzAwLDExLjY3MyBDNTcuOTE0LDExLjY3MyA1NS45NDUsMTguOTQ5IDU1Ljk0NSwzMS4xMDUgQzU1Ljk0NSw0My4yNjAgNTcuOTE0LDUwLjUzNiA2OC43MDAsNTAuNTM2IEM3My4xNTEsNTAuNTM2IDc3LjE3NCw0OS43NjYgODEuNTQwLDQ3LjU0MCBMODUuOTkxLDU3LjcyNyBDNzkuOTk5LDYxLjMyMiA3NC41MjEsNjIuMDkzIDY4LjUyOSw2Mi4wOTMgQzUwLjAzOSw2Mi4wOTMgNDIuNzYzLDUyLjI0OCA0Mi43NjMsMzEuMTA1IEM0Mi43NjMsOS45NjEgNTAuMDM5LDAuMTE3IDY4LjUyOSwwLjExNyBDNzQuNTIxLDAuMTE3IDc5Ljk5OSwwLjg4NyA4NS45OTEsNC40ODMgTDgxLjU0MCwxNC42NjkgQzc3LjE3NCwxMi40NDQgNzMuMTUxLDExLjY3MyA2OC43MDAsMTEuNjczIFpNMC42NDUsMS4xNDQgTDEzLjc0MiwxLjE0NCBMMTMuNzQyLDQ5LjUwOSBMNDAuNTM1LDQ5LjUwOSBMNDAuNTM1LDYxLjA2NSBMMC42NDUsNjEuMDY1IEwwLjY0NSwxLjE0NCBaIi8+PC9zdmc+',
			'26.2985'
		);
		remove_submenu_page( 'livechatx', 'admin.php?page=livechatx' );
		remove_submenu_page( 'livechatx', 'edit.php?post_type=lcx_auto_msgs' );

		// Chat Console
		add_submenu_page(
			'livechatx',
			__( 'Chat Console', 'lcx' ),
			__( 'Chat Console', 'lcx' ),
			'lcx_chat_with_visitors',
			'livechatx',
			array( $this, 'render_console' ) // callback
		);

	}

	/**
	 * Setup late items of admin menu.
	 */
	function admin_menu_last() {

		// Auto-messages
		/*add_submenu_page(
			'livechatx',
			__( 'Auto-messages', 'lcx' ),
			__( 'Auto-messages', 'lcx' ),
			'lcx_admin',
			'edit.php?post_type=lcx_auto_msgs'
		);*/

		// Custom forms
		// remove_submenu_page( 'livechatx', 'edit.php?post_type=lcx_custom_form' );
		/*add_submenu_page(
			'livechatx',
			__( 'Custom Forms', 'lcx' ),
			__( 'Custom Forms', 'lcx' ),
			'lcx_admin',
			'edit.php?post_type=lcx_custom_form'
		);*/

		// Extensions
		/*add_submenu_page(
			'livechatx',
			__( 'Extensions', 'lcx' ),
			__( 'Extensions', 'lcx' ),
			'lcx_admin',
			'admin.php?page=lcx_extensions',
			array( $this, 'render_extensions' ) // callback
		);*/

		// Support
		/*add_submenu_page(
			'livechatx',
			__( 'Support', 'lcx' ),
			__( 'Support', 'lcx' ),
			'lcx_chat_with_visitors',
			'admin.php?page=lcx_support',
			array( $this, 'render_support' ) // callback
		);*/

		// Options
		/*add_submenu_page(
			'livechatx',
			__( 'Options', 'lcx' ),
			__( 'Options', 'lcx' ),
			'lcx_admin',
			'admin.php?page=lcx-settings',
			array( 'LCX_Options', 'renderme' )
		);*/
	}

	/**
	 * Render extensions page.
	 */
	function render_extensions() {
		include LCX_PATH . '/core/templates/admin/extensions.php';

		$plugins = get_plugins();
		$installedExts = array();

		if( !empty( $plugins ) ) {
		    foreach( $plugins as $name => $plugin ) {
		        if( substr( $name, 0, 12 ) === "screets-lcx-" ) {
		            $installedExts[ $name ] = $plugin['Version'];
		        }
		    }
		}
		?>

		<!-- <iframe id="lcx-iframe" src="//support.lamoga.de/extensions/?p=<?php echo LCX_SLUG; ?>&amp;v=<?php echo LCX_VERSION; ?>&amp;installed=<?php echo http_build_query( $installedExts ); ?>&amp;api=<?php echo lcx_get_option( 'general', 'license_api' ); ?>" frameborder="0"></iframe> -->
	<?php }

	/**
	 * Render support page.
	 */
	function render_support() { 
		global $wp_version;
		?>
		<iframe id="lcx-iframe" src="//support.lamoga.de/inapp/?p=<?php echo LCX_SLUG; ?>&amp;v=<?php echo LCX_VERSION; ?>&amp;api=<?php echo lcx_get_option( 'general', 'license_api' ); ?>&amp;php=<?php echo phpversion(); ?>&amp;wp=<?php echo $wp_version; ?>" frameborder="0"></iframe>

		<style>
			#wpfooter { display: none; }
			#wpbody-content {
				padding-bottom: 0;
			}

			#lcx-iframe {
				width: 100%; 
				height: calc(100vh - 36px);
				box-sizing: border-box;
			}
			

			#wpcontent, #wpfooter {
				margin-left: 140px;
			}
			@media only screen and (max-width: 960px) {
				.auto-fold #wpcontent, .auto-fold #wpfooter {
					margin-left: 16px;
				}
			}
		</style>
	<?php }

	/**
	 * Render the main plugin page.
	 */
	function render_console() {
		$user = wp_get_current_user();
		$msgs = lcx_get_option_group( 'msgs', true );
		$basics = lcx_get_option( 'general', 'display_basics' );
		$site = lcx_get_option_group( 'site' );
		$emailNtfs = get_option( 'lcx_email_ntfs' );

		$tpl = new LiveChatX_Template();
		$tpl->logoURL = LCX_URL . '/assets/img/logo-120x.png';
		$tpl->user = $user;
		$tpl->site = $site;
		$tpl->emailNtfs = ( empty( $emailNtfs ) ) ? array() : $emailNtfs;
		$tpl->closingMsg = lcx_get_option( 'msgs', 'popup_closing_msg' );

		$tpl->_signin = __( 'Sign-in', 'lcx' );
		$tpl->_signout = __( 'Sign-out', 'lcx' );
		$tpl->_online = __( 'Online', 'lcx' );
		$tpl->_offline = __( 'Offline', 'lcx' );
		$tpl->_close = __( 'Close', 'lcx' );
		$tpl->_settings = __( 'Settings', 'lcx' );
		$tpl->_name = __( 'Name', 'lcx' );
		$tpl->_email = __( 'Email', 'lcx' );
		$tpl->_phone = __( 'Phone', 'lcx' );
		$tpl->_country = __( 'Country', 'lcx' );
		$tpl->_notes = __( 'Notes', 'lcx' );
		$tpl->_caseNo = __( 'Case no', 'lcx' );
		$tpl->_lookingAt = __( 'Looking at', 'lcx' );
		$tpl->_profileImg = __( 'Profile Image', 'lcx' );

		// Prepare basics
		$basics = empty( $basics ) ? array() : $basics;

		$tpl->appOpts = apply_filters( 'lcx_console_app_opts',
			array(
				'db' => fn_lcx_get_realtime_config(),
				'ajax' => array(
					'nonce' => wp_create_nonce( LiveChatX_AJAX::NONCE ),
					'url' => LiveChatX()->ajax_url()
				),
				'user' => fn_lcx_get_user_data(),
				'autoinit' => true,
				'anonymousImage' => fn_lcx_get_anonymous_img(),
				'systemImage' => LCX_URL . '/assets/img/logo-120x.png',
				'companyName' => lcx_get_option( 'site', 'info_name' ),
				'companyURL' => lcx_get_option( 'site', 'info_url' ),
				'companyLogo' => lcx_get_option( 'site', 'info_logo' ),
				'welcomeMsg' => lcx_get_option( 'msgs', 'popup_welcome_msg' ),
				'dbVersion' => LCX_DB_VERSION,
				'ntfDuration' => 7000,
				'disableLookingAt' => in_array( 'disableLookingAt', $basics ),

				'_pluginurl' => LCX_URL,
				'_siteurl' => get_site_url(),
				'_optsurl' => admin_url( 'admin.php?page=lcx-settings' )
			)
		);
		$tpl->appStrings = apply_filters( 'lcx_console_app_strings', array(
			'conn' => $msgs['ntf_conn'],
			'connected' => $msgs['ntf_connected'],
			'reconn' => $msgs['ntf_reconn'],
			'noConn' => $msgs['ntf_no_conn'],
			'reqFields' => $msgs['ntf_reqFields'],
			'invEmail' => $msgs['ntf_invEmail'],
			'you' => $msgs['ntf_you'],
		));

		echo $tpl->render( LCX_PATH . '/core/templates/admin/console.php' );

	}

	/**
	 * Render the main plugin page.
	 */
	function disable_emoji() {

		// Disable emoji support on "Chat Console"
		if( !empty( $_GET['page'] ) ) {	
			if( $_GET['page'] == 'admin.php?page=livechatx' ) {
				remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
				remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
				remove_action( 'wp_print_styles', 'print_emoji_styles' );
				remove_action( 'admin_print_styles', 'print_emoji_styles' );	
				remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
				remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
				remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			}
		}

	}

}

return new LiveChatX_Admin();