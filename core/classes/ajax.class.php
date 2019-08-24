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
 * AJAX class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_AJAX {
	/**
	 * Action hook used by the AJAX class.
	 *
	 * @var string
	 */
	const ACTION = 'lcx_action';
 
	/**
	 * Action argument used by the nonce validating the AJAX request.
	 *
	 * @var string
	 */
	const NONCE = 'lcx';
 
	/**
	 * Register the AJAX handler class with all the appropriate WordPress hooks.
	 *
	 * @since Live Chat X (2.4.0)
	 */
	public static function register() {
		$handler = new self();
 
		add_action( 'wp_ajax_' . self::ACTION, array( $handler, 'handle' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( $handler, 'handle' ) );
	}
 
	/**
	 * Handles the AJAX request for my plugin.
	 *
	 * @since Live Chat X (2.4.0)
	 */
	public function handle() {

		// Make sure we are getting a valid AJAX request
		check_ajax_referer( self::NONCE );

		$mode = (string) $_POST['mode'];
 	
 		if( method_exists( $this, $mode ) ) {
 			$this->$mode();
 		} else {
 			wp_send_json_error( array( 'error' => 'Wrong mode: ' . $mode ) );
 		}
	}

	/**
	 * Get the widget data.
	 *
	 * @since Live Chat X (2.6.0)
	 */
	private function getWidget() {

		if( isset( $_GET['wpml_lang'] ) )
			do_action( 'wpml_switch_language',  $_GET[ 'wpml_lang' ] );

		include_once LCX_PATH . '/core/functions/widget.php';

		wp_send_json( fn_lcx_get_widget_content() );
	}

	/**
	 * Send offline form.
	 *
	 * @since Live Chat X (2.6.0)
	 */
	private function sendOffline() {
		try {

			include_once LCX_PATH . '/core/classes/email.class.php';

			$sender = new LiveChatX_Email( 'basic' );

			// Get site info
			$site_name = lcx_get_option( 'site', 'info_name' );
			$site_url = lcx_get_option( 'site', 'info_url' );
			$site_email = lcx_get_option( 'site', 'info_email' );
			$site_reply_to = lcx_get_option( 'site', 'info_reply_to' );

			// Get email data
			$subject = "[$site_name] " . __( 'New offline message', 'lcx' );
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$headers[] = 'From: ' . $site_name .' <' . $site_email . '>';

			if( !empty( $site_reply_to ) )
				$headers[] = 'Reply-To: ' . $site_name .' <' . $site_reply_to . '>';

			if( !empty( $_POST['email'] ) ) {
				$replyto = !empty( $_POST['name'] ) ? $_POST['name'] .' <' . $_POST['email'] . '>' : $_POST['email'];
				$headers[] = 'Reply-To: ' . $replyto;
			}

			$content = '';
			foreach( $_POST as $k => $v ) {
				switch( $k ) {
					case 'mode':
					case 'action':
					case '_ajax_nonce':
						break;
					default:
						$content .= "<strong>$k:</strong> $v <br>";
				}
			}


			$sender->render( 
				__( 'New offline message', 'lcx' ), // email title
				$content // email content
			);

			if( !$sender->send( $subject, $site_email, $headers ) ) {
				throw new Exception( 'SENT_ERR' );
			}

			wp_send_json( $_POST );

		} catch( Exception $e ) {
			wp_send_json( array( 
				'code' => $e->getCode(), 
				'error' => $e->getMessage()
			));
		}
	}

	/**
	 * Save operator settings.
	 *
	 * @since Live Chat X (2.4.0)
	 */
	private function saveSettings() {

		if( !is_user_logged_in() || !current_user_can( 'lcx_chat_with_visitors' ) ) {
			wp_send_json_error( __( 'You don\'t have permission to perform this action', 'lcx' ) );
		}

		$user = wp_get_current_user();

		$email_ntfs = get_option( 'lcx_email_ntfs' );

		// 
		// (Un)Subscribe "new chat requests"
		// 
		if( empty( $email_ntfs['chatReqs'] ) ) {
			$email_ntfs = array(
				'chatReqs' => array()
			);
		}

		if( @$_POST['subscribeChatReqs'] == "true" ) {
			$email_ntfs['chatReqs']['u'.$user->ID] = $user->user_email;
		} else {
			unset( $email_ntfs['chatReqs']['u'.$user->ID] );
		}
		update_option( 
			'lcx_email_ntfs', 
			$email_ntfs, 
			false 
		);

		// Saved!
		wp_send_json_success( $_POST );
	}

	/**
	 * Notify operators about new updates.
	 *
	 * @since Live Chat X (2.4.0)
	 */
	private function notifyOps() {

		try {

			include_once LCX_PATH . '/core/classes/email.class.php';

			$sender = new LiveChatX_Email( 'basic' );
			
			// Get operator emails
			$emails = get_option( 'lcx_email_ntfs' );

			// Get site info
			$site_name = lcx_get_option( 'site', 'info_name' );
			$site_url = lcx_get_option( 'site', 'info_url' );
			$site_email = lcx_get_option( 'site', 'info_email' );

			// Visitor's data
			$caseNo = @$_POST['caseNo'];
			$visitorName = @$_POST['visitorName'];
			$msg = wpautop( @$_POST['msg'] );

			// Set email content
			$subject = sprintf( __( '[%s] New chat started by %s', 'lcx' ), $site_name, $visitorName );
			$content  = '<strong>' . __( 'Case No', 'lcx' ) . ':</strong> ' . $caseNo . '<br>';
			$content .= '<strong>' . __( 'Visitor Name', 'lcx' ) . ':</strong> ' . $visitorName . '<br><br>';
			$content .= '<strong>' . __( 'Message', 'lcx' ) . ':</strong> ' . $msg . '<br><br>';
			$content .= '<strong>' . __( 'Chat Console', 'lcx' ) . ':</strong> <a href="' . admin_url( 'admin.php?page=livechatx' ) . '">' . admin_url( 'admin.php?page=livechatx' ) . '</a><br>';

			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$headers[] = 'From: ' . $site_name .' <' . $site_email . '>';

			// Notify operators
			if( !empty( $emails['chatReqs'] ) ) {
				foreach( $emails['chatReqs'] as $opid => $email) {
					$sender->render( 
						sprintf( __( '%s is started chat.', 'lcx' ), $visitorName ), // email title
						$content // email content
					);

					if( !$sender->send( $subject, $email, $headers ) ) {
						throw new Exception( 'SENT_ERR' );
					}
				}
			}

			wp_send_json_success( true );

		} catch( Exception $e ) {
			wp_send_json( array( 
				'code' => $e->getCode(), 
				'error' => $e->getMessage()
			));
		}		
	}

	/**
	 * Send chat logs to the visitor.
	 */
	private function sendChatLogs() {

		try {

			include_once LCX_PATH . '/core/classes/email.class.php';

			$sender = new LiveChatX_Email( 'basic' );
			
			// Get operator emails
			$email = $_POST['email'];

			// Get site info
			$site_name = lcx_get_option( 'site', 'info_name' );
			$site_url = lcx_get_option( 'site', 'info_url' );
			$site_email = lcx_get_option( 'site', 'info_email' );

			$caseNo = @$_POST['caseNo'];
			$logs = @$_POST['logs'];

			// Set email content
			$subject = str_replace( array( '{siteName}', '{caseNo}' ), array( $site_name, "#{$caseNo}" ), lcx_get_option( 'msgs', 'email_chat_logs_subject' ) );
			$content  = '<strong>' . lcx_get_option( 'msgs', 'others_case_no' ) . ':</strong> #' . $caseNo . '<br>';
			$content .= '<br><br>' . $logs . '<br>';

			// Set headers
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$headers[] = 'From: ' . $site_name .' <' . $site_email . '>';

			$sender->render( 
				'',
				$content
			);

			if( !$sender->send( $subject, $email, $headers ) ) {
				throw new Exception( __( 'Something went wrong!  Please try again.', 'lcx' ) );
			}

			wp_send_json( array(
				'chatid' => $_POST['chatid']
			));

		} catch( Exception $e ) {
			wp_send_json( array( 
				'code' => $e->getCode(), 
				'error' => $e->getMessage()
			));
		}
	}


	/**
	 * Get setup data.
	 */
	private function getSetupData() {

		$defaultData = json_decode( file_get_contents( LCX_PATH . '/data/default-db-data.json') );

		wp_send_json_success( $defaultData );
	}


	/**
	 * Setup real-time database.
	 */
	/*private function setupDB() {

		$db = fn_lcx_get_realtime_config();

		$defaultData = json_decode( file_get_contents( LCX_PATH . '/data/default-db-data.json') );
        $rules = json_decode( file_get_contents( LCX_PATH . '/data/rules.json') );

		// $updateDB = fn_lcx_curl_post( 
		// 	$db['databaseURL'] . '/_livechat.json?access_token=' . $_POST['accessToken'], 
		// 	$defaultData['_livechat'],
		// 	array( 'CURLOPT_CUSTOMREQUEST' => "PUT" )
		// );

		include_once LCX_PATH . '/core/library/firebase/firebase-client.php';

        $firebase = new LCX_FirebaseLib( $db['databaseURL'] );
        $firebase->setToken( $_POST['accessToken']  );

        $caseNo = $firebase->get( '/caseNo' );
        $updateDB = $firebase->set( '/_livechat', $defaultData->_livechat );

        // Don't update case number if already exists
        if( $caseNo == 'null' )
        	$updateDB = $firebase->set( '/caseNo', 100222000 );

        $updateDB = $firebase->set( '/db_version', LCX_DB_VERSION );
        $updateRules = $firebase->set( '/.settings/rules', $rules );

		wp_send_json( array( $caseNo, $updateRules ) );


		// include LCX_PATH . '/core/classes/firebase.class.php';

		// $db = new LiveChatX_Firebase();

		// try {
		// 	$data = $db->setup( $_POST['auth'] );
		// 	wp_send_json_success( $data );

		// } catch( Exception $e ) {
		// 	wp_send_json( array( 
		// 		'code' => $e->getCode(), 
		// 		'error' => $e->getMessage()
		// 	));
		// }
	}*/



	/** ------- **/

	/**
	 * Get token.
	 */
	/*private function getToken() {

		// Get token
		$token = LiveChatX()->get_token();
		
		if( !empty( $token ) ) {
			wp_send_json_success( array( 'token' => $token ) );
		} else {
			wp_send_json( array( 
				'code' => 100, 
				'error' => __( 'You don\'t have permission to perform this action', 'lcx' )
			));
		}

	}*/

	/**
	 * Check if any operator is currently accepting chats.
	 * If yes, we will create a new chat for current user.
	 */
	/*private function isOpAvailable() {

		include LCX_PATH . '/core/classes/firebase.class.php';

		$db = new LiveChatX_Firebase();

		try {

			// Check if any operator accepts chat
			$is_available = $db->isAvailable();

			// Create new chat
			if( $is_available ) {
				wp_send_json_success( array( 
					'online' => false 
				));
			}
			
			wp_send_json_success( array( 'online' => false ) );

		} catch( Exception $e ) {
			wp_send_json( array( 
				'code' => $e->getCode(), 
				'error' => $e->getMessage()
			));
		}
	}*/

	/**
	 * Get a valid auto-message.
	 */
	/*private function getAutoMsg() {
		
		wp_send_json_success( fn_lcx_get_auto_msg() );

	}*/

	/**
	 * Send offline form.
	 */
	/*private function sendOffline() {

		include_once LCX_PATH . '/core/classes/form.class.php';

		$_POST = stripslashes_deep( $_POST );

		// Create custom form
		$form = new LiveChatX_Form( $_POST['form-id'], $_POST );

		// Setup form fields
		$form->setup_fields();

		// Send notification email
		if( $form->send_email( true ) ) {
			wp_send_json_success();

		// Something went wrong!
		} else {
			wp_send_json_error();
		}
	}*/

	/**
	 * Save operator settings.
	 */
	/*private function secure_saveSettings() {

		if( !current_user_can( 'lcx_chat_with_visitors' ) ) {
			wp_send_json_error( __( 'You don\'t have permission to perform this action', 'lcx' ) );
		}

		// Update user meta
		update_user_meta( $_POST['uid'], 'lcx-op-name', $_POST['opName'] );
		update_user_meta( $_POST['uid'], 'lcx-op-photoURL', $_POST['opPhotoURL'] );

		wp_send_json_success();

	}*/
}
