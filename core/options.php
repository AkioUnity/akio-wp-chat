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
 *
 */

add_filter( 'lcx_register_settings_lcx', '_fn_lcx_opts_data' );

/**
 * Tabbed example
 */

function _fn_lcx_opts_data( $opts ) {
	global $wpdb;
	
	// Get pure domain
	$domain = fn_lcx_get_pure_domain( fn_lcx_get_current_url() );

	// Get users
	$users = get_users( array(
		'fields' => array( 'ID', 'user_login', 'user_email', 'display_name' )
	));

	$site_email = get_option( 'admin_email' );

	$all_pages = get_pages();
	$pages = array();

    if( !empty( $all_pages ) ) {
        foreach( $all_pages as $page ) {
            $pages[ $page->ID] = $page->post_title;
        }
    }

	// Get usernames
	$usernames = array();
	foreach( $users as $user ) {
		$usernames[$user->ID] = $user->user_login;
	}

	// 
	// Tabs
	//
	$opts['tabs'] = array(
		array(
			'id' => 'general',
			'title' => __( 'General', 'lcx' )
		),
		array(
			'id' => 'design',
			'title' => __( 'Design', 'lcx' )
		),
		array(
			'id' => 'chats',
			'title' => __( 'Chats', 'lcx' ),
		),
		array(
			'id' => 'site',
			'title' => __( 'Site', 'lcx' ),
		),
		array(
			'id' => 'users',
			'title' => __( 'Users', 'lcx' ),
		),
		array(
			'id' => 'msgs',
			'title' => __( 'Messages', 'lcx' )
		),
		array(
			'id' => 'realtime',
			'title' => __( 'Real-time', 'lcx' ),
		),
		array(
			'id' => 'advanced',
			'title' => __( 'Advanced', 'lcx' ),
		)
	);
	
	// Settings
	$opts['sections'] = array(
		array(
			'tab_id' => 'general',
			'section_id' => 'display',
			'section_title' => __( 'Display', 'lcx' ),
			'section_description' => 'You don’t have to offer chat everywhere to every customer. Consider carefully where the unique benefits of live chat will make the most impact.',
			'section_order' => 10,
			'fields' => array(
				array(
					'id' => 'basics',
					'title' => '',
					'choices' => array(
						'showWPusers' => __( 'Show up for logged in WordPress users only', 'lcx' ),
						'hideMobile' => '📱 ' . __( 'Hide on mobile devices', 'lcx' ),
						'hideSSL' => '🔑 ' . __( 'Hide from pages that uses SSL', 'lcx' ),
						'hideOffline' => __( 'Hide when all operators are offline', 'lcx' ) . ' <span data-balloon="If all operators suddenly go offline when visitor\'s chat popup is open, it won\'t go offline until next page refresh." data-balloon-pos="right" data-balloon-length="large" class="lcx-helper"><span class="dashicons dashicons-editor-help"></span></span>',
						'disableLookingAt' => __( 'Don\'t track visitor in real-time', 'lcx' ) . ' <span class="lcx-helper" data-balloon="It hides \'Last seen\' and \'Looking at\' data of visitors from your chat console. It is good when you\'ve high usage in your Firebase database" data-balloon-pos="right" data-balloon-length="large" ><span class="dashicons dashicons-editor-help"></span></span>'
					),
					'req' => array(),
					'type' => 'checkboxes'
				),
				array(
					'id' => 'type',
					'title' => __( 'Where to show up?', 'lcx' ),
					'choices' => array(
						'show' => '✔ ' . __( 'Show everywhere', 'lcx' ),
						'hide' => '✖️ ' . __( 'Hide everywhere', 'lcx' )
					),
					'default' => 'show',
					'type' => 'radio'
				),
				array(
					'id' => 'except_pages',
					'title' => '',
					'desc' => __( 'Except those pages', 'lcx' ) . ':',
					'choices' => $pages,
					'type' => 'checkboxes'
				),
				array(
					'id' => 'home',
					'title' => __( 'Display on specific pages', 'lcx' ),
					'desc' => __( 'Homepage', 'lcx' ). ':',
					'choices' => array(
						'default' => __( 'Default', 'lcx' ),
						'show' => __( 'Always show', 'lcx' ),
						'hide' => __( 'Always hide', 'lcx' )
					),
					'req' => array(),
					'default' => 'default',
					'type' => 'radio'
				),
				array(
					'id' => 'blog',
					'title' => '',
					'desc' => __( 'Blog-related pages', 'lcx' ) . ':',
					'choices' => array(
						'default' => __( 'Default', 'lcx' ),
						'show' => __( 'Always show', 'lcx' ),
						'hide' => __( 'Always hide', 'lcx' )
					),
					'req' => array(),
					'default' => 'default',
					'type' => 'radio'
				)
			)
		),

		array(
			'tab_id' => 'general',
			'section_id' => 'license',
			'section_title' => __( 'Licensing', 'lcx' ),
			'section_description' => '',
			'section_order' => 30,
			'fields' => array(
				array(
					'id' => 'api',
					'title' => '<span class="dashicons dashicons-post-status"></span> Screets API key <span class="lcx-req">*</span>',
					'desc' => '<strong><a href="https://support.lamoga.de/api/?domain=' . $domain .'" target="_blank">Get your API key</a></strong> <span class="lcx-ico-new-win"></span><br>' . __( 'It is required to activate the plugin and get <strong>free updates</strong>.', 'lcx' ) . '<br><small>* ' . __( 'Note that you might need to re-login WordPress after updating your API key.', 'lcx' ) . '</small>',
					'placeholder' => 'Screets API Key',
					'type' => 'text'
				),
			)
		),

		array(
			'tab_id' => 'chats',
			'section_id' => 'response',
			'section_title' => __( 'Response Info', 'lcx' ),
			'section_description' => __( 'Set your live chat hours to an <strong>achievable level</strong>. Because for customers, “live chat” implies an almost instant response, so a delayed reply to a chat is a much poorer experience than a slow email reply.', 'lcx' ),
			'section_order' => 20,
			'fields' => array(
				array(
					'id' => 'replies_online',
					'title' => __( 'Typically replies within...', 'lcx' ),
					'desc' => __( 'Online', 'lcx' ) . ' (' . __( 'At least one operator is online', 'lcx' ) . ')',
					'choices' => array(
						'none' => __( 'Not specified', 'lcx' ),
						'aFewMins' => __( 'a few minutes', 'lcx' ),
						'in15min' => __( '15 minutes', 'lcx' ),
						'in30min' => __( '30 minutes', 'lcx' ),
						'in1hour' => __( 'an hour', 'lcx' ),
						'inFewHours' => __( 'a few hours', 'lcx' ),
						'inDay' => __( 'a day', 'lcx' ),
						'1BDay' => __( '1 business day', 'lcx' ),
						'2BDay' => __( '2 business days', 'lcx' ),
						'3BDay' => __( '3 business days', 'lcx' )
					),
					'default' => 'aFewMins',
					'type' => 'select'
				),
				array(
					'id' => 'replies_offline',
					'title' => '',
					'desc' => __( 'Offline', 'lcx' ) . ' (' .__( 'No operators online', 'lcx' ) . ')',
					'choices' => array(
						'none' => __( 'Not specified', 'lcx' ),
						'aFewMins' => __( 'a few minutes', 'lcx' ),
						'in15min' => __( '15 minutes', 'lcx' ),
						'in30min' => __( '30 minutes', 'lcx' ),
						'in1hour' => __( 'an hour', 'lcx' ),
						'inFewHours' => __( 'a few hours', 'lcx' ),
						'inDay' => __( 'a day', 'lcx' ),
						'1BDay' => __( '1 business day', 'lcx' ),
						'2BDay' => __( '2 business days', 'lcx' ),
						'3BDay' => __( '3 business days', 'lcx' )
					),
					'default' => '1BDay',
					'type' => 'select'
				)
			)
		),

		/*array(
			'tab_id' => 'chats',
			'section_id' => 'automsg',
			'section_title' => __( 'Auto-messages', 'lcx' ),
			'section_description' => '',
			'section_order' => 25,
			'fields' => array(
				array(
					'id' => 'type',
					'title' => '',
					'choices' => array(
						'currentPage' => __( 'Current page URL', 'lcx' ),
						'referral' => __( 'Referral URL', 'lcx' ),
						'timePage' => __( 'Time on current page', 'lcx' ),
						'single_visits' => __( 'Single page views', 'lcx' ),
						'total_visits' => __( 'Total visits', 'lcx' ),
					),
					'desc' => '<span class="lcx-rules-desc">
						<span id="lcx-desc-currentPage">' . __( 'The specific page a visitor last viewed on your site.', 'lcx' ) . '</span><span id="lcx-desc-referral">' . __( 'A third-party website that linked to your site.', 'lcx' ) . '</span><span id="lcx-desc-timePage">' . __( 'How long a person has spent viewing a specific page.', 'lcx' ) . '</span><span id="lcx-desc-single_visits">' . __( 'The number of times someone has viewed a single page on your site.', 'lcx' ) . '</span><span id="lcx-desc-total_visits">' . __( 'The number of times someone has visited your site.', 'lcx' ) . '</span>
					</span>',
					'type' => 'select'
				),
				array(
					'id' => 'comparison',
					'title' => '',
					'choices' => array(
						'eq' => __( 'is', 'lcx' ),
						'nq' => __( 'is not', 'lcx' ),
						'starts_with' => __( 'starts with', 'lcx' ),
						'ends_with' => __( 'ends with', 'lcx' ),
						'contains' => __( 'contains', 'lcx' ),
						'not_contains' => __( 'does not contains', 'lcx' ),
						'unknown' => __( 'is unknown', 'lcx' ),
						'any' => __( 'has any value', 'lcx' ),
						'gt' => __( 'is more than', 'lcx' ),
						'ls' => __( 'is less than', 'lcx' ),
					),
					'default' => 'eq',
					'type' => 'radio'
				),
				array(
					'id' => 'url_value',
					'title' => '',
					'placeholder' => __( 'Enter URL', 'lcx' ),
					'type' => 'text'
				),
				array(
					'id' => 'time_value',
					'title' => '',
					'desc' => __( 'seconds', 'lcx' ),
					'type' => 'number'
				)
			)
		),*/

		array(
			'tab_id' => 'chats',
			'section_id' => 'offline',
			'section_title' => __( 'Offline capabilities', 'lcx' ),
			'section_description' => '<span class="dashicons dashicons-lock"></span> <strong>GDPR Note:</strong> Don’t ask fields on the offline form that you don’t need. Unless you absolutely need the data for contacting your customer, you shouldn’t collect it. Names you should probably always collect, but unless you are calling back, the phone field is unnecessary.',
			'section_order' => 30,
			'fields' => array(
				array(
					'id' => 'initResponse',
					'title' => __( 'Initial response', 'lcx' ),
					'desc' => __( 'How the application should response when visitors try to start a <strong>new conversation</strong>?', 'lcx' ),
					'choices' => array(
						'allowChat' => __( 'Allow visitors to start new conversation', 'lcx' ) . ' (' . __( 'Shows up collector card', 'lcx' ) . ')',
						'showOfflineForm' => __( 'Show offline form', 'lcx' ),
					),
					'default' => 'allowChat',
					'type' => 'select'
				),
				array(
					'id' => 'coll_desc',
					'default' => '',
					'type' => 'custom'
				),

				array(
					'id' => 'coll_fields',
					'title' => '<span class="dashicons dashicons-nametag"></span> ' . __( 'Collector card', 'lcx' ) . '<p class="description">This form appears just after visitor starts a chat if any required field is not provided by visitor. So you can collect some information about your visitors.</p>',
					'desc' => __( 'Select visible fields:', 'lcx' ) . ' <span class="dashicons dashicons-visibility"></span>',
					'choices' => array(
						'email' => __( 'Email', 'lcx' ),
						'name' => __( 'Name', 'lcx' ),
						'company_name' => __( 'Company name', 'lcx' ),
						'phone' => __( 'Phone', 'lcx' )
					),
					'default' => array( 'email', 'name', 'ask_question' ),
					'type' => 'checkboxes'
				),
				array(
					'id' => 'coll_req_fields',
					'title' => '<p class="description"><strong class="lcx-tip">(?)</strong> Want to hide collector card? Simply, uncheck all required fields on the right.</p>',
					'desc' => __( 'Select required fields:', 'lcx' ) . ' <span class="lcx-req">*</span>',
					'choices' => array(
						'email' => __( 'Email', 'lcx' ),
						'name' => __( 'Name', 'lcx' ),
						'company_name' => __( 'Company name', 'lcx' ),
						'phone' => __( 'Phone', 'lcx' )
					),
					'default' => array( 'email', 'ask_question' ),
					'type' => 'checkboxes'
				),
				array(
					'id' => 'fields',
					'title' => __( 'Offline form', 'lcx' ) . '<p class="description">Shows up offline form when all operators are offline.</p>',
					'desc' => __( 'Select visible fields:', 'lcx' ) . ' <span class="dashicons dashicons-visibility"></span>',
					'choices' => array(
						'email' => __( 'Email', 'lcx' ),
						'name' => __( 'Name', 'lcx' ),
						'company_name' => __( 'Company name', 'lcx' ),
						'phone' => __( 'Phone', 'lcx' ),
						'ask_question' => __( 'Ask question', 'lcx' ),
					),
					'default' => array( 'email', 'name', 'ask_question' ),
					'type' => 'checkboxes'
				),
				array(
					'id' => 'req_fields',
					'title' => '',
					'desc' => __( 'Select required fields:', 'lcx' ) . ' <span class="lcx-req">*</span>',
					'choices' => array(
						'email' => __( 'Email', 'lcx' ),
						'name' => __( 'Name', 'lcx' ),
						'company_name' => __( 'Company name', 'lcx' ),
						'phone' => __( 'Phone', 'lcx' ),
						'ask_question' => __( 'Ask question', 'lcx' ),
					),
					'default' => array( 'email', 'ask_question' ),
					'type' => 'checkboxes'
				),
				array(
					'id' => 'opts',
					'title' => '',
					'desc' => __( 'GDPR Compliance', 'lcx' ),
					'choices' => array(
						'privacy_cb' => __( 'Show "I consent..." checkbox', 'lcx' ) . '<p class="description">To use this feature, also update <strong>Privacy Policy URL</strong> found in site chat options.</p>'
					),
					'default' => null,
					'type' => 'checkboxes'
				),

				
			)
		),

		array(
			'tab_id' => 'chats',
			'section_id' => 'time',
			'section_title' => __( 'Date/time format', 'lcx' ),
			'section_order' => 40,
			'fields' => array(
				array(
					'id' => 'dateFormat',
					'title' => __( 'Date format', 'lcx' ),
					'choices' => array(
						'd/m/Y' => 'd/m/Y &nbsp; <span class="description">(' . date('d/m/Y') . ')</span>',
						'd-m-Y' => 'd-m-Y &nbsp; <span class="description">(' . date('d-m-Y') . ')</span>',
						'Y/m/d' => 'Y/m/d &nbsp; <span class="description">(' . date('Y/m/d') . ')</span>',
						'Y-m-d' => 'Y-m-d &nbsp; <span class="description">(' . date('Y-m-d') . ')</span>',
						'm/d/Y' => 'm/d/Y &nbsp; <span class="description">(' . date('m/d/Y') . ')</span>',
						'm-d-Y' => 'm-d-Y &nbsp; <span class="description">(' . date('m-d-Y') . ')</span>'
					),
					'default' => 'd/m/Y',
					'type' => 'radio'
				)
			)
		),

		/*array(
			'tab_id' => 'chatmsgs',
			'section_id' => 'visitor',
			'section_title' => __( 'Visitor auto messages', 'lcx' ),
			'section_description' => '',
			'section_order' => 10,
			'fields' => array(
				array(
					'id' => 'test',
					'title'      => __( 'Auto message', 'lcx' ) . ' - 1',
					'default' => array(
						'is tedigin kadar' => 'aa',
						'22' => 'bb',
						'33' => 'ccc',
					),
					'type' => 'multiinputs'
				),
				array(
					'id' => 'state',
					'title'      => __( 'Auto message', 'lcx' ) . ' - 1',
					'choices' => array(
						'and' => __( 'visitors who match ALL those rules', 'lcx' ),
						'or' => __( 'visitors who match ANY of those rules', 'lcx' ),
					),
					'type' => 'select'
				),
				array(
					'id' => 'rules',
					'title' => '',
					'desc' => __( 'Rules', 'lcx' ) . ':',
					'type' => 'group',
					'subfields' => array(
						array(
							'id' => 'type',
							'title' => '',
							'choices' => array(
								'currentPage' => __( 'Current page URL', 'lcx' ),
								'referral' => __( 'Referral URL', 'lcx' ),
								'timePage' => __( 'Time on current page', 'lcx' ),
								'single_visits' => __( 'Single page views', 'lcx' ),
								'total_visits' => __( 'Total visits', 'lcx' ),
							),
							'desc' => '<span class="lcx-rules-desc">
								<span id="lcx-desc-currentPage">' . __( 'The specific page a visitor last viewed on your site.', 'lcx' ) . '</span><span id="lcx-desc-referral">' . __( 'A third-party website that linked to your site.', 'lcx' ) . '</span><span id="lcx-desc-timePage">' . __( 'How long a person has spent viewing a specific page.', 'lcx' ) . '</span><span id="lcx-desc-single_visits">' . __( 'The number of times someone has viewed a single page on your site.', 'lcx' ) . '</span><span id="lcx-desc-total_visits">' . __( 'The number of times someone has visited your site.', 'lcx' ) . '</span>
							</span>',
							'type' => 'select'
						),
						array(
							'id' => 'comparison',
							'title' => '',
							'choices' => array(
								'eq' => __( 'is', 'lcx' ),
								'nq' => __( 'is not', 'lcx' ),
								'starts_with' => __( 'starts with', 'lcx' ),
								'ends_with' => __( 'ends with', 'lcx' ),
								'contains' => __( 'contains', 'lcx' ),
								'not_contains' => __( 'does not contains', 'lcx' ),
								'unknown' => __( 'is unknown', 'lcx' ),
								'any' => __( 'has any value', 'lcx' ),
								'gt' => __( 'is more than', 'lcx' ),
								'ls' => __( 'is less than', 'lcx' ),
							),
							'default' => 'eq',
							'type' => 'radio'
						),
						array(
							'id' => 'url_value',
							'title' => '',
							'placeholder' => __( 'Enter URL', 'lcx' ),
							'type' => 'text'
						),
						array(
							'id' => 'time_value',
							'title' => '',
							'desc' => __( 'seconds', 'lcx' ),
							'type' => 'number'
						)
					)
				),
				array(
					'id' => 'msg',
					'title' => '',
					'desc' => __( 'Message', 'lcx' ),
					'type' => 'textarea'
				),
			)
		),*/

		array(
			'tab_id' => 'site',
			'section_id' => 'info',
			'section_title' => __( 'Site info', 'lcx' ),
			'section_order' => 10,
			'fields' => array(
				array(
					'id' => 'name',
					'title' => __( 'Site name', 'lcx' ) . '<span class="lcx-req">*</span>',
					'desc' => __( 'We will show this name as your site/company name when needed', 'lcx' ),
					'placeholder' => __( 'Site name', 'lcx' ),
					'default' => get_bloginfo(), // site title
					'type' => 'text'
				),
				array(
					'id' => 'url',
					'title' => __( 'Site url', 'lcx' ) . '<span class="lcx-req">*</span>',
					'default' => 'http://' . fn_lcx_get_pure_domain( get_bloginfo( 'url' ) ),
					'placeholder' => __( 'Site url', 'lcx' ),
					'type' => 'text'
				),
				array(
					'id' => 'logo',
					'title' => __( 'Site logo', 'lcx' ) . '<span class="lcx-req">*</span>',
					'type' => 'file'
				),
				array(
					'id' => 'email',
					'title' => __( 'Site email', 'lcx' ) . '<span class="lcx-req">*</span>',
					'default' => $site_email,
					'placeholder' => 'FROM',
					'desc' => __( '"FROM" part of all emails sending to your visitors' ) . '.',
					'type' => 'text'
				),
				array(
					'id' => 'reply_to',
					'title' => '',
					'placeholder' => 'REPLY-TO (' . __( 'Optional', 'lcx' ) . ')',
					'desc' => 'REPLY-TO (' . __( 'Optional', 'lcx' ) . '): ' . __( 'Your visitors will reply to this email.', 'lcx' ),
					'type' => 'text'
				),
				array(
					'id' => 'email_logo_width',
					'title' => __( 'Logo width', 'lcx' ) . ' (' . __( 'Email', 'lcx' ) . ')',
					'desc' => __( 'If original size 100px, we recommend you to set 50px for retina displays', 'lcx' ),
					'default' => 50,
					'attrs' => array(
						'min' => 0,
						'max' => 200
					),
					'type' => 'number'
				),
				array(
					'id' => 'privacy_url',
					'title' => __( 'Privacy policy URL', 'lcx' ),
					'placeholder' => '/privacy',
					'desc' => 'Page URL of your website privacy policy.',
					'type' => 'text'
				)
			)
		),

		array(
			'tab_id' => 'design',
			'section_id' => 'colors',
			'section_title' => __( 'Colors', 'lcx' ),
			'section_order' => 10,
			'fields' => array(
				array(
					'id' => 'primary',
					'title' => __( 'Primary', 'lcx' ),
					'default' => '#ea3c3b',
					'placeholder' => '#ea3c3b',
					'type' => 'color'
				),
				array(
					'id' => 'secondary',
					'title' => __( 'Secondary', 'lcx' ),
					'default' => '#7e8bfe',
					'placeholder' => '#7e8bfe',
					'type' => 'color'
				),
				array(
					'id' => 'highlight',
					'title' => __( 'Highlight', 'lcx' ),
					'default' => '#fffc79',
					'placeholder' => '#fffc79',
					'type' => 'color'
				),
			)
		),

		array(
			'tab_id' => 'design',
			'section_id' => 'ui',
			'section_title' => __( 'Appearance', 'lcx' ),
			'section_order' => 20,
			'fields' => array(
				array(
					'id' => 'position',
					'title' => __( 'Widget position', 'lcx' ) . ' (px)',
					'default' => 'bottom-right',
					'choices' => array(
						'bottom-left' => __( 'Bottom Left', 'lcx' ),
						'bottom-right' => __( 'Bottom Right', 'lcx' )
					),
					'type' => 'select'
				),
				array(
					'id' => 'starter_size',
					'title' => __( 'Starter', 'lcx' ),
					'desc' => __( 'Starter size', 'lcx' ) . ' (px)',
					'default' => 60,
					'attrs' => array(
						'min' => 30,
						'max' => 500
					),
					'type' => 'number'
				),
				array(
					'id' => 'starter_icon_size',
					'title' => '',
					'desc' => __( 'Starter icon size', 'lcx' ) . ' (px)',
					'default' => 30,
					'attrs' => array(
						'min' => 15,
						'max' => 60
					),
					'type' => 'number'
				),
				array(
					'id' => 'popup_width',
					'title' => __( 'Popup width', 'lcx' ) . ' (px)',
					'default' => 370,
					'attrs' => array(
						'min' => 100,
						'max' => 500
					),
					'type' => 'number'
				),
				/*array(
					'id' => 'popup_height_default',
					'title' => __( 'Popup heights', 'lcx' ),
					'desc' => __( 'Default', 'lcx' ) . ' (px)',
					'default' => 380,
					'attrs' => array(
						'min' => 100,
						'max' => 500
					),
					'type' => 'number'
				),
				array(
					'id' => 'popup_height_online',
					'title' => '',
					'desc' => __( 'Online chat', 'lcx' ) . ' (px)',
					'default' => 420,
					'attrs' => array(
						'min' => 100,
						'max' => 500
					),
					'type' => 'number'
				),*/
				array(
					'id' => 'radius',
					'title' => __( 'Radius', 'lcx' ) . ' (px)',
					'desc' => __( 'Normal', 'lcx' ),
					'default' => 4,
					'attrs' => array(
						'min' => 0,
						'max' => 20
					),
					'type' => 'number'
				),
				array(
					'id' => 'radius_big',
					'title' => '',
					'desc' => __( 'Big', 'lcx' ),
					'default' => 8,
					'attrs' => array(
						'min' => 0,
						'max' => 20
					),
					'type' => 'number'
				),
				array(
					'id' => 'offset_x',
					'title' => __( 'Offset', 'lcx' ) . ' (px) <p class="description">' .__( 'Sets the distance between the page corner and the chat widget', 'lcx' ) . '</p>',
					'desc' => __( 'Horizontal offset', 'lcx' ),
					'default' => 20,
					'attrs' => array(
						'min' => 0,
						'max' => 60
					),
					'type' => 'number'
				),
				array(
					'id' => 'offset_y',
					'title' => '',
					'desc' => __( 'Vertical offset', 'lcx' ),
					'default' => 20,
					'attrs' => array(
						'min' => 0,
						'max' => 60
					),
					'type' => 'number'
				),
				array(
					'id' => 'fontEmbed',
					'title' => 'Embed Font <span class="lcx-helper" data-balloon="LC works in iframe. That\'s why it can\'t use your website fonts. You need to embed your custom font again here if you want to use one. This action  won\'t affect your web page performance." data-balloon-length="large" data-balloon-pos="right"><span class="dashicons dashicons-editor-help"></span></span>',
					'desc' => __( 'Paste your embed code(s) here.', 'lcx' ) . ' ' . __( 'Example','lcx' ) . ':<br> <code>' . htmlspecialchars( '<link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500" rel="stylesheet">' ) . '</code>',
					'type' => 'textarea'
				),
				array(
					'id' => 'font_family',
					'title' => '',
					'desc' => __( 'Font family name.', 'lcx' ) . ' ' . __( 'Example','lcx' ) . ':<br><code>"Roboto", sans-serif</code>',
					'placeholder' => htmlentities( '"Roboto", sans-serif' ),
					'type' => 'text'
				),
				array(
					'id' => 'alt_font_family',
					'title' => '',
					'placeholder' => htmlentities( '"Helvetica Neue", Helvetica, Arial, sans-serif' ),
					'desc' => __( 'Alternative font family name.', 'lcx' ) . ' ' . __( 'It will be used mostly for small texts, buttons, message-balloons and form elements.', 'lcx' ). ' ' . __( 'Leave empty to use default.', 'lcx' ),
					'type' => 'text'
				),
			)
		),

		array(
			'tab_id' => 'design',
			'section_id' => 'advanced',
			'section_title' => __( 'Advanced', 'lcx' ),
			'section_order' => 30,
			'fields' => array(
				array(
					'id' => 'customCSS',
					'title' => __( 'Custom CSS', 'lcx' ),
					'desc' => __( 'Custom CSS for chat widget.', 'lcx' ) . '<ul><li>
						Wrap all your selectors with ".lcx" class like <code>.lcx .lcx-ops { display: none; }</code>
					</li><li>Most of the time, you don\'t need <em>!important</em></li></ul>',
					'type' => 'textarea'
				)
			)
		),

		array(
			'tab_id' => 'users',
			'section_id' => 'ops',
			'section_title' => __( 'Operators', 'lcx' ),
			'section_order' => 10,
			'fields' => array(
				array(
					'id' => 'caps',
					'title' => __( 'User capabilities', 'lcx' ),
					'default' => __fn_lcx_render_caps(),
					'type' => 'custom'
				),
			)
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'intro',
			'section_title' => '',
			'section_order' => 10,
			'fields' => array()
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'btn',
			'section_title' => __( 'Chat button', 'lcx' ),
			'section_order' => 30,
			'fields' => array(
				array(
					'id' => 'title',
					'title' => __( 'Title', 'lcx' ),
					'default' => 'Chat with us',
					'placeholder' => 'Chat with us',
					'type' => 'text'
				)
			)
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'ops',
			'section_title' => file_get_contents( LCX_PATH . '/assets/icons/admin/chatbox-working.svg' ) . ' &nbsp;' . __( 'Operators', 'lcx' ),
			'section_order' => 40,
			'fields' => array(
				/*array(
					'id' => 'online_welc_single',
					'title' => __( 'Online status', 'lcx' ) . '<p class="description">' . __( 'The plugin gets latest online operators list on page load. That\'s why it uses past tense.', 'lcx' ) . '</p>',
					'desc' => __( 'Single operator', 'lcx' ) . '. (' .__( 'Example preview', 'lcx' ) . ': Tom was online recently)',
					'default' => '%s was online recently.',
					'placeholder' => '%s was online recently.',
					'type' => 'text'
				),
				
				array(
					'id' => 'online_welc_multi',
					'title' => '',
					'desc' => __( 'Multiple operators', 'lcx' ) . '. (' .__( 'Example preview', 'lcx' ) . ': Tom, Diva and Joe were online recently)',
					'default' => '%s1 and %s2 were online recently.',
					'placeholder' => '%s1 and %s2 were online recently.',
					'type' => 'text'
				),*/
				array(
					'id' => 'intro',
					'title' => __( 'Team intro', 'lcx' ) . '<p class="description">' . __( 'Introduce your team to make the chat box feel more personal for your visitors.', 'lcx' ) . '</p>',
					'desc' => __( 'Online mode', 'lcx' ),
					'default' => 'Save time by starting your support request online and we\'ll connect you to an expert.',
					'placeholder' => 'Save time by starting your support request online and we\'ll connect you to an expert.',
					'type' => 'textarea'
				),
				array(
					'id' => 'intro_away',
					'title' => '',
					'desc' => __( 'Away mode', 'lcx' ),
					'default' => 'We aren\'t online at the moment, but leave your questions and your email here and we\'ll get back to you, asap.',
					'placeholder' => 'We aren\'t online at the moment, but leave your questions and your email here and we\'ll get back to you, asap.',
					'type' => 'textarea'
				),
				array(
					'id' => 'status_online',
					'title' => __( 'Statuses', 'lcx' ),
					'desc' => __( 'When operator is online', 'lcx' ),
					'default' => 'Online',
					'placeholder' => 'Online',
					'type' => 'text'
				),
				array(
					'id' => 'status_away',
					'title' => '',
					'desc' => __( 'When operator is away', 'lcx' ),
					'default' => 'Away',
					'placeholder' => 'Away',
					'type' => 'text'
				),
			)
		),



		array(
			'tab_id' => 'msgs',
			'section_id' => 'gdpr',
			'section_title' => __( 'GDPR Compliance', 'lcx' ),
			'section_order' => 50,
			'fields' => array(
				
				array(
					'id' => 'note',
					'title' => '',
					'desc' => htmlentities( 'Don\'t remove "<a>" tags. We will update related links.' ),
					'default' => 'This form collects submitted data so that we can correspond with you. Check out <a href="#1">privacy policy</a> for more information.',
					'placeholder' => htmlentities( 'This form collects submitted data so that we can correspond with you. Check out <a href="#1">privacy policy</a> for more information.' ),
					'type' => 'textarea'
				),
				array(
					'id' => 'cb',
					'title' => '',
					'default' => 'I consent to my submitted data being collected and stored.',
					'placeholder' => 'I consent to my submitted data being collected and stored.',
					'type' => 'text'
				),
			)
		),


		array(
			'tab_id' => 'msgs',
			'section_id' => 'collector',
			'section_title' => __( 'Collector card', 'lcx' ),
			'section_order' => 50,
			'fields' => array(
				array(
					'id' => 'precard',
					'title' => __( 'Pre-card message', 'lcx' ),
					'default' => 'A few more details will help get you to the right person:',
					'placeholder' => 'A few more details will help get you to the right person:',
					'type' => 'text'
				),
				array(
					'id' => 'postcard',
					'title' => __( 'Post-card message', 'lcx' ),
					'default' => 'Great! We typically reply in {time}.',
					'placeholder' => 'Great! We typically reply in {time}.',
					'type' => 'text'
				),
			)
		),



		array(
			'tab_id' => 'msgs',
			'section_id' => 'popup',
			'section_title' => __( 'Chat popup', 'lcx' ),
			'section_order' => 60,
			'fields' => array(
				/*array(
					'id' => 'prechat_greeting_new',
					'title' => __( 'Greetings', 'lcx' ) . ' 📣',
					'desc' => '<strong>' . __( 'Pre-chat', 'lcx' ) . '</strong> (' . __( 'New visitors', 'lcx' ) . ') <br><small>' . __( 'Custom variables', 'lcx' ) . ': {siteName}</small>',
					'default' => 'Welcome to {siteName} Support! Start a chat session now and ask us anything!',
					'placeholder' => 'Welcome to {siteName} Support! Start a chat session now and ask us anything!',
					'type' => 'textarea'
				),
				array(
					'id' => 'prechat_greeting_returning',
					'title' => '',
					'desc' => '<strong>' . __( 'Pre-chat', 'lcx' ) . '</strong> (' . __( 'Returning visitors who has started chat before', 'lcx' ) . ') <br><small>' . __( 'Custom variables', 'lcx' ) . ': {siteName}</small>',
					'default' => 'Feel free to start a <strong>new conversation</strong>! We will be happy to assist you.',
					'placeholder' => 'Feel free to start a <strong>new conversation</strong>! We will be happy to assist you.',
					'type' => 'textarea'
				),
				array(
					'id' => 'pending_greeting',
					'title' => '',
					'desc' => __( 'In chat', 'lcx' ) . ' (' .__( 'When the visitor is pending an operator to accept chat.', 'lcx' ) . ') <br><small>' . __( 'Custom variables', 'lcx' ) . ': {time}</small>',
					'default' => 'An advisor will be with you in {time}.',
					'placeholder' => 'An advisor will be with you in {time}.',
					'type' => 'textarea'
				),*/
				array(
					'id' => 'welcome_msg',
					'title' => __( 'Welcome message', 'lcx' ) . '<br><p class="description">' . __( 'This message is sent for the first conversation of visitors automatically.', 'lcx' ) .'</p>',
					'desc' => '<small>' .__( 'Custom variables', 'lcx' ) . ': {operatorName}</small>',
					'default' => "Hi, my name is {operatorName}. It'll be just a moment while I review the comments you provided.",
					'placeholder' => "Hi, my name is {operatorName}. It'll be just a moment while I review the comments you provided.",
					'type' => 'textarea'
				),
				array(
					'id' => 'closing_msg',
					'title' => __( 'Closing message', 'lcx' ) . '<br><p class="description">' . __( 'Operators might choose to send this message while ending a chat.', 'lcx' ) . '</p>',
					'default' => 'Thank you for contacting us today. Now that the conversation ended, you will receive the chat transcript in your email shortly. Have a nice day.',
					'placeholder' => 'Thank you for contacting us today. Now that the conversation ended, you will receive the chat transcript in your email shortly. Have a nice day.',
					'type' => 'textarea'
				),
				array(
					'id' => 'replies_in',
					'title' => __( 'Response time', 'lcx' ),
					'desc' => '<small>' .__( 'Custom variables', 'lcx' ) . ': {time}</small>',
					'default' => 'Typically replies in {time}.',
					'placeholder' => 'Typically replies in {time}.',
					'type' => 'textarea'
				),
				array(
					'id' => 'case_no',
					'title' => __( 'Case No.', 'lcx' ),
					'desc' => '<small>' . __( 'Custom variables', 'lcx' ) . ': {caseNo}</small>',
					'default' => 'Your case number is {caseNo}.',
					'placeholder' => 'Your case number is {caseNo}.',
					'type' => 'textarea'
				),
				array(
					'id' => 'end_chat',
					'title' => __( 'Chat buttons', 'lcx' ),
					'desc' => 'End chat',
					'default' => 'End chat',
					'placeholder' => 'End chat',
					'type' => 'text'
				),
				array(
					'id' => 'solved',
					'title' => '',
					'desc' => 'Solved',
					'default' => 'Solved',
					'placeholder' => 'Solved',
					'type' => 'text'
				),
				array(
					'id' => 'not_solved',
					'title' => '',
					'desc' => 'Not solved',
					'default' => 'Not solved',
					'placeholder' => 'Not solved',
					'type' => 'text'
				),
				array(
					'id' => 'chatStatus_close',
					'title' => __( 'Chat statuses', 'lcx' ),
					'desc' => 'Closed',
					'default' => 'Closed',
					'placeholder' => 'Closed',
					'type' => 'text'
				),
				array(
					'id' => 'chatStatusMsgs_close',
					'title' => __( 'Chat status messages', 'lcx' ),
					'desc' => __( 'Closed chat', 'lcx' ),
					'default' => 'This chat has ended.',
					'placeholder' => 'This chat has ended.',
					'type' => 'text'
				)
			)
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'ntf',
			'section_title' => __( 'Notifications', 'lcx' ),
			'section_order' => 70,
			'fields' => array(
				array(
					'id' => 'newMsg_wtitle',
					'title' => __( 'Window title', 'lcx' ),
					'default' => '%s says...',
					'placeholder' => '%s says...',
					'desc' => __( 'It appears on visitor\'s window title when operator replies.', 'lcx' ),
					'type' => 'text'
				),
				array(
					'id' => 'voted',
					'title' => __( 'Chats', 'lcx' ),
					'default' => 'Thank you for your review!',
					'placeholder' => 'Thank you for your review!',
					'type' => 'text'
				),
				array(
					'id' => 'sending',
					'title' => __( 'Messages', 'lcx' ),
					'default' => 'Sending...',
					'placeholder' => 'Sending...',
					'type' => 'text'
				),
				array(
					'id' => 'notSeen',
					'title' => '',
					'default' => 'Not seen yet',
					'placeholder' => 'Not seen yet',
					'type' => 'text'
				),
				array(
					'id' => 'seen',
					'title' => '',
					'default' => 'Seen',
					'placeholder' => 'Seen',
					'type' => 'text'
				),
				array(
					'id' => 'conn',
					'title' => __( 'Connection', 'lcx' ),
					'default' => 'Connecting...',
					'placeholder' => 'Connecting...',
					'type' => 'text'
				),
				array(
					'id' => 'connected',
					'title' => '',
					'default' => 'Connected!',
					'placeholder' => 'Connected!',
					'type' => 'text'
				),
				array(
					'id' => 'reconn',
					'title' => '',
					'default' => 'Reconnecting. Please wait...',
					'placeholder' => 'Reconnecting. Please wait...',
					'type' => 'text'
				),
				array(
					'id' => 'no_conn',
					'title' => '',
					'default' => 'No internet connection!',
					'placeholder' => 'No internet connection!',
					'type' => 'text'
				),
				array(
					'id' => 'reqFields',
					'title' => __( 'Forms', 'lcx' ),
					'default' => 'Please fill out all required fields!',
					'placeholder' => 'Please fill out all required fields!',
					'type' => 'text'
				),
				array(
					'id' => 'invEmail',
					'title' => '',
					'default' => 'Email is invalid!',
					'placeholder' => 'Email is invalid!',
					'type' => 'text'
				),
				array(
					'id' => 'optional',
					'title' => '',
					'default' => 'Optional',
					'placeholder' => 'Optional',
					'type' => 'text'
				),
				array(
					'id' => 'send_success',
					'title' => '',
					'default' => 'Your message has been sent successfully!',
					'placeholder' => 'Your message has been sent successfully!',
					'type' => 'text'
				),
				array(
					'id' => 'smth_wrong',
					'title' => '',
					'default' => 'Something went wrong!  Please try again.',
					'placeholder' => 'Something went wrong!  Please try again.',
					'type' => 'text'
				),
				array(
					'id' => 'you',
					'title' => __( 'Others', 'lcx' ),
					'default' => 'You',
					'placeholder' => 'You',
					'type' => 'text'
				),
			)
		),



		array(
			'tab_id' => 'msgs',
			'section_id' => 'email',
			'section_title' => __( 'Emails', 'lcx' ),
			'section_order' => 80,
			'fields' => array(
				array(
					'id' => 'notify_visitor_subject',
					'title' => __( 'Notify visitor email', 'lcx' ),
					'desc' => __( 'Subject', 'lcx' ),
					'default' => '%s replied your message.',
					'placeholder' => '%s replied your message.',
					'type' => 'text'
				),
				array(
					'id' => 'notify_visitor_title',
					'title' => '',
					'default' => '%s replied',
					'placeholder' => '%s replied',
					'type' => 'text'
				),
				array(
					'id' => 'chat_logs_subject',
					'title' => __( 'Chat logs email', 'lcx' ),
					'desc' => __( 'Subject', 'lcx' ) .'<br><small>' .__( 'Custom variables', 'lcx' ) . ': {caseNo} {siteName}</small>',
					'default' => '[{siteName}] Your chat transcript - {caseNo}.',
					'placeholder' => '[{siteName}] Your chat transcript - {caseNo}.',
					'type' => 'text'
				),
			)
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'forms',
			'section_title' => __( 'Forms', 'lcx' ),
			'section_order' => 90,
			'fields' => array(
				array(
					'id' => 'name',
					'title' => __( 'Name field', 'lcx' ),
					'default' => 'Name',
					'placeholder' => 'Name',
					'type' => 'text'
				),
				array(
					'id' => 'email',
					'title' => __( 'Email field', 'lcx' ),
					'default' => 'Email',
					'placeholder' => 'Email',
					'type' => 'text'
				),
				array(
					'id' => 'phone',
					'title' => __( 'Phone field', 'lcx' ),
					'default' => 'Phone number',
					'placeholder' => 'Phone number',
					'type' => 'text'
				),
				array(
					'id' => 'company_name',
					'title' => __( 'Company name field', 'lcx' ),
					'default' => 'Company name',
					'placeholder' => 'Company name',
					'type' => 'text'
				),
				array(
					'id' => 'reply',
					'title' => __( 'Reply box placeholder', 'lcx' ),
					'default' => 'Type your message',
					'placeholder' => 'Type your message',
					'type' => 'text'
				),/*
				array(
					'id' => 'subs_footnote',
					'title' => __( 'Subscription footnote', 'lcx' ),
					'default' => 'No spam. It is only for notifications.',
					'placeholder' => 'No spam. It is only for notifications.',
					'type' => 'text'
				),*/
				array(
					'id' => 'ask_question',
					'title' => __( 'Ask question', 'lcx' ),
					'default' => 'Please describe your issue...',
					'placeholder' => 'Please describe your issue...',
					'type' => 'text'
				),
				array(
					'id' => 'save_btn',
					'title' => __( 'Save button', 'lcx' ),
					'default' => 'Save',
					'placeholder' => 'Save',
					'type' => 'text'
				),
				array(
					'id' => 'submit_btn',
					'title' => __( 'Send button', 'lcx' ),
					'default' => 'Send a message',
					'placeholder' => 'Send a message',
					'type' => 'text'
				),
				array(
					'id' => 'start_chat_btn',
					'title' => __( 'Start chat button', 'lcx' ),
					'default' => 'Start chat',
					'placeholder' => 'Start chat',
					'type' => 'text'
				),
			)
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'others',
			'section_title' => __( 'Others', 'lcx' ),
			'section_order' => 100,
			'fields' => array(
				array(
					'id' => 'cnv',
					'title' => 'Conversations',
					'default' => 'Conversations',
					'placeholder' => 'Conversations',
					'type' => 'text'
				),
				array(
					'id' => 'cnvWith',
					'title' => '',
					'desc' => '%s will be replaced with your site name.',
					'default' => 'with %s',
					'placeholder' => 'with %s',
					'type' => 'text'
				),
				array(
					'id' => 'noCnv',
					'title' => '',
					'default' => 'No conversations found.',
					'placeholder' => 'No conversations found.',
					'type' => 'text'
				),
				array(
					'id' => 'new_cnv',
					'title' => '',
					'default' => 'New conversation',
					'placeholder' => 'New conversation',
					'type' => 'text'
				),
				array(
					'id' => 'chat_logs',
					'title' => 'Chat logs',
					'default' => 'Chat logs',
					'placeholder' => 'Chat logs',
					'type' => 'text'
				),
				array(
					'id' => 'case_no',
					'title' => 'Case no.',
					'default' => 'Case no.',
					'placeholder' => 'Case no.',
					'type' => 'text'
				),
			)
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'date',
			'section_title' => __( 'Date &amp; Time', 'lcx' ),
			'section_order' => 110,
			'fields' => array(
				array(
					'id' => 'prefix',
					'title' => __( 'Time ago strings', 'lcx' ),
					'desc' => 'Prefix (' . __( 'Optional', 'lcx' ) . ')',
					'default' => '',
					'placeholder' => '',
					'type' => 'text'
				),
				array(
					'id' => 'suffix',
					'title' => '',
					'desc' => 'Suffix (' . __( 'Optional', 'lcx' ) . ')',
					'default' => ' ago',
					'placeholder' => ' ago',
					'type' => 'text'
				),
				array(
					'id' => 'seconds',
					'title' => '',
					'default' => 'Just now',
					'desc' => 'Just now',
					'placeholder' => 'Just now',
					'type' => 'text'
				),
				array(
					'id' => 'minute',
					'title' => '',
					'desc' => 'About a minute',
					'default' => 'About a minute',
					'placeholder' => 'About a minute',
					'type' => 'text'
				),
				array(
					'id' => 'minutes',
					'title' => '',
					'desc' => '%d minutes',
					'default' => '%d minutes',
					'placeholder' => '%d minutes',
					'type' => 'text'
				),
				array(
					'id' => 'hour',
					'title' => '',
					'desc' => 'about an hour',
					'default' => 'about an hour',
					'placeholder' => 'about an hour',
					'type' => 'text'
				),
				array(
					'id' => 'hours',
					'title' => '',
					'desc' => '%d hours',
					'default' => '%d hours',
					'placeholder' => '%d hours',
					'type' => 'text'
				),
				array(
					'id' => 'day',
					'title' => '',
					'desc' => 'a day',
					'default' => 'a day',
					'placeholder' => 'a day',
					'type' => 'text'
				),
				array(
					'id' => 'days',
					'title' => '',
					'desc' => '%d days',
					'default' => '%d days',
					'placeholder' => '%d days',
					'type' => 'text'
				),
				array(
					'id' => 'month',
					'title' => '',
					'desc' => 'about a month',
					'default' => 'about a month',
					'placeholder' => 'about a month',
					'type' => 'text'
				),
				array(
					'id' => 'months',
					'title' => '',
					'desc' => '%d months',
					'default' => '%d months',
					'placeholder' => '%d months',
					'type' => 'text'
				),
				array(
					'id' => 'year',
					'title' => '',
					'desc' => 'about a year',
					'default' => 'about a year',
					'placeholder' => 'about a year',
					'type' => 'text'
				),
				array(
					'id' => 'years',
					'title' => '',
					'desc' => '%d years',
					'default' => '%d years',
					'placeholder' => '%d years',
					'type' => 'text'
				),
				array(
					'id' => 'today',
					'title' => __ ( 'Date', 'lcx' ),
					'default' => 'Today',
					'placeholder' => 'Today',
					'type' => 'text'
				),
				array(
					'id' => 'yesterday',
					'title' => '',
					'default' => 'Yesterday',
					'placeholder' => 'Yesterday',
					'type' => 'text'
				),
			)
		),

		array(
			'tab_id' => 'msgs',
			'section_id' => 'response_time',
			'section_title' => __( 'Response Time', 'lcx' ),
			'section_order' => 120,
			'fields' => array(
				array(
					'id' => 'aFewMins',
					'title' => __( 'Typically replies within...', 'lcx' ),
					'default' => 'a few minutes',
					'placeholder' => 'a few minutes',
					'type' => 'text'
				),
				array(
					'id' => 'in15min',
					'title' => '',
					'default' => '15 minutes',
					'placeholder' => '15 minutes',
					'type' => 'text'
				),
				array(
					'id' => 'in30min',
					'title' => '',
					'default' => '30 minutes',
					'placeholder' => '30 minutes',
					'type' => 'text'
				),
				array(
					'id' => 'in1hour',
					'title' => '',
					'default' => 'an hour',
					'placeholder' => 'an hour',
					'type' => 'text'
				),
				array(
					'id' => 'inFewHours',
					'title' => '',
					'default' => 'a few hour',
					'placeholder' => 'a few hour',
					'type' => 'text'
				),
				array(
					'id' => 'inDay',
					'title' => '',
					'default' => 'a day',
					'placeholder' => 'a day',
					'type' => 'text'
				),
				array(
					'id' => '1BDay',
					'title' => '',
					'default' => '1 business day',
					'placeholder' => '1 business day',
					'type' => 'text'
				),
				array(
					'id' => '2BDay',
					'title' => '',
					'default' => '2 business days',
					'placeholder' => '2 business days',
					'type' => 'text'
				),
				array(
					'id' => '3BDay',
					'title' => '',
					'default' => '3 business days',
					'placeholder' => '3 business days',
					'type' => 'text'
				),
			)
		),

		array(
			'tab_id' => 'realtime',
			'section_id' => 'firebase',
			'section_title' => 'Firebase',
			'section_description' => '<table style="width:100%;"><tr><td style="width:100%; font-size: 15px; line-height: 1.4em; padding-right: 30px" valign="top"><ol style="margin:0 0 30px 15px;">
							<li>' . sprintf( 'Create new <a href="%s" target="_blank">Firebase</a> project', 'https://console.firebase.google.com' ) . '</li>
							<li>Click <span class="dashicons dashicons-admin-generic"></span> and select <strong>Project Settings</strong></li>
							<li>Change "Public-facing name" as "Whatsapp Live Chat" (optional, but its useful for later.)</li> 
							<li>Find <strong>Project ID</strong> and <strong>Web API key</strong> and copy/paste to related fields on this page below.</li>
							<li>Go to "Service accounts" tab.</li>
							<li>Click "Generate New Private key" button and download JSON file.</li>
							<li>Open downloaded JSON file and copy/paste the content into <strong>Private Key</strong> field on this page below.</li>
							<li>Go to Authentication > Sign-in Methods tab.</li>
							<li>Enable:
								<ul style="margin: 5px 0 10px 15px; list-style-type: disc; list-style-position: inside;">
									<li>Email/Password</li>
									<li>Anonymous</li>
									<li>Google</li>
								</ul>
							</li>
							<li>Find <strong>Authorized domains</strong>  on the same page and add <code class="lcx-red">' . $domain . '</code>.</li>
							<li>Go to your <a href="https://console.firebase.google.com/project/_/database/my-domain-name/rules" target="_blank">Database Rules</a> of your Firebase project and change the content with the one in "Security Rules" field below.</li>
							<li>Now go to your chat console and sign-in.</li>
						</ol></td><td valign="top"><iframe width="390" height="219" src="https://www.youtube-nocookie.com/embed/7P3WuML3_dg?rel=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe><p><a href="https://youtu.be/7P3WuML3_dg" target="_blank">View video</a> <span class="dashicons dashicons-external"></span></p><div class="lcx-ntf lcx-warn" style="display:none">For some reason, if you ever want to stop using this plugin or do a <strong>fresh authorization</strong>, you can revoke access by visiting: <a href="https://security.google.com/settings/security/permissions" target="_blank">your Google account settings</a>.</div></td></tr></table>
						',
			'section_order' => 20,
			'fields' => array(
				array(
					'id' => 'email',
					'title' => 'Your Google Email <span class="lcx-req">*</span>',
					'desc' => __( 'Which email do you use to login your Firebase account?', 'lcx' ),
					'placeholder' => '',
					'type' => 'text'
				),
				array(
					'id' => 'projectId',
					'title' => 'Project ID <span class="lcx-req">*</span>',
					'placeholder' => 'i.e. my-project-3-4d23a',
					'type' => 'text'
				),
				array(
					'id' => 'apiKey',
					'title' => 'Web API Key <span class="lcx-req">*</span>',
					'placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxxxxxxxxx',
					'type' => 'text'
				),
				array(
					'id' => 'private_key',
					'title' => 'Private Key <span class="lcx-req">*</span>',
					'class' => 'is-full',
					'desc' => 'To find your Private Key: <br>
							* Go to <a href="https://console.firebase.google.com/project/_/settings/serviceaccounts/adminsdk" target="_blank">Service Accounts</a><br> 
							* Click <strong>Generate New Private Key</strong><br>
							* Open JSON file you downloaded in any text editor <br>
							* Copy/paste the content of file to "Private Key" field above.',
					'placeholder' => '',
					'type' => 'textarea'
				),
				array(
					'id' => 'security_rules',
					'title' => 'Security rules <span class="lcx-req">*</span>',
					'default' => '<p>Go to your <a href="https://console.firebase.google.com/project/_/database/my-domain-name/rules" target="_blank">Database Rules</a> of your Firebase project and change the content with the one below:</p><textarea readonly style="width:100%;height: 200px; margin-top: 15px">' . file_get_contents( LCX_PATH . '/data/rules.json' ). '</textarea>',
					'type' => 'custom'
				)
			)
		),

		array(
			'tab_id' => 'advanced',
			'section_id' => 'server',
			'section_title' => 'Server Info',
			'section_order' => 10,
			'fields' => array(
				array(
					'id' => '_phpinfo',
					'title' => 'PHP version',
					'default' => phpversion(),
					'type' => 'custom'
				),
				array(
					'id' => '_mysqlinfo',
					'title' => 'MySQL version',
					'default' => $wpdb->get_var( 'select version()' ),
					'type' => 'custom'
				),
				array(
					'id' => '_upldirinfo',
					'title' => 'Upload dir',
					'default' => fn_lcx_get_upload_dir_var( 'baseurl', '/lcx' ),
					'type' => 'custom'
				),
			)
		),

		array(
			'tab_id' => 'advanced',
			'section_id' => 'secure',
			'section_title' => 'Security',
			'section_order' => 20,
			'fields' => array(
				array(
					'id' => 'dev_mode',
					'title' => 'Developer Mode',
					'placeholder' => 'Developer Mode',
					'desc' => 'Active <p class="description">Uses original JS/CSS files instead of compiled ones.</p>',
					'type' => 'checkbox'
				),
				array(
					'id' => 'proxy',
					'title' => 'Reverse proxy IPs',
					'placeholder' => 'Reverse proxy IPs',
					'desc' => 'If your server is behind a reverse proxy, you must whitelist the proxy IP addresses from which WordPress should trust the HTTP_X_FORWARDED_FOR header in order to properly identify the visitor\'s IP address. Comma-delimited, e.g. \'10.0.1.200,10.0.1.201\'',
					'type' => 'text'
				),
			)
		)
	);

	return $opts;
}



/**
 * Render capabilities
 */
function __fn_lcx_render_caps() {
	$output = '';
	$lcx_caps = fn_lcx_get_capabilities();

	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
	}
	$roles = get_editable_roles();

	foreach( $roles as $role_name => $role ) {
		$output .= '<strong>' . $role['name'] . '</strong><ul>';

		$i=0;
		foreach( $lcx_caps as $cap => $cap_name ) {
			$checked = ( array_key_exists( $cap, $role['capabilities'] ) );
			$field_id = 'lcx_role_'.$role_name . '_' . $cap;
			$disabled = ( $role_name == 'administrator' && $cap == 'lcx_admin' ) ? 'disabled':'';

			$output .= '<li>'
							.'<label for="'.$field_id.'">'
							.'<input type="checkbox" name="op_caps['.$role_name.'][]" id="' . $field_id . '" ' . checked( true, $checked, 0 ) . ' value="'.$cap.'" ' . $disabled . '> '. $cap_name
							.'</label> <small style="display:inline-block; background: #efefef; border-radius:2px; color: #999; padding:2px 5px; margin-left: 5px;">' . $cap . '</small>'
					 . '</li>';

			$i++;
		}

		$output .= '</ul>';
	}
	return $output;
}