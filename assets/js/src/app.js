/*!
 * Live Chat X, by Screets.
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

class NBird {

	/**
	 * Validate email.
	 */
	static isEmail( email ) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test( email );
	}

	/**
	 * Format time.
	 */
	static time( ts, format ) {
		
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
	static timeago( obj, str ) {
		/*var str = {
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
	    };*/
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
	        const seconds = ((now.getTime() - time) * .001) >> 0;
	        const minutes = seconds / 60;
	        const hours = minutes / 60;
	        const days = hours / 24;
	        const years = days / 365;

	        return str.prefix + (
	                seconds < 45 && template('seconds', seconds) ||
	                seconds < 90 && template('minute', 1) ||
	                minutes < 45 && template('minutes', minutes) ||
	                minutes < 90 && template('hour', 1) ||
	                hours < 24 && template('hours', hours) ||
	                hours < 42 && template('day', 1) ||
	                days < 30 && template('days', days) ||
	                days < 45 && template('month', 1) ||
	                days < 365 && template('months', days / 30) ||
	                years < 1.5 && template('year', 1) ||
	                template('years', years)
	                );
	    };

	    if( obj ) {
	    	obj.innerHTML = timer( obj.getAttribute( 'datetime' ) );
	    } else {
		    const elements = document.getElementsByClassName( 'lcx-timeago' );
		    for ( var i in elements ) {
		        obj = elements[i];
		        if ( typeof obj === 'object' ) {
		            obj.innerHTML = timer( obj.getAttribute('datetime') );
		        }
		    }
	    }
	}

	/**
	 * Returns a random integer between min (inclusive) and max (inclusive)
 	 * Using Math.round() will give you a non-uniform distribution!
	 */
	static randInt( min, max ) {
	    return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	/**
	 * Remove a DOM object.
	 *
	 * @param obj string|object  Object ID or object itself.
	 */
	static delObj( obj ) {
		if( typeof obj === 'string' ) {
			obj = document.getElementById( obj );
		}

		if( obj ) {
			obj.parentNode.removeChild( obj );
		}
	}

	/**
	 * Insert a DOM object after an element.
	 *
	 * @link https://stackoverflow.com/a/4793630/272478
	 */
	static insertAfter( newNode, refNode ) {
		refNode.parentNode.insertBefore(newNode, refNode.nextSibling);
	}

	/**
	 * A simple replace-all function.
	 */
	static replace( str, data, isTag ) {
		let findme = '';

		for( const find in data ) {
			findme = isTag ? '{'+find+'}' : find;
			str = str.replace( new RegExp( NBird._escRgx( findme ), 'g' ), data[find] );
		}

		return str;
	}
	static _escRgx( str ) {
		return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
	}

	/**
	 * Replace all tags in a strings.
	 */
	static replaceAll( str, data, wrapid, prefix='' ) {

		for( const find in data ) {
			// Get raw data without wrap: {__TAGNAME}
			str = str.replace( new RegExp( NBird._escRgx('{__'+prefix+find+'}'), 'g' ), data[find] );

			// Add wrapper onto {TAGNAME} style tags
			if( wrapid )
				str = str.replace( new RegExp( NBird._escRgx('{'+prefix+find+'}'), 'g' ), '<span class="_nbird-tpl-'+find+' _nbird-tpl-'+prefix+wrapid+'-'+find+'">'+data[find]+'</span>');
		}

		return str;
	}

	/**
	 * Sanitize a message.
	 */
	static sanitizeMsg( str ) {

		// Remove "p" and "span" tags
		str = str.replace(/<\/?p[^>]*>/g,"");
		str = str.replace(/<\/?span[^>]*>/g,"");

		return str;
	}

	/**
	 * Render a message.
	 */
	static renderMsg( str ) {
		let rgx1, 
			rgx2, 
			rgx3;

	    // URLs starting with http://, https://, or ftp://
	    rgx1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
	    str = str.replace(rgx1, '<a href="$1" target="_blank">$1</a>');

	    // URLs starting with "www." (without // before it, or it'd re-link the ones done above).
	    rgx2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
	    str = str.replace(rgx2, '$1<a href="http://$2" target="_blank">$2</a>');

	    // Change email addresses to mailto:: links.
	    rgx3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
	    str = str.replace(rgx3, '<a href="mailto:$1">$1</a>');

	    return str;
	}

	/**
	 * Play a sound.
	 */
	static play( name, pluginurl ) {
		const audio = new Audio( pluginurl + '/assets/sounds/' + name + '.mp3' );
		const p = audio.play();

		if (p && (typeof Promise !== 'undefined') && (p instanceof Promise)) {
			p.catch(() => {});
		}
	}

	/**
	 * Refresh existing template tags.
	 */
	static refreshTags( id, data, prefix='' ) {
		let tags;
		for( const key in data ) {
			tags = document.getElementsByClassName( '_nbird-tpl-' + prefix + id + '-' + key );

			if( tags ) {
				let html;
				for( var i=0; i<tags.length; i++ ) {
					tags[i].innerHTML = data[key];
				}
			}
		}
	}

	/**
	 * Clear empty template tags.
	 */
	static clearTags( str, prefix ) {
		const fn_escRgx = function( str ) {
			return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
		};

		str = str.replace( new RegExp( '{__' + prefix + '(.*?)' + '}', 'gm' ), '' );

		return str;
	}

	/**
	 * Select all text in an object.
	 */
	static selAll( obj ) {
		var range = document.createRange();
		range.selectNodeContents( obj );

		var sel = window.getSelection();
		sel.removeAllRanges();
		sel.addRange(range);
	}

	/**
	 * Find ideal text color by giving color.
	 */
	static idealTextColor( hex ) {
		const nThreshold = 105;
		const components = this.convertToRGB( hex );
		const bgDelta = ( components.R * 0.299 ) + ( components.G * 0.587 ) + ( components.B * 0.114 );

		return ( ( 255 - bgDelta ) < nThreshold ) ? '#333' : '#fff';   
	}

	/**
	 * Convert hex color to RGB.
	 */
	static convertToRGB( hex ) {       
		var r = hex.substring( 1, 3 );
		var g = hex.substring( 3, 5 );
		var b = hex.substring( 5, 7 );

		return {
			R: parseInt( r, 16 ),
			G: parseInt( g, 16 ),
			B: parseInt( b, 16 )
		};
	}

	/**
	 * Scroll object to down.
	 */
	static scrollDown( obj ) {
		obj.scrollTop = obj.scrollHeight;
	}

	/**
	 * Send a post request to the server.
	 */
	static post( mode, data, ajax, callback ) {

		data.mode = mode;
		data.action = data.action || 'lcx_action';
		data._ajax_nonce = ajax.nonce;

		const xhr = new XMLHttpRequest();
		const fd = new FormData();

		xhr.open( 'POST', ajax.url, true );

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

	static get( url, data, callback ) {
		const xhr = new XMLHttpRequest();

		xhr.open( 'GET', url, true );

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

		xhr.send();
	}
	
}