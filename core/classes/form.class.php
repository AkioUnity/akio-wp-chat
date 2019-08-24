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
 * Form class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Form extends LiveChatX_Abstract {

	protected $id = 0;
	protected $is_sent = false;
	protected $data = array(); // Custom form data
	protected $udata = array(); // User form data
	protected $email = array(); // Email data
	protected $email_class;
	protected $forward_to = '';
	protected $meta = array(); // Form meta


	/**
	 * Construction.
	 *
	 * @access public
	 */
	public function __construct( $formid, $user_data = array() ) {

		// Update current data
		$this->id = $formid;
		$this->udata = $user_data;

	}

	/**
	 * Get a custom form data.
	 *
	 * @access public
	 * @return array
	 */
	public function get() {

		return array( 
			'name' => get_post_meta( $this->id, 'name', true ),
			'subject' => get_post_meta( $this->id, 'subject', true ),
			'ntf_email' => get_post_meta( $this->id, 'ntf_email', true ),
			'reply_to' => get_post_meta( $this->id, 'reply_to', true ),
			'fields' => get_post_meta( $this->id, 'form_fields', true ),
			'is_sent' => get_post_meta( $this->id, 'is_sent', true )
		);

	}

	/**
	 * Save into database.
	 *
	 * @access public
	 */
	public function save() {

		$meta_input = array();

		$meta = array(
			'formid' => $this->id,
			'meta' => $this->meta,
			'email_opts' => $this->data,
			'udata' => $this->udata,
			'timestamp' => current_time( 'mysql' ),
			'is_sent' => $this->is_sent 
		);

		$data = array(
			'post_title' => $this->email['subject'],
			'meta_input' => $meta,
			'post_type' => 'lcx_offline_msg',
			'post_status'   => 'publish'
		);

		// Insert the post into the database
		wp_insert_post( $data );
	}

	/**
	 * Setup form fields with user form data by type.
	 *
	 * @access public
	 */
	public function setup_fields() {

		$this->data = $this->get();

		foreach( $this->data['fields'] as $field ) {
			if( !empty( $this->udata[$field['name']] ) ) {
				$this->add_meta( $field['type'], $field, $this->udata[$field['name']] );
			}
		}

	}

	/**
	 * Add form meta.
	 *
	 * @access public
	 */
	public function add_meta( $name, $attr, $val ) {

		switch( $name ) {
			// Update visitor name and email
			case 'fullname':
				if( !empty( $val['name'] ) ) {
					$this->meta['visitor_name'] = array(
						'label' => $attr['label'],
						'val' => $val['name']
					);
				}

				if( !empty( $val['email'] ) ) {
					$this->meta['email'] = array(
						'label' => __( 'Email', 'lcx' ),
						'val' => $val['email']
					);
				}
				break;

			// Update message
			case 'issue':
				if( !empty( $val ) ) {
					$this->meta['message'] = array(
						'label' => $attr['label'],
						'val' => $val
					);
				}
				break;

			// Update email
			case 'email':
				if( !empty( $val ) ) {
					$this->meta['email'] =  array(
						'label' => $attr['label'],
						'val' => $val
					);
				}
				break;

			// Just set "forward_to" variable to use later while sending email
			case 'support-cats':
				$termid = $this->udata[$attr['name']];
				$support_cats_data = get_post_meta( $this->id, 'support_cats', true );

				$support_email = $support_cats_data[$termid]['email'];

				if( is_email( $support_email ) ) {
					$this->forward_to = trim( $support_email );
				}
				break;

			// Update form data
			default:
				$this->meta['form_data'][$name] = array(
					'label' => $attr['label'],
					'val' => $val
				);
		}
	}

	/**
	 * Remove form meta.
	 *
	 * @access public
	 */
	public function remove_meta( $name ) {
		if( !empty( $this->meta[$name] ) ) {
			unset( $this->meta[$name] );
		}
	}

	/**
	 * Prepare notification email data.
	 *
	 * @access public
	 * @return array
	 */
	public function render_email() {

		if( empty( $this->data['fields'] ) ) {
			return false;
		}

		include_once( LCX_PATH . '/core/classes/email.class.php' );

		$this->email_class = new LiveChatX_Email( 'custom_form' );

		// Email basics
		$this->email = array(
			'to' => ( !empty( $this->forward_to ) ) ? $this->forward_to : $this->data['ntf_email'],
			'subject' => $this->data['subject'],
			'headers' => array( 'Content-Type: text/html; charset=UTF-8' )
		);

		// Set FROM
		$this->email['headers'][] = 'From: ' . $this->email['to'];

		// Set REPLY-TO
		if( !empty( $this->forward_to ) ) {
			$this->email['headers'][] = 'Reply-to: ' . $this->forward_to;
			
			// Update email meta
			$this->meta['email']  = $this->forward_to;

		} elseif( !empty( $this->data['reply_to'] ) ) {
			$this->email['headers'][] = 'Reply-to: ' . $this->data['reply_to'];
		}

		// Render email body
		$this->email['msg'] = fn_lcx_get_template( 'email/custom-form', array( 'fields' => $this->meta ) );

		// Render whole email
		return $this->email_class->render( $this->email['msg'] );

	}

	/**
	 * Send notification email.
	 *
	 * @access public
	 * @return bool
	 */
	public function send_email( $save_offline = true ) {

		if( empty( $this->email ) ) {
			$this->render_email();
		}

		// Send email
		$this->is_sent = $this->email_class->send( 
			$this->email['subject'], 
			$this->email['to'], 
			$this->email['headers']
		);

		// Save to offline messages
		if( $save_offline ) {
			$this->save();
		}

		return $this->is_sent;
	}
}

