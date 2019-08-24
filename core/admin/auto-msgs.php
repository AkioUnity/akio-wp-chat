<?php
/**
 * Heino © 2019
 *
 * Heino, All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 *
 * @package LiveChatX
 * @author Akio
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Auto-messages page class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_AutoMsgs extends LiveChatX_Abstract {

    public $post_type = 'lcx_auto_msgs';

    /**
     * Constructor.
     */
    function __construct() {

        wp_register_script(
            'lcx-auto-msgs',
            LCX_URL . '/assets/js/admin/auto-msgs.js',
            '',
            LCX_VERSION
        );
        wp_enqueue_script( 'lcx-auto-msgs' );

        wp_register_style(
            'lcx-auto-msgs',
            LCX_URL . '/assets/css/admin/auto-msgs.css',
            null,
            LCX_VERSION
        );
        wp_enqueue_style( 'lcx-auto-msgs' );

        // Conditions metabox
        add_action( "add_meta_boxes_{$this->post_type}", array( $this, 'metaboxes' ) );
        add_action( "save_post_{$this->post_type}", array( $this, 'mb_save' ) );


    }
    function metaboxes() {
        add_meta_box( 
            $this->post_type . '_conditions_meta_box', 
            __( 'Conditions', 'sbook' ), 
            array( $this, 'mb_conditions' ), 
            'lcx_auto_msgs', 
            'normal',
            'high'
        );
        add_meta_box( 
            $this->post_type . '_msg_meta_box', 
            __( 'Message', 'sbook' ), 
            array( $this, 'mb_message' ), 
            'lcx_auto_msgs', 
            'normal',
            'high'
        );
    }

    function mb_conditions() {
        // Make sure the form request comes from WordPress
        wp_nonce_field( basename( __FILE__ ), $this->post_type . '_conditions_metabox_nonce' );
         ?>
    
	    <fieldset id="lcx-conditions">
		
	        <div class="lcx-condition-group lcx-condition-group-0" id="lcx-condition-group-0">
	        	

	            <table class="form-table">
	                <tr>
	                    <td>
	                        <select name="type[]">
				            	<option value="currentPageURL"><?php _e( 'Current page URL', 'lcx' ); ?></option>
				            	<option value="referralURL"><?php _e( 'Referral URL', 'lcx' ); ?></option>
				            	<option value="pageviews"><?php _e( 'Single page views', 'lcx' ); ?></option>
				            	<option value="visits"><?php _e( 'Total visits', 'lcx' ); ?></option>
				            </select>
	            
	                        <p class="description"></p>
	                    </td>
	                </tr>

	                <tr>
	                	<td>
	                		<label class="radio-label"><input type="radio" name="comparison[]" value="eq"> <?php _e( 'is', 'lcx' ); ?></label>

	                		<label class="radio-label"><input type="radio" name="comparison[]" value="nq"> <?php _e( 'is not', 'lcx' ); ?></label>

	                		<label class="radio-label"><input type="radio" name="comparison[]" value="starts_with"> <?php _e( 'starts with', 'lcx' ); ?></label>

	                		<label class="radio-label"><input type="radio" name="comparison[]" value="ends_with"> <?php _e( 'ends with', 'lcx' ); ?></label>

	                		<label class="radio-label"><input type="radio" name="comparison[]" value="contains"> <?php _e( 'contains', 'lcx' ); ?></label>

	                		<label class="radio-label"><input type="radio" name="comparison[]" value="not_contains"> <?php _e( 'does not contains', 'lcx' ); ?></label>

	                		<label class="radio-label"><input type="radio" name="comparison[]" value="unknown"> <?php _e( 'is unknown', 'lcx' ); ?></label>

	                		<label class="radio-label"><input type="radio" name="comparison[]" value="any"> has any value</label>

	            			<label class="radio-label"><input type="radio" name="comparison[]" value="gt"> <?php _e( 'is more than', 'lcx' ); ?></label>

	           				<label class="radio-label"><input type="radio" name="comparison[]" value="ls"> <?php _e( 'is less than', 'lcx' ); ?></label>
	                	</td>
	                </tr>
	                <tr>
	                    <td>
	                        <input type="text" class="input-text" name="url[]" placeholder="<?php _e( 'Enter URL', 'lcx' ); ?>">
	            
	                        <p class="description"></p>
	                    </td>
	                </tr>
	                <tr>
	                    <td>
	                        <input type="text" class="input-text" name="url[]"> <?php _e( 'seconds', 'lcx' ); ?>
	            
	                        <p class="description"></p>
	                    </td>
	                </tr>
	            </table>
	        </div>
	    </fieldset>

	    <div class="lcx-new-condition">
	        <button class="button lcx-btn-add-condition" id="lcx-btn-add-condition"><?php _e( 'New condition', 'lcx' ); ?></button>
	    </div>
    <?php }


    function mb_message() {

    ?>

    <fieldset id="lcx-conditions">
    	<table class="form-table">
    		<tr>
    			<td>
    				<?php wp_editor( '', 'lcx_msg', array(
				    	'textarea_name' => 'msg[]',
				    	'teeny' => true,
				    	'textarea_rows' => '5',
				    	'tinymce' => array( 
				            'content_css' => LCX_URL . '/assets/css/admin/auto-msgs-editor.css' 
				       ) 
				    ) ); ?>
    			</td>
    		</tr>
    	</table>

    </fieldset>
    	
    <?php }

    function mb_save() {}

}

new LiveChatX_AutoMsgs();