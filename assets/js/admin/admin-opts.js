/*!
 *
 * Heino, All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

'use strict';




class lcx_AdminOpts {

	constructor() {

		this._ui();
	}

	_ui() {

		const selectBtns = document.getElementsByClassName( 'lcx-browse' );
		const hash = window.location.hash.substr(1) || sessionStorage.getItem( 'lcx-adminOpts-lastTab' ) || '';

		const fn_resetTab = () => {
			this.resetTab();
		};

		// 
		// Auto-messages.
		// 
		this._autoMsgs();

		// 
		// Manage tabs.
		// 
		const tabs = document.getElementsByClassName( 'lcx-tab-link' );
		for( let i=0; i<tabs.length; i++ ) {
			tabs[i].addEventListener( 'click', function(e) {
				e.preventDefault();

				// Deactivate last active tab
				fn_resetTab();

				const tabHash = this.getAttribute('href').substring(1);

				// Activate current tab
				this.classList.add( 'nav-tab-active' );
				document.getElementById( tabHash ).classList.add( 'lcx-tab--active' );

				// Save the last tab
				sessionStorage.setItem( 'lcx-adminOpts-lastTab', tabHash );
			});
		}

		if( hash ) {
			fn_resetTab();

			const tabLink = document.querySelector( '[href="#' + hash +'"]' );
			if( tabLink ) {
				tabLink.classList.add( 'nav-tab-active' );
				document.getElementById( hash ).classList.add( 'lcx-tab--active' );
			}
		}

		const fn_trigger_dynamic_fields = function( $wrap ) {
			
		};
		const groups = document.querySelectorAll( '.lcx-group__row' );
		if( groups ) {
			for( const group of groups ) {
				fn_trigger_dynamic_fields( document.getElementById( group.id ) );
			}
		}

		const self = this;
		jQuery( function($) {
			
			// 
			// Select media buttons.
			//
			let mediaFrame;
			var fn_openMedia = function(e) {
				e.preventDefault();

				if( mediaFrame ) {
					mediaFrame.open();
					return;
				}

				mediaFrame = wp.media({
					title: 'Select or upload new one',
					button: {
						text: 'Select'
					},
					multiple: false
				});


				mediaFrame.on( 'select', () => {

					const attachment = mediaFrame.state().get('selection').first().toJSON();

					this.previousElementSibling.value = attachment.url;

				});

				mediaFrame.open();
			};


			if( selectBtns ) {

				for( let i=0; i<selectBtns.length; i++ ) {
					selectBtns[i].addEventListener( 'click', fn_openMedia );
				}
			}

			/**
	         * Reindex a group of repeatable rows.
	         *
	         * @param arr $group
	         */
			const fn_reindex_group = ( $group ) => {
				if( $group.find(".lcx-group__row").length == 1 ) {
	                $group.find(".lcx-group__row-remove").hide();
	            } else {
	                $group.find(".lcx-group__row-remove").show();
	            }

	            $group.find(".lcx-group__row").each(function(index) {

	                $(this).removeClass('alternate');

	                if(index%2 == 0)
	                    $(this).addClass('alternate');

	                $(this).find("input").each(function() {
	                    var name = jQuery(this).attr('name'),
	                        id = jQuery(this).attr('id');

	                    if(typeof name !== typeof undefined && name !== false)
	                        $(this).attr('name', name.replace(/\[\d+\]/, '['+index+']'));

	                    if(typeof id !== typeof undefined && id !== false)
	                        $(this).attr('id', id.replace(/\_\d+\_/, '_'+index+'_'));

	                });

	                $(this).find('.lcx-group__row-index span').html( index );
	                $(this).attr( 'id', $(this).attr('id').replace(/\_\d/, '_'+index));
	            });
			};

			// 
			// Group type options.
			// 
			$(document).on( 'click', '.lcx-group__row-add', function() {

                var $group = $(this).closest('.lcx-group'),
                    $row = $(this).closest('.lcx-group__row'),
                    template_name = $(this).data('template'),
                    $template = $('#'+template_name).html();

                $row.after( $template );

                fn_reindex_group( $group );

                fn_trigger_dynamic_fields( document.getElementById( $row.next().attr( 'id' ) ) );

                return false;

            });

            // remove row
            $(document).on('click', '.lcx-group__row-remove', function() {

                var $group = jQuery(this).closest('.lcx-group'),
                    $row = jQuery(this).closest('.lcx-group__row');

                $row.remove();

                fn_reindex_group( $group );

                return false;

            });

		});

		// 
		// Colors.
		// 
		var colorOpts = {
			// a callback to fire whenever the color changes to a valid color
			change: function( event, ui ) {},

			// a callback to fire when the input is emptied or an invalid color
			clear: function() {},

			// hide the color picker controls on load
			hide: true,
			// show a group of common colors beneath the square
			// or, supply an array of colors to customize further
			palettes: [  '#ea3c3b', '#7e8bfe', '#fffc79', '#ff80a7', '#212121' ]
		};

		(function ($, document) {
			jQuery( '.lcx-color' ).wpColorPicker( colorOpts );
		}(jQuery, document));
	}

	_autoMsgs() {
		let lastMsgType;
		const autoMsgType = document.getElementById( 'chats_automsg_type' );

		if( !autoMsgType )
			return;
		
		autoMsgType.addEventListener( 'change', function(e) {
			if( lastMsgType )
				document.getElementById( `lcx-desc-${lastMsgType}` ).style.display = 'none';


			lastMsgType = autoMsgType.options[autoMsgType.selectedIndex].value;
			document.getElementById( `lcx-desc-${lastMsgType}` ).style.display = 'block';
		});
		const ev = document.createEvent( 'HTMLEvents' );
		ev.initEvent( 'change', false, true );
		autoMsgType.dispatchEvent( ev );
	}

	// Deactivate last tab
	resetTab() {
		const activeNavTab = document.querySelector( '.nav-tab-active' );
		const lcxTabActive = document.querySelector( '.lcx-tab--active' )
		if( !activeNavTab || ! lcxTabActive )
			return;

		activeNavTab.classList.remove( 'nav-tab-active' );
		lcxTabActive.classList.remove( 'lcx-tab--active' );
	}
}

document.addEventListener( 'DOMContentLoaded', function() {
	const lcx_adminOpts = new lcx_AdminOpts();
});