<?php
/**
 * SCREETS © 2017
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 *
 * @package LiveChatX
 * @author Screets
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } 

// $extensions = apply_filters( 'lcx_extensions_list', array() );
$plugins = get_plugins();
$extensions = array();

if( !empty( $plugins ) ) {
    foreach( $plugins as $name => $plugin ) {
        if( substr( $name, 0, 12 ) === "screets-lcx-" ) {
            $extensions[ $name ] = $plugin;
        }
    }
}
?>

<div class="lcx-extensions">
    <h1 class="wp-heading-inline"><?php _e( 'Extensions', 'lcx' ); ?></h1>

    <hr>

    <div class="lcx-row lcx-row--1of3">
    <?php if( !empty( $extensions ) ): foreach( $extensions as $slug => $extension ): 
        $is_active = is_plugin_active( $slug );
        $plugin_name = trim( str_replace( 'Screets Live Chat X -', '', $extension['Name'] ) );
    ?>
        <div class="lcx-col lcx-ext <?php echo ( $is_active ) ? 'lcx-active' : ''; ?>">
            <span class="lcx-title"><?php echo $plugin_name; ?></span>
            <span class="lcx-desc"><?php echo $extension['Description']; ?></span>
            <span class="lcx-author">by <?php echo $extension['Author']; ?></span>
            <span class="lcx-version"><?php echo $extension['Version']; ?></span>
            <span class="lcx-actions">
                <?php if( $is_active ): ?>
                    <a href="<?php echo admin_url( 'admin.php?page=admin.php?page=lcx_extensions&name=' . $slug . '&action=deactivate' ); ?>" class="button"><?php _e( 'Deactivate', 'lcx' ); ?></a>
                <?php else: ?>
                    <a href="<?php echo admin_url( 'admin.php?page=admin.php?page=lcx_extensions&name=' . $slug . '&action=activate' ); ?>" class="button button-primary"><?php _e( 'Active', 'lcx' ); ?></a>

                <?php endif; ?>
            </span>
        </div>
    <?php endforeach; else: ?>
        <li><?php _e( 'No extensions found.', 'lcx' );?></li>
    <?php endif; ?>
    </ul>
</div>