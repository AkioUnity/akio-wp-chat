/*!
 * Live Chat X, by Screets.
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

class nightBird {

	constructor( opts, strings ) {

		const defaultStrs = {
			conn: 'Connecting...',
			reconn: 'Reconnecting. Please wait...',
			noConn: 'No internet connection!',
			connected: 'Connected successfully!',
			reqFields: 'Please fill out all required fields!',
			invEmail: 'Email is invalid!',
			noEmail: 'No email has provided yet.',
			edit: 'Edit',
			you: 'You',
			saving: 'Saving...',
			saved: 'Saved!',
			updated: 'Updated successfully!',
			useHere: 'Use here',
			errDuplicateSess: 'Console is open in different window.',
			logsSent: 'Chat transcript has sent to the visitor.',
			errLogsSent: 'Chat transcript couldn\'t be sent to the visitor. Please check your <a href="%s" target="_blank">email settings</a>.',
			cantEndChat: 'This chat can\'t be ended!  Try refresh page.',
			newMsg: '%s says:',
			setupDB: 'You should configure real-time database from <a href="%s">here</a>! 😌',
			newChat: '%s started new chat!',
			confirmDelete: 'This item will be DELETED immediately. You can\'t undo this action',
			time: {
				prefix: "",
		        suffix: " ago",
		        seconds: "less than a minute",
		        minute: "about a minute",
		        minutes: "%d minutes",
		        hour: "about an hour",
		        hours: "about %d hours",
		        day: "a day",
		        days: "%d days",
		        month: "about a month",
		        months: "%d months",
		        year: "about a year",
		        years: "%d years"
			},
			timeShort: {
				prefix: "",
		        suffix: "",
		        seconds: "1m",
		        minute: "1m",
		        minutes: "%dm",
		        hour: "1h",
		        hours: "%dh",
		        day: "1d",
		        days: "%dd",
		        month: "1mh",
		        months: "%dmh",
		        year: "1y",
		        years: "%dy"
			}
		};

		// Setup and establish options
		this.opts = opts;	
		
		// Establish strings
		this.str = defaultStrs;
		if( typeof strings === 'object' ) {
			for( const k in strings ) { this.str[k] = strings[k]; }
		}

		// Applications
		this.event =  lcx_events;

		// Useful data
		this._wstate = true;
		this._wtitle = document.title;
		this._win = {};
		this._onlineVisitors = {};
		this._unreadChats = []; // unread chats
		this._countNewMsgs = {};
		this._chatGroups = {};
		this._chatid = ''; // current chat
		this._chatids = {}; // active chats
		this._listenItems = [];
		this._listenChats = []; // the chat where we get notifications
		this._newChats = {}; // not initiated chats
		this._chatsList = null;
		this._archiveList = null;

		if( this.opts.autoinit ) {
			document.addEventListener( 'DOMContentLoaded', () => {
				this.init();
			}, false);
		}
	}

	init() {
		// Useful objects
		this.$console = document.getElementById( 'nbird-console' );
		this.$signin = document.getElementById( 'nbird-signin' );
		this.$toggleOnline = document.getElementById( 'nbird-toggle-online' );
		this.$switchOnline = document.getElementById( 'nbird-switcher-online' );
		this.$btnProfile = document.getElementById( 'nbird-profile' );
		this.$btnSaveProfile = document.getElementById( 'btn-save-profile' );
		this.$ntfs = document.getElementById( 'nbird-ntfs' );
		this.$chats = document.getElementById( 'chats-list' );
		this.$archivedChats = document.getElementById( 'archived-chats-list' );
		this.$win0 = document.getElementById( 'win-0' );
		this.$win1 = document.getElementById( 'win-1' );

		// Fold admin menu
		document.body.classList.add( 'folded' );

		if( !this.opts.db || !this.opts.db.apiKey ) {
			this.$console.classList.add( 'disabled' );

			this.newNtf( 
				'error', 
				this.str.setupDB.replace( '%s', this.opts._optsurl + '#tab-realtime' ), 
				'setup'
			);
			return;
		}

		// Applications
		this.db = lcx_db;

		// Listen database events
		this._dbEvents();

		// Listen UI
		this._uiEvents();

		// Initialize the database
		this.db.init();

		// Listen settings wrap actions
		this.actions( document.getElementById( 'nbird-modal-profile' ) );
	}

	/**
	 * Listen actions buttons.
	 */
	actions( wrap ) {
		wrap = wrap || document;
		const btns = wrap.querySelectorAll( '.lcx-action' );

		const fn_run = function(e) {
			e.preventDefault();

			const type = this.getAttribute( 'data-action' );

			switch( type ) {
				case 'updateUserInput':
					const input = this.parentNode.querySelector( '.' + this.getAttribute( 'href' ).substring(1) );
					input.classList.toggle( 'd-hide' );
					input.focus();

					input.addEventListener( 'keydown', function(e) {
						if( e.keyCode === 13 ) {
							fn_updateEmailForNtf( this.value, ( success ) => {
								if( success ) {
									this.classList.add('d-hide');
								}
							});
						}
					});
					break;

				case 'resetOps':
					if( confirm( 'Are you sure you want to reset operators data?' ) )
						fn_resetOps();

					break;
			}

			return false;
		};

		const fn_updateEmailForNtf = ( email, cb ) => {
			if( !NBird.isEmail( email ) ) {
				this.newNtf( 'error', this.str.invEmail, 'userProfile', true );
				return;
			}

			const uid = this._win.rawid;
			if( uid )
				this.db.updateUser( uid, { emailForNtf: email }, () => {
					this.newNtf( 'success', this.str.updated, 'userProfile', true );
					cb( true );
				});
		};

		const fn_resetOps = () => {
			this.db.db.ref( 'onlineOps' ).set( null );
			this.db.db.ref( 'operators' ).set( null, () => {
				this.newNtf( 'error', this.str.reconn, 'auth', true );
				this.$console.classList.add( 'disabled' );
				window.location.reload( true );
				this.closeModals();
			});
		};

		if( btns ) {
			for( var btn of btns ) {
				btn.addEventListener( 'click', fn_run, false );
			}
		}
	}

	/**
	 * 
	 * ======= USER INTERFACE =======
	 *
	 */
	_uiEvents() {

		const fn_toggleSignin = () => {
			this.hideNtfGroup( 'auth' );
			this.db.toggleSignin( this.opts.authMethod, this.opts.db.token );
		};

		const fn_toggleOnline = ( isOnline ) => {
			this.db.toggleOnline( isOnline );
		};

		// 
		// Sign-in button.
		// 
		this.$signin.addEventListener( 'click', function(e) {
			e.preventDefault();

			this.disabled = true;
			fn_toggleSignin();
		});

		// 
		// Toggle online.
		// 
		if( this.$switchOnline ) {
			this.$switchOnline.addEventListener( 'change', function(e) {
				fn_toggleOnline( this.checked );
			});
		}

		// 
		// Complete installation.
		// 
		const fn_completeSetup = () => {
			this.db.setup( function() {
				window.location.reload(true);
			});
		};
		document.getElementById( 'lcx-complete-install' ).addEventListener( 'click', function(e) {
			e.preventDefault();

			this.disabled = true;
			this.classList.add('disabled');

			fn_completeSetup();
		});

		// 
		// Save settings.
		//
		this.$btnSaveProfile.addEventListener( 'click', (e) => {
			let settings = {};
			const elements = document.getElementById('form-settings').elements;

			this.$btnSaveProfile.disabled = true;

			for( var i=0, el; el = elements[i++]; ) {
				switch( el.nodeName ) {
					case 'INPUT':
					case 'TEXTAREA':
						if( el.type === 'checkbox' ) {
							settings[el.name] = el.checked || null;
						} else {
							settings[el.name] = el.value;
						}
						break;
				}
			}

			// Save settings now
			this.saveSettings( settings );

			return;
		});

		// 
		// Activate desktop notifications.
		// 
		if ( ( 'Notification' in window ) && Notification.permission !== 'granted' && Notification.permission !== 'denied' ) {
			const ntfAlert = document.getElementById( 'lcx-ntf-alert');
			ntfAlert.classList.remove( 'd-hide' );
			document.getElementById( 'lcx-activate-ntfs' ).addEventListener( 'click', (e) => {
				Notification.requestPermission( ( r ) => {
					if ( r === 'granted' ) {
						this.newDeskNtf( 
							'Hey', 
							'dn-activated', 
							'It is working! Good luck with sales :D', 
							'dn-activated', 
							10000 
						);
						 ntfAlert.classList.add( 'd-hide' );
					}
				});
			});
		}

		// 
		// Listen ending chat modal.
		// 
		const btnEndChat = document.getElementById( 'btn-endChat' );
		const btnEndChatWoMsg = document.getElementById( 'btn-endChatWoMsg' );
		const cbSendChatLogs = document.getElementById( 'cb-sendChatLogs' );
		const loaderEndChat = document.getElementById( 'loader-endChat' );

		const fn_sendLogs = ( chatid, chat, cb ) => {
			const user = this.db.$_users[chat.visitorid] || {};
			const msgs = this.db.$_msgs[chatid];
			let logs = [ '<div class="chat-logs">' ];
			let msg, author;

			if( !user && !user.emailForNtf ) {
				cb( false );
				return false;
			}

			// Prepare chat logs
			if( msgs ) {
				for( const id in msgs ) {
					msg = msgs[id];
					author = ( msg.platform === 'frontend' ) ? this.str.you : msg.name;
					logs.push( '<div class="msg-item"><span class="msg-time">', NBird.time( msg.date, this.opts.dateFormat ), ':</span> <span class="msg-author">', author, ':</span> <span class="msg-content">', NBird.renderMsg( msg.msg ), '</span></div>')
				}
			}

			logs.push( '</div>' );

			if( chat.opid && chat.opid in this.db.$_users ) {
				chat.opName = this.db.$_users[chat.opid].name;
			}

			NBird.post( 'sendChatLogs', 
				{
					chatid: chatid,
					email: user.emailForNtf,
					logs: logs.join(''),
					caseNo: chat.caseNo,
					opName: chat.opName,
					opPhotoURL: chat.opPhotoURL
				},
				this.opts.ajax,
				cb
			);
		};
		const fn_resetEndChat = function() {
			btnEndChat.classList.remove( 'disabled' );
			btnEndChatWoMsg.classList.remove( 'disabled' );
			loaderEndChat.classList.add( 'd-hide' );
		};

		const fn_endChat = ( chatid, sendClosingMsg ) => {
			this.db.endChat( chatid, () => {
				fn_resetEndChat();
				this.closeModals();

				if( sendClosingMsg )
					fn_sendClosingMsg( chatid );
			});
		};

		const fn_sendClosingMsg = ( chatid ) => {
			const closingMsg = document.getElementById( 'f-closing-msg' );

			if( closingMsg.value.length > 0 )
				this.db.pushMsg( chatid, closingMsg.value );
		};

		const fn_onEndingChat = ( r, sendClosingMsg ) => {
			// Successfully sent!
			if( !r.error && r.chatid) {
				fn_endChat( r.chatid, sendClosingMsg );

				this.newNtf( 'success', this.str.logsSent, 'logs', true );

			// Something went wrong!
			} else {
				this.newNtf( 
					'error', 
					this.str.errLogsSent.replace( '%s', this.opts._optsurl + '#tab-site' ), 
					'logs', 
					true
				);
				fn_resetEndChat();
				this.closeModals();
			}
		};

		btnEndChat.addEventListener( 'click', (e) => {
			e.preventDefault();

			const chatid = this._chatid || null;
			const chat = this.db.$_chats[chatid];

			btnEndChat.classList.add( 'disabled' );
			btnEndChatWoMsg.classList.add( 'disabled' );
			loaderEndChat.classList.remove( 'd-hide' );

			if( !chatid || !chat ) {
				this.newNtf( 'error', this.str.cantEndChat, 'endchat', true );
				fn_resetEndChat();
				this.closeModals();
				return false;
			}

			// End chat without sending chat logs
			if( !cbSendChatLogs.checked ) {
				fn_endChat( chatid, true );

			// End chat and send logs..
			} else {
				fn_sendLogs( chatid, chat, function(r) {
					fn_onEndingChat( r, true );
				});
			}
		});

		btnEndChatWoMsg.addEventListener( 'click', (e) => {
			e.preventDefault();

			const chatid = this._chatid || null;
			const chat = this.db.$_chats[chatid];

			btnEndChat.classList.add( 'disabled' );
			btnEndChatWoMsg.classList.add( 'disabled' );
			loaderEndChat.classList.remove( 'd-hide' );

			if( !chatid || !chat ) {
				this.newNtf( 'error', this.str.cantEndChat, 'endchat', true );
				fn_resetEndChat();
				this.closeModals();
				return false;
			}

			// End chat without sending chat logs
			if( !cbSendChatLogs.checked ) {
				fn_endChat( this._chatid );

			// End chat and send logs
			} else {
				fn_sendLogs( chatid, chat, fn_onEndingChat );
			}
		});

		// 
		// Listen time ago field.
		// 
		const fn_to = () => {
			NBird.timeago( null, this.str.timeShort );
			setTimeout( fn_to, 7000 ); // refresh every 7 seconds.
		};
		fn_to();

		// 
		// Listen window states.
		// 
		window.addEventListener( 'focus', () => { this._wstate = true; });
		window.addEventListener( 'blur', () => { this._wstate = false; });

		// 
		// Prevent auto-open modal popups on page load.
		// 
		this.closeModals();


		// 
		// Handle lists.
		// 
		this.lists();
	}

	/**
	 * Manage lists.
	 */
	lists() {
		/*let opts = {
			item: 'chats-list-tpl',
			listClass: 'side-list',
			pagination: true,
			valueNames: [
				'timeago',
				'name',
				{ data: ['id', 'status', 'chat-status' ] },
				{ name: 'itemLink', attr: 'href' },
				{ name: 'itemTime', attr: 'datetime' }
			]
		};
		this._chatsList = new List( 'chats-list-wrap', opts );

		opts = {
			item: 'archived-chats-list-tpl',
			listClass: 'side-list',
			valueNames: [
				'timeago',
				'name',
				'caseNo',
				{ name: 'undoLink', attr: 'href' },
				{ name: 'itemTime', attr: 'datetime' }
			]
		};
		this._archiveList = new List( 'archived-chats-list-wrap', opts );
		*/

		const fn_openList = ( id ) => {
			const current = document.getElementById( id );
			const last = current.parentNode.querySelector( '.list-tab-content.active' );

			if( last )
				last.classList.remove( 'active' );

			current.classList.add( 'active' );
		};

		// Listen list tabs
		const listTabs = document.querySelectorAll( '.list-tabs a' );
		if( listTabs ) {
			for( var link of listTabs ) {
				link.addEventListener( 'click', function(e) {
					e.preventDefault();
					fn_openList( this.getAttribute('href').substring(1) );

					const current = this.parentNode;
					const last = current.parentNode.querySelector( '.active' );

					if( last )
						last.classList.remove( 'active' );

					current.classList.add( 'active' );
				});
			}
		}
	}

	createItem( type, id, data ) {
		const itemid = type + '-item-' + id;
		const item = document.createElement( 'li' );
		
		item.id = itemid;
		item.setAttribute( 'data-id', id );
		item.setAttribute( 'data-status', data._status );

		if( type === 'visitor' ) {

			item.innerHTML = document.getElementById( 'chats-list-item-tpl' ).innerHTML;

			// Highlight the item if there is new chat
			if( this._newChats[id] && Object.keys( this._newChats[id] ).length > 0 )
				item.setAttribute( 'data-chat-status', data._status );

			item.querySelector( '.lcx-name' ).innerText = data.nickname || data.name;
			item.querySelector( '.lcx-timeago' ).setAttribute( 'datetime', Number( data.lastSeen/1000 ) );
			item.querySelector( '.lcx-item-link' ).setAttribute( 'href', '#'+id );

			this.$chats.insertBefore( item, this.$chats.firstChild );

		} else if( type === 'archivedChat' ) {

			item.innerHTML = document.getElementById( 'archived-chats-list-item-tpl' ).innerHTML;

			item.querySelector( '.lcx-name' ).innerText = data.nickname || data.name;
			item.querySelector( '.lcx-caseNo' ).innerText = data.caseNo;
			item.querySelector( '.lcx-timeago' ).setAttribute( 'datetime', Number( data.lastActive ) );
			item.querySelector( '.lcx-undo-link' ).setAttribute( 'href', '#'+id );
			
			this.$archivedChats.insertBefore( item, this.$archivedChats.firstChild );
		}

		// Invoke the event
		this.event.emit( 'newItem-'+type, item, id, data );

		return item;
	}
	updateItem( markType, itemid, val ) {

		const listItem = document.getElementById( itemid );

		if( !listItem )
			return;

		switch( markType ) {
			case 'newChat':
				listItem.setAttribute( 'data-chat-status', 'new' );
				break;

			case 'noNewChat':
				listItem.removeAttribute( 'data-chat-status' );
				break;
		}

		this.event.emit( 'updateItem-'+markType, listItem, itemid, val );
	}
	removeItem( type, id ) {
		const itemindex = this._listenItems.indexOf( id );
		if( itemindex !== -1 )
			this._listenItems.splice( itemindex, 1 );

		switch( type ) {
			case 'visitor':
				
				break;

			case 'archivedChat':
				NBird.delObj( 'archivedChat-item-' + id );
				break;
		}
	}

	/**
	 * Manage windows.
	 */
	openWin( type, id, content, cb ) {
		const winid = 'nbird-win-' + id;
		const sidewinid = 'nbird-sideWin-' + id;
		const win = document.createElement( 'div' );
		const sidebar = document.createElement( 'div' );
		const tpl = document.getElementById( '_win-tpl-' + type );

		// Prepare main window
		win.id = winid;
		win.className = 'window-' + type + ' ' + winid + ' window';
		win.innerHTML = content.main;

		// Clear window
		this.$win0.innerHTML = '';
		this.$win1.innerHTML = '';

		// Render it
		this.$win0.appendChild( win );

		// Prepare sidebar window
		if( content.sidebar ) {
			sidebar.id = sidewinid;
			sidebar.className = 'window-' + type + ' ' + sidewinid + ' window';
			sidebar.innerHTML = content.sidebar;

			// Render sidebar as well
			this.$win1.appendChild( sidebar );
		}

		// Update window data
		this._win = {
			id: winid,
			rawid: id, // i.e. chatid, userid
			type: type
		};

		if( cb ) 
			cb( id );

		// Call event function
		this._onOpenWin( type, id );
	}

	/**
	 * Close current window.
	 */
	closeWin() {

		this._win = {};

		// Clear window
		this.$win0.innerHTML = '';
		this.$win1.innerHTML = '';
	}

	/**
	 * Manage notifications.
	 */
	newNtf( type, msg, group, autohide ) {
		const uniqid = 'nbird-ntf-' + Math.floor( Math.random() * 99999 ) + 1; // between 1 and 99999
		const ntf = document.createElement('div');
		let html = document.getElementById( 'nbird-ntf-' + type ).innerHTML;

		html = NBird.replaceAll( html, {
			'msg': msg
		});

		ntf.id = uniqid;
		ntf.innerHTML = html;

		if( group ) {
			ntf.className = 'nbird-ntf nbird-ntf-' + group;
			this.hideNtfGroup( group );
		}

		// Show notification
		this.$ntfs.appendChild( ntf );

		// Listen close button
		ntf.querySelector( '.close-btn' ).addEventListener( 'click', this.hideNtf.bind( null, uniqid ) );
		
		// Hide after a while
		if( autohide ) {
			setTimeout( this.hideNtf.bind( null, uniqid ), this.opts.ntfDuration );
		}


	}
	hideNtf( id ) {
		NBird.delObj( id );
	}
	hideNtfGroup( group ) {
		const ntfs = document.getElementsByClassName( 'nbird-ntf-' + group );

		if( ntfs ) {
			for( var i=0; i<ntfs.length; i++ ) {
				NBird.delObj( ntfs[i] );
			}
		}
	}
	newDeskNtf( title, type, msg, group, duration, icon ) {

		if ( this._wstate || !( 'Notification' in window ) && Notification.permission !== 'granted' )
			return;

		const notification = new Notification( title, {
			body: msg,
			icon: icon || this.opts.systemImage,
			tag: group || type
		});

		if( duration ) {
			setTimeout( notification.close.bind( notification ), duration );
		}
		notification.onclick = function() {
			notification.close();
		};

		// Play notification sound
		if( type ) {
			NBird.play( type, this.opts._pluginurl );
		}
	}

	/**
	 * Handle chats.
	 */
	refreshChat( visitorid, chat, prevId ) {
		let tplData = {};
		const visitor = this.db.$_users[visitorid] || {};

		if( !visitor )
			return;

		let item = document.getElementById( 'visitor-item-' + visitorid );

		for( const chatid in this._chatGroups[visitorid] ) {
			this.db.listenChat( chatid );
		}

		// Set visitor data
		visitor._status = visitor.sessions && visitor.sessions.frontend ? 'online' : 'offline';

		const lastItemStatus = item.getAttribute( 'data-status' );

		// Move the item into the related list
		if( lastItemStatus !== visitor._status && lastItemStatus !== 'init' ) {
			const isItemActive = item.querySelector('a').classList.contains( 'active' );

			NBird.delObj( item );
			const newItem = this.createItem( 'visitor', visitorid, visitor );

			if( isItemActive )
				newItem.querySelector('a').classList.add( 'active' );

			const itemindex = this._listenItems.indexOf( visitorid );
			if( itemindex !== -1 )
				this._listenItems.splice( itemindex, 1 );

			// orange is the new black
			item = newItem;
		}

		const itemLink = item.querySelector( 'a' );

		if( visitor.lastSeen )
			NBird.timeago( item.querySelector( '.lcx-timeago' ), this.str.timeShort );

		// Listen chat item events
		this.listenChatItem( visitorid, itemLink );

		// Clean "no item" info
		this.$chats.querySelector( '._no-item' ).classList.add( 'd-hide' );

		// Update chat window content
		this.refreshChatWin( chat.id );
	}

	refreshChatWin( id ) {
		const chat = this.db.$_chats[id];
		const tabItem = document.getElementById( 'chat-tab-item-' + id );
		const tabContent = document.getElementById( 'lcx-chat-tab-' + id );

		if( !chat || this._chatid !== id || !tabContent )
			return;

		const user = this.db.$_users[chat.visitorid] || {};

		const winFooter = this.$win0.querySelector( '.win-footer' );
		const ntfOpTalking = this.$win0.querySelector( '.ntf--op-talking' );
		const ntfEndChat = this.$win0.querySelector( '.ntf--ended-chat' );
		const ntfArchiveChat = this.$win0.querySelector( '.ntf--archived-chat' );
		const btnJoinChat = tabContent.querySelector( '.btn-join-chat' );
		const btnEndChat = tabContent.querySelector( '.btn-end-chat' );
		const btnArchiveChat = tabContent.querySelector( '.btn-archive-chat' );
		const btnDeleteChat = tabContent.querySelector( '.btn-delete-chat' );
		const cbSendChatLogs = document.getElementById( 'cb-sendChatLogs' );

		const feedback = this.$win0.querySelector( '.lcx-user-meta-feedback' );

		feedback.querySelector( '.lcx-desc' ).classList.add( 'd-hide' );
		feedback.querySelector( '.lcx--solved' ).classList.add( 'd-hide' );
		feedback.querySelector( '.lcx--notsolved' ).classList.add( 'd-hide' );

		if( typeof chat.solved === 'boolean' ) {
			feedback.querySelector( '.lcx-desc' ).classList.add( 'd-hide' );

			if( chat.solved === true ) {
				feedback.querySelector( '.lcx--solved' ).classList.remove( 'd-hide' );
			} else {
				feedback.querySelector( '.lcx--notsolved' ).classList.remove( 'd-hide' );
			}
		} else {
			feedback.querySelector( '.lcx-desc' ).classList.remove( 'd-hide' );
		}

		btnJoinChat.classList.remove( 'd-hide' );
		btnEndChat.classList.remove( 'd-hide' );
		btnArchiveChat.classList.remove( 'd-hide' );
		btnDeleteChat.classList.remove( 'd-hide' );
		winFooter.classList.add( 'd-hide' );
		
		ntfOpTalking.classList.add( 'd-hide' );
		ntfEndChat.classList.add( 'd-hide' );
		ntfArchiveChat.classList.add( 'd-hide' );

		tabItem.setAttribute( 'data-status', chat.status );

		if( chat.opid && chat.opid in this.db.$_users ) {
			chat.opName = this.db.$_users[chat.opid].name;
		}

		switch( chat.status ) {
			case 'init':
				btnEndChat.classList.add( 'd-hide' );
				break;

			case 'open':
				btnJoinChat.classList.add( 'd-hide' );
				winFooter.classList.remove( 'd-hide' );

				if( chat.opid !== this.db._uid ) {
					ntfOpTalking.innerHTML = NBird.replace( ntfOpTalking.innerHTML, chat, true );
					ntfOpTalking.classList.remove( 'd-hide' );
				}
				break;

			case 'close':
				ntfEndChat.classList.remove( 'd-hide' );
				btnJoinChat.classList.remove( 'd-hide' );
				btnEndChat.classList.add( 'd-hide' );
				break;
		}

		if( chat.archived ) {
			ntfArchiveChat.classList.remove( 'd-hide' );
			btnJoinChat.classList.add( 'd-hide' );
			btnEndChat.classList.add( 'd-hide' );
			btnArchiveChat.classList.add( 'd-hide' );
		}

		if( !user.emailForNtf ) {
			cbSendChatLogs.checked = false;
			cbSendChatLogs.disabled = true;
		} else {
			cbSendChatLogs.checked = true;
			cbSendChatLogs.disabled = false;
		}

	}
	readChat( chatid, cb ) {
		const i = this._unreadChats.indexOf( chatid );

		if( i === -1 ) // no unread msg found
			return;

		const chat = this.db.$_chats[chatid];

		if( !chat )
			return;

		// Remove chat from unread chats
		this._unreadChats.splice( i, 1 );

		this.db.readChat( chatid, cb );

		// Reset count
		if( this._countNewMsgs[chatid] ) 
			this._countNewMsgs[chatid] = 0;

		// Mark chat item as read
		const item = document.getElementById( 'visitor-item-' + chat.visitorid );
		const tab = this.$win0.querySelector( '#chat-tab-item-' + chatid + ' a' );

		if( item )
			item.classList.remove( 'is--new' );

		if( tab ) {
			tab.classList.remove( 'badge' );
			tab.removeAttribute( 'data-badge' );
		}
	}
	renderEmail( email ) {

		if( email )
			return '<a href="mailto:' + email + '">' + email + '</a>';

		return '<span class="lcx-desc __lcx-no-email">' + this.str.noEmail + '</span>';

	}
	_getChatTabContent( chatid ) {
		const tpl = document.getElementById( '__win-tpl-chat-content' );
		const div = document.createElement( 'div' );
		const tabid = 'lcx-chat-tab-' + chatid;

		// real-time data
		const chat = this.db.$_chats [chatid];
		const msgs = this.db.$_msgs[chatid];
		const user = this.db.$_users[chat.visitorid];

		if( !chat )
			return;

		div.id = tabid;
		div.className = 'lcx-chat-tab ' + tabid;
		div.innerHTML = NBird.replaceAll( tpl.innerHTML, chat, chatid, 'chat-' );

		const customClasses = tpl.getAttribute( 'data-classes' );
		if( customClasses )
			div.className += ' ' + customClasses;

		const msgsWrap = div.querySelector( '.lcx-msgs' );
		msgsWrap.id = 'lcx-msgs-' + chatid;

		// Load messages.
		if( msgs ) {
			let msg;
			for( const msgid in msgs ) {
				msg = this.db.$_msgs[chatid][msgid];
				this.renderMsg( msgid, msg, msgsWrap, msg.prevId );
			}
		}

		return div;
	}
	listenChatTab( chatid ) {
		const lastActive = this.$win0.querySelector( '.chat-tab-item.active' );
		const lastActiveContent = this.$win0.querySelector( '.lcx-chat-tab.active' );

		const currentTab = document.getElementById( 'chat-tab-item-' + chatid );
		const currentTabContent = document.getElementById( 'lcx-chat-tab-' + chatid );

		// Update current chat id
		this._chatid = chatid;

		// Deactivate last chat tab and content
		if( lastActive ) {
			lastActive.classList.remove('active');
			lastActiveContent.classList.remove('active');
		}

		// Activate current tab content
		currentTab.classList.add('active');
		currentTabContent.classList.add('active');

		const lastMsg = sessionStorage.getItem( 'nbird-reply-' + chatid ) || '';
		this.$win0.querySelector('.nbird-reply').innerHTML = lastMsg;

		// Update chat window content.
		this.refreshChatWin( chatid );

		// Scroll-down conversation
		NBird.scrollDown( currentTabContent.querySelector( '.lcx-msgs-container' ) );
	}
	getChatWin( visitorid ) {
		let tabs = document.createElement( 'ul' );
		const win = document.createElement( 'div' );
		const sidebar = document.createElement( 'div' );
		const chatIDs = this._chatGroups[visitorid];
		const winMainContent = document.getElementById( '__win-tpl-chat' );
		let winSidebarContent = document.getElementById( '__win-tpl-chat-sidebar' ).innerHTML;

		const user = this.db.$_users[visitorid];


		user.phone = user.phone ? user.phone : `<span class="text-gray-light">N/A</span>`;
		user.companyName = user.companyName ? user.companyName : `<span class="text-gray-light">N/A</span>`;

		// Update active chats data
		this._chatids = chatIDs;

		// Clean current page url
		if( user.currentPageUrl )
			user.currentPageUrlClean = user.currentPageUrl.replace( this.opts._siteurl, '' );

		winSidebarContent = NBird.replaceAll( winSidebarContent, user, visitorid, 'user-' );

		win.innerHTML = winMainContent.innerHTML;
		sidebar.innerHTML = winSidebarContent;

		// Build tab and contents
		let i=0;
		for( var chatid in chatIDs ) {
			tabs.insertAdjacentHTML( 'afterbegin', this.getChatTabItem( chatid ).innerHTML );

			win.querySelector('.lcx-chat-tabs').appendChild( this._getChatTabContent( chatid ) );

			i++;
		}

		// Include tabs
		win.querySelector( '.win-tabs' ).innerHTML = tabs.innerHTML;

		// Refresh user data
		this.refreshUser( visitorid, user );

		const fn_updateUser = ( uid, key, val ) => {
			let data = {};
			data[key] = val;

			if( key === 'email' && val ) {

				if( !NBird.isEmail( val ) )
					this.newNtf( 'error', this.str.invEmail, 'user', true );
			}

			this.db.updateUser( uid, data );
		};

		return {
			main: win.innerHTML,
			sidebar: sidebar.innerHTML
		};
	}
	getChatTabItem( chatid ) {
		const chat = this.db.$_chats[chatid];
		const div = document.createElement( 'div' );
		const li = document.createElement( 'li' );

		li.id = 'chat-tab-item-' + chatid;
		li.className = 'tab-item chat-tab-item chat-tab-item-' + chatid;
		li.setAttribute( 'data-status', chat.status );
		li.innerHTML = '<a href="#'+chatid+'">'+chat.caseNo+'</a>';

		div.appendChild( li );
		return div;
	}
	listenChatItem( itemid, itemLink ) {
		if( this._listenItems.indexOf( itemid ) !== -1 )
			return;

		this._listenItems.push(itemid);

		// Listen the new item
		itemLink.addEventListener( 'click', function(e) {
			e.preventDefault();

			fn_openWin( this.getAttribute('href').substring(1) );

			const currentId = this.parentNode.id;
			const lastActive = document.querySelector( '.side-list a.active' );

			this.classList.add( 'active' );

			if( lastActive && lastActive.parentNode.id !== currentId ) {
				lastActive.classList.remove( 'active' );
			}

			return false;
		});

		const fn_openWin = ( visitorid ) => {
			const content = this.getChatWin( visitorid );
			this.openWin( 'chat', visitorid, content );
		};

		/*
			// 
			// Listen chat metabox updates.
			//
			if( btnSaveMeta ) {
				btnSaveMeta.addEventListener( 'click', (e) => {
					e.preventDefault();

					btnSaveMeta.setAttribute( 'disabled', true );

					fn_saveMeta( chatid, win, () => {
						btnSaveMeta.removeAttribute( 'disabled' );

						// Throw notification
						this.newNtf( 'success', this.str.saved, 'settings', true );
					} );
				});
			}
		};

		const fn_saveMeta = ( chatid, win, cb ) => {

			const chat = this.db.$_chats[chatid];
			const metas = win.getElementsByClassName( '__auto-update' );

			if( !chat ) return;

			let metaUpdateType,
				meta,
				metaVal, 
				userData = {},
				chatData = {};

			if( metas ) {
				for( var i=0; i<metas.length; i++ ) {
					metaVal = '';
					meta = metas[i];
					metaUpdateType = meta.getAttribute( 'data-type' );

					switch( meta.nodeName ) {
						case 'INPUT':
						case 'TEXTAREA':
							metaVal = meta.value;
							break;

						case 'SELECT':
							metaVal = meta.options[meta.selectedIndex].value;
							break;
					}

					if( metaUpdateType === 'user' ) {
						userData[meta.name] = metaVal;
					} else {
						chatData[meta.name] = metaVal;
					}
				}
			}

			if( chatData ) 
				this.db.updateChat( chatid, chatData );
			
			if( chat.visitorid )
				this.db.updateUser( chat.visitorid, userData, cb );
			else if( cb )
				cb();


		};*/
	}
	removeTab( type, id, data ) {
		if( type === 'chat' ) {

			if( !data )
				return;

			if( this._chatid === id )
				this._chatid = '';

			// Remove visitor from the list 
			// if it's the last chat tab
			if( !( data.visitorid in this._chatGroups ) ) {
				this.removeItem( 'visitor', data.visitorid );

				// Close window as well
				this.closeWin();
			}
			// Show "no chats found" message
			if( Object.keys( this._chatGroups ).length === 0 ) {
				this.$chats.querySelector( '._no-item' ).classList.remove( 'd-hide' );
			}
		}

		// Delete tab link and content
		NBird.delObj( type + '-tab-item-' + id );
		NBird.delObj( 'lcx-' + type + '-tab-' + id );

	}

	/**
	 * Handle messages.
	 */
	renderMsg( msgid, msg, $wrap, prevId ) {

		let classes = [];
		const isYou = msg.platform === 'console' && this.db._uid === msg.uid;
		let user = isYou ? this.db.$_user : this.db.$_users[msg.uid];
		const itemid = 'lcx-msg-' + msg.chatid + msgid;

		if( !user || !$wrap )
			return;

		const item = document.createElement( 'li' );
		item.id = itemid;
		classes.push( itemid );

		if( isYou ) {
			classes.push( 'lcx-msg--you' );
			msg.name = this.str.you;
		}

		// Set avatar
		let avatar;
		if( msg.photoURL || !user.nickname ) {
			const photoURL = msg.photoURL || this.opts.companyLogo || this.opts.anonymousImage;
			avatar = '<span class="lcx-avatar"><img src="' + photoURL + '" alt="" /></span>';

		} else {
			const hex = user.color;
			let shortName = user.name.match(/\b(\w)/g);
			if( !shortName ) // Get first char instead for other langs. (i.e. arabic)
				shortName = [ user.name.charAt(0) ];

			shortName = shortName.join('');
			shortName = ( shortName.length > 2 ) ? shortName.substring( 0,2 ) : shortName;

			avatar = '<span class="lcx-avatar" style="color: ' + NBird.idealTextColor( hex ) + ';background-color:' + hex + ';">' + shortName + '</span>';
			classes.push( 'lcx-msg--avatarText' );
		}

		// Set date/time
		const time = NBird.time( msg.date, this.opts.hourFormat );
		const date = NBird.time( msg.date, this.opts.dateFormat );

		// Set author desc (i.e. by Screets)
		const authorDesc = '';

		item.className = classes.join(' ');

		item.innerHTML = '<div class="lcx-msg-wrap">' + avatar + '<div class="lcx-content"><div class="lcx-meta"><span class="lcx-msg-status"></span><span class="lcx-time" title="'+ date +'">' + time + '</span></div><div class="lcx-author"><span class="lcx-title">' + user.name + '</span><span class="lcx-desc">' + authorDesc + '</span></div><span class="lcx-msg">' + NBird.renderMsg( msg.msg ) + '</span></div></div>';

		$wrap.appendChild( item );

		NBird.scrollDown( $wrap.parentNode );
	}

	/**
	 * Handle other users.
	 */
	refreshUser( uid, user ) {

		if( !user )
			return;

		const item = document.getElementById( 'visitor-item-' + uid );

		user.name = user.nickname || user.name;
		user.__emailForNtf = this.renderEmail( user.emailForNtf );
		user.phone = user.phone ? user.phone : `<span class="text-gray-light">N/A</span>`;
		user.companyName = user.companyName ? user.companyName : `<span class="text-gray-light">N/A</span>`;

		if( item ) {
			// Update item data
			item.querySelector( '.lcx-timeago' ).setAttribute( 'datetime', user.lastSeen/1000 );
			item.querySelector( '.lcx-name' ).innerText = user.name;

			// Clean current page url
			if( user.currentPageUrl )
				user.currentPageUrlClean = user.currentPageUrl.replace( this.opts._siteurl, '' );

			// Update template tags
			NBird.refreshTags( uid, user, 'user-' );
		}

		// Update online visitor users data.
		if( user.chatsAsVisitor && user.sessions && user.sessions.frontend ) {
			this._onlineVisitors[ uid ] = true;

			if( item )
				item.setAttribute( 'data-status', 'online' );

		} else {
			delete this._onlineVisitors[ uid ];

			if( item )
				item.setAttribute( 'data-status', 'offline' );
		}

		if( this._win ) {
			const editableUsername = this.$win1.querySelector( '.user-info .nbird-username' );

			// Update editable username
			if( editableUsername && this._win.rawid === uid)
				editableUsername.innerText = user.name;
		}

		// this._chatsList.reIndex();
		// this._chatsList.sort( 'status', { order: 'desc' } );

		// Include user meta details

		// Refresh current chat window
		this.refreshChatWin( this._chatid );
	}

	/**
	 * Handle modals.
	 */
	closeModals() {
		document.querySelector( '.modal-overlay' ).click();
	}

	/**
	 * Handle settings.
	 */
	saveSettings( settings ) {

		this.newNtf( 'info', this.str.saving, 'settings' );

		const fn_save = (r) => {
			if( !r.error ) {
				this.db.updateProfile({
					name: settings.name || this.db.getRandName(),
					photoURL: settings.photoURL || ''
				}, () => {

					// Throw notification
					this.newNtf( 'success', this.str.saved, 'settings', true );

					// Re-activate save button
					this.$btnSaveProfile.disabled = false;
				});

			} else {
				console.error( r );
			}
		};

		NBird.post( 'saveSettings', settings, this.opts.ajax, fn_save );
	}


	/**
	 * 
	 * ======= EVENTS =======
	 *
	 */
	/**
	 * Listen database events.
	 */
	_dbEvents() {

		this.event.on( 'setup', this._onSetup.bind(this) );

		// Authentication events.
		this.event.on( 'connect', this._onConnect.bind(this) );
		this.event.on( 'disconnect', this._onDisconnect.bind(this) );
		this.event.on( 'authState', this._onAuthState.bind(this) );
		this.event.on( 'authError', this._onAuthErr.bind(this) );

		// Session events.
		this.event.on( 'endSession', this._onEndSession.bind(this) );
		this.event.on( 'duplicateSession', this._onDuplicateSess.bind(this) );

		// Online operator events.
		this.event.on( 'newOp', this._onNewOp.bind(this) );
		this.event.on( 'deleteOp', this._onDeleteOp.bind(this) );

		// Other user events.
		this.event.on( 'newUser', this._onNewUser.bind(this) );
		this.event.on( 'updateUser', this._onUpdateUser.bind(this) );
		this.event.on( 'deleteUser', this._onDeleteUser.bind(this) );

		// Chat events.
		this.event.on( 'newChat', this._onNewChat.bind(this) );
		this.event.on( 'updateChat', this._onUpdateChat.bind(this) );
		this.event.on( 'deleteChat', this._onDeleteChat.bind(this) );

		// Archived chat events.
		this.event.on( 'newArchivedChat', this._onNewArchivedChat.bind(this) );
		this.event.on( 'updateArchivedChat', this._onUpdateArchivedChat.bind(this) );
		this.event.on( 'deleteArchivedChat', this._onDeleteArchivedChat.bind(this) );

		// Chat message events.
		this.event.on( 'newMsg', this._onNewMsg.bind(this) );
		this.event.on( 'updateMsg', this._onUpdateMsg.bind(this) );
		this.event.on( 'deleteMsg', this._onDeleteMsg.bind(this) );

		// Chat member events.
		this.event.on( 'newMember', this._onNewMember.bind(this) );
		this.event.on( 'updateMember', this._onUpdateMember.bind(this) );
		this.event.on( 'deleteMember', this._onDeleteMember.bind(this) );

		// Current user events.
		this.event.on( 'profile', this._onProfileUpdate.bind(this) );
	}
	/**
	 * Setup is required.
	 */
	_onSetup() {
		document.body.setAttribute( 'data-auth-state', 'setup' );
	}
	/**
	 * Network is connected.
	 */
	_onConnect() {
		document.body.setAttribute( 'data-conn-status', 'connect' );

		// Keep chat console active 
		this.$console.classList.remove( 'disabled' );

		// Throw notification
		this.newNtf( 'success', this.str.connected, 'auth', true );
	}

	/**
	 * Network is disconnected.
	 */
	_onDisconnect( reason ) {
		document.body.setAttribute( 'data-conn-status', 'disconnect' );

		// Disable chat console when no network connection
		this.$console.classList.add( 'disabled' );

		// Throw notification
		if( !this.db._isFirstConn )
			this.newNtf( 'error', this.str.noConn, 'auth' );
	}

	/**
	 * Handle authentication state changes.
	 */
	_onAuthState( user ) {

		let authState;

		// Signed in
		if ( user ) {
			authState = 'signedin';

			// Show related UI objects
			this.$toggleOnline.classList.remove( 'd-hide' );
			this.$btnProfile.classList.remove( 'd-hide' );

			// Listen operator events.
			this.db.listenOpEvents();

			// Verify the installation and required data.
			this.db.verify();

			
		// Signed out
		} else {

			authState = 'signedout';

			// Hide related UI objects
			this.$toggleOnline.classList.add( 'd-hide' );
			this.$btnProfile.classList.add( 'd-hide' );

			// Switch to offline in chat
			document.body.removeAttribute( 'data-online' );
			this.$switchOnline.checked = false;

			// Reset basic user info on UI.
			document.getElementById( 'currentUser-name' ).innerHTML = '&nbsp;';
			document.getElementById( 'currentUser-photoURL' ).src = this.opts.anonymousImage;

			// FIXME: We need to refresh here.
			// Because it confuses to load messages.
			if( !this.db._isFirstConn )
				window.location.reload( true );

		}

		// Update signin button
		this.$signin.setAttribute( 'data-status', authState );
		this.$signin.disabled = false;

		// Update body
		document.body.setAttribute( 'data-auth-state', authState );

	}

	/**
	 * Handle sessions.
	 */
	_onEndSession() {
		this._onDuplicateSess();
	}
	_onDuplicateSess( user ) {

		// Disable console
		this.$console.classList.add( 'disabled' );

		// Throw notification
		this.newNtf( 'error', this.str.errDuplicateSess + ' <a href="#" id="nbird-use-here">' + this.str.useHere + '</a>', 'auth' );

		const useHere = document.getElementById('nbird-use-here');

		if( useHere ) {
			useHere.addEventListener( 'click', (e) => {
				e.preventDefault();

				this.db.endSession( () => {
					// Refresh browser window. 
					// It is important to reload real-time data
					window.location.reload( true );
				});
			});
		}
	}

	/**
	 * Handle authentication errors.
	 */
	_onAuthErr( error ) {
		this.newNtf( 
			'error',
			error.message,
			'auth'
		);

		// Re-enable the sign-in button
		this.$signin.disabled = false;
	}

	/**
	 * Handle online operators.
	 */
	_onProfileUpdate( uid ) {

		const user = this.db.$_user;

		if( !user ) 
			return;

		let opData;

		// Update basic info if its missing
		if( !user.name || !user.email || !user.photoURL ) {

			this.db.updateEmail( this.opts.user.email );
			this.db.updateProfile({
				name: this.opts.user.name,
				email: this.opts.user.email,
				photoURL: this.opts.user.photoURL
			});

			opData = {
				name: this.opts.user.name, 
				photoURL: this.opts.user.email,
				lastOnline: firebase.database.ServerValue.TIMESTAMP 
			};

		} else {
			opData = {
				name: user.name, 
				photoURL: user.photoURL,
				lastOnline: firebase.database.ServerValue.TIMESTAMP 
			};

			
		}
		
		this.db.db.ref( 'operators/' + uid ).set( opData );


		// Update listening chats
		this._listenChats = user.chats ? Object.keys( user.chats ) : [];

		// 
		// Update settings.
		// 
		document.getElementById('field-user-name').value = user.name || this.opts.user.name;
		document.getElementById('field-user-email').value = user.email || this.opts.user.email;
		document.getElementById('field-user-photo-url').value = user.photoURL || this.opts.user.photoURL;

		// 
		// Update basic user info.
		//
		document.getElementById( 'currentUser-name' ).innerHTML = user.name || '&nbsp;';
		document.getElementById( 'currentUser-photoURL' ).src = user.photoURL || this.opts.anonymousImage;
		
	}

	/**
	 * Handle other users.
	 */
	_onNewUser( id, user ) {
		this.refreshUser( id, user );
	}
	_onUpdateUser( id, user ) {
		this.refreshUser( id, user );
	}
	_onDeleteUser( id, ) {
		this.refreshUser( id, null );
	}

	/**
	 * Handle online operators.
	 */
	_onNewOp( userid, prevId ) {

		// We're online.
		if( this.db._uid === userid ) {
			document.body.setAttribute( 'data-online', true );

			this.$switchOnline.checked = true;
		}
	}	
	_onDeleteOp( userid, prevId ) {
		
		// We're offline :(
		if( this.db._uid === userid ) {
			document.body.removeAttribute( 'data-online' );

			this.$switchOnline.checked = false;
		}
	}

	/**
	 * Handle chats.
	 */
	_onNewChat( id, chat, prevId ) {
		const visitorid = chat.visitorid;
		const isNotified = sessionStorage.getItem( 'nbird-chatNotified-' + id );
		let firsttime = !this._chatGroups[visitorid];

		if( !visitorid )
			return;

		if( firsttime ) {
			this._chatGroups[visitorid] = {};
			this._newChats[visitorid] = {};
			const visitor = this.db.$_users[visitorid];

			visitor._status = visitor.sessions && visitor.sessions.frontend ? 'online' : 'offline';

			this.createItem( 'visitor', visitorid, visitor );
		}

		if( chat.status === 'init' ) {

			// Notify operator
			if( !isNotified ) {
				sessionStorage.setItem( 'nbird-chatNotified-' + id, true )

				this.newDeskNtf( 
					this.str.newChat.replace( '%s', chat.name ),
					'chat-started',
					chat.subject,
					id,
					this.opts.ntfDuration,
					this.opts.companyLogo || this.opts.systemImage
				);
			}

			// Include new chats
			this._newChats[chat.visitorid][id] = true;

			// Mark the chat item
			this.updateItem( 'newChat', 'visitor-item-'+chat.visitorid );
		}

		// Update chat data
		chat.id = id;
		this._countNewMsgs[id] = 0;

		// Group chat
		this._chatGroups[chat.visitorid][id] = true;

		// Include new conversation into open chat window
		if( this._win.rawid === chat.visitorid ) {
			const content = this.getChatWin( visitorid );
			this.openWin( 'chat', chat.visitorid, content );
		}

		// Render group chat
		this.refreshChat( visitorid, chat, prevId );		
	}
	_onUpdateChat( id, chat, prevId ) {
		const visitorid = chat.visitorid;

		chat.id = id;
		this.refreshChat( visitorid, chat, prevId );

		// Update new chats data
		if( chat.status === 'init' )
			this._newChats[visitorid][id] = true;
		else if( this._newChats[visitorid] && this._newChats[visitorid][id] )
			delete this._newChats[visitorid][id];

		// Update item highlight
		const markType = this._newChats[visitorid] && Object.keys( this._newChats[visitorid] ).length > 0 ? 'newChat' : 'noNewChat';
		this.updateItem( markType, 'visitor-item-' + visitorid );
	}
	_onDeleteChat( id, chat, prevId ) {
		const visitorid = chat.visitorid;

		// Reset data
		if( this._chatGroups[visitorid] && id in this._chatGroups[visitorid] ) {
			delete this._chatGroups[visitorid][id];
			
			if( Object.keys( this._chatGroups[visitorid] ).length === 0 )
				delete this._chatGroups[visitorid];
		} 

		if( this._countNewMsgs[id] ) 
			delete this._countNewMsgs[id];

		if( this._newChats[visitorid][id] )
			delete this._newChats[visitorid][id];

		if( this._win.rawid === visitorid ) {
			this.removeTab( 'chat', id, chat );
		}

		// No chats found. Delete 
		if( !( visitorid in this._chatGroups ) ) {
			NBird.delObj( 'visitor-item-' + visitorid );
		}

		// Update item highlight
		const markType = this._newChats[visitorid] && Object.keys( this._newChats[visitorid] ).length > 0 ? 'newChat' : 'noNewChat';
		this.updateItem( markType, 'visitor-item-' + visitorid );
	}

	/**
	 * Handle archived chats.
	 */
	_onNewArchivedChat( id, chat, prevId ) {

		// Clean "no item" info
		this.$archivedChats.querySelector( '._no-item' ).classList.add( 'd-hide' );

		const item = this.createItem( 'archivedChat', id, chat );
		const visitor = this.db.$_users[chat.visitorid];

		if( !visitor )
			return;

		NBird.timeago( item.querySelector( '.lcx-timeago' ), this.str.timeShort );
		item.querySelector( '.lcx-name' ).innerText = visitor.nickname || visitor.name;

		item.querySelector('.lcx-undo-link').addEventListener( 'click', function(e) {
			e.preventDefault();
			fn_undo( this.getAttribute('href').substring(1) );
		});

		const fn_undo = ( chatid ) => {
			this.db.updateChat( chatid, { archived: null } );
		};

	}
	_onUpdateArchivedChat( id, chat, prevId ) {

	}
	_onDeleteArchivedChat( id, chat, prevId ) {
		if( Object.keys( this.db._archives ).length === 0 ) {
			this.$archivedChats.querySelector( '._no-item' ).classList.remove( 'd-hide' );
		}

		this.removeItem( 'archivedChat', id );
	}

	/**
	 * Handle chat messages.
	 */
	_onNewMsg( id, msg, prevId ) {

		if( !this._win )
			return;

		// Render immediately if the chat window is open.
		if( this._win.type === 'chat' && msg.chatid in this._chatids ) {
			const $wrap = document.getElementById( 'lcx-msgs-' + msg.chatid );
			this.renderMsg( id, msg, $wrap, prevId );
		}

		if( msg.__unread && this._listenChats.indexOf( msg.chatid ) === -1 ) {
			const author = this.db.$_users[msg.uid];
			const chat = this.db.$_chats[msg.chatid];
			const count = ++this._countNewMsgs[msg.chatid];

			this.newDeskNtf( 
				this.str.newMsg.replace( '%s', author.name ),
				'msg',
				msg.msg,
				msg.chatid,
				this.opts.ntfDuration,
				author.photoURL || this.opts.companyLogo
			);

			// Highlight chat list and tab items
			const listItem = document.getElementById( 'visitor-item-' + chat.visitorid );
			const tabItemLink = document.querySelector( '#chat-tab-item-' + msg.chatid + ' a' );

			if( listItem )
				listItem.classList.add( 'is--new' );

			if( tabItemLink ) {
				tabItemLink.classList.add( 'badge' );
				tabItemLink.setAttribute( 'data-badge', count );
			}

			// Insert chat into unread chat data
			this._unreadChats.push( msg.chatid );
		}
	}
	_onUpdateMsg( id, msg, prevId ) {}
	_onDeleteMsg( id, msg, prevId ) {
		NBird.delObj( 'lcx-msg-' + msg.chatid + id );
	}

	/**
	 * Handle chat members.
	 */
	_onNewMember( id, member, prevId ) {}
	_onUpdateMember( id, member, prevId ) {}
	_onDeleteMember( id, member, prevId ) {}

	/**
	 * Handle errors.
	 */
	_onError( error ) {
		console.error(error);
	}

	/**
	 * Handle window events.
	 */
	_onOpenWin( type, id ) {
		// 
		// Manage chat window.
		// 
		if( type === 'chat' ) {
			const fn_listenTab = ( chatid ) => {
				sessionStorage.setItem( 'nbird-lastTab_' + this._win.rawid, chatid );
				this.listenChatTab( chatid );
			};
			const fn_tabClick = function() {
				fn_listenTab( this.getAttribute('href').substring(1) );
			};

			let tabItems;
			let tabLink;
			let chatid;
			const tabs = this.$win0.getElementsByClassName( 'win-tabs' );
			const lastActiveTab = sessionStorage.getItem( 'nbird-lastTab_' + this._win.rawid ) || '';
			
			for( var i=0; i<tabs.length; i++ ) {
				tabItems = tabs[i].getElementsByTagName( 'li' );

				tabLink = '';
				chatid = '';
				for( var k=0; k<tabItems.length; k++ ) {
					tabLink = tabItems[k].querySelector('a');
					tabLink.addEventListener( 'click', fn_tabClick, false );

					chatid = tabLink.getAttribute( 'href' ).substring(1);
					if( lastActiveTab === chatid  ) {
						tabLink.click();
					} else if( k === 0 )
						tabLink.click();
				}
			}

			let currentJoinBtn;
			const fn_clickJoin = function() {
				currentJoinBtn = this;
				this.classList.add( 'disabled' );
				fn_joinChat();
			};


			const fn_joinChat = () => {
				const chat = this.db.$_chats[ this._chatid ];

				if( !chat )
					return;

				chat.operatorName = this.db.$_user.name;

				// Get visitor data
				const visitor = this.db.$_users[ chat.visitorid ];

				// Send welcome message for the first conversation of visitors
				let msg;
				if( visitor.chatsAsVisitor && Object.keys( visitor.chatsAsVisitor ).length === 1 ) {
					msg = this.opts.welcomeMsg;
					
					// Update message tags
					msg = msg ? NBird.replace( this.opts.welcomeMsg, chat, true ) : '';
				}

				this.db.joinChat( this._chatid, msg, function() {
					currentJoinBtn.classList.remove( 'disabled' );
				});
			};

			const fn_updateChat = ( data ) => {
				this.db.updateChat( this._chatid, data );
			};

			const fn_delChat = ( chatid ) => {
				if( confirm( this.str.confirmDelete ) ) {
					this.db.deleteChat( this._chatid );
				}
			};

			const fn_clickArchive = function(e) {
				e.preventDefault();
				fn_updateChat({ archived: true });
			};

			const fn_clickDelete = function(e) {
				e.preventDefault();
				fn_delChat();
			};			

			const btnsJoin = this.$win0.querySelectorAll( '.btn-join-chat' );
			const btnsArchive = this.$win0.querySelectorAll( '.btn-archive-chat' );
			const btnsDelete = this.$win0.querySelectorAll( '.btn-delete-chat' );

			for( var link of btnsJoin )
				link.addEventListener( 'click', fn_clickJoin, false );
			
			for( var link of btnsArchive )
				link.addEventListener( 'click', fn_clickArchive, false );

			for( var link of btnsDelete )
				link.addEventListener( 'click', fn_clickDelete, false );

			let __sending = false;
			const fn_reply = function(e) {
				if( e && e.keyCode === 13 && !e.shiftKey ) {

					if( __sending ) return;

					__sending = true;

					let msg = this.innerHTML;

					if( !msg ) {
						__sending = false;
						return;
					}

					// Clear reply box and storage
					this.innerText = '';
					fn_resetReply();

					// Push message now
					fn_pushMsg( msg, function() {
						__sending = false;
					});

					e.preventDefault();
				}
			};

			const fn_replyBlur = function() {
				fn_saveMsg( this.innerHTML );
			};

			const fn_saveMsg = ( msg ) => {
				sessionStorage.setItem( 'nbird-reply-' + this._chatid, msg );
			};

			const fn_resetReply = () => {
				sessionStorage.removeItem( 'nbird-reply-' + this._chatid );
				
				// Mark chat as read
				this.readChat( this._chatid );
			};

			const fn_pushMsg = ( msg, cb ) => {
				msg = NBird.sanitizeMsg( msg );
				this.db.pushMsg( this._chatid, msg, cb, this._onError );
			};

			const reply = this.$win0.querySelector('.nbird-reply');

			// Listen reply boxes
			reply.addEventListener( 'keydown', fn_reply, false );
			reply.addEventListener( 'blur', fn_replyBlur );


			// Read chat when operator hovers the chat conversations.
			const cnvs = this.$win0.getElementsByClassName( 'lcx-msgs-container' );
			if( cnvs ) {
				for( var i=0; i<cnvs.length; i++ ) {
					cnvs[i].addEventListener( 'mouseenter', (e) => {
						this.readChat( this._chatid );
					});
				}
			}

			( function($) {
				
				// Show editor on reply
				if( !reply.classList.contains( 'trumbowyg-editor' ) ) {
					$( '.nbird-reply' ).trumbowyg({
					    btns: [['bold', 'italic'], ['viewHTML', 'removeformat' ]],
					    autogrow: true,
					    removeformatPasted: true
					});
				}
			 })(jQuery); 

			
			// 
			// Handle window header.
			//
			const chat = this.db.$_chats[ this._chatid ];
			const user = this.db.$_users[id];
			const usernameWrap = this.$win1.querySelector( '.user-info .nbird-username-wrap' );
			const username = this.$win1.querySelector( '.user-info .nbird-username' );

			// Update user name.
			username.innerText = user.nickname;

			// Select all when user name is clicked.
			let _focused = false; 
			let _username;
			let _newUsername = '';
			usernameWrap.addEventListener( 'click', function() {
				_username = username.textContent;
				if( !_focused ) {
					username.focus();
					NBird.selAll( username );
					_focused = true;
				}
			});
			username.addEventListener( 'blur', (e) => {
				_focused = false;
				_newUsername = username.textContent;
				_newUsername = _newUsername.trim();

				if( _newUsername.length === 0 ) {
					username.textContent = _username;
					return;
				}

				// Rename chat
				this.db.renameUser( id, _newUsername );
			});
			username.addEventListener( 'keydown', (e) => {
				if( e && e.keyCode === 13 && !e.shiftKey ) { // Typed "enter" key (NOT shift+enter)
					e.preventDefault();
					username.blur();
					return;
				}
			});
			

			// Invoke the event
			this.event.emit( 'openChatWin', id /*winid*/, user, chat );
		}


		// Refresh window just in case.
		this.refreshChatWin( id );

		// Listen actions buttons
		this.actions( this.$win1 );
	}
}