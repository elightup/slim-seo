( function ( window, document, $ ) {
	'use strict';

	var $postStatus = $( '#posts-migration-status' ),
		$termStatus = $( '#terms-migration-status' ),
		$button = $( '#process ' ),
		restart;

	$button.on( 'click', function ( e ) {
		e.preventDefault();

		// Set global variable true to restart again
		restart = 1;
		preProcess();
		handleMigratePosts();
	} );

	function preProcess() {
		$button.closest('form').hide();
		var message = '<p>' + $button.data( 'pre-process' ) + '</p>';
		$postStatus.html( message );
	}

	/**
	 * Import data.
	 * Keep sending ajax requests for the action until done.
	 */
	function handleMigratePosts() {
		$.post( ajaxurl, {
			action: 'migrate_posts',
			restart: restart,
			_ajax_nonce: $button.attr( 'data-nonce' )
		}, function ( response ) {
			restart = 0; // Set this global variable = false to make sure all other calls continue properly.
			postsMigrationCallback( response, handleMigratePosts );
		} );
	}

	function handleMigrateTerms() {
		$.post( ajaxurl, {
			action: 'migrate_terms',
			restart: restart,
			_ajax_nonce: $button.attr( 'data-nonce' )
		}, function ( response ) {
			restart = 0; // Set this global variable = false to make sure all other calls continue properly.
			termsMigrationCallback( response, handleMigrateTerms );
		} );
	}

	function termsMigrationCallback( response, func ) {
		var html = $termStatus.html(),
			message;

		if ( ! response.success ) {
			$termStatus.addClass( 'error' );

			message = '<p>' + response.data + '</p>';
			$termStatus.html( message );
			return;
		}

		if ( response.data.posts ) {
			message = response.data.posts ? '<p>' + response.data.message + '</p>' : '';
			$termStatus.html( message );
		}

		// Submit form again
		if ( response.data.type == 'continue' ) {
			func();
		} else {
			message = '<p>' + $button.data( 'done_text' ) + '</p>';
			$termStatus.append( message );
		}
	}

	/**
	 * Callback function to display messages
	 *
	 * @param response JSON object returned from WordPress
	 * @param func Callback function
	 */
	function postsMigrationCallback( response, func ) {
		var html = $postStatus.html(),
			message;

		if ( ! response.success ) {
			$postStatus.addClass( 'error' );

			message = '<p>' + response.data + '</p>';
			$postStatus.html( message );
			return;
		}

		if ( response.data.posts ) {
			message = response.data.posts ? '<p>' + response.data.message + '</p>' : '';
			$postStatus.html( message );
		}

		// Submit form again
		if ( response.data.type == 'continue' ) {
			func();
		} else {
			restart = 1;
			handleMigrateTerms();
		}
	}

} )( window, document, jQuery );
