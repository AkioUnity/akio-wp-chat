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
 *
 * Abstract Class.
 * A helper class for action and filter hooks.
 *
 * @since Live Chat X (2.4.0)
 *
 */
abstract class LiveChatX_Abstract {

	public function __construct() {}

	public function addAction( $hook, $function_to_add, $priority = 30, $accepted_args = 1 ) {
		add_action( $hook, array( &$this, $function_to_add), $priority, $accepted_args );
	}

	public function addFilter( $tag, $function_to_add, $priority = 30, $accepted_args = 1 ) {
		add_action( $tag, array( &$this, $function_to_add), $priority, $accepted_args );
	}

}
