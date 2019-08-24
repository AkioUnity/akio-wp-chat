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
 * Firebase class.
 *
 * @since Live Chat X (2.4.0)
 *
 */
class LiveChatX_Firebase extends LiveChatX_Abstract {

    protected $db = '';

    /**
     * Construction.
     *
     * @access public
     */
    public function __construct() {
        $this->db = fn_lcx_get_realtime_config();
    }

    /**
     * Get a chat data.
     *
     * @access public
     * @return error|object
     */ 
    public function setup( $token ) {

        $data = json_decode( file_get_contents( LCX_PATH . '/data/default-db-data.json') );
        $rules = json_decode( file_get_contents( LCX_PATH . '/data/rules.json') );

        include LCX_PATH . '/core/library/firebase/firebase-client.php';

        $firebase = new LCX_FirebaseLib( $this->db['databaseURL'] );
        $firebase->setToken( $token  );


        $firebase->set( '/_livechat', $data->_livechat );
        return $firebase->set( '/.settings/rules', $rules );
    }

}