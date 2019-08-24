<?php
/**
 * End chat popup.
 */
?>
<div id="nbird-modal-endChat" class="modal modal-sm nbird-modal-endChat">
    <a href="#close" class="modal-overlay" aria-label="Close"></a>
    <div class="modal-container">
        <div class="modal-body">
            <form id="form-joinChat" action="" class="form-joinChat">
                <div class="form-group">
                    <label class="form-label" for="f-closing-msg"><strong><?php _e( 'Closing message', 'lcx' ); ?></strong>:</label>
                    <textarea class="form-input" id="f-closing-msg" class="f-closing-msg" rows="5"><?php echo $closingMsg; ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-switch">
                        <input type="checkbox" checked="true" class="cb-sendChatLogs" id="cb-sendChatLogs">
                        <i class="form-icon"></i> <?php _e( 'Send chat transcript to visitor\'s email', 'lcx' ); ?>
                    </label>
                </div>
                <br>
                <div class="form-group">
                    <span id="loader-endChat" class="loading d-hide"></span>
                    <a href="#" id="btn-endChat" class="btn-endChat btn btn-primary btn-sm"><?php _e( 'End chat', 'lcx' ); ?></a>
                    <a href="#" id="btn-endChatWoMsg" class="btn-endChatWoMsg btn btn-sm"><?php _e( 'End chat without message', 'lcx' ); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>