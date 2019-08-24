<?php
/**
 * Online popup template.
 */

/**
 * Response time.
 */
$rt_online = fn_lcx_get_response_time( 'online', $msg['popup_replies_in'] );
$rt_offline = fn_lcx_get_response_time( 'offline', $msg['popup_replies_in'] );

?>

<!-- Online popup header -->
<div id="__lcx-popup-header-online" class="__lcx-tpl">
    <?php
    /**
     * Actions before online popup header.
     */
    do_action( 'lcx_tpl_before_online_popup_header' );
    ?>
    

    <div class="lcx-onNewChat __lcx-center">
        
        <div class="lcx-title">
            <?php echo $site['info_name']; ?>
        </div>

        <!-- Show operators -->
        <div class="lcx-ops"></div>

        <!-- Online message -->
        <div class="lcx-online-msg lcx-desc lcx-onOnline">
            <?php echo $msg['ops_intro']; ?>
        </div>

        <!-- Away message -->
        <div class="lcx-hideOnOffline">
            <div class="lcx-away-msg lcx-desc lcx-onAway">
                <?php echo $msg['ops_intro_away']; ?>
            </div>
        </div>

        
        <div class="lcx-response-time lcx-footer lcx-hideOnOffline">
            <?php if( !empty( $rt_online ) ):?>
                <div class="lcx-rtime lcx-rtime--online lcx-onOnline"><?php echo $rt_online; ?></div>
            <?php endif; ?>
            
            <?php if( !empty( $rt_offline ) ):?>
                <div class="lcx-rtime lcx-rtime--away lcx-onAway"><?php echo $rt_offline; ?></div>
            <?php endif; ?>
        </div>

    </div>

    <div class="lcx-onInitChat">
        <div class="lcx-op-info lcx-row">
            <div class="lcx-op-pic lcx-col lcx-col--auto lcx-col--vCenter">
                <img src="<?php echo $site['info_logo']; ?>" alt="">
            </div>
            <div class="lcx-op-content lcx-col lcx-col--vCenter">
                <div class="lcx-op-name"><?php echo $site['info_name']; ?></div>
                <div class="lcx-op-desc">
                    <?php if( !empty( $rt_online ) ):?>
                        <div class="lcx-rtime--online lcx-onOnline"><?php echo $rt_online; ?></div>
                    <?php endif; ?>
                    
                    <?php if( !empty( $rt_offline ) ):?>
                        <div class="lcx-rtime--away lcx-onAway"><?php echo $rt_offline; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="lcx-col lcx-col--auto">
                <a href="#" class="lcx-btn-end-chat lcx-btn lcx-btn--xs lcx-btn--lined lcx-btn--primary-inv lcx-action" data-action="endChat"><?php echo $msg['popup_end_chat']; ?></a>
            </div>
        </div>
    </div>


    <div class="lcx-onOpenChat">
        
        <div class="lcx-row">
            <div class="lcx-col">
                <!-- Current operator's info -->
                <div class="lcx-op-info lcx-current-op lcx-row">
                    <div class="lcx-op-pic lcx-current-op-pic lcx-col lcx-col--auto lcx-col--vCenter"></div>
                    <div class="lcx-op-content lcx-col lcx-col--vCenter">
                        <div class="lcx-op-name lcx-current-op-name"></div>
                        <div class="lcx-op-desc lcx-current-op-desc"></div>
                    </div>
                </div>
            </div>

            <div class="lcx-col lcx-col--auto">
                <a href="#" class="lcx-btn-end-chat lcx-btn lcx-btn--xs lcx-btn--lined lcx-btn--primary-inv lcx-action" data-action="endChat"><?php echo $msg['popup_end_chat']; ?></a>
            </div>
        </div>
    </div>

    <div class="lcx-onCloseChat __lcx-center">
        <?php echo $msg['popup_chatStatusMsgs_close']; ?>
    </div>

    <?php
    /**
     * Actions after online popup header.
     */
    do_action( 'lcx_tpl_after_online_popup_header' );
    ?>
</div>

<!-- Online popup body -->
<div id="__lcx-popup-body-online" class="__lcx-tpl">

    <?php
    /**
     * Actions before online popup body.
     */
    do_action( 'lcx_tpl_before_online_popup_body' );
    ?>

    <div class="lcx-msgs lcx-hideOnOffline"></div>
    
    <?php
    /**
     * Offline form.
     */
    ?>
    

    <?php
    /**
     * Actions before offline form.
     */
    do_action( 'lcx_tpl_before_offline_form' );
    ?>
    <div class="lcx-offline-form lcx-showOnOffline __lcx-hide">
        <?php
        $offlineFields = @$chats['offline_fields'];
        $offlineReqFields = @$chats['offline_req_fields'];

        if( !empty( $offlineFields ) ): ?>



        <form class="lcx-form lcx-form--offline">

            <div class="lcx-form-desc">
                <?php echo $msg['ops_intro_away']; ?>
                
                <?php if( !empty( $rt_offline ) ):?>
                    <div class="lcx-rtime lcx-rtime--away"><?php echo $rt_offline; ?></div>
                <?php endif; ?>
            </div>
            
            <?php
            /**
             * Actions on beginning of the offline form.
             */
            do_action( 'lcx_tpl_prepend_offline_form' );
            ?>

            <?php 
            foreach( $offlineFields as $i => $name ):
                switch( $name ) {
                    case 'email': $type = 'email'; break;
                    case 'ask_question': $type = 'textarea'; break;
                    default: $type = 'text';
                } 

                $is_req = in_array( $name, $offlineReqFields ); ?>
            
                <div class="lcx-form-field lcx-field-<?php echo $name; ?>">                   
                    <div class="lcx-field-wrap">

                        <?php if( $type !== 'textarea' ): ?>
                            <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="lcx-f-offline-<?php echo $name; ?>" class="lcx-input-text lcx-field lcx-f-offline-<?php echo $name; ?>" placeholder="<?php echo $msg['forms_' . $name ]; ?><?php echo !$is_req ? " ($msg[ntf_optional])" : ''; ?>" <?php echo $is_req ? 'required' : ''; ?>>

                        <?php else: ?>
                            
                            <textarea name="<?php echo $name; ?>" id="lcx-f-offline-<?php echo $name; ?>" class="lcx-textarea lcx-field lcx-f-offline-<?php echo $name; ?>" placeholder="<?php echo $msg['forms_' . $name ]; ?>" <?php echo $is_req ? 'required' : ''; ?>></textarea>

                        <?php endif; ?>
                        
                        <div class="lcx-valid-field"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/ok.svg' ); ?></div>
                    </div>
                </div>
            
            <?php 

            endforeach;

            //
            // Include "I agree to the privacy policy note".
            //
            if( !empty( $site['info_privacy_url'] ) && in_array( 'privacy_cb', $chats['offline_opts'] ) ): ?>
                <div class="lcx-form-field lcx-field-offline-agree">
                    <div class="lcx-field-wrap">
                        <label class="lcx-cb-wrap lcx-gdpr-cb"><input type="checkbox" name="agree" id="lcx-f-offline-agree" class="lcx-cb" value="1" required> <?php echo $msg['gdpr_cb']; ?></label>

                        <?php if( !empty( $site['info_privacy_url'] ) ): ?>
                            <div class="lcx-gdpr-note"><?php echo str_replace( '<a href="#1">', '<a href="' . $site['info_privacy_url'] . '" target="_blank">', $msg['gdpr_note'] ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php endif;



            /**
             * Actions on ending of the offline form.
             */
            do_action( 'lcx_tpl_append_offline_form' );
            ?>
            
        </form>
        <?php endif; ?>
    </div>

    <?php
    /**
     * Actions after offline form.
     */
    do_action( 'lcx_tpl_after_offline_form' );
    ?>

    <?php
    /**
     * Actions after online popup body.
     */
    do_action( 'lcx_tpl_after_online_popup_body' );
    ?>
</div>

<!-- Online popup footer -->
<div id="__lcx-popup-footer-online" class="__lcx-tpl">

    <?php
    /**
     * Actions before online popup footer.
     */
    do_action( 'lcx_tpl_before_online_popup_footer' );
    ?>

    <div class="lcx-onNewChat lcx-onInitChat lcx-onOpenChat">
        <div id="lcx-reply-wrap" class="lcx-reply-wrap lcx-hideOnOffline">
            <div class="lcx-row">
                <div class="lcx-col">
                    <div id="lcx-reply" class="lcx-reply" data-placeholder="<?php echo $msg['forms_reply']; ?>" contenteditable="true"></div>
                </div>
                <div class="lcx-col lcx-col--auto __lcx-showMobile">
                    <a href="" class="lcx-btn lcx-send-btn lcx-action" data-action="sendReply"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/send.svg' ); ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="lcx-onCloseChat lcx-popup-footer-closed">
        <div class="lcx-row">
            <div class="lcx-col">
                
                <div class="lcx-rating">
                    <a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined lcx-btn--success lcx-btn--ico lcx-btn-chat-solved lcx-btn-vote" data-vote="yes"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/like.svg' ); ?> <?php echo $msg['popup_solved']; ?></a>
                    
                    <a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined lcx-btn--danger lcx-btn-chat-not-solved lcx-btn-vote" data-vote="no" title="<?php echo $msg['popup_not_solved']; ?>">&nbsp;<?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/dislike.svg' ); ?></a>
                </div>
            </div>
            <div class="lcx-col lcx-col--auto"></div>
        </div>
    </div>

    <div class="lcx-send lcx-send--offline __lcx-right lcx-showOnOffline __lcx-hide">
        <a href="#" class="lcx-btn lcx-btn--offflineForm">
            <?php echo $msg['forms_submit_btn']; ?>
        </a>
    </div>

    <?php
    /**
     * Actions after online popup footer.
     */
    do_action( 'lcx_tpl_after_online_popup_footer' );
    ?>
</div>