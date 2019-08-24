<?php
/**
 * Profile modal.
 */
?>
<div id="nbird-modal-profile" class="modal nbird-modal-profile">
    <a href="#modals" class="modal-overlay"></a>
    <div class="modal-container">
        <div class="modal-header">
            <a href="#modals" class="btn btn-clear float-right"></a>
            <div class="modal-title h5"><?php _e( 'Profile', 'lcx' ); ?></div>
        </div>

        <div class="modal-body">
            <div class="content">
                <form id="form-settings" action="" class="form-settings form-horizontal">
                    <div class="form-group">
                        <div class="col-3">
                            <label class="form-label label-sm" for="field-user-name"><?php echo $_name; ?></label>
                        </div>
                        <div class="col-9">
                            <input class="form-input input-sm" type="text" id="field-user-name" name="name" placeholder="<?php echo $_name; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-3">
                            <label class="form-label label-sm" for="field-user-email"><?php echo $_email; ?></label>
                        </div>
                        <div class="col-9">
                            <input class="form-input input-sm" type="email" name="email" id="field-user-email" placeholder="<?php echo $_email; ?>" value="<?php echo $user->user_email; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-3">
                            <label class="form-label label-sm" for="field-user-photo-url"><?php echo $_profileImg; ?></label>
                        </div>
                        <div class="col-9">
                            <input class="form-input input-sm" type="url" name="photoURL" id="field-user-photo-url" placeholder="i.e. https://yourdomain.com/avatar.png">
                        </div>
                    </div>

                    <div class="gap gap-lg"></div>

                    <h3 class="h6">Email notifications</h3>

                    <div class="form-group">
                      <label class="form-switch">
                        <input type="checkbox" name="subscribeChatReqs" <?php echo ( !empty( $emailNtfs['chatReqs']['u'.$user->ID] ) ) ? 'checked=""' : ''; ?>>
                        <i class="form-icon"></i> Subscribe new chat requests <br><small class="text-gray"><?php _e( 'Only works if any operator doesn\'t accept new chats automatically at that moment.', 'lcx' ); ?></small>
                      </label>
                    </div>

                    <h3 class="h6">Advanced settings</h3>

                    <p>
                        <a href="#" class="btn btn-sm lcx-action" data-action="resetOps"><span class="dashicons dashicons-trash"></span> Reset operators</a><br>
                        <small class="description">Other operators need to re-signin again to show up in chat box. It is good to remove ex-operators.</small>
                    </p>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#modals" class="btn btn-link"><?php _e( 'Close', 'lcx' ); ?></a> &nbsp;
            <button class="btn btn-primary btn-save-profile" id="btn-save-profile"><?php _e( 'Save', 'lcx' ); ?></button>
        </div>
    </div>
</div>