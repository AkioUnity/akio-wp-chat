/*!
 * Live Chat X, by Screets.
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

class nightBird {
    constructor( opts, strings )
    {
        const defaultStrs = {};

        // Setup and establish options & strings
        this.opts = opts;
        this.str = strings;

        // Applications
        this.event =  lcx_events;

        // Useful data
        this._i = this.opts._iframe;
        this._d = this._i.contentWindow.document;
        this._w = this._i.contentWindow.window;
        this._widget = this._i.parentNode;
        this._wstate = true;
        this._wtitle = document.title;
        this._wtimeout = false;
        this._cardtimeout = false;
        this._mode = 'loading';
        this._pstate = false; // popup status
        this._popup = '';
        this._chatid = sessionStorage.getItem( 'lcx-chatid' ) || ''; // current chat id
        this._chat = null, // current chat data
        this._lastchatid = ''; // last opened chat id
        this._chats = {}; // listening chats
        this._unreads = {}; // unread chats
        this._lastuid = ''; // the userid of the last sent message
        this._lastPlatform = ''; // the platform of the last sent message
        this._lastBreak = ''; // last breakpoint date
        this._dbInit = false; // is db initialized?
        this._initMsg = ''; // first msgid of the visitor
        this._cnvList = {};
        this._isMobile = false;

        // Current user data
        this._uid = '';
        this._uname = '';
        this._user = ''; // auth data.. check this.$_profile

        // Real-time data
        this.$_msgs = {};
        this.$_members = {};
        this.$_profile = {};
        this.$_operators = {};
        this.$_onlineOps = {};
        this.$_chats = {};

        if( this.opts.autoinit )
            this.init();

    }

    init() {
        // Common objects
        this.$btn = this._d.getElementById( 'lcx-starter' );
        this.$menu = this._d.getElementById( 'lcx-menu' );
        this.$ntf = this._d.getElementById( 'lcx-ntfs' );
        this.$popup = this._d.getElementById( 'lcx-popup' );
        this.$pbody = this._d.getElementById( 'lcx-popup-body' );
        this.$pchead = this._d.getElementById( 'lcx-pcontent-header' );
        this.$pcbody = this._d.getElementById( 'lcx-pcontent-body' );
        this.$pcfoot = this._d.getElementById( 'lcx-pcontent-footer' );

        // Hide widget to check if any operators are available
        if( this.opts.hideOffline )
            this._widget.classList.add( 'lcx--hidden' );

        // Connect to db.
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

        // Common template objects
        this.__msgWrap = this._d.getElementById( '__lcx-msg--wrapper' ).innerHTML;

        // Listen actions
        this.actions(); // widget actions
        this.actions( document ); //main site actions

        // Listen UI
        this._uiEvents();
    }

    /**
     * Listen common user interface events.
     */
    _uiEvents() {
        // Auto-popup
        if( this._pstate )
            setTimeout( () => { this.open( this._popup ); }, 300 );

        // Listen starter button clicks
        this.$btn.addEventListener( 'click', (e) => {
            e.preventDefault();

            if( !this._pstate )
                this.open();
            else
                this.close();
        });

        // Listen menu link clicks
        this.$menu.addEventListener( 'click', (e) => {
            e.preventDefault();
            this.open( 'cnv' );
        });

        // Update "last seen" data when visitor hovers the popup
        this.$popup.addEventListener( 'mouseenter', () => { 
            this.readChat();
        });

        // 
        // Listen window resizes
        // 
        const fn_resize = () => {
            var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

            if( width <= this.opts.mobileBreakpoint ) {
                this._widget.classList.add( 'lcx-mobileView' );
                this._d.body.classList.add( 'lcx-mobileView' );
                
                this._isMobile = true;

            } else {
                this._widget.classList.remove( 'lcx-mobileView' );
                this._d.body.classList.remove( 'lcx-mobileView' );
                
                this._isMobile = false;
            }
        };
        fn_resize();
        window.addEventListener( 'resize', fn_resize, true );
    }

    /**
     * Listen action buttons.
     */
    actions( wrap ) {

        wrap = !wrap ? this._d : wrap;

        const btns = wrap.getElementsByClassName( 'lcx-action' );

        if( !btns ) return;

        const fn_run = function(e) {
            e.preventDefault();
            const type = this.getAttribute( 'data-action' );

            switch( type ) {
                case 'openPopup':
                    fn_openPopup( this.getAttribute( 'data-value' ) );
                    break;
                case 'closePopup':
                    fn_closePopup();
                    break;
                case 'newChat':
                    fn_newChat( this.getAttribute( 'data-value' ) );
                    break;
                case 'endChat':
                    fn_endChat();
                    break;
                case 'online':
                case 'away':
                    fn_status( type );
                    break;

                case 'sendReply':
                    fn_reply();
                    break;
            }
        };

        const fn_openPopup = ( name ) => {
            if( name || !this._pstate )
                this.open( name );
            else
                this.close();
        };

        const fn_closePopup = () => {
            this.close();
        };

        const fn_newChat = ( msg ) => {
            this.resetChat();

            this.open( 'online' );

            this.delAttr( 'offline' );

            msg = msg ? [msg] : null;
            this.createTmpChat( msg );
        };

        const fn_endChat = () => {
            this.updateChat( { status: 'close' } );
        };

        const fn_status = ( status ) => {
            this.mode( status );
        };

        const fn_reply = () => {
            const $reply = this.$popup.querySelector('.lcx-reply');

            $reply.dispatchEvent( new KeyboardEvent( 'keydown', { 'keyCode': 13 } ) );
        };
            
        for( let i=0; i<btns.length; i++ ) {
            btns[i].addEventListener( 'click', fn_run );
        }
    }

    /**
     * Open popup.
     *
     * @param string name   (optional) Popup name.
     * @param bool   force  (optional) Force to open requested popup by name.
     */
    open( name, force ) {
        // Set initial page if its first time
        if( !force && ( !this._popup || !name ) )
            name = localStorage.getItem( 'lcx-popup' ) || this.opts.initPopup;

        // Check if name is allowed for popup page
        if( ! this.opts.allowedPopups.includes( name ) )
            name = this.opts.initPopup;

        // Open if not open already...
        this._d.body.classList.add( 'lcx--open' );
        this._widget.classList.add( 'lcx--open' );
        this.$btn.classList.add( 'lcx--active' );
        this.$popup.classList.add( 'lcx--active' );
        
        this._pstate = true;

        // Go to specific popup page.
        this.goPage( name );

        // Clear in-app messages
        this.delAppMsgs();

        // Don't scroll background
        if( this._isMobile ) {
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
            document.body.style.height = '100%';
        }

        // Trigger event
        this._onOpenPopup();
    }

    close() {
        this._d.body.classList.remove( 'lcx--open' );
        this._widget.classList.remove( 'lcx--open' );
        this.$btn.classList.remove( 'lcx--active' );
        this.$popup.classList.remove( 'lcx--active' );

        this._pstate = false;

        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.height = '';

        // Trigger event
        this._onClosePopup();
    }

    /**
     * Reset user data.
     */
    resetUser() {
        const userRef = this.db.ref( `users/${this._uid}` );

        userRef.off();
        userRef.child( '/lastSeen' ).onDisconnect().cancel();

        this._uid = '';
        this._uname = '';
        this._user = '';

        this.$_profile = {};

        window.localStorage.removeItem( 'lcx-refreshToken' );
    }

    /**
     * Start session.
     */
    startSess() {
        const ref = this.db.ref( `users/${this._uid}/sessions/${this.opts.platform}` );

        ref.set({
            started: firebase.database.ServerValue.TIMESTAMP 
        });
        ref.onDisconnect().remove();
    }

    /**
     * Start chat.
     */
    startChat( initMsg, msgType ) {
        const chatRef = this.db.ref( 'chats' ).push();
        const chatid = chatRef.key;
        const now = firebase.database.ServerValue.TIMESTAMP;
        const uid = this._uid;

        let chatData = {
            name: this._uname,
            subject: initMsg,
            lastMsg: initMsg,
            date: now,
            visitorid: uid,
            status: 'init', // chat initiated.
            type: 'support'
        };

        // Get new case number
        this.db.ref( 'caseNo' ).transaction( ( current ) => {
            return ( current || 100222000 ) + 1;

        // Update chat data
        }).then( ( caseNoTxn ) => {
            chatData.caseNo = caseNoTxn.snapshot.val();
            return chatRef.set( chatData );

        // Add current user into members
        }).then( () => {
            return this.db.ref( `members/${chatid}/${uid}` ).set( { chatid: chatid } );

        // Insert it into user's chat
        }).then( () => {
            return this.db.ref( `users/${uid}/chatsAsVisitor/${chatid}` ).set( true );

        // Push the message now
        }).then( () => {
            return this.db.ref( `messages/${chatid}` ).push({
                chatid: chatid,
                date: now, 
                msg: initMsg,
                name: this._uname,
                platform: 'frontend',
                photoURL: this._user.photoURL,
                uid: this._uid,
                type: msgType || 'basic'
            });
        })
        .then( () => {

            // Show collector card if necessary
            this.showCollector();

            // Activate the chat as current 
            this.openChat( chatid );

            // Handle new chat
            this._onNewChat( chatid );
            
        }).catch( ( err ) => {
            this.createNtf( err.message, 'error' );
            console.error(err);
        });
    }

    /**
     * Activate a chat. 
     * It means that the application is listening/updating this chat right now.
     */
    openChat( chatid ) {

        if( !( chatid in this.$_chats ) )
            return;

        this._chatid = chatid;
        this._lastchatid = chatid;
        sessionStorage.setItem( 'lcx-chatid', chatid );

        this._chat = this.$_chats[chatid];
    
        this.attr( 'chat-id', chatid );
        this.attr( 'chat-status', this._chat.status );

        // Invoke the event (once)
        if( this._lastchatid !== chatid )
            this._onOpenChat( chatid, this._chat );

    }

    /**
     * Reset chat data. No chat is active now.
     */
    resetChat() {
        this._chatid = '';
        this._chat = null;
        this._lastchatid = '';
        this._lastuid = '';
        this._lastPlatform = '';
        this._lastBreak = '';
        sessionStorage.removeItem( 'lcx-chatid' );
        
        // Clean chat messages
        if( this.$msgs )
            this.$msgs.innerText = null;

        this.delAttr( 'chat-id' );
        this.delAttr( 'chat-status' );
        this.delAttr( 'op-status' );

        // Make chat status "new" on online popups
        /*if( this._popup === 'online' )
            this.attr( 'chat-status', 'new' );*/

    }

    /**
     * Create a temporary chat without authentication in the real-time database.
     */
    createTmpChat( welcomeMsgs ) {

        // Reset chat
        this.resetChat();

        if( welcomeMsgs ) {
            for( var msg of welcomeMsgs ) {
                this.createMsg( 'basic', '_initMsg-'+this.rand(), {
                    date: Date.now(),
                    unread: true,
                    msg: msg,
                    tmpMsg: true,
                    isYou: true,
                    sound: 'first-msg'
                });
            }
        }

        this.goPage( 'online' );
    }

    /**
     * Change chat mode.
     */
    mode( status ) {
        this._mode = status;
        this.attr( 'mode', status );

        // Show related operators (online or recently online)
        this.showOps();

        // Refresh offline form on new conversations
        this.refreshAwayUI();

        // Invoke the event
        this.event.emit( 'mode', status );
    }

    /**
     * Go to a specific popup page.
     * It doesn't open popup. Just refresh the popup content.
     */
    goPage( name ) {

        if( !name || this._popup === name )
            return;

        let _pageFound = false;
        const div = document.createElement( 'div' );
        const header = this._d.getElementById('__lcx-popup-header-' + name );
        const body = this._d.getElementById('__lcx-popup-body-' + name );
        const footer = this._d.getElementById('__lcx-popup-footer-' + name );

        if( header ) {
            div.classList.add( '__lcx-fadein' );
            div.innerHTML = header.innerHTML;
            this.$pchead.innerText = '';
            this.$pchead.appendChild( div );

            _pageFound = true;
        }

        if( body ) {
            this.$pcbody.innerHTML = body.innerHTML;
            _pageFound = true;
        }

        if( footer ) {
            this.$pcfoot.innerHTML = footer.innerHTML;
            _pageFound = true;
        }

        if( _pageFound ) {
            this._popup = name;
            localStorage.setItem( 'lcx-popup', name );
            this.$popup.setAttribute( 'data-name', name );

            // Trigger event.
            this._onOpenPopupPage( name );

            return true;

        // Open initial popup if this popup can't be found in templates
        } else {
            this.open( this.opts.initPopup );
            return false;
        }
    }

    /**
     * Send a message to the cloud.
     */
    pushMsg( msg, msgType = 'basic' ) {

        this.freezeReply( true );

        const _msgid = '_initMsg-' + this.rand();
        const now = firebase.database.ServerValue.TIMESTAMP;

        // Start new chat, then push the message
        if( !this._chatid ) {

            // Show initial message of visitor with "sending" meta
            this.createMsg( msgType, _msgid, {
                time: now,
                msg: msg,
                unread: true,
                meta: this.str.ntf_sending,
                tmpMsg: true,
                isYou: true,
                sound: 'first-msg'
            });

            this.event.once( 'newChat', ( chatid ) => {
                
                // Unfreeze reply box
                this.freezeReply( false );

                // Reset some data
                this._manualConn = false;
            }); 

            // Authenticate user, then start chat
            if( !this.auth.currentUser ) {
                this._manualConn = true;
                this.db.goOnline();

                this.event.once( 'signin', () => {
                    this.startChat( msg, msgType );
                });
                this.auth.signInAnonymously().catch( this._onAuthErr.bind(this) );

            // Start chat now
            } else {
                this.startChat( msg, msgType );
            }

        // Directly push the message into current chat
        } else {
            const chat = this.$_chats[this._chatid];

            // Unarchive the chat cause of new message
            if( chat.archived ) {
                this.db.ref( `chats/${this._chatid}/archived` ).set(null);
            }

            this.db.ref( `messages/${this._chatid}` ).push({
                chatid: this._chatid,
                date: now, 
                msg: msg,
                name: this._uname,
                platform: 'frontend',
                photoURL: this._user.photoURL,
                uid: this._uid,
                type: msgType
            });
        }
    }

    /**
     * Render a message on current chat conversation.
     */
    createMsg( type = 'basic', msgid, msg = {} ) {

        if( !this.$msgs )
            return;

        const today = new Date(),
            now = Date.now(),
            date = msg.date ? new Date( msg.date ) : today,
            dateStr = date.toDateString(),
            isToday = dateStr === today.toDateString(),
            yesterday = new Date( today.setDate( today.getDate() - 1 ) ),
            isYesterday = dateStr === yesterday.toDateString(),
            msgTime = msg.date || now;

        msg.uid = msg.uid || this._uid || null;
        msg.platform = msg.platform || this.opts.platform;
        msg.fullDate = this.time( msgTime, ( isToday ? this.opts.hourFormat : `${this.opts.dateFormat} ${this.opts.hourFormat}` ) );

        const isYou = msg.isYou || ( this._uid === msg.uid && msg.platform === this.opts.platform );
        const _breakpoint = this._lastBreak && this._lastBreak !== dateStr;
        const _repeating = msg.uid === this._lastuid && msg.platform === this._lastPlatform;

        let classes = [ 'lcx-msg-item' ];

        // Get template
        const _tpl = this._d.getElementById( `__lcx-msg--${type}` );
        if( !_tpl )
            return;

        // Sanitize message
        if( msg.msg )
            msg.msg = this.sanitize( msg.msg );

        const objid = `lcx-msg-${msgid}`;

        // Delete the same message
        this.delObj( this.$popup.querySelector( '.' + objid ) );

        const tpl = document.createElement( 'div' );
        tpl.id = objid;
        tpl.className = `lcx-msg lcx-msg--${type} ${objid}`;
        tpl.innerHTML = this.replace( _tpl.innerHTML, msg, true );
        const $lastMsg = this.$msgs ? this.$msgs.lastElementChild : null;
        
        // Clean last message meta
        if( $lastMsg ) {
            const $meta = $lastMsg.querySelector( '.lcx-msg-meta' );
            if( $meta ) $meta.innerText = '';
        }

        // New message
        if( !_repeating || _breakpoint ) {

            const wrapper = document.createElement( 'div' );
            wrapper.innerHTML = this.__msgWrap;

            if( msg.unread )
                classes.push( 'lcx--new' );

            if( isYou )
                classes.push( 'lcx--you' );

            wrapper.className = classes.join(' ');

            wrapper.querySelector( '.lcx-msg-container' ).appendChild( tpl );
            wrapper.querySelector( '.lcx-msg-avatar img' ).src = msg.avatar || this.opts.companyLogo || this.opts.anonymousImage;


            if( msg.meta ) wrapper.querySelector( '.lcx-msg-meta' ).innerHTML = msg.meta;

            // Show breakpoint
            if( _breakpoint ) {
                let _bpText = '';
                const div = document.createElement( 'div' );
                const _bpTime = Math.abs( msg.date/1000 ) || now;

                if( isToday )
                    _bpText = this.str.date_today;
                else if( isYesterday )
                    _bpText = this.str.date_yesterday;


                div.className = 'lcx-breakpoint';
                div.innerHTML = `<span datetime="${_bpTime}">${_bpText}</span>`;

                if( !_bpText ) 
                    this.timeago( div.querySelector( 'span' ) );
                
                this.$msgs.appendChild( div );

            }

            this.$msgs.appendChild( wrapper );


        // Repeating message
        } else {
            if( msg.unread )
                tpl.classList.add( 'lcx--new' );
            if( isYou )
                tpl.classList.add( 'lcx--you' );

            if( $lastMsg )
                $lastMsg.querySelector( '.lcx-msg-container' ).appendChild( tpl );
            
            if( msg.meta ) $lastMsg.querySelector( '.lcx-msg-meta' ).innerHTML = msg.meta;
        }

        if( !msg.tmpMsg ) {
            this._lastuid = msg.uid;
            this._lastPlatform = msg.platform;
        } else {
            this._initMsg = tpl.id;
        }

        // Scroll down
        this.scrollDown( this.$pbody );

        // Update last breakpoint date here
        this._lastBreak = dateStr;

        // Play notification sound
        if( msg.sound )
            this.play( msg.sound, this.opts._pluginurl );

        // Invoke the event
        this._onCreateMsg( type, msg.id, msg );
    }

    updateMsgMeta( msgid, str ) {
        const msg = this._d.getElementById( msgid );

        if( msg ) {
            const meta = msg.parentNode.parentNode.parentNode.querySelector( '.lcx-msg-meta' );

            if( meta )
                meta.innerHTML = str;
        }
    }

    createNtf( msg, type = 'error', group, autohide ) {
        const uniqid = 'lcx-ntf-' + this.rand();
        const div = document.createElement( 'div' );

        div.id = uniqid;
        div.className = 'lcx-ntf lcx-ntf--' + type;
        div.innerHTML = `<span class="lcx-ntf-content">${msg}</span>`;

        if( group ) {
            this.hideNtf( group );
            div.classList.add( 'lcx-ntf-group-' + group );
        }

        if( autohide ) {
            setTimeout( this.delObj.bind( this, uniqid ), 3000 );
        }

        this.$ntf.appendChild( div );
    }
    hideNtf( group ) {
        const ntfs = this._d.getElementsByClassName( 'lcx-ntf-group-' + group );

        if( !ntfs ) return;

        for( let i=0; i<ntfs.length; i++ ) {
            this.delObj( ntfs[i] );
        }
    }
    
    /**
     * Listen a reply box.
     */
    listenReply( $reply, autofocus ) {

        if( !$reply ) return;

        // Listen reply box
        const fn_push = ( msg ) => {
            this.pushMsg( msg ); 
        };

        $reply.addEventListener( 'keydown', function(e)  {
            // Don't allow typing when froze
            if( this.disabled ) {
                e.preventDefault();
                return;
            }

            if( e && e.keyCode === 13 && !e.shiftKey ) {
                const msg = this.innerText;

                if( msg.length === 0 ) {
                    e.preventDefault();
                    return;
                }

                // Push message
                fn_push( this.innerHTML );

                // Clear message
                this.innerText = '';

                e.preventDefault();
            }
        });

        // Cancel paste and insert it manually.
        $reply.addEventListener( 'paste', (e) => {
            e.preventDefault();

            var text = e.clipboardData.getData( 'text/plain' );
            this._d.execCommand( 'insertText', false, text );
        });
    }
    
    /**
     * Freeze/unfreeze current reply box.
     */
    freezeReply( freeze ) {

        const reply = this.$popup.querySelector( '.lcx-reply' );

        if( !reply ) return;

        this._freezeReply = freeze;

        if( freeze ) {
            reply.disabled = true;
            reply.classList.add( 'lcx--freeze' );
        } else {
            reply.disabled = false;
            reply.classList.remove( 'lcx--freeze' );
        }
    }
    
    /**
     * Show/update recently active operators 
     * by current chat status and operators.
     */
    showOps() {
        const list = this.$popup.querySelector( '.lcx-ops' );
        
        if( !list ) return;

        let div;
        let op;
        let objid;

        list.innerText = null;
        list.classList.remove( '__lcx-hide' );

        const fn_show = ( opid ) => {
            op = this.$_operators[opid];
            objid = `lcx-op-item-${opid}`;
            div = document.createElement( 'div' );

            this.delObj( objid );

            div.id = objid;
            div.className = 'lcx-op-item';
            div.innerHTML = `<div class="lcx-op-pic" title="${op.name}"><img src="${op.photoURL}" alt=""></div><span class="lcx-op-name">${op.name}</span>`;
            list.appendChild( div );
        };

        // Open (accepted) chat
        if( this._chat && this._chat.status === 'open' ) {
            if( this._chat.opid in this.$_operators )
                op = this.$_operators[this._chat.opid];

            const opStatus = this._chat.opid in this.$_onlineOps ? 'online':'away';
            const avatar = op.photoURL || this.opts.companyLogo;

            this.attr( 'op-status', opStatus );

            this.$popup.querySelector( '.lcx-current-op-name' ).innerHTML = op.name;
            this.$popup.querySelector( '.lcx-current-op-pic' ).innerHTML = `<img src="${avatar}" alt="" />`;
            this.$popup.querySelector( '.lcx-current-op-desc' ).innerHTML = this.str['ops_status_' + opStatus];
        
        // New chat (Online mode)
        } else if( this._mode === 'online' && this.$_onlineOps ) {
            for( var opid in this.$_onlineOps ) {
                fn_show( opid );
            }

        // New chat (Away mode)
        } else if( this.$_operators ) {
            let i=0;
            for( var opid in this.$_operators ) {
                fn_show( opid );

                i++;

                if( i === 3 /* show 3 ops */ )
                    break;
            }

        // No operators found
        } else
            list.classList.add( '__lcx-hide' );
    }

    /**
     * Listen/setup chat UI.
     */
    chatUI() {
        if( !this._chat )
            return;

        // 
        // Setup voting..
        //
        const votingWrap = this.$popup.querySelector( '.lcx-rating' );

        if( votingWrap ) {  

            if( this._chat.solved )
                votingWrap.classList.add( '__lcx-hide' );

            const fn_vote = function(e) {
                e.preventDefault();
                fn_sendVote( this.getAttribute( 'data-vote' ) );
            };
            const fn_sendVote = ( vote ) => {
                const isSolved = ( vote === 'yes' );
                this.updateChat( { solved: isSolved }, () => {
                    this.createNtf( this.str.ntf_voted, 'success', 'vote', true );

                    votingWrap.classList.add( '__lcx-hide' );

                    this.event.emit( 'vote', this._chatid, isSolved );
                });
            };
            let voteBtns = this.$popup.querySelectorAll('.lcx-btn-vote');
            for( var btn of voteBtns )
                btn.addEventListener( 'click', fn_vote );
        }

    }

    /**
     * Create in-app message.
     */
    inAppMsg( msg, header, chatid ) {

        if( this._pstate )
            return;

        let obj = this._widget.querySelector( '.lcx-inApp' );

        // Delete last in-app message
        this.delObj( obj );

        obj = document.createElement( 'div' );
        obj.className = 'lcx-inApp';
        obj.setAttribute( 'data-chat-id', chatid );
        obj.innerHTML = `<div class="lcx-inApp-header">${header}</div><div class="lcx-inApp-content">${msg}</div>`;

        this._widget.insertBefore( obj, this._i );

        obj.addEventListener( 'click', function(e) {
            e.preventDefault();
            fn_open( this.getAttribute( 'data-chat-id' ) );
        });

        const fn_open = ( chatid ) => { 
            this.openChat( chatid );
            this.open( 'online', true );
        };
    }
    delAppMsgs() {
        let obj = this._widget.querySelector( '.lcx-inApp' );
        this.delObj( obj );
    }

    /**
     * Load current chat messages.
     */
    loadCnv() {
        const chatid = this._chatid;
        const cnv = this.$_msgs[chatid];

        if( !cnv )
            return;

        let msg;
        for( var msgid in cnv ) {
            msg = cnv[msgid];

            this.createMsg( msg.type, msgid, msg );
        }

    }

    /**
     * Show conversations list.
     */
    showCnvs() {
        const list = this.$popup.querySelector( '.lcx-cnvs' );

        if( !list || !Object.keys( this._cnvList ).length )
            return;
    
        let chat;
        for( var chatid in this._cnvList ) {
            chat = this._cnvList[chatid]

            this.delObj( chat.id );
            list.insertBefore( chat, list.firstChild );

            this.timeago( chat.querySelector( '.lcx-cnv-time' ) );
            
        }
    }

    /**
     * Create a conversation list item.
     */
    createCnv( id, data ) {

        if( id in this._cnvList )
            return;

        let op = {};

        if( data.opid && data.opid in this.$_operators )
            op = this.$_operators[data.opid];

        const tpl = document.createElement( 'a' );
        const _tpl = this._d.getElementById( '__lcx-cnv--item' );
        const avatar = op.photoURL || this.opts.companyLogo || this.opts.anonymousImage;

        data.author = op.name || this.opts.companyName;
        data.date = Math.abs( data.date/1000 );

        if( avatar )
            data.avatar = `<img src="${avatar}" alt="" />`;

        tpl.id = `lcx-cnv-item-${id}`;
        tpl.innerHTML = this.replace( _tpl.innerHTML, data, true );
        tpl.className = 'lcx-cnv lcx-row';
        tpl.setAttribute( 'data-chatid', id );

        tpl.addEventListener( 'click', function(e) {
            e.preventDefault();
            fn_open( this.getAttribute('data-chatid') );
        });

        const fn_open = ( chatid ) => {
            this.resetChat();
            this.openChat( chatid );
            this.goPage( 'online' );
        };

        this._cnvList[id] = tpl;
    }

    /**
     * Update current chat data in real-time database.
     */
    updateChat( $data, cb ) {
        if( this._chatid )
            this.db.ref( `chats/${this._chatid}` ).update( $data ).then( cb );
        else if( cb )
            cb( false );
    }

    /**
     * Mark as read of current chat messages.
     */
    readChat( cb ) {
        this.updateChat( { lastRead: firebase.database.ServerValue.TIMESTAMP }, cb );
        document.title = this._wtitle;
        window.clearInterval( this._wtimeout );

        if( this._chatid in this._unreads ) {
            delete this._unreads[ this._chatid ];
        }

        this.updateCount();
    }

    /**
     * Update new messages counter.
     */
    updateCount() {
        const unreads = this.$popup.querySelector( '.lcx-count' );
        if( !unreads )
            return;

        let unreadChats = 0;

        const totalNewMsgs = Object.keys( this._unreads ).length;
        if( totalNewMsgs > 0 ) {
            
            for( var chatid in this._unreads ) {
                if( chatid != this._chatid )
                    unreadChats++;
            }

            if( unreadChats > 0 ) {
                unreads.innerText = totalNewMsgs;
                unreads.classList.remove( '__lcx-hide' );
            } else
                unreads.classList.add( '__lcx-hide' );
            
        } else {
            unreads.classList.add( '__lcx-hide' );
        }
    }

    /**
     * Throw collector card.
     */
    showCollector() {
        let step = 0;
        let postpone = true;
        let show = false;
        const profile = this.$_profile;

        // Check if any required field is missing in user profile
        if( this.opts.collectorReqs ) {
            for( var field of this.opts.collectorReqs ) {
                console.log( field, field in profile );
                if( !( field in profile ) ) {
                    show = true;
                    break;
                }
            }
        }

        if( !show )
            return;

        // Show pre-card message
        const fn_precardMsg = () => {
            step = 1;
            this.createMsg( 'basic', '_preCard-' + this.rand(), {
                date: Date.now(),
                unread: true,
                msg: this.str.collector_precard,
                uid: 'bot',
                sound: 'msg-alt'
            });

            this._cardtimeout = window.setTimeout( fn_show, 1500 );
        };

        // Show collector card
        const fn_show = () => {
            step = 2;
            postpone = false;
            this.createMsg( 'collector', '_collector-'+this.rand() , {
                date: Date.now(),
                unread: true,
                msg: '',
                uid: 'bot',
                sound: 'msg-alt'
            });

            // Listen fields
            const forms = this.$popup.querySelectorAll( '.lcx-form--collector .lcx-form-field' );
            if( forms ) {
                for( const form of forms ) {
                    fn_listenField( form );
                    break;
                }
            }
        };

        // Listen form field
        let submitBtn;
        let form;
        const fn_listenField = ( formObj ) => {
            form = formObj;
            submitBtn = form.querySelector( '.lcx-submit-field' );
            submitBtn.addEventListener( 'click', fn_save );
            form.addEventListener( 'submit', fn_save );

            // Focus on the field
            form.querySelector( '.lcx-field' ).focus();
        };

        // Save field
        const fn_save = ( e ) => {
            e.preventDefault();

            let valid = true;
            let err;

            for( var el of form.elements ) {
                switch( el.nodeName ) {
                    case 'INPUT':
                    case 'TEXTAREA':
                    case 'SELECT':
                        if( el.required ) {
                            if( el.value.length === 0 ) {
                                valid = false;
                                err = this.str.ntf_reqFields;
                            } else if( el.type === 'email' && !this.isEmail( el.value ) ) {
                                valid = false;
                                err = this.str.ntf_invEmail;
                            } else if( el.type === 'checkbox' && !el.checked) {
                                valid = false;
                                err = this.str.ntf_reqFields;
                            }
                        }

                        break;
                }

                // Show error
                if( !valid ) {
                    el.classList.add( 'lcx-error' );
                    this.createNtf( err, 'error', 'offlineForm', true );

                    return;

                // Valid! Save and go to the next field
                } else {
                    let fieldName = el.name;

                    switch( fieldName ) {
                        case 'name': fieldName = 'nickname'; break;
                        case 'email': fieldName = 'emailForNtf'; break;
                        case 'company_name': fieldName = 'companyName'; break;
                    }

                    // Update data
                    this.db.ref( `users/${this._uid}/${fieldName}` ).set( el.value );

                    // Success!
                    form.classList.add( 'lcx-field--success' );
                    el.classList.remove( 'lcx-error' );
                    el.classList.add( 'lcx-disabled' );
                    el.disabled = true;

                    // Show valid field
                    form.querySelector( '.lcx-valid-field' ).classList.remove( '__lcx-hide' );

                    // Hide submit button
                    submitBtn.classList.add( '__lcx-hide' );

                    // Show up next step
                    if( form.nextElementSibling ) {
                        form.nextElementSibling.classList.remove( '__lcx-hide' );
                        fn_listenField( form.nextElementSibling );
                    
                    // Finish collector card
                    } else {
                        setTimeout( () => {
                            this.createMsg( 'basic', '_preCard-' + this.rand(), {
                                date: Date.now(),
                                unread: true,
                                msg: ( this.$_onlineOps ) ? this.str.collector_postcard_online : this.str.collector_postcard_offline,
                                uid: 'bot',
                                sound: 'msg-alt'
                            });
                        }, 2000 );
                    }
                }


            }
        };

        this._cardtimeout = window.setTimeout( fn_precardMsg, 5000 );

        // Listen reply box
        const $reply = this.$popup.querySelector( '.lcx-reply' );
        if( $reply ) {
            $reply.addEventListener( 'keyup', (e) => {
                if( postpone ) {
                    window.clearTimeout( this._cardtimeout );

                    switch( step ) {
                        case 0:
                            this._cardtimeout = window.setTimeout( fn_precardMsg, 5000 );
                            break;
                        case 1:
                            this._cardtimeout = window.setTimeout( fn_show, 5000 );
                            break;
                    }
                }
            });
        }
    }

    /**
     * Refresh UI by "away" mode.
     */
    refreshAwayUI() {

        if( this._popup !== 'online' || this.opts.offlineInit !== 'showOfflineForm' )
            return;

        const hideObjs = this.$popup.querySelectorAll( '.lcx-hideOnOffline' );
        const showObjs = this.$popup.querySelectorAll( '.lcx-showOnOffline' );
        
        if( !this._chatid && this._mode === 'away' ) {
            for( var obj of hideObjs )
                obj.classList.add( '__lcx-hide' );

            for( var obj of showObjs )
                obj.classList.remove( '__lcx-hide' );


            // 
            // Listen offline form.
            // 
            const offlineForm = this.$popup.querySelector( '.lcx-form--offline' );
            const profile = this.$_profile;

            // Auto-fill the form.
            for( var el of offlineForm.elements ) {
                switch( el.name ) {
                    case 'email':
                        if( 'emailForNtf' in profile )
                            el.value = profile.emailForNtf;
                        else if( 'email' in profile )
                            el.value = profile.email;
                        break;

                    case 'name':
                        if( 'name' in profile )
                            el.value = profile.name;
                        break;
                }
            }


            const sendOffline = this.$popup.querySelector( '.lcx-btn--offflineForm' );
            if( sendOffline ) {
                sendOffline.addEventListener( 'click', (e) => {
                    e.preventDefault();
                    let form = {};
                    let valid;
                    let err;
                    const elements = offlineForm.elements;

                    // Show "sending" message
                    this.createNtf( this.str.ntf_sending, 'blink', 'offlineForm' );

                    sendOffline.classList.add( 'lcx-disabled' );

                    // Reset fields
                    for( var el of elements ) {
                        el.classList.remove( 'lcx-error' );
                    }
                    // Validate fields
                    for( var el of elements ) {
                        valid = true;
                        err = '';
                        switch( el.nodeName ) {
                            case 'INPUT':
                            case 'TEXTAREA':
                            case 'SELECT':
                                form[el.name] = el.value;
                                break;
                        }

                        if( el.required ) {
                            if( el.value.length === 0 ) {
                                valid = false;
                                err = this.str.ntf_reqFields;
                            } else if( el.type === 'email' && !this.isEmail( el.value ) ) {
                                valid = false;
                                err = this.str.ntf_invEmail;
                            } else if( el.type === 'checkbox' && !el.checked) {
                                valid = false;
                                err = this.str.ntf_reqFields;
                            }
                        }

                        if( !valid ) {
                            el.classList.add( 'lcx-error' );
                            this.createNtf( err, 'error', 'offlineForm', true );
                            sendOffline.classList.remove( 'lcx-disabled' );
                            break;
                        }
                    }

                    if( valid ) {
                        this.post( 'sendOffline', form, (r) => {
                            sendOffline.classList.remove( 'lcx-disabled' );

                            // Successfully sent!
                            if( r && !r.error ) {
                                this.goPage( 'cnv' );
                                this.createNtf( this.str.ntf_send_success, 'success', 'offlineForm', true );
                                

                            // Something went wrong..
                            } else
                                this.createNtf( this.str.ntf_smth_wrong, 'error', 'offlineForm', true );
                        });
                    }
                });
            }

            return;
        }

        for( var obj of showObjs )
            obj.classList.add( '__lcx-hide' );

        for( var obj of hideObjs )
            obj.classList.remove( '__lcx-hide' );


    }

    /**
     * Set a widget attribute.
     */
    attr( name, value ) {

        name = `data-${name}`;

        this._d.body.setAttribute( name, value );
        this._widget.setAttribute( name, value );
    }
    delAttr( name ) {
        this._d.body.removeAttribute( name );
        this._widget.removeAttribute( name );
    }
    

    /**
     * 
     * ======= FRONT-END EVENTS =======
     *
     */
    /**
     * New message is created on front-end.
     */
    _onCreateMsg( type, msgid ) {

        // Trigger event
        this.event.emit( 'createMsg', type, msgid );
    }

    /**
     * The popup is opened (showed up).
     */
    _onOpenPopup() {

        if( this._popup === 'online' ) {
            // Scroll down
            this.scrollDown( this.$pbody );
        }

        // Trigger event
        this.event.emit( 'openPopup' );
    }

    /**
     * The popup is closed/minimized.
     */
    _onClosePopup() {

        // Reset popup
        this._popup = '';

        // Trigger event
        this.event.emit( 'closePopup' );
    }

    /**
     * A popup page is opened.
     */
    _onOpenPopupPage( name ) {

        // 
        // Online popup.
        // 
        if( name === 'online' ) {
            this.$msgs = this.$popup.querySelector( '.lcx-msgs' );

            // Listen reply box
            this.listenReply( 
                this.$popup.querySelector('.lcx-reply'), 
                true /*autofocus*/
            );

            // Load operators list
            this.showOps();

            // Load messages
            this.loadCnv();

            // Setup chat UI
            this.chatUI();

            // Make chat status "new" if no chat is active
            if( !this._chatid )
                this.attr( 'chat-status', 'new' );

        // 
        // Conversation popup.
        // 
        } else if( name === 'cnv' ) {

            // Show conversations list
            this.showCnvs();

            // Reset chat
            this.resetChat();
        }

        // Re-check actions
        this.actions();

        // Refresh away user interface
        this.refreshAwayUI();

        // Trigger event
        this.event.emit( 'newPopupPage', name );
    }


    /**
     * 
     * ======= DATABASE EVENTS =======
     *
     */
    _onOnlineOpsUpdate( snap ) {
        const mode = snap.exists() ? 'online' : 'away';

        this.$_onlineOps = snap.exists() ? snap.val() : {};

        // Go online/away mode by online operators status
        this.mode( mode );

        // Show up chat box if any operator is online
        if( this.opts.hideOffline ) {
            
            if( mode === 'online' ) {
                this._widget.classList.remove( 'lcx--hidden' );
            
            // Hide chat box if popup is closed
            } else if( !this._pstate )
                this._widget.classList.add( 'lcx--hidden' );
        }
        // Update current chat operator status
        /*if( this._chatid ) {
            if( this._chat && this._chat.opid ) {

            }
        }*/
    }

    /**
     * Network is connected.
     */
    _onConnect() {

        this.hideNtf( 'conn' );

        this.attr( 'connected', true );

        // Initialize the database completely
        if( !this._dbInit ) {

            const fn_listenOps = ( snap ) => {

                // Get recently active operators
                this.$_operators = snap.exists() ? snap.val() : {};

            };

            this.db.ref( 'operators' ).orderByChild('lastOnline').on( 'value', fn_listenOps );

            // Listen online operator changes
            this.db.ref( 'onlineOps' ).on( 'value', this._onOnlineOpsUpdate.bind(this) );


            // Database initialized successfully.
            this._dbInit = true;

            // Invoke the event
            this.event.emit( 'connect' );

        // Re-connected
        } else {
            // Throw re-connected notification.
            this.createNtf( this.str.ntf_connected, 'success', 'conn', true );
        }
        
        /*if( !this._manualConn ) {

            this.db.ref( 'onlineOps' ).once( 'value' ).then( ( snap ) => {

                // Go online/away mode by online operators status
                this.mode( snap.hasChildren() ? 'online' : 'away' );

                // Show recently online 3 operators
                this.db.ref( 'operators' ).orderByChild('lastOnline').limitToLast(3).on( 'value', ( snapOps ) => {

                    if( snapOps.exists() ) {
                        this.$_operators = snapOps.val();
                    }

                    this.showOps();

                    // Disconnect from real-time database 
                    // if visitor isn't signed in yet
                    if( !this.auth.currentUser ) {
                        this.db.goOffline();
                        return;
                    }
                    // Re-connected message.
                    if( this._dbInit )
                        this.createNtf( this.str.ntf_connected, 'success', 'conn', true );
                    
                    // Database initialized successfully.
                    this._dbInit = true;

                    // Invoke the event
                    this.event.emit( 'connect' );
                });
            });
        }*/

    }
    /**
     * Network is disconnected.
     */
    _onDisconnect() {

        if( this._dbInit ) {
            this.createNtf( this.str.ntf_no_conn, 'error', 'conn' );
            
            // Invoke the event
            this.event.emit( 'disconnect' );
        }

    }
    
    /**
     * Current user updates.
     */
    _onProfileUpdate( snap ) {

        if( !snap.exists() )
            return;

        let needUpdate = false;
        const user = snap.val();
        this.$_profile = user;

        if( user.chatsAsVisitor ) {
            for( var chatid in user.chatsAsVisitor ) {
                this._onNewChat( chatid );
            }
        }

        let userData = {};

        if( !this.opts.disableLookingAt && this._w.location.href !== user.currentPageUrl ) {
            needUpdate = true;
            userData.currentPageUrl = this._w.location.href;
        }

        if( !user.nickname ) {
            needUpdate = true
            for( const key in this.opts.user._nickname )
                userData[key] = this.opts.user._nickname[key];
        }

        if( this.opts.user._custom ) {
            if( userData.custom && userData.custom.ipAddr !== this.opts.user._custom.ipAddr )
                needUpdate = true

            userData.custom = this.opts.user._custom;
        }

        if( needUpdate ) {
            this.db.ref( `users/${snap.key}` ).update( userData );
        }

        // Invoke the event
        this.event.emit( 'profile', this.$_profile );
    }
    
    /**
     * Authentication updates.
     */
    _onAuthState( user ) {

        if( user ) {
            this._uid = user.uid;
            this._uname = user.displayName;
            this._user = user;

            const now = firebase.database.ServerValue.TIMESTAMP;
            const userRef = this.db.ref( `users/${user.uid}` );
            
            if( !this.opts.disableLookingAt )
                userRef.child( 'lastSeen').onDisconnect().set( now );

            window.localStorage.setItem( 'lcx-refreshToken', user.refreshToken );

            // Listen further updates
            userRef.on( 'value', this._onProfileUpdate.bind(this) );

            this.startSess();
            
            // Invoke the event
            this.event.emit( 'signin', user );
        
        } else {
            this.resetUser();

            // Invoke the event
            this.event.emit( 'signout', user );
        }
    }

    /**
     * Handle authentication error events.
     */
    _onAuthErr( error ) {
        this.createNtf( error.message, 'error', 'auth' );
    }

    /**
     * Open chat.
     */
    _onOpenChat( chatid, chat ) {

        // Invoke the event
        this.event.emit( 'openChat', chatid, chat );
    }

    /**
     * New chat is created.
     */
    _onNewChat( _chatid ) {

        if( _chatid in this._chats )
            return;

        this._chats[_chatid] = true;

        // Listen chat updates
        this.db.ref( `chats/${_chatid}` ).on( 'value', this._onUpdateChat.bind(this) );
        this.db.ref( 'chats' ).on( 'child_removed', this._onDeleteChat.bind(this) );

        

    }

    /**
     * Delete chat.
     */
    _onDeleteChat( snap ) {
        const chatid = snap.key;

        if( !( chatid in this.$_chats ) ) 
            return;

        this.db.ref( `chats/${chatid}` ).off();

        // Reset chat data
        delete this.$_chats[chatid];
        delete this.$_msgs[chatid];
        delete this.$_members[chatid];
        delete this._cnvList[chatid];

        if( this._chatid === chatid ) {
            this.resetChat();
            this.goPage( 'cnv' );
        }
        
        // Delete chat from conversations list
        this.delObj( 'lcx-cnv-item-' + chatid );

        this.event.emit( 'deleteChat', chatid );

    }

    /**
     * Update chat.
     */
    _onUpdateChat( snap ) {

        if( !snap.exists() ) {
            return;
        } 

        let eventName;
        const chatid = snap.key;
        const data = snap.val();

        //
        // It is new chat.
        //
        if( !( chatid in this.$_chats ) ) {
            
            this.$_msgs[chatid] = {};
            this.$_members[chatid] = {};

            // Listen chat messages
            const msgsRef = this.db.ref( `messages/${chatid}` );
            msgsRef.on( 'child_added', this._onNewMsg.bind(this) );
            msgsRef.on( 'child_changed', this._onUpdateMsg.bind(this) );
            msgsRef.on( 'child_removed', this._onDeleteMsg.bind(this) );

            // Listen chat members
            /*const membersRef = this.db.ref( `members/${chatid}` );
            membersRef.on( 'child_added', this._onNewMember.bind(this) );
            membersRef.on( 'child_changed', this._onUpdateMember.bind(this) );
            membersRef.on( 'child_removed', this._onDeleteMember.bind(this) );*/

            // Update widget data
            this.attr( 'has-cnv', true );

            // Add conversation in the chats list
            this.createCnv( chatid, data );

            // Update conversations list
            this.showCnvs();

            // 
            // Notify operators if not done before
            // 
            if( !data.notified ) {
                this.post( 'notifyOps', {
                    caseNo: data.caseNo,
                    visitorName: this.$_profile.nickname,
                    msg: data.lastMsg
                }, (r) => {
                    if( !r.error )
                        this.db.ref( `chats/${chatid}/notified` ).set( true );
                });
            }

            // Set the event
            eventName = 'newChat';


        // 
        // Update chat.
        // 
        } else {
            
            // Set the event
            eventName = 'updateChat';
        }

        // Update chat data
        this.$_chats[chatid] = data;
        this.openChat( this._chatid );

        // Open chat window if its the active one
        if( this._chatid && !this._chat) {
            this.goPage( 'online' );
        }

        if( this._chatid === chatid ) {
            this._chat = data;

            // Operator is typing...
            /*if( data.opTyping ) {
                this.createMsg( 'typing', 'lcx-typing', {
                    uid: 'operator'
                });

            // No operator typing...
            } else {

            }*/
        }


        // Update operators list
        this.showOps();

        // Update chat UI
        this.chatUI();

        // Invoke the event.
        this.event.emit( eventName, chatid, data );

    }

    /**
     * Chat messages.
     */
    _onNewMsg( snap, prevId ) {
        const msg = snap.val();
        const msgid = snap.key;

        if( msg.chatid in this.$_chats ) {
            const lastRead = this.$_chats[msg.chatid].lastRead || null;
            msg.__unread = lastRead && lastRead < msg.date && ( msg.platform !== this.opts.platform || msg.uid !== this._uid  );
        }

        // Update temporary message meta
        if( this._initMsg ) {
            this.updateMsgMeta( this._initMsg, this.str.date_seconds+'.' );
            this._initMsg = '';
        }

        // Update data
        this.$_msgs[msg.chatid][msgid] = msg;

        let sound = msg.uid === this._uid ? 'msg-alt' : 'msg';
        if( msg.__unread ) {
            this.inAppMsg( msg.msg, msg.name, msg.chatid );

            this.play( 'msg' );

            const newTitle = this.str.ntf_newMsg_wtitle.replace( '%s', msg.name );

            // Blink title
            const fn_blink = () => {
                document.title = document.title === newTitle ? this._wtitle : newTitle;
            };
            this._wtimeout = window.setInterval( fn_blink, 700 );
            // Mark chat as unread
            this._unreads[msg.chatid] = true;
        }

        // Update new messages counter
        this.updateCount();

        // Create message if we're on active chat
        if( this._chatid === msg.chatid ) {
            this.createMsg( msg.type, msgid, {
                date: msg.date,
                unread: msg.__unread || false,
                msg: msg.msg,
                uid: msg.uid,
                platform: msg.platform,
                sound: sound
            });
        }

        // Unfreeze reply box
        this.freezeReply( false );

        // Invoke the event
        this.event.emit( 'newMsg', msgid, msg, prevId );
    }
    _onUpdateMsg( snap, prevId ) {
        const msg = snap.val();
        const msgid = snap.key;

        // Update data
        this.$_msgs[msg.chatid][msgid] = msg;

        // Invoke the event
        this.event.emit( 'updateMsg', msgid, msg, prevId );
    }
    _onDeleteMsg( snap, prevId ) {
        const msgid = snap.key;
        const msg = snap.val();

        if( this.$_msgs[msg.chatid] )
            delete this.$_msgs[msg.chatid][msgid];

        this.event.emit( 'deleteMsg', msgid, msg, prevId );
    }



    /**
     * ====== HELPERS =====
     */
    _escRgx( str ) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }
    
    /**
     * Simple replace-all function.
     */
    replace( str, data, isTag ) {
        let findme = '';

        for( const find in data ) {
            findme = isTag ? '{'+find+'}' : find;
            str = str.replace( new RegExp( this._escRgx( findme ), 'g' ), data[find] );
        }

        return str;
    }

    /**
     * Play a sound.
     */
    play( name ) {
        const audio = new Audio( this.opts._pluginurl + '/assets/sounds/' + name + '.mp3' );
        const p = audio.play();

        if (p && (typeof Promise !== 'undefined') && (p instanceof Promise)) {
            p.catch(() => {});
        }
    }

    /**
     * Sanitize a message.
     */
    sanitize( str ) {

        // URLs starting with http://, https://, or ftp://
        const rgx1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
        str = str.replace(rgx1, '<a href="$1" target="_blank">$1</a>');

        // URLs starting with "www." (without // before it, or it'd re-link the ones done above).
        const rgx2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
        str = str.replace(rgx2, '$1<a href="http://$2" target="_blank">$2</a>');

        // Change email addresses to mailto:: links.
        const rgx3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
        str = str.replace(rgx3, '<a href="mailto:$1">$1</a>');

        return str;
    }

    /**
     * Get random number starts from 0.
     */
    rand( max = 9999999 ) {
        return Math.floor( Math.random() * max ) + 1;
    }

    /**
     * Remove a DOM object.
     *
     * @param obj string|object  Object ID or object itself.
     */
    delObj( obj ) {
        if( typeof obj === 'string' )
            obj = this._d.getElementById( obj );

        if( obj )
            obj.parentNode.removeChild( obj );
    }

    /**
     * Time ago.
     *
     * Usage:
     *      <abbr class="timeago" data-datetime="2011-12-17T09:24:17Z">2 years ago</abbr>
     *      <abbr class="timeago" data-datetime="December 17, 2012">6 months ago</abbr>
     *      <time class="timeago" datetime="2013-01-17T09:24:17Z">5 months ago</time>
     *      <span class="timeago" data-datetime="1372218564">about 20 hours ago</span>
     *
     * @link: https://coderwall.com/p/uub3pw/javascript-timeago-func-e-g-8-hours-ago
     */
    timeago( obj ) {
        const str = {
            prefix: this.str.date_prefix,
            suffix: this.str.date_suffix,
            seconds: this.str.date_seconds,
            minute: this.str.date_minute,
            minutes: this.str.date_minutes,
            hour: this.str.date_hour,
            hours: this.str.date_hours,
            day: this.str.date_day,
            days: this.str.date_days,
            month: this.str.date_month,
            months: this.str.date_months,
            year: this.str.date_year,
            years: this.str.date_years
        };
        const template = function(t, n) {
            let txt = str[t] && str[t].replace(/%d/i, Math.abs(Math.round(n)));

            if( t !== 'seconds' )
                txt += str.suffix;

            return txt;
        };

        const timer = function(time) {
            if (!time)
                return;
            time = time.replace(/\.\d+/, ""); // remove milliseconds
            time = time.replace(/-/, "/").replace(/-/, "/");
            time = time.replace(/T/, " ").replace(/Z/, " UTC");
            time = time.replace(/([\+\-]\d\d)\:?(\d\d)/, " $1$2"); // -04:00 -> -0400
            time = new Date(time * 1000 || time);

            const now = new Date();
            const seconds = ( ( now.getTime() - time ) * .001 ) >> 0;
            const minutes = seconds / 60;
            const hours = minutes / 60;
            const days = hours / 24;
            const years = days / 365;

            return str.prefix + (
                seconds < 45 && template( 'seconds', seconds ) ||
                seconds < 90 && template( 'minute', 1 ) ||
                minutes < 45 && template( 'minutes', minutes ) ||
                minutes < 90 && template( 'hour', 1 ) ||
                hours < 24 && template( 'hours', hours ) ||
                hours < 42 && template( 'day', 1 ) ||
                days < 30 && template( 'days', days ) ||
                days < 45 && template( 'month', 1 ) ||
                days < 365 && template( 'months', days / 30 ) ||
                years < 1.5 && template( 'year', 1 ) ||
                template( 'years', years )
            );
        };

        if( obj )
            obj.innerHTML = timer( obj.getAttribute( 'datetime' ) );
    }

    /**
     * Format time.
     */
    time( ts, format ) {
        
        const date = new Date( ts );
        const val = {
            Y: date.getFullYear(),
            m: date.getMonth()+1,
            d: date.getDate(),
            H: date.getHours(),
            i: date.getMinutes(),
            s: date.getSeconds()
        };

        // Convert one digit "0" to "00" for both hours and minutes
        if( val.H.toString().length === 1 ) { val.H = '0' + val.H; }
        if( val.i.toString().length === 1 ) { val.i = '0' + val.i; }

        /* Source: http://stackoverflow.com/a/15604206/272478 */
        format = format.replace(/Y|m|d|H|i|s/gi, function(matched){
            return val[matched];
        });

        return format;
    }


    /**
     * Validate email.
     */
    isEmail( email ) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test( email );
    }
    /**
     * Send a post request to the server.
     */
    post( mode, data, callback ) {

        data.mode = mode;
        data.action = data.action || 'lcx_action';
        data._ajax_nonce = this.opts.ajax.nonce;

        const xhr = new XMLHttpRequest();
        const fd = new FormData();

        xhr.open( 'POST', this.opts.ajax.url, true );

        // Handle response
        xhr.onreadystatechange = function() {

            if ( xhr.readyState == 4 ) {

                // Perfect!
                if( xhr.status == 200 ) {
                    if( callback ) { callback( JSON.parse( xhr.responseText ) ); }

                // Something wrong!
                } else {
                    if( callback ) { callback( null ); }
                }
            }

        };

        // Get data
        for( const k in data ) { fd.append( k, data[k] ) ; }

        // Initiate a multipart/form-data upload
        xhr.send( fd );
    }

    /**
     * Scroll object to down.
     */
    scrollDown( obj ) {
        obj.scrollTop = obj.scrollHeight;
    }

}