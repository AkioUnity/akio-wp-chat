<div class="container grid-xl">

    <?php
    /**
     * Chat console layout.
     */
    ?>
    <div id="nbird-console" class="nbird-console nbird-main">
        <header class="navbar">
            <section class="navbar-section">
                
                <!-- Operator info -->
                <div class="nbird-op-info">
                    <img id="currentUser-photoURL" class="currentUser-photoURL" src="<?php echo fn_lcx_get_anonymous_img(); ?>" alt="">
                    <div class="nbird-is-content">
                        <a href="<?php echo admin_url( 'admin.php?page=lcx-settings#tab-site' ); ?>" id="lcx-sitename" class="lcx-sitename" title="Site name" target="_blank"><?php echo $site['info_name']; ?></a><span class="lcx-sep">&rsaquo;</span><span id="currentUser-name" class="currentUser-name">&nbsp;</span>
                    </div>
                </div>
            </section>
            <section class="navbar-center">
                <img src="<?php echo $logoURL; ?>" alt="" class="logo">
            </section>
            <section class="navbar-section">
                <!-- Online/offline buttons -->
                <div class="form-group">
                    <label id="nbird-toggle-online" class="nbird-toggle-online form-switch is-error c-hand d-hide">
                        <input type="checkbox" id="nbird-switcher-online" class="nbird-switcher-online">
                        <i class="form-icon"></i> 
                        
                        <span class="nbird-online-status text-gray-light c-hand" data-online="<?php echo $_online; ?>" data-offline="<?php echo $_offline; ?>"></span>
                    </label>

                </div>
                
                <?php
                /**
                 * Sign-in button.
                 */
                ?>
                <button id="nbird-signin" class="btn btn-sm btn-narrow nbird-signin" disabled data-signin="<?php echo $_signin; ?>" data-signout="<?php echo $_signout; ?>"><i class="icon icon-shutdown"></i></button>
                &nbsp; &nbsp;

                <a href="#nbird-modal-profile" id="nbird-profile" class="nbird-settings btn btn-primary btn-narrow btn-sm d-hide"><i class="icon icon-people"></i></a>
            </section>
        </header>

        <!--
         - Welcome section.
         -->
        <section id="nbird-welcome-section" class="nbird-section nbird-welcome-section main welcome">
            <!-- Welcome loader -->
            <div class="nbird-welcome-loader">
                <div class="wrapper">
                    <div class="content">
                        <div class="loading loading-lg"></div>
                    </div>
                </div>
            </div>

            <!-- Welcome message -->
            <div class="nbird-welcome-conn d-hide">
                <div class="wrapper">                       
                    <div class="content is-vMiddle" style="text-align: center;">
                        <div class="h3" style="margin-bottom: 15px;">
                            Screets <?php echo LCX_SNAME; ?>
                        </div>

                        <div class="lcx-subtitle">
                            Night Bird <?php echo LCX_VERSION; ?>
                        </div>

                        
                    </div>
                </div>
            </div>
        </section>
        
        <!--
         - Installation section.
         -->
        <section id="nbird-install-section" class="nbird-section nbird-install-section main install d-hide">
            <div class="wrapper">
                <div class="content">
                    <div class="lcx-title2">
                        Update Security Rules
                    </div>

                    <div class="columns">
                        <div class="column col-6">
                            <p class="lcx-subtitle">It is the last step to complete installation. You will want to:</p>

                            <ul>
                                <li>Go to your Firebase project</li>
                                <li>Click <strong>Database</strong> > <strong>Rules</strong></li>
                                <li>Change the content with the one on the right side</li>
                                <li>Click "Publish" button</li>
                            </ul>

                            <p>&nbsp;</p>

                            <p><a href="" id="lcx-complete-install" class="btn btn-primary" style="text-transform: uppercase;"><span class="dashicons dashicons-yes"></span> <?php _e( 'I have completed the steps above', 'lcx' ); ?></a></p>
                        </div>
                        <div class="column col-6">
                            
                            <pre class="code" data-lang="JSON"><code><?php echo file_get_contents( LCX_PATH . '/data/rules.json' ); ?></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!--
         - Chat section.
         -->
        <section id="nbird-chat-section" class="nbird-chat-section main columns col-gapless d-hide">
            <div class="side column col-2">
                
                <!-- Side menu tabs -->
                <ul class="side-title side-menu list-tabs">
                    <?php 
                    /**
                     * Actions for before the side tabs.
                     */
                    do_action( 'lcx_tpl_before_side_tabs' );
                    ?>
                    <li class="active">
                        <a href="#chats-list-wrap" class="tooltip tooltip-top" data-tooltip="<?php _e( 'Chats', 'lcx' ); ?>"><?php echo file_get_contents( LCX_PATH . '/assets/icons/admin/tab-chats.svg' ); ?></a>
                    </li>
                    <li>
                        <a href="#archived-chats-list-wrap" class="tooltip tooltip-top" data-tooltip="<?php _e( 'Archive', 'lcx' ); ?>"><?php echo file_get_contents( LCX_PATH . '/assets/icons/admin/tab-archive.svg' ); ?></a>
                    </li>
                    <?php 
                    /**
                     * Actions for after the side tabs.
                     */
                    do_action( 'lcx_tpl_after_side_tabs' );
                    ?>
                </ul>


                <!-- Desktop notifications -->
                <div id="lcx-ntf-alert" class="toast toast-primary d-hide" style="border-radius: 0">
                    <p>
                        <strong><?php _e( 'Get notified of chat notifications.', 'lcx' ); ?></strong>
                    </p>

                    <a href="#" id="lcx-activate-ntfs">
                        <?php _e( 'Turn on', 'lcx' ); ?>
                    </a>
                </div>

                

                <?php 
                /**
                 * Actions for side tab contents.
                 */
                do_action( 'lcx_tpl_side_tab_contents' );
                ?>

                <?php
                /**
                 * Chats list template.
                 */
                ?>
                <div id="chats-list-wrap" class="chats-list-wrap list-tab-content active">
                    <ul id="chats-list" class="chats-list side-list">
                        <?php
                        /**
                         * "No chats found" filter.
                         */
                        echo apply_filters( 'lcx_tpl_no_chats_found', '<li class="_no-item text-gray-light"><small>' . __( 'No chats found.', 'lcx' ) . '</small></li>' );
                        ?>
                    </ul>
                </div>

                <?php
                /**
                 * Archived chats list template.
                 */
                ?>
                <div id="archived-chats-list-wrap" class="archived-chats-list-wrap list-tab-content">
                    <ul id="archived-chats-list" class="archived-chats-list side-list">
                        <?php
                        /**
                         * "No chats found" filter.
                         */
                        echo apply_filters( 'lcx_tpl_no_archvied_chats_found', '<li class="_no-item text-gray-light"><small>' . __( 'No archived chats found.', 'lcx' ) . '</small></li>' );
                        ?>
                    </ul>
                </div>
                
                <?php
                /**
                 * Dynamic templates.
                 */
                ?>
                <div class="d-hide">

                    <li id="chats-list-item-tpl">
                        <?php
                        /**
                         * Actions before chat item meta.
                         */
                        do_action( 'lcx_tpl_before_chat_item' );
                        ?>
                        <a href="#" class="d-block lcx-item-link">
                            <?php
                            /**
                             * Actions before chat item link meta.
                             */
                            do_action( 'lcx_tpl_before_chat_item_link' );
                            ?>
                    
                            <span class="lcx-prefix"></span>
                            <span class="lcx-timeago" datetime=""></span>
                            <span class="lcx-name text-ellipsis"></span>

                            <?php
                            /**
                             * Actions after chat item link meta.
                             */
                            do_action( 'lcx_tpl_after_chat_item_link' );
                            ?>
                        </a>

                        <?php
                        /**
                         * Actions after chat item meta.
                         */
                        do_action( 'lcx_tpl_after_chat_item' );
                        ?>
                    </li>
                    
                    <?php
                    /**
                     * Archived chat list item template filter.
                     */
                    echo apply_filters( 'lcx_tpl_archived_chat_item', '<li id="archived-chats-list-item-tpl"><span class="d-block lcx-item-link"><span class="lcx-timeago" datetime=""></span><span class="lcx-name text-ellipsis"></span><span class="lcx-msg text-ellipsis"><a href="#" class="lcx-undo-link btn btn-primary">' . __( 'Undo', 'lcx' ) . '</a><span class="lcx-caseNo"></span></span></span></li>' );
                    ?>
                </div>


                <!-- Plugin info -->
                <div class="lcx-info">

                    <span class="lcx-logo"><img src="<?php echo LCX_URL; ?>/assets/img/nb-logo-200x.png" alt="<?php echo LCX_NAME; ?>"></span>
                    <span class="lcx-version">
                        <strong><?php echo LCX_SNAME; ?></strong>
                        <?php echo LCX_VERSION; ?>      
                    </span>
                </div>
                
            </div>

            <div id="win-0" class="win-0 column col-7"></div>
            <div id="win-1" class="win-1 column col-3"></div>
        </section>
    </div>
</div>