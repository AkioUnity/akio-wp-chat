/*!
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

document.addEventListener( 'DOMContentLoaded', function() {
	

	let conditionNum = 0;
	const BTN_NEW_COND = document.getElementById( 'lcx-btn-add-condition' );
	const WRAP_CONDS = document.getElementById( 'lcx-conditions' );
	const OBJ_COND = document.getElementById( 'lcx-condition-group-0' );

	BTN_NEW_COND.addEventListener( 'click', ( e ) => {
		e.preventDefault();

		conditionNum++;

		const COND_ID = 'lcx-condition-group-' + conditionNum;

		const DIV = document.createElement( 'div' );
		DIV.id = COND_ID;
		DIV.className = `lcx-condition-group ${COND_ID}`;
		DIV.innerHTML = OBJ_COND.innerHTML;


		WRAP_CONDS.appendChild( DIV );
		WRAP_CONDS.classList.add( 'lcx-multiple' );

	});
	

});