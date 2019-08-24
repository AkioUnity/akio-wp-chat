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
 * @author chadminick.com
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Template class
 *
 * @return void
 */
class LiveChatX_Template {

	private $vars  = array();

	/**
	 * Get template class
	 *
	 * @access public
	 * @return void
	 */
	public function __get( $name ) {
		return $this->vars[$name];
	}

	/** 
	 * Set template class
	 *
	 * @access public
	 * @return void
	 */
	public function __set( $name, $value ) {

		if( $name == 'view_template_file' ) {
			throw new Exception( "Cannot bind variable named 'view_template_file'" );
		}
		$this->vars[$name] = $value;

	}

	/**
	 * Render template
	 *
	 * @access public
	 * @return void
	 */
	public function render( $view_template_file ) {

		if( array_key_exists( 'view_template_file', $this->vars ) ) {
			throw new Exception( "Cannot bind variable called 'view_template_file'" );
		}

		extract( $this->vars );

		ob_start();
		include( $view_template_file );
		return ob_get_clean();
	}
}