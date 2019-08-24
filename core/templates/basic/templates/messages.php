
<?php
/** 
 * Chat message wrapper template.
 */
?>
<div id="__lcx-msg--wrapper" class="__lcx-tpl">
	<?php
	/**
	 * Actions before chat message wrapper.
	 */
	do_action( 'lcx_tpl_before_chat_msg_wrapper' );
	?>
	<div class="lcx-row">
		<div class="lcx-msg-avatar lcx-col lcx-col--auto lcx-col--bottom">
			<img src="" alt="">
		</div>
		<div class="lcx-msg-container lcx-col">
			<!-- Message(s) is here (including repeating ones) -->
		</div>
	</div>
	<div class="lcx-msg-meta"></div>

	<?php
	/**
	 * Actions after chat message wrapper.
	 */
	do_action( 'lcx_tpl_after_chat_msg_wrapper' );
	?>
</div>

<?php
/** 
 * Basic chat message content template.
 */
?>
<div id="__lcx-msg--basic" class="__lcx-tpl">
	<div class="lcx-msg-wrap">
		<div class="lcx-msg-date">{fullDate}</div>
		
		<?php
		/**
		 * Actions before a basic chat message.
		 */
		do_action( 'lcx_tpl_before_basic_chat_msg' );
		?>
		<div class="lcx-msg-content">{msg}</div>
		<?php
		/**
		 * Actions after a basic chat message.
		 */
		do_action( 'lcx_tpl_after_basic_chat_msg' );
		?>
		
		<div class="lcx-msg-footer"></div>
	</div>
</div>

<?php
/** 
 * Ask-customer template.
 */
?>
<div id="__lcx-msg--askCustomer" class="__lcx-tpl">
	<?php
	/**
	 * Actions before ask-customer chat messages.
	 */
	do_action( 'lcx_tpl_before_ask_customer_msg' );
	?>

	<div class="lcx-msg-content">
		{msg}
	</div>
	<div class="lcx-msg-footer lcx-msg-block">
		<div class="lcx-btn-group">
			<a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined">Yes, i am a customer</a>
			<a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined">No, i am not yet</a>
		</div>
	</div>

	<?php
	/**
	 * Actions after ask-customer chat messages.
	 */
	do_action( 'lcx_tpl_after_ask_customer_msg' );
	?>

</div>

<?php
/** 
 * Collector template (full message content).
 */
?>
<div id="__lcx-msg--collector" class="__lcx-tpl">
	<div class="__lcx-msg">
		<?php
		/**
		 * Actions before collector messages.
		 */
		do_action( 'lcx_tpl_before_collector_msg' );
		?>

		<?php
		$collector = @$chats['offline_coll_fields'];
		$collectorReqs = @$chats['offline_coll_req_fields'];
		
		if( !empty( $collector ) ): ?>
		<div class="lcx-form lcx-form--collector">
			<div class="lcx-form-steps"></div>
			<?php foreach( $collector as $i => $name ): 
				$is_req = in_array( $name, $collectorReqs );
				$type = ( $name === 'email' ) ? 'email' : 'text'; ?>
			
			<form class="lcx-form-field lcx-field-<?php echo $name; ?> lcx-step-<?php echo $i+1; ?> lcx-form-field--suffix <?php echo ( $i > 0 ) ? '__lcx-hide':''; ?>">

				<?php
				/**
				 * Actions on beginning of the collector form.
				 */
				do_action( 'lcx_tpl_prepend_collector_form' );
				?>

				<div class="lcx-label"><?php echo $i+1; ?>. <?php echo $msg['forms_' . $name ]; ?></div>
				
				<div class="lcx-row">
					<div class="lcx-field-wrap lcx-col">
						<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="lcx-f-collector-<?php echo $name; ?>" class="lcx-input-text lcx-field lcx-f-collector-<?php echo $name; ?>" placeholder="<?php echo $msg['forms_' . $name ]; ?>" <?php echo $is_req ? 'required' : ''; ?>>
						
						<div class="lcx-valid-field __lcx-hide"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/ok.svg' ); ?></div>
					</div>
					
					<div class="lcx-col lcx-col--auto">
						<a href="" class="lcx-btn lcx-submit-field"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/ok.svg' ); ?></a>
					</div>
				</div>

				<?php
				/**
				 * Actions on ending of the collector form.
				 */
				do_action( 'lcx_tpl_append_collector_form' );
				?>
			</form>
			
			<?php endforeach; ?>
			
		</div>
		<?php endif; ?>

		<?php
		/**
		 * Actions after collector messages.
		 */
		do_action( 'lcx_tpl_after_collector_msg' );
		?>
	</div>
</div>

<?php
/** 
 * Topics template.
 */
?>
<div id="__lcx-msg--topics" class="__lcx-tpl">
	<?php
	/**
	 * Actions before topics chat messages.
	 */
	do_action( 'lcx_tpl_before_ask_topics_msg' );
	?>

	<div class="__lcx-msg-content">
		A few more details will help get you to the right person:
	</div>
	<div class="__lcx-msg-footer">
		<div class="lcx-form">
			<div class="lcx-form-intro">Select a topic:</div>
			<div class="lcx-btn-group">
				<a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined">Sales</a>
				<a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined">Installing</a>
				<a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined">Configuring</a>
				<a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined">Licensing</a>
				<a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined">Other</a>
			</div>
		</div>
	</div>

	<?php
	/**
	 * Actions after topics chat messages.
	 */
	do_action( 'lcx_tpl_after_ask_topics_msg' );
	?>

</div>



<?php
/** 
 * Typing chat message content template.
 */
?>
<div id="__lcx-msg--typing" class="__lcx-tpl">
	<div class="lcx-msg-wrap">
		<span></span>
		<span></span>
		<span></span>
	</div>
</div>