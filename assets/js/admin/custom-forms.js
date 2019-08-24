/*!
 *
 * SCREETS, d.o.o. Sarajevo. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

;(function ($) {	

	var W = window,
		D = document;

	jQuery( D ).ready(function($) {

		//
		// Add/delete form fields
		//
		$(D).on( 'click', '.lcx-btn-new-form-field', function(e) {
			e.preventDefault();
			var $row = $(this).closest('tr');
			var currentPos = parseInt( $row.find('.lcx-f-field-pos').val() ) || 0;
			var $new = $row.clone();
			$new = $new.find( 'input, textarea' ).val('').end();
			$new = $new.find('.lcx-f-field-pos').val( currentPos+10 ).end();
			$row.after( $new );
		});

		$(D).on( 'click', '.lcx-btn-delete-form-field', function(e) {
			e.preventDefault();
			$(this).closest('tr').remove();
		});

		//
		// Add/delete support categories
		//
		$(D).on( 'click', '.lcx-btn-new-support-cat', function(e) {
			e.preventDefault();
			var $row = $(this).closest('tr');
			$row.after( $row.clone().find( 'input' ).val('').end() );
		});

		$(D).on( 'click', '.lcx-btn-delete-support-cat', function(e) {
			e.preventDefault();
			$(this).closest('tr').remove();
		});
	});

})();