( function ( window, document, $ ) {
	'use strict';

	var $status = $( '#status' ),
		$button = $( '#process ' ),
		$ajaxLoader = $button.siblings( '.spinner' ),
		restart;

	$button.on( 'click', function ( e ) {
		e.preventDefault();
		$ajaxLoader.css( {
			'display': 'inline-block',
			'float': 'none'
		} );

		// Set global variable true to restart again
		restart = 1;
		handleMigrate();
	} );

	/**
	 * Import data.
	 * Keep sending ajax requests for the action until done.
	 */
	function handleMigrate() {
		$.post( ajaxurl, {
			action: 'migrate_yoast',
			restart: restart,
			_ajax_nonce: $button.data( 'nonce' )
		}, function ( response ) {
			restart = 0; // Set this global variable = false to make sure all other calls continue properly.
			callback( response, importData );
		} );
	}

	/**
	 * Callback function to display messages
	 *
	 * @param response JSON object returned from WordPress
	 * @param func Callback function
	 */
	function callback( response, func ) {
		var html = $status.html(),
			message;

		if ( ! response.success ) {
			$status.addClass( 'error' );

			message = '<p>' + response.data + '</p>';
			html = html ? html + message : message;
			$status.html( html );
			return;
		}

		if ( response.data.message ) {
			$status.addClass( 'updated' );
			message = response.data.message ? '<p>' + response.data.message + '</p>' : '';
			html = html ? html + message : message;
			$status.html( html );
		}

		// Submit form again
		if ( response.data.type == 'continue' ) {
			func();
		} else {
			$ajaxLoader.hide();
			alert( $button.data( 'done_text' ) );
		}
	}

} )( window, document, jQuery );
