/**
 * Live Chat X, by Screets.
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 *
 * Original source: https://github.com/krasimir/EventBus
 */

'use strict';

(function (root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define("lcxEmoji", [], factory);
	else if(typeof exports === 'object')
		exports["lcxEmoji"] = factory();
	else
		root["lcxEmoji"] = factory();
})(this, function() {

	var lcxEmojiClass = {};
	lcxEmojiClass = function() {
		this.listeners = {};
	};
	lcxEmojiClass.prototype = {

		on: function( type, callback, scope ) {
			var args = [];
			var numOfArgs = arguments.length;
			for(var i=0; i<numOfArgs; i++){
				args.push(arguments[i]);
			}
			args = args.length > 3 ? args.splice(3, args.length-1) : [];
			if(typeof this.listeners[type] != "undefined") {
				this.listeners[type].push({scope:scope, callback:callback, args:args});
			} else {
				this.listeners[type] = [{scope:scope, callback:callback, args:args}];
			}
		},

		off: function( type, callback, scope ) {
			
		}
	};
	
	var lcxEmoji = new lcxEmojiClass();
	return lcxEmoji;
});