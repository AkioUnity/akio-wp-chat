/**
 * Live Chat X, by Akio.
 *
 * Heino, All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

class NBirdDB {

	constructor( opts ) {

		this.opts = opts;

		// Applications
		this.event =  lcx_events;

		// Useful data
		this.db = '';
		this.auth = '';
		this._isFirstConn = true; // Is this first try to connect DB since page refresh?
		this._uid = localStorage.getItem( 'nbird-uid' ) || '';
		this._user = ''; // Current user
		this._sess = ''; // Current session data
		this._refUser = '';
		this._refSess = '';
		this._listen = { 'chats': {}, 'users': {} };
		this._archives = {};

		// Real-time data
		this.$_user = {};
		this.$_users = {};
		this.$_onlineOps = {};
		this.$_msgs = {};
		this.$_chats = {};
		this.$_chatReqs = {};
		this.$_members = {};

	}

	/**
	 * Initialize the database.
	 */
	init() {
		firebase.initializeApp({
			apiKey: this.opts.db.apiKey,
			authDomain: this.opts.db.authDomain,
			databaseURL: this.opts.db.databaseURL
		});

		// Create database references
		this.db = firebase.database();
		this.auth = firebase.auth();

		// 
		// Listen network connections.
		// 
		this.db.ref( '.info/connected' ).on( 'value', (snap) => {
			// Connected
			if( snap.val() === true ) {
				this._isFirstConn = false;
				this._onConnect();

			// Disconnected
			} else {

				this._onDisconnect( 'networkError' );
			}
		});

		// 
		// Observe authentication states.
		// 
		this.auth.onAuthStateChanged( ( user ) => {
			this._onAuthState( user );
		});
	}


	/**
	 * Verify installation and basic data.
	 */
	verify() {
		this.db.ref( 'db_version' ).once( 'value' ).then( (snap) => {
			if( !snap.exists() ) {
				this._onSetupDB();
				return;
			}
			
			if( Number( snap.val() ) < Number( this.opts.dbVersion ) ) {
				this._onSetupDB();
				return;
			}
		}).catch( ( error ) => {
			this._onSetupDB();
		});
	}

	setup( cb ) {

		// Setup Firebase
		let updates = {};

		updates['db_version'] = this.opts.dbVersion;
		updates['_livechat'] = null; // not necessary anymore

		this.db.ref().update( updates ).then( cb );
	}

	/**
	 * Handle current user profile.
	 */
	updateProfile( data, cb ) {
		// Update profile data
		this._user.updateProfile({
			displayName: data.name || '',
			photoURL: data.photoURL || ''
		});

		this._refUser.update( data );

		// Update operators list
		this.db.ref( `operators/${this._uid}` ).update( data ).then( cb );
	}
	updateEmail( email, cb ) {
		this._user.updateEmail( email ).then( cb );
	}
	resetUserData() {
		// Cancel offline capabilities
		if( this._refUser )
			this._refUser.child( 'lastSeen' ).onDisconnect().cancel();

		// Reset data
		this._uid = this._user = this._refUser = '';

		// Reset local storage
		localStorage.removeItem( 'nbird-uid' );
		localStorage.removeItem( 'nbird-accessToken' );
	}
	getNewCaseNo( cb ) {
		this.db.ref( 'caseNo' ).transaction( function( caseNo ) {
			return ++caseNo;
		}, 
		( error, commmited, snap ) => {
			if( !error ) {
				const newCaseNo = snap.val();

				this.db.ref( 'users/' + this._uid + '/identity/lastCaseNo' )
					.set( newCaseNo )
					.then( cb.bind( null, newCaseNo ) );
			}
		});
	}
	createIdentity( cb ) {
		const colorIndex = NBird.randInt( 0, 145 );
		const animalIndex = NBird.randInt( 0, 232 );

		let identity = {};

		// Get case number.
		this.db.ref( 'caseNo' ).transaction( ( caseNo ) => {
			identity.lastCaseNo = caseNo + 1;
			return ++caseNo;
		}).then( () => {
			return this.db.ref( '_livechat/colors/' + NBird.randInt( 0, 145 ) ).once( 'value' );

		// Get animal
		}).then( (snap ) => {
			const color = snap.val();
			const colorName = Object.keys( color )[0];
			identity['color'] = {
				name: colorName,
				hex: color[colorName]
			};
			return this.db.ref( '_livechat/animals/' + NBird.randInt( 0, 232 ) ).once( 'value' );

		}).then( ( snap ) => {
			identity['animal'] = snap.val();
			identity['nickname'] = this.opts.user.name || identity.color.name + ' ' + identity.animal;

			// Trigger callback
			cb( identity );
		});
	}

	/**
	 * Handle users.
	 */
	createUser( id, data, cb ) {
		this.db.ref( 'users/' + id ).set( data ).then( cb );
	}
	updateUser( id, data, cb ) {
		this.db.ref( 'users/' + id ).update( data ).then( cb );
	}
	renameUser( id, name, cb ) {
		if( !name ) return;

		this.db.ref( 'users/' + id + '/nickname' ).set( name ).then( cb );
	}

	/**
	 * Manage authentication.
	 */
	signin( method, token ) {

		switch( method ) {
			case 'custom':
				this.auth.signInWithCustomToken( token ).catch( this._onAuthErr.bind(this) );
				break;

			case 'google':
				const provider = new firebase.auth.GoogleAuthProvider();
				provider.addScope( 'https://www.googleapis.com/auth/firebase' );
				provider.addScope( 'https://www.googleapis.com/auth/cloud-platform' );

				// Help user to select right email
				provider.setCustomParameters({
					'login_hint': this.opts.db.email
				});

				firebase.auth().signInWithPopup( provider ).then( ( result ) => {	
					const user = result.additionalUserInfo;
					const userData = {
						name: user.profile.given_name,
						photoURL: user.profile.picture,
						email: user.profile.email
					};
					localStorage.setItem( 'nbird-accessToken', result.credential.accessToken ); // access token for Google API

					if( user.isNewUser ) {
						this.createUser( result.user.uid, {
							name: user.profile.given_name,
							photoURL: user.profile.picture,
							email: user.profile.email
						});
					} else {
						this.updateUser( result.user.uid, {
							email: user.profile.email
						});
					}

				}).catch( ( error ) => {
					this._onAuthErr( error );
				});
				break;
			case 'anonymous':
				this.auth.signInAnonymously().catch( this._onAuthErr.bind(this) );
				break;
		}
	}
	signout() {

		let updates = {};

		// Remove session
		updates['users/' + this._uid + '/sessions/' + this.opts.platform] = null;

		// Make us offline in chat
		updates['onlineOps/' + this._uid] = null;
		
		// Reset user data immediately.
		this.resetUserData();

		// Update changes, and sign out
		this.db.ref().update( updates ).then( () => {
			return this.auth.signOut();
		});

	}
	connect() {
		this.db.goOnline();
	}
	disconnect() {
		this.db.goOffline();
	}

	/**
	 * Handle the sign-in button process.
	 */
	toggleSignin( method, token ) {
		
		if( this._user ) {
			this.signout();
		
		// Signin
		} else {
			this.signin( method, token );
		}
	}

	/**
	 * Handle the online state.
	 */
	toggleOnline( isOnline ) {
		const refOnline = this.db.ref( 'onlineOps/' + this._uid );
		const refOpLastOnline = this.db.ref( 'operators/' + this._uid + '/lastOnline' );
		
		isOnline = ( isOnline ) ? true : null;
		
		refOnline.set( isOnline );

		if( !isOnline )
			refOpLastOnline.set( firebase.database.ServerValue.TIMESTAMP );

		if( isOnline ) {
			refOnline.onDisconnect().remove();
			refOpLastOnline.onDisconnect().set( firebase.database.ServerValue.TIMESTAMP );
		}
	}

	/**
	 * Handle sessions.
	 */
	startSession() {
		this._sess = { 
			started: firebase.database.ServerValue.TIMESTAMP 
		};

		this._refSess.set( this._sess );
		this._refSess.onDisconnect().remove();

		// Listen this session
		this._refSess.off();
		this._refSess.on( 'child_removed', this._onEndSession.bind(this) );
	}
	endSession( cb ) {
		this._refSess.remove().then(cb);
	}

	/**
	 * Handle operator events.
	 */
	listenOpEvents() {

		const refOps = this.db.ref( 'onlineOps' );
		const refUsers = this.db.ref( 'users' ).orderByChild( 'lastSeen' );
		const refChats = this.db.ref( 'chats' ).limitToLast(100).orderByChild( 'archived' ).equalTo( null );
		const refArchive = this.db.ref( 'chats' ).orderByChild( 'archived' ).equalTo( true );

		// Listen online operators
		refOps.off();
		refOps.on( 'child_added', this._onNewOp.bind(this) );
		refOps.on( 'child_removed', this._onDeleteOp.bind(this) );

		// Listen users
		refUsers.off();
		refUsers.on( 'child_added', this._onNewUser.bind(this) );
		refUsers.on( 'child_changed', this._onUpdateUser.bind(this) );
		refUsers.on( 'child_removed', this._onDeleteUser.bind(this) );

		// Listen chats
		refChats.off();
		refChats.on( 'child_added', this._onNewChat.bind(this) );
		refChats.on( 'child_changed', this._onUpdateChat.bind(this) );
		refChats.on( 'child_removed', this._onDeleteChat.bind(this) );

		// Listen archived chats
		refArchive.off();
		refArchive.on( 'child_added', this._onNewArchivedChat.bind(this) );
		refArchive.on( 'child_changed', this._onUpdateArchivedChat.bind(this) );
		refArchive.on( 'child_removed', this._onDeleteArchivedChat.bind(this) );
	}
	getOnlineOps( cb ) {
		// cb( null );return;
		this.db.ref( 'onlineOps' ).once( 'value' ).then( function( snap ) {
			cb( snap.val() );
		});
	}

	/**
	 * Handle chats.
	 */
	startChat( data, cb, onError ) {
		const chat = this.db.ref( 'chats' ).push();
		const chatid = chat.key;

    	let updates = {};

    	updates[ 'chats/' + chatid ] = {
    		name: data.name || null,
    		subject: data.msg || null,
    		lastMsg: data.msg || null,
    		date: firebase.database.ServerValue.TIMESTAMP,
    		visitorid: this._uid,
    		caseNo: data.caseNo,
    		status: 'init', // chat initiated.
    		type: 'support'
    	};

    	updates[ 'members/' + chatid + '/' + this._uid ] = {
    		chatid: chatid
    	};


		this.db.ref().update( updates ).then( () => {

			this.listenChat( chatid, true );

			if( data.msg ) {
				this.pushMsg( chatid, data.msg );
			}

			if( cb ) 
				cb( chatid );

		}).catch( onError );

	}
	updateChat( chatid, data, cb ) {
		this.db.ref( 'chats/' + chatid ).update( data ).then( cb );
	}
	deleteChat( chatid, cb ) {
		let updates = {};

		const chat = this.$_chats[chatid];

		updates[ 'chats/' + chatid ] = null;
		updates[ 'messages/' + chatid ] = null;
		updates[ 'members/' + chatid ] = null;

		// Clean chat from related users
		updates[ 'users/' + this._uid + '/chats/' + chatid ] = null;
		updates[ 'users/' + chat.visitorid + '/chatsAsVisitor/' + chatid ] = null;

		this.db.ref().update( updates );
		
	}
	listenChat( chatid, listenItself, cb ) {
		if( chatid in this._listen.chats )
			return;

		this._listen.chats[chatid] = true;

		// Manually create chat data
		this.$_msgs[chatid] = {};
		this.$_members[chatid] = {};

		// Listen chat itself
		if( listenItself ) {
			const refChat = this.db.ref( 'chats/' + chatid ).orderByChild( 'archived' ).equalTo(null);

			refChat.on( 'value', this._onSingleChatUpdate.bind(this) );
		}

		// Listen chat messages
		const refMsgs = this.db.ref( 'messages/' + chatid );
		refMsgs.on( 'child_added', this._onNewMsg.bind(this) );
		refMsgs.on( 'child_changed', this._onUpdateMsg.bind(this) );
		refMsgs.on( 'child_removed', this._onDeleteMsg.bind(this) );

		// Listen chat members
		const refMembers = this.db.ref( 'members/' + chatid );
		refMembers.on( 'child_added', this._onNewMember.bind(this) );
		refMembers.on( 'child_changed', this._onUpdateMember.bind(this) );
		refMembers.on( 'child_removed', this._onDeleteMember.bind(this) );
	}
	unlistenChat( chatid ) {

		this.db.ref( 'chats/' + chatid ).off();
		this.db.ref( 'messages/' + chatid ).off();
		this.db.ref( 'members/' + chatid ).off();

		delete this._listen.chats[chatid];
		delete this.$_chats[chatid];
		delete this.$_msgs[chatid];
		delete this.$_members[chatid];

		if( !this.$_chats ) this.$_chats = {};
		if( !this.$_msgs ) this.$_msgs = {};
		if( !this.$_members ) this.$_members = {};
	}
	joinChat( chatid, msg, cb, onError ) {

		const chat = this.$_chats[chatid] || null;

		if( !chat ) {
			if( onError ) onError();
			return;
		}
		
		let updates = {};
		updates[ 'chats/' + chatid + '/status' ] = 'open';

		// Join chat with welcome message
		if( msg && chat.status === 'init' ) {

			this.pushMsg( chatid, msg, cb, updates );

		// Join chat
		} else {
			updates = this.getJoinChatUpdates( chatid, updates );
		}

		// Update changes
		this.db.ref().update( updates ).then( cb ).catch( onError );
	}
	endChat( chatid, cb ) {
		this.updateChat( chatid, {
			status: 'close',
			endedAt: firebase.database.ServerValue.TIMESTAMP
		}, cb );
	}
	readChat( chatid, cb ) {
		if( chatid in this.$_chats ) {			
			this.db.ref( `chats/${chatid}/lastReadByOp` ).set( firebase.database.ServerValue.TIMESTAMP ).then( cb );
		}
	}

	/**
	 * Handle chat messages.
	 */
	pushMsg( chatid, msg, cb, xtraUpdates, onError ) {
		let updates = {};
		const refMsg = this.db.ref( 'messages' ).push();
		const msgid = refMsg.key;

		const user = this.$_user;

		const username = this.opts.platform === 'frontend' ? user.nickname : user.name;
		updates[ 'chats/' + chatid + '/lastMsg' ] = msg;
		
		updates[ 'messages/' + chatid + '/' + msgid ] = {
			chatid: chatid,
			date: firebase.database.ServerValue.TIMESTAMP,
			msg: msg,
			name: username || '',
			photoURL: user.photoURL || '',
			uid: this._uid,
			platform: this.opts.platform
		};
		updates[ 'users/' + this._uid + '/lastSeen' ] = firebase.database.ServerValue.TIMESTAMP;

		// Get join chat updates
		updates = this.getJoinChatUpdates( chatid, updates );

		if( xtraUpdates ) {
			for( const path in xtraUpdates ) {
				updates[ path ] = xtraUpdates[path];
			}
		}

		// Update changes
		this.db.ref().update( updates ).then( cb ).catch( onError );
	}
	getJoinChatUpdates( chatid, updates ) {

		// Add the current user as member.
		if( !( this._uid in this.$_members ) ) {
			updates[ 'members/' + chatid + '/' + this._uid ] = {
				chatid: chatid
			}

			const chat = this.$_chats[chatid];

			// If we're operator, handle this chat.
			if( chat && !chat.opid && this.opts.platform === 'console' ) {
				updates[ 'chats/' + chatid + '/opid' ] = this._uid;
				updates[ 'chats/' + chatid + '/lastReadByOp' ] = firebase.database.ServerValue.TIMESTAMP;
			}
		}

		return updates;
	}

	/**
	 * Useful functions.
	 */
	getServerTime( cb ) {
		this.db.ref( '.info/serverTimeOffset' ).once( 'value', function( snap ) {
		  var offset = snap.val();

		  // Get server time by milliseconds
		  cb( new Date().getTime() + offset );
		});
	}

	/**
	 * 
	 * ======= EVENTS =======
	 *
	 */
	_onSetupDB() {

		this.event.emit( 'setup' );

		/*firebase.auth().currentUser.getIdToken( true).then( (idToken) => {
			
		}).catch(function(error) {
		  // Handle error
		});*/
	}

	/**
	 * Handle network events.
	 */
	_onConnect() {
		this.event.emit( 'connect' );
	}
	_onDisconnect( reason ) {
		this.event.emit( 'disconnect', reason );
	}

	/**
	 * Handle authentication state events.
	 */
	_onAuthState( user ) {
		if( user ) {

			// Update user data
			this._uid = user.uid;
			this._user = this.auth.currentUser;
			this._refUser = this.db.ref( 'users/' + this._uid );

			this._refUser.once( 'value', ( snap ) => {

				// Get session reference
				this._refSess = this._refUser.child( 'sessions/' + this.opts.platform );
				
				// Prevent duplicate windows
				if( this.opts.preventDuplicate && snap.hasChild( 'sessions/' + this.opts.platform ) ) {
					this.event.emit( 'duplicateSession', user );
					return;
				}

				// Update local storage
				localStorage.setItem( 'nbird-uid', this._uid );

				// Listen profile updates
				this._refUser.off();
				this._refUser.on( 'value', this._onProfileUpdate.bind(this) );

				// Listen last seen
				if( this.opts.platform === 'frontend' ) {
					const now = firebase.database.ServerValue.TIMESTAMP;
					const refLastSeen = this._refUser.child( 'lastSeen' );
					refLastSeen.set( now );
					refLastSeen.onDisconnect().set( now );
				}

				// Start a new session
				this.startSession();
			});
		} else {
			// Reset user data
			this.resetUserData();

			if( this._refreshReq )
				window.location.reload( true );

		}
		this.event.emit( 'authState', user );
	}

	/**
	 * Handle authentication error events.
	 */
	_onAuthErr( error ) {
		this.event.emit( 'authError', error );
	}

	/**
	 * Handle session events.
	 */
	_onEndSession() {
		this._sess = '';
		
		// Throw this event if user is signed in
		// Otherwise, we don't need to warn unlogged user about session.
		if( this._uid ) {
			this.event.emit( 'endSession' );
		}
	}

	/**
	 * Handle current user events.
	 */
	_onProfileUpdate( snap ) {
		this.$_user = snap.val();

		this.event.emit( 'profile', snap.key );
	}

	/**
	 * Handle other users events.
	 */
	_onNewUser( snap, prevId ) {
		const id = snap.key;
		const user = snap.val();

		this.$_users[id] = user;
		this.event.emit( 'newUser', id, user, prevId );
	}
	_onUpdateUser( snap, prevId ) {
		const id = snap.key;
		const user = snap.val();

		this.$_users[id] = user;
		this.event.emit( 'updateUser', id, user, prevId );
	}
	_onDeleteUser( snap, prevId ) {
		const id = snap.key;

		delete this.$_users[id];
		
		if( !this.$_users )
			this.$_users = {};

		this.event.emit( 'deleteUser', snap.key, snap.val(), prevId );
	}

	_onHandleUser( snap ) {
		/*let event;
		const id = snap.key;
		const user = snap.val();

		// Deleted.
		if( user === null ) {
			delete this.$_users[id];
			this.event.emit( 'deleteUser', id );

			if( !this.$_users )
				this.$_users = {};
			return;
		}

		// Updated.
		if( this.$_users.hasOwnProperty(id) ) {
			this.$_users[id] = user;
			event = 'updateUser';

		// Created.
		} else {
			event = 'newUser';
		}
		
		// Update data
		this.$_users[id] = user;

		// Trigger event.
		this.event.emit( event, id, user );*/
	}

	/**
	 * Handle online operator events.
	 */
	_onNewOp( snap, prevId ) {

		this.$_onlineOps[snap.key] = true;

		this.event.emit( 'newOp', snap.key, prevId );
	}
	_onDeleteOp( snap, prevId ) {

		delete this.$_onlineOps[snap.key];

		if( !this.$_onlineOps ) this.$_onlineOps = {};

		this.event.emit( 'deleteOp', snap.key, prevId );
	}

	/**
	 * Handle chat events.
	 */
	_onNewChat( snap, prevId ) {
		const id = snap.key;
		const chat = snap.val();

		this.$_msgs[id] = {};
		this.$_members[id] = {};

		this.$_chats[id] = chat;
		this.event.emit( 'newChat', id, chat, prevId );
	}
	_onUpdateChat( snap, prevId ) {
		const id = snap.key;
		const chat = snap.val();

		this.$_chats[id] = chat;
		this.event.emit( 'updateChat', id, chat, prevId );
	}
	_onDeleteChat( snap, prevId ) {

		const id = snap.key;

		this.unlistenChat( id );

		this.event.emit( 'deleteChat', snap.key, snap.val(), prevId );
	}
	_onSingleChatUpdate( snap, prevId ) {

		let eventName = 'updateSingleChat';
		const id = snap.key;
		const chat = snap.val();

		// 
		// Deleted.
		// 
		if( chat === null ) {
			delete this.$_chats[id];

			this.event.emit( 'deleteSingleChat', id, prevId );

			return;
		}

		// 
		// New (default "updated").
		// 
		if( !( id in this.$_chats ) ) {
			eventName = 'newSingleChat';
		}

		this.$_chats[id] = chat;
		this.event.emit( eventName, id, chat, prevId );
	}

	/**
	 * Handle archived chat events.
	 */
	_onNewArchivedChat( snap, prevId ) {
		const chat = snap.val();

		// Skip if its not full update
		if( !chat.caseNo )
			return;

		this._archives[snap.key] = chat;
		this.event.emit( 'newArchivedChat', snap.key, chat, prevId );
	}
	_onUpdateArchivedChat( snap, prevId ) {

		if( !( snap.key in this._archives ) ) {
			this._onNewArchivedChat( snap, prevId );
			return;
		}

		this._archives[snap.key] = snap.val();
		this.event.emit( 'updateArchivedChat', snap.key, snap.val(), prevId );
	}
	_onDeleteArchivedChat( snap, prevId ) {
		delete this._archives[snap.key];
		this.event.emit( 'deleteArchivedChat', snap.key, snap.val(), prevId );
	}

	/**
	 * Handle chat message events.
	 */
	_onNewMsg( snap, prevId ) {
		const id = snap.key;
		const msg = snap.val();
		// const userChats = this.opts.platform === 'console' ? this.$_user.chats : this.$_user.chatsAsVisitor;
		const lastRead = this.$_chats[msg.chatid].lastReadByOp || null;

		msg.prevId = prevId;

		if( lastRead < msg.date && ( msg.platform !== this.opts.platform || msg.uid !== this._uid  ) ) {
			msg.__unread = true;
		}

		this.$_msgs[msg.chatid][id] = msg;
		this.event.emit( 'newMsg', id, msg, prevId );
	}
	_onUpdateMsg( snap, prevId ) {
		const id = snap.key;
		const msg = snap.val();

		msg.prevId = prevId;

		this.$_msgs[msg.chatid][id] = msg;
		this.event.emit( 'updateMsg', id, msg, prevId );
	}
	_onDeleteMsg( snap, prevId ) {
		const msg = snap.val();

		if( this.$_msgs[msg.chatid] )
			delete this.$_msgs[msg.chatid][snap.key];

		this.event.emit( 'deleteMsg', snap.key, msg, prevId );
	}

	/**
	 * Handle chat member events.
	 */
	_onNewMember( snap, prevId ) {
		const id = snap.key;
		const member = snap.val();

		this.$_members[member.chatid][id] = member;
		this.event.emit( 'newMember', id, member, prevId );
	}
	_onUpdateMember( snap, prevId ) {
		const id = snap.key;
		const member = snap.val();

		this.$_members[member.chatid][id] = member;
		this.event.emit( 'updateMember', id, member, prevId );
	}
	_onDeleteMember( snap, prevId ) {
		const member = snap.val();

		if( this.$_members[member.chatid] )
			delete this.$_members[member.chatid][snap.key];

		this.event.emit( 'deleteMember', snap.key, member, prevId );
	}
}

