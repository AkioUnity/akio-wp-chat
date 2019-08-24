/**
 * Live Chat X, by Screets.
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

'use strict';

class ScreetsPopover {

	constructor( target, opts ) {
		
		const defaults = {
			event: 'click', // Trigger event
			size: 220,
			className: 'screets-popover',
			classActive: 'screets-show',
			content: '',
			offset: [5, 15],
			zindex: 99
		};

		// Setup and establish options
		this.opts = defaults;
		for( const k in opts ) {
			this.opts[k] = opts[k]; 
		}

		// Useful data
		this._currentStatus = 'hide';

		// Common objects
		this.$target = target;
		this.$obj = document.createElement( 'div' );
		
		if( !this.$target ) return;

		// Get position of target object
		const pos = this.$target.getBoundingClientRect();

		// Style popover
		this.$obj.style.position = 'absolute';
		this.$obj.style.width = this.opts.size + 'px';
		this.$obj.style.zIndex = this.opts.zindex;
		this.$obj.className = this.opts.className;
		this.$obj.innerHTML = this.opts.content;

		// Update object
		this.$obj.setAttribute( 'data-popover-targetid', Math.random() * (9999999 - 1) + 1 ); // 1-9999999

		// Insert just before body content
		document.body.appendChild( this.$obj );

		// Position element
		this._position();

		// Listen showing event
		this.$target.addEventListener( this.opts.event, (e) => {
			e.preventDefault();

			if( this._status() !== 'show' ) {
				this.show();
			} else {
				this.hide();
			}
		});

		// Listen window resize
		window.addEventListener( 'resize', this._position.bind(this), true );

		// 
		// Listen outside clicks
		// 
		const _obj = this.$obj;
		const _target = this.$target;
		const fn_hide = () => {
			this.hide();
		};

		document.addEventListener( 'click', function( e ) {
			if ( !_obj.contains( e.target ) && !_target.contains( e.target ) ) {
				fn_hide();
			}
		});

	}

	/**
	 * Position the popover.
	 */
	_position() {

		const pos = this.$target.getBoundingClientRect();

		this.$obj.style.top = pos.top + pos.height + this.opts.offset[0] + 'px';
		this.$obj.style.left = ( pos.left - this.opts.offset[1] ) + 'px';

		const objX = this.opts.size + pos.left;

		if( objX > window.innerWidth ) {
			// this.$obj.style.left = '';
			this.$obj.style.left = ( pos.right - this.opts.size - this.opts.offset[1] ) + 'px';
		}

	}

	/**
	 * Change or get popover status. It is good for custom events.
	 */
	_status( status ) {
		
		if( !status ) return this._currentStatus;

		// Update popover data
		this.$target.setAttribute( 'data-screets-popover', status );

		// Update status
		this._currentStatus = status;

	}

	/**
	 * Show up popover.
	 */
	show() {

		// Show up
		this.$obj.classList.add( this.opts.classActive );

		// Update status
		this._status( 'show' );
	}

	/**
	 * Hide popover.
	 */
	hide() {

		// Hide
		this.$obj.classList.remove( this.opts.classActive );

		// Update status
		this._status( 'hide' );
		
	}

	/**
	 * Get all popovers.
	 */
	getAll( callback ) {
		let _targetid = '';
		const popovers = document.querySelectorAll( '.' + this.opts.className );

		if( popovers ) {
			for( let i=0; i<popovers.length; i++ ) {
				callback( popovers[i] );
			}
		}
	}

	/**
	 * Hide all popovers.
	 */
	hideAll() {

		this.getAll( ( $popover ) => {
			this.$obj = $popover;
			this.$target = document.getElementById( $popover.getAttribute( 'data-popover-targetid' ) );

			// Hide now
			setTimeout( () => { this.hide(); }, 0);
		});
		
	}

	/**
	 * Re-position popovers.
	 */
	repositionAll() {
		this.getAll( ( $popover ) => {
			const _targetid = $popover.getAttribute( 'data-popover-targetid' );

			if( _targetid ) {
				this.$obj = $popover;
				this.$target = document.getElementById( _targetid );
				this._position();
			}
		});
	}

}