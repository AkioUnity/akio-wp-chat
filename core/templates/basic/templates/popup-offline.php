<?php
/**
 * Offline popup template.
 */
?>

<!-- Offline popup header -->
<div id="__lcx-popup-header-offline" class="__lcx-tpl">
    <?php
    /**
     * Actions before offline popup header.
     */
    do_action( 'lcx_tpl_before_offline_popup_header' );
    ?>

   

    <?php
    /**
     * Actions after offline popup header.
     */
    do_action( 'lcx_tpl_after_offline_popup_header' );
    ?>
</div>

<!-- Offline popup body -->
<div id="__lcx-popup-body-offline" class="__lcx-tpl">

    <?php
    /**
     * Actions before offline popup body.
     */
    do_action( 'lcx_tpl_before_offline_popup_body' );
    ?>

    <!-- Offline form -->
    <div class="lcx-offline-form __lcx-hide">
        <?php
        $offlineFields = @$chats['offline_fields'];
        $offlineReqFields = @$chats['offline_req_fields'];

        if( !empty( $offlineFields ) ): ?>

        <form class="lcx-form lcx-form--offline">

            <?php 
            foreach( $offlineFields as $i => $name ):
                switch( $name ) {
                    case 'email': $type = 'email'; break;
                    case 'askQuestion': $type = 'textarea'; break;
                    default: $type = 'text';
                } 

                $is_req = in_array( $name, $offlineReqFields );

            ?>
            
            <div class="lcx-form-field lcx-field-<?php echo $name; ?>">
                <div class="lcx-label"><?php echo $msg['forms_' . $name ]; ?></div>
                
                <div class="lcx-field-wrap">

                    <?php if( $type !== 'textarea' ): ?>
                        <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="lcx-f-offline-<?php echo $name; ?>" class="lcx-input-text lcx-field lcx-f-offline-<?php echo $name; ?>" placeholder="<?php echo $msg['forms_' . $name ]; ?><?php echo !$is_req ? " ($msg[ntf_optional])" : ''; ?>" <?php echo $is_req ? 'required' : ''; ?>>

                    <?php else: ?>
                        
                        <textarea name="<?php echo $name; ?>" id="lcx-f-offline-<?php echo $name; ?>" class="lcx-textarea lcx-field lcx-f-offline-<?php echo $name; ?>" placeholder="<?php echo $msg['forms_' . $name ]; ?>" <?php echo $is_req ? 'required' : ''; ?>></textarea>

                    <?php endif; ?>
                    
                    <div class="lcx-valid-field"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/ok.svg' ); ?></div>
                </div>
            </div>
            
            <?php endforeach; ?>
            
        </form>
        <?php endif; ?>
    </div>

    <?php
    /**
     * Actions after offline popup body.
     */
    do_action( 'lcx_tpl_after_offline_popup_body' );
    ?>
</div>

<!-- Offline popup footer -->
<div id="__lcx-popup-footer-offline" class="__lcx-tpl">

    <?php
    /**
     * Actions before offline popup footer.
     */
    do_action( 'lcx_tpl_before_offline_popup_footer' );
    ?>

    <div class="lcx-send--offline __lcx-center __lcx-hide">
        <a href="#" class="lcx-btn lcx-btn--offflineForm lcx-btn--noAnim">
            <?php echo $msg['forms_submit_btn']; ?>
        </a>
    </div>

    <?php
    /**
     * Actions after offline popup footer.
     */
    do_action( 'lcx_tpl_after_offline_popup_footer' );
    ?>
</div>

<!-- Message template -->