( function ( window, document, $ ) {
	'use strict';

	var $status = $( '#status' ),
		$button = $( '#process ' ),
		$ajaxLoader = $button.siblings( '.spinner' ),
		$progressBar = $( '.ss-progressbar' ),
		$progressBarValue = $( '.ss-progressbar-value' ),
		restart;

	var totalPosts = $progressBar.data( 'max-post' );

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
			_ajax_nonce: $button.attr( 'data-nonce' )
		}, function ( response ) {
			restart = 0; // Set this global variable = false to make sure all other calls continue properly.
			callback( response, handleMigrate );
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
			$status.html( message );
			return;
		}

		if ( response.data.posts ) {
			$status.addClass( 'updated' );
			message = response.data.posts ? '<p>' + response.data.message + '</p>' : '';
			$status.html( message );
			var percentage = response.data.posts * 100 / totalPosts;
			$( '.ss-progressbar-value' ).css( 'width', percentage + '%' );
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
