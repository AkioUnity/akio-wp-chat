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
 * Plugins page class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Plugins {

	/**
	 * Constructor.
	 */
	function __construct() {
		
		add_filter( 'all_plugins', array( $this, 'manage_plugins' ) );

	}

    function manage_plugins( $plugins ) {

        return $plugins;
    }

}

new LiveChatX_Plugins();