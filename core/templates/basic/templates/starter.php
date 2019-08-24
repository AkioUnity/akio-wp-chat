<div id="lcx-starter" class="lcx-starter">
    <?php
    /**
     * Actions before content of starter.
     */
    do_action( 'lcx_tpl_before_starter' );
    ?>

    <span class="lcx-starter-default">
        <?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/starter-open.svg' ); ?>
    </span>

    <span class="lcx-starter-minimized __lcx-hide">
        <span class="lcx-starter-prefix"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/starter-close.svg' ); ?></span>
    </span>

    <?php
    /**
     * Actions after content of starter.
     */
    do_action( 'lcx_tpl_after_starter' );
    ?>
</div>