
<?php
/**
 * Chat window wrapper.
 */
?>
<div id="__win-tpl-chat" class="d-hide">
	
	<div class="win-container panel">

		<!-- Window header -->
		<div class="win-header panel-header">

			<!-- Locate tabs here -->
			<div class="win-tabs-wrapper">
				<ul class="tab win-tabs">
					
				</ul>
			</div>

			<div class="columns">
				<div class="column col-12">
					<div class="ntf--op-talking toast toast-warning d-hide">
						<?php _e( '{opName} is talking with this user.', 'lcx' ); ?>
					</div>
					<div class="ntf--ended-chat toast toast-primary d-hide">
						<?php _e( 'Chat has ended.', 'lcx' ); ?>
					</div>
					<div class="ntf--archived-chat toast toast-warning d-hide">
						<?php _e( 'Chat has been archived.', 'lcx' ); ?>
					</div>
				</div>
			</div>
		</div>

		<!-- Window body -->
		<div class="lcx-chat-tabs win-body panel-body">
			
		</div>

		<!-- Window footer -->
		<div class="win-footer panel-footer">
			<div class="input-group">
				<div class="nbird-reply" placeholder="<?php _e( 'Write a message', 'lcx' ); ?>"></div>
				<!-- <button class="btn input-group-btn"><i class="icon icon-photo"></i></button>
				<button class="btn btn-primary input-group-btn">Send</button> -->
			</div>
		</div>
	</div>
</div>


		
<?php
/**
 * Chat tab content.
 */
?>
<div id="__win-tpl-chat-content" class="d-hide" data-classNames="win-body panel-body">

	<!-- Window body -->
	<div class="columns">
		<div class="lcx-msgs-container column col-8">
			<ul class="lcx-msgs"></ul>
		</div>
		<div class="column col-4">
			<div class="chat-meta">
				<div class="chat-action-btns">
					<span class="dropdown">
						<a class="btn btn-xs dropdown-toggle" tabindex="0"><i class="icon icon-caret"></i></a>
						<ul class="menu">
							<!-- Archive chat -->
							<li class="menu-item">
								<a href="" class="btn-archive-chat"><?php _e( 'Archive', 'lcx' ); ?></a>
							</li>

							<!-- Delete chat -->
							<li class="menu-item">
								<a href="" class="btn-delete-chat"><?php _e( 'Delete', 'lcx' ); ?></a>
							</li>

						</ul>
					</span>
					
					<!-- Join chat button -->
					<a href="#" class="btn-join-chat btn btn-primary btn-sm"> <?php _e( 'Join chat', 'lcx' ); ?></a>

					<!-- End chat button -->
					<a href="#nbird-modal-endChat" class="btn-end-chat btn btn-sm"> <?php _e( 'End chat', 'lcx' ); ?></a>
					

					
				</div>
	
				<dl class="list">
			
					<?php
					/**
					 * Actions before chat meta.
					 */
					do_action( 'lcx_tpl_before_chat_meta' );
					?>
			
					<dt><?php echo $_caseNo; ?></dt>
					<dd class="c-auto">#{chat-caseNo}</dd>

					<dt><?php _e( 'Feedback', 'lcx' ); ?></dt>
					<dd class="lcx-user-meta-feedback c-auto">
						<span class="lcx-desc"><?php _e( 'No review yet.', 'lcx' ); ?></span>
						<span class="lcx--solved d-hide" style="color: #2de7c7;"><?php _e( 'Solved', 'lcx' ); ?></span>
						<span class="lcx--notsolved  d-hide" style="color: #fb6445;"><?php _e( 'Not solved!', 'lcx' ); ?></span>
					</dd>
			
					<?php
					/**
					 * Actions after chat meta.
					 */
					do_action( 'lcx_tpl_after_chat_meta' );
					?>
			
					<!-- <dd>
						<a href="#" class="btn btn-save-meta btn-primary btn-sm"><?php _e( 'Save', 'lcx' ); ?></a>
					</dd> -->
				</dl>
			</div>
		</div>
	</div>

	
</div>

<?php
/**
 * Chat tab sidebar.
 */
?>
<div id="__win-tpl-chat-sidebar" class="d-hide" data-classNames="lcx-chat-sidebar">
	
	<div class="user-info">
		<div class="h5">
			<div class="btn-lg">
				<div class="nbird-username-wrap editable">
					<div class="nbird-username" contenteditable="true"></div>
					<i class="icon dashicons dashicons-edit"></i>
				</div>
			</div>
		</div>

		<dl class="list">

			<?php
			/**
			 * Actions before user meta.
			 */
			do_action( 'lcx_tpl_before_user_meta' );
			?>

			<dt><?php _e( 'Details', 'lcx' ); ?></dt>

			<?php 
			/**
			 * Actions for before details of users meta.
			 */
			do_action( 'lcx_tpl_before_user_meta_details' );
			?>
			<dd class="lcx-user-meta-emailForNtf c-auto">
				{user-__emailForNtf}
				
				<a href="#input-edit-user-email" class="lcx-action btn btn-xs" data-action="updateUserInput"><i class="icon dashicons dashicons-edit"></i></a>

				<input type="text" class="input-edit-user-email form-input input-sm d-hide" placeholder="<?php echo $_email; ?> (<?php _e( 'Hit enter to update', 'lcx' ); ?>)" data-field-name="emailForNtf">
			</dd>

			<dd class="lcx-user-meta-companyName c-auto"><span class="dashicons dashicons-store"></span> {user-companyName}</dd>
			<dd class="lcx-user-meta-phone c-auto"><span class="dashicons dashicons-phone"></span> {user-phone}</dd>
			<?php 
			/**
			 * Actions for after details of users meta.
			 */
			do_action( 'lcx_tpl_after_user_meta_details' );
			?>
			
			<?php if( !$appOpts['disableLookingAt'] ): ?>
				<dt><?php echo $_lookingAt; ?></dt>
				<dd class="c-auto">{user-currentPageUrlClean}</dd>
			<?php endif; ?>

			<?php
			/**
			 * Actions after user meta.
			 */
			do_action( 'lcx_tpl_after_user_meta' );
			?>
		</dl>
	</div>
</div>