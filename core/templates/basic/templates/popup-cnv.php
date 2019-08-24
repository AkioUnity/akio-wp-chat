<?php
/**
 * Conversations popup template.
 */
?>


<!-- Conversation popup header -->
<div id="__lcx-popup-header-cnv" class="__lcx-tpl">

    <?php
    /**
     * Actions before conversations popup header.
     */
    do_action( 'lcx_tpl_before_cnv_popup_header' );
    ?>

    <div class="lcx-title"><?php echo $msg['others_cnv']; ?></div>
    <div class="lcx-subtitle"><?php echo sprintf( $msg['others_cnvWith'], $site['info_name'] ); ?></div>

    <?php
    /**
     * Actions after conversation popup header.
     */
    do_action( 'lcx_tpl_after_cnv_popup_header' );
    ?>
</div>

<!-- Conversation popup body -->
<div id="__lcx-popup-body-cnv" class="__lcx-tpl">

    <?php
    /**
     * Actions before conversations popup body.
     */
    do_action( 'lcx_tpl_before_cnv_popup_body' );
    ?>
    <div class="lcx-cnvs lcx-onConnect ">
        <div class="lcx--noItem __lcx-hide">
            <?php echo $msg[ 'others_noCnv' ]; ?>
        </div>

    </div>

    <?php
    /**
     * Actions after conversation popup body.
     */
    do_action( 'lcx_tpl_after_cnv_popup_body' );
    ?>
</div>

<!-- Conversation popup footer -->
<div id="__lcx-popup-footer-cnv" class="__lcx-tpl">

    <?php
    /**
     * Actions before conversations popup footer.
     */
    do_action( 'lcx_tpl_before_cnv_popup_footer' );
    ?>

    <a href="#" class="lcx-btn lcx-action" data-action="newChat">
        <?php echo $msg['others_new_cnv']; ?>
    </a>

    <?php
    /**
     * Actions after conversation popup footer.
     */
    do_action( 'lcx_tpl_after_cnv_popup_footer' );
    ?>
</div>


<?php
/** 
 * Conversation list item.
 */
?>
<div id="__lcx-cnv--item" class="__lcx-tpl">

    <?php
    /**
     * Actions before a conversation item.
     */
    do_action( 'lcx_tpl_before_cnv_item' );
    ?>
    <div class="lcx-cnv-avatar lcx-col lcx-col--vCenter lcx-col--auto">{avatar}</div>
    <div class="lcx-cnv-content lcx-col">
        <div class="lcx-cnv-title __lcx-truncate">{lastMsg}</div>
        <div class="lcx-cnv-meta">
            <span class="lcx-cnv-author">{author}</span>
            <span class="lcx-cnv-time" datetime="{date}"></span>
            <span class="lcx-cnv-caseNo">{caseNo}</span>
        </div>
    </div>
    <?php
    /**
     * Actions after a conversation item.
     */
    do_action( 'lcx_tpl_after_cnv_item' );
    ?>
</div>