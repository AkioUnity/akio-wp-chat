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
 * Actions before console template.
 */
do_action( 'lcx_tpl_before_console' );

include 'console/win.chat.php';
?>

<div class="lcx">

	<?php
	include 'console/notifications.php';
	include 'console/body.php';
	include 'console/popup.endChat.php';
	include 'console/modal.profile.php';

	?>

	<script>
		var lcxOpts = <?php echo json_encode( $appOpts ) ?>;
		var lcxStr = <?php echo json_encode( $appStrings ) ?>;
		var lcx_events = new nBirdEvents();

		var __lcxOpts = {
			db: {},
			ajax: {},
			user: {},
			autoinit: true,
			authMethod: 'custom',
			ntfDuration: 5000, // ms.
			platform: 'console',
			dateFormat: 'd/m/Y H:i',
			hourFormat: 'H:i',
			preventDuplicate: true, // prevent duplicate sessions.
			
			// Company data
			companyName: '',
			companyURL: '',
			companyLogo: '',
			anonymousImage: '',
			systemImage: '',

			// Messages
			welcomeMsg: '',

			// Common URLs
			_pluginurl: '',
			_optsurl: ''
		};

		for( const k in lcxOpts ) {
			__lcxOpts[k] = lcxOpts[k]; 
		}

		var lcx_db = new NBirdDB( __lcxOpts );
		<?php
		/**
		 * Actions before console script.
		 */
		do_action( 'lcx_before_console_script' );
		?>

		var lcx_nightBird = new nightBird( __lcxOpts, lcxStr );

		<?php
		/**
		 * Actions after console script.
		 */
		do_action( 'lcx_after_console_script' );
		?>
	</script>
</div>

<?php
/**
 * Actions after console template.
 */
do_action( 'lcx_tpl_after_console' );

?>
