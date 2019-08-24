<?php
/**
* SCREETS © 2018
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

/**
 * Admin navigation menus
 */
add_action( 'admin_init', array( 'LiveChatX_NavMenu', 'init' ) );

class LiveChatX_NavMenu {
    const LANG = 'lcx';

    public static function init() {
	    $class = __CLASS__;
	    new $class;
    }

    public function __construct() {
	    // Abort if not on the nav-menus.php admin UI page - avoid adding elsewhere
	    global $pagenow;
    	
    	if ( 'nav-menus.php' !== $pagenow )
        	return;

        $this->add_some_meta_box();
    }

    /**
     * Adds the meta box container
     */
    public function add_some_meta_box(){
        add_meta_box(
            'lcx-links',
            __( 'Live Chat Links', self::LANG ),
            array( $this, 'render_meta_box_content' ),
            'nav-menus', // important !!!
            'side', // important, only side seems to work!!!
            'low'
        );
    }

    /**
     * Render Meta Box content
     */
    public function render_meta_box_content() {
        $menu_links = array(
			'open-popup' => array(
				'title' => __( 'Chat now!', 'lcx' ),
				'desc' => __( 'Shows up chat popup.', 'lcx' )
			)
		);
		?>
		<div id="posttype-lcx-links" class="posttypediv">
			<div id="tabs-panel-lcx-links" class="tabs-panel tabs-panel-active">
				<ul id="lcx-links-checklist" class="categorychecklist form-no-clear">
					<?php
					$i = -1;
					foreach ( $menu_links as $id => $link ) {
					?>
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( $link['title'] ); ?>
							<br><small class="description"><?php echo $link['desc']; ?></small>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_html( $link['title'] ); ?>" />
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="#<?php echo esc_attr( $id ); ?>" />
						<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" value="lcx-action-menu" />
					</li>
					<?php
						$i --;
					}
					?>
				</ul>

				<p class="button-controls">
					<span class="list-controls">
						<a href="<?php echo admin_url( 'nav-menus.php?page-tab=all&selectall=1#posttype-lcx-links' ); ?>" class="select-all"><?php _e( 'Select All', 'lcx' ); ?></a>
					</span>
					<span class="add-to-menu">
						<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'lcx' ); ?>" name="add-post-type-menu-item" id="submit-posttype-lcx-links">
						<span class="spinner"></span>
					</span>
				</p>
			</div>
		</div>
	<?php
    }
}
