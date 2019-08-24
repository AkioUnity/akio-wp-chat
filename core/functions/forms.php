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
 * Get custom forms including fields by page (i.e. "offline").
 *
 * @param string|array $page_slug Location of the form.
 *
 * @since Live Chat X (2.4.0)
 * @return array
 */
function fn_lcx_get_custom_forms( $page_slug ) {

	include_once LCX_PATH . '/core/classes/form.class.php';

	$data = array();

	// Get forms
	$forms = get_posts( array( 
		'post_type' => 'lcx_custom_form',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'cache_results' => false,
		'no_found_rows' => true,
		'tax_query' => array(
			array(
				'taxonomy' => 'lcx_page',
				'field'    => 'slug',
				'terms'    => $page_slug,
			),
		),
	));

	// Get form fields
	if( !empty( $forms ) ) {
		foreach( $forms as $id ) {
			$form = new LiveChatX_Form( $id );
			$data[$id] = $form->get();
		}
	}

	return $data;
}

/**
 * Get offline support categories by offline form id (WP Post ID).
 *
 * @since Live Chat X (2.4.0)
 * @return array
 */
function fn_lcx_get_support_cats( $form_id, $include_hidden = false ) {

	$data = get_post_meta( $form_id, 'support_cats', true );

	if( ! $include_hidden ) {
		foreach( $data as $i => $cat ) {
			if( empty($cat['show'] ) ) {
				unset( $data[$i] );
			}
		}
	}

	return $data;

}

/**
 * Get custom from types.
 *
 * @since Live Chat X (2.4.0)
 * @return array
 */
function fn_lcx_get_form_types() {

	return apply_filters( 'lcx_custom_form_types', array(
		'text' => __( 'Text', 'lcx' ),
		'email' => __( 'Email', 'lcx' ),
		'textarea' => __( 'Textarea', 'lcx' ),
		'subject' => __( 'Subject', 'lcx' ),
		'issue' => __( 'Question / issue', 'lcx' ),
		'fullname' => __( 'Name &amp; Email', 'lcx' ),
		'phone' => __( 'Phone', 'lcx' ),
		'support-cats' => __( 'Support categories', 'lcx' ),
	));
}