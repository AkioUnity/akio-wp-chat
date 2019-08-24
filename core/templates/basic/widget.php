<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <base target="_parent">
    <style>
    <?php
    // CSS files
    if( !empty( $css ) ) {
        foreach( $css as $path ) {
            echo file_get_contents( $path );
        }
    }
    ?>
    </style>

    <?php 
        if( !empty( $design['ui_fontEmbed'] ) )
            echo $design['ui_fontEmbed'];
    ?>

    <?php 
    /**
     * Actions in header.
     */
    do_action( 'lcx_tpl_header' ); ?>
</head>
<body id="lcx" class="lcx">
    
    <?php
    /**
     * Actions before widget body content.
     */
    do_action( 'lcx_tpl_before_widget_body' );

    // Include starter
    include apply_filters( 'lcx_tpl_starter', 'templates/starter.php' );

    // Include templates
    include 'templates/popup-online.php';
    include 'templates/popup-cnv.php';
    include 'templates/messages.php';
    ?>
    
    <?php
    /**
     * Popup wrapper.
     */
    ?>
    <div id="lcx-popup" class="lcx-popup __lcx-hide" style="/*background-image: url(<?php //echo LCX_URL; ?>/assets/img/chatbox/bg1-light.png); background-size: 50%;*/">
        

        <!-- Loader -->
        <div id="lcx-loader" class="lcx-loader lcx-popup-body lcx-col--vCenter lcx-onDisconnect">
            <?php echo $msg['ntf_conn']; ?>
        </div>
    
        <!-- Common popup header -->
        <div id="lcx-popup-header" class="lcx-popup-header lcx-onConnect">
        
            <?php
            /**
             * Actions before common popup header.
             */
            do_action( 'lcx_tpl_before_popup_header' );
            ?>
            <div class="lcx-row lcx-popup-header-wrap">
        
                <?php
                /**
                 * Actions after begin of common popup header.
                 */
                do_action( 'lcx_tpl_after_begin_popup_header' );
                ?>
        
                <div id="lcx-pcontent-header" class="lcx-content lcx-col lcx-col--vCenter">
                    
                </div>
                
                <div class="lcx-rside lcx-col lcx-col--auto">
                    <a href="" id="lcx-menu" class="lcx-menu">
                        <?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/back.svg' ); ?> <span class="lcx-count __lcx-hide"></span>
                    </a>

                    <a href="#" class="lcx-action lcx-btn-close" data-action="closePopup">
                        <?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/close.svg' ); ?>
                    </a>
                </div>
        
                <?php
                /**
                 * Actions before end of common popup header.
                 */
                do_action( 'lcx_tpl_before_end_popup_header' );
                ?>
            </div>
        
            <?php
            /**
             * Actions after common popup header.
             */
            do_action( 'lcx_tpl_after_popup_header' );
            ?>
        
            <!-- Notifications -->
            <div id="lcx-ntfs" class="lcx-ntfs"></div>
        </div>
        
            
        <!-- Common popup body -->
        <div id="lcx-popup-body" class="lcx-popup-body lcx-onConnect">
            
        
            <?php
            /**
             * Actions before common popup body.
             */
            do_action( 'lcx_tpl_before_popup_body' );
            ?>
        
            <div id="lcx-pcontent-body" class="lcx-content"></div>
        
            <?php
            /**
             * Actions after common popup body.
             */
            do_action( 'lcx_tpl_after_popup_body' );
            ?>
        </div>
        
        <!-- Common popup footer -->
        <div id="lcx-popup-footer" class="lcx-popup-footer lcx-onConnect">
            <?php
            /**
             * Actions before common popup footer.
             */
            do_action( 'lcx_tpl_before_popup_footer' );
            ?>
        
            <div id="lcx-pcontent-footer" class="lcx-content">
                footer
            </div>
        
            <?php
            /**
             * Actions after common popup footer.
             */
            do_action( 'lcx_tpl_after_popup_footer' );
            ?>
        </div>
    </div>

    <?php
    /**
     * Actions after widget body content.
     */
    do_action( 'lcx_tpl_after_widget_body' ); ?>
</body>
</html>