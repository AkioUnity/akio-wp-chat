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
 * Get translated string.
 *
 * @param string section id
 * @param string field id
 *
 * @since Live Chat X (2.6.1)
 * @return string $value
 */
function lcx__( $section_id, $field_id )
{

	$str = lcx_get_option( $section_id, $field_id );

	if( has_filter( 'wpml_translate_single_string' ) ) {
		return apply_filters( 'wpml_translate_single_string', $str, LCX_NAME, $field_id );

	} else if( function_exists( 'pll_register_string' ) ) {
		return pll__( $str );
	
	} else
		return $str;
}

/**
 * Print translated string.
 *
 * @param string section id
 * @param string field id
 *
 * @since Live Chat X (2.6.1)
 * @return string $value
 */
function lcx_e( $section_id, $field_id )
{
	echo lcx__( $section_id, $field_id );
}

/**
 * Get an option from an option group.
 *
 * @param string section id
 * @param string field id
 *
 * @since Live Chat X (2.4.0)
 * @return mixed setting or false if no setting exists
 */
function lcx_get_option( $section_id, $field_id )
{
	$opts = LiveChatX()->opts();

	if( isset( $opts[$section_id .'_'. $field_id] ) ) {
		return $opts[$section_id .'_'. $field_id];
	}
	return false;
}

/**
 * Get an option group.
 *
 * @param string section id
 * @param translate Return translated value.
 *
 * @since Live Chat X (2.4.0)
 * @return array|null Setting array or false if no setting exists
 */
function lcx_get_option_group( $section_id, $translate = false )
{
	$opts = LiveChatX()->opts();

	if( empty( $opts ) )
		return null;

	$len = strlen( $section_id ) + 1;

	$output = array();
	foreach ( $opts as $k => $v ) {
		if ( strpos( $k, $section_id . '_') === 0 ) {
			$k = substr( $k, $len );
			if( is_numeric( $v ) )
				$output[$k] = (int) $v;
			else
				$output[$k] = $v;

			if( $translate ) {
				if( has_filter( 'wpml_translate_single_string' ) ) {
					$output[$k] = apply_filters( 'wpml_translate_single_string', $output[$k], LCX_NAME, $k );
				}

				elseif( function_exists( 'pll_register_string' ) )
					$output[$k] = pll__( $k );
			}
		}
	}

	return $output;
}

/**
 * Delete all the saved settings.
 *
 * @param string option group id
 */
function fn_lcx_delete_options() 
{
	delete_option( 'lcx_opts' );
}

/**
 * Generate random hash string.
 *
 * @since Live Chat X (2.4.0)
 * @return string
 */
function fn_lcx_hash() {
	return md5( uniqid() );
}

/**
 * Get current page URL.
 *
 * @since Live Chat X (2.4.0)
 * @return string URL
 */
function fn_lcx_get_current_url() 
{
	$page_URL = 'http';
	
	if( !empty( $_SERVER['HTTPS'] ) ) {
		if ( @$_SERVER['HTTPS'] == 'on' )
			$page_URL .= "s";
	}

	$page_URL .= '://';

	if ( !in_array( @$_SERVER['SERVER_PORT'], array( 80, 443 ) ) )
		$page_URL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .$_SERVER['REQUEST_URI'];
	else
		$page_URL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

	return $page_URL;

}

/**
 * Get pure domain without http or subdomains & subfolders.
 *
 * @since Live Chat X (2.4.0)
 * @return string
 */
function fn_lcx_get_pure_domain( $url, $include_subdomain = true ) 
{
	$urlobj = parse_url($url);
	$domain = @$urlobj['host'];
	$output = false;
	
	// Parse standart TLD like (com, co.uk)
	if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs ) ) {
		$output = $regs['domain'];
	
	// Parse standart long-TLD like (traveling)
	} elseif ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,12})$/i', $domain, $regs ) ) {
		$output = $regs['domain'];
	}

	// Include sub-domain
	if( $output and $include_subdomain ) {
		preg_match('/(?:http[s]*\:\/\/)*(.*?)\.(?=[^\/]*\..{2,5})/i', $domain, $subdomain );

		if( !empty( $subdomain ) ) {
			$output = $subdomain[0].$output;
		}
	}

	return $output;
}

/**
 * Get phone country code.
 *
 * @since Live Chat X (2.4.0)
 * @return string
 */
function fn_lcx_get_phone_code( $country_code ) 
{
	$codes = include( LCX_PATH . '/data/phone-codes.php' );

	return '+' . @$codes[$country_code];
}

/**
 * Get countries select-type list.
 *
 * @since Live Chat X (2.4.0)
 * @return string List of countries.
 */
function fn_lcx_get_countries_list( $default = '', $show_phone_codes = true ) 
{
	$output = '';
	$pcode = '';
	$phone_codes = include( LCX_PATH . '/data/phone-codes.php' );

	foreach( include( LCX_PATH . '/data/countries.php' ) as $code => $name ) {

		if( $show_phone_codes ) {
			if( !empty( $phone_codes[$code] ) ) {
				$prefix = ( $phone_codes[$code][0] == '+' ) ? '' : '+';
			} else {
				$prefix = '';
			}
			$pcode = $prefix . @$phone_codes[$code];
		}
		$selected = selected( $default, $code, 0 );
		$output .= "<option value=\"{$code}\" {$selected}>$name $pcode</option>";
	}

	return $output;
}


/**
 * Get social links by array.
 *
 * @since Live Chat X (2.4.0)
 * @return array.
 */
function fn_lcx_get_social_links() 
{
	$response = array();
	$links = get_option( 'lcx_opts_social' );

	if( !empty( $links ) ) {
		foreach( $links as $name => $url ) {
			if( filter_var( $url, FILTER_VALIDATE_URL ) !== FALSE ) {
				$response[$name] = $url;
			}
		}
	}

	return $response;
}

/**
 * Get the upload URL/path in right way (works with SSL).
 *
 * @param $param string "basedir" or "baseurl"
 * @param $subfolder string Subfolder. Must started with "/"
 * @since Live Chat X (2.4.0)
 * @return string
 */
function fn_lcx_get_upload_dir_var( $param, $subfolder = '' ) {
	$upload_dir = wp_upload_dir();
	$url = $upload_dir[ $param ];

	if ( $param === 'baseurl' && is_ssl() ) {
		$url = str_replace( 'http://', 'https://', $url );
	}

	return $url . $subfolder;
}

/**
 * Fetch from array.
 *
 * This is a helper function to retrieve values from global arrays.
 *
 * @param array
 * @param string
 * @param bool
 *
 * @since Live Chat X (2.4.0)
 * @return string
 */
function fn_lcx_fetch_from_array( &$array, $index = '' ) {
	if ( !isset( $array[$index] ) ) {
		return FALSE;
	}

	return $array[$index];
}

/**
 * Sort multi-dimensional arrays.
 *
 * Example: $data = fn_lcx_sort_array_orderby( $data, 'position', SORT_ASC );
 *
 * @since Live Chat X (2.4.0)
 * @link http://php.net/manual/en/function.array-multisort.php#100534
 * @return array
 */
function fn_lcx_sort_array_orderby() {
	$args = func_get_args();
	$data = array_shift($args);
	foreach ($args as $n => $field) {
		if (is_string($field)) {
			$tmp = array();
			foreach ($data as $key => $row)
				$tmp[$key] = $row[$field];
			$args[$n] = $tmp;
			}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}

/**
 * Check if current page is a blog post?
 *
 * @return bool
 */
function fn_lcx_is_blog_page() {

    global $post;

    // Post type must be 'post'.
    $post_type = get_post_type( $post );

    // Check all blog-related conditional tags, as well as the current post type, 
    // to determine if we're viewing a blog page.
    return (
        ( is_home() || is_archive() || is_single() )
        && ($post_type == 'post')
    ) ? true : false ;

}


/** 
 * Check if current user connect by a mobile device.
 *
 * @since Live Chat X (2.4.0)
 * @return bool 
 */ 
function fn_lcx_is_mobile() 
{ 
	return preg_match( "/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER[ 'HTTP_USER_AGENT' ] );
}

/** 
 * Send a POST request using cURL.
 *
 * @param string $url to request 
 * @param array $post values to send 
 * @param array $options for cURL
 *
 * @since Live Chat X (2.4.0)
 * @return string 
 */ 
function fn_lcx_curl_post( $url, array $post = NULL, array $options = array() ) 
{ 
	$defaults = array( 
		CURLOPT_POST => 1, 
		CURLOPT_HEADER => 0, 
		CURLOPT_URL => $url, 
		CURLOPT_FRESH_CONNECT => 1, 
		CURLOPT_RETURNTRANSFER => 1, 
		CURLOPT_FORBID_REUSE => 1, 
		CURLOPT_TIMEOUT => 4, 
		CURLOPT_POSTFIELDS => json_encode( $post ),
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6',
	); 

	$ch = curl_init(); 
	curl_setopt_array($ch, ($options + $defaults)); 
	if( ! $result = curl_exec($ch)) 
	{ 
		trigger_error(curl_error($ch)); 
	} 
	curl_close($ch); 
	return $result; 
} 

/** 
 * Send a GET request using cURL.
 *
 * @param string $url to request 
 * @param array $get values to send 
 * @param array $options for cURL 
 *
 * @since Live Chat X (2.4.0)
 * @return string 
 */ 
function fn_lcx_curl_get( $url, array $get = NULL, array $options = array() ) 
{    
	$defaults = array( 
		CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
		CURLOPT_HEADER => 0, 
		CURLOPT_TIMEOUT => 4,
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6',
	); 
	
	$ch = curl_init(); 
	curl_setopt_array($ch, ($options + $defaults)); 
	if( ! $result = curl_exec($ch)) 
	{ 
		trigger_error(curl_error($ch)); 
	} 
	curl_close($ch); 
	return $result; 
}