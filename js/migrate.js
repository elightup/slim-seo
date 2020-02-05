( function ( window, document, $ ) {
	'use strict';

	var $postStatus = $( '#posts-migration-status' ),
		$termStatus = $( '#terms-migration-status' ),
		$platformSelect = $( '#platform' ),
		$button = $( '#process ' ),
		platform,
		restart;

	$button.on( 'click', function ( e ) {
		e.preventDefault();

		platform = $platformSelect.val();

		// Set global variable true to restart the session.
		restart = 1;
		preparing();
		beforeMigration();
	} );

	function preparing() {
		$button.closest( 'form' ).hide();
		var message = '<p>' + $button.data( 'pre-process' ) + '</p>';
		$postStatus.html( message );
	}

	/**
	 * Before migration.
	 * Setup replacer and restart the counter.
	 */
	function beforeMigration() {
		$.post( ajaxurl, {
			action: 'before_migration',
			platform,
			_ajax_nonce: $button.attr( 'data-nonce' )
		}, function ( response ) {
			restart = 0; // Set this global variable = false to make sure all other calls continue properly.
			handleMigratePosts();
		} );
	}

	/**
	 * Import data.
	 * Keep sending ajax requests for the action until done.
	 */
	function handleMigratePosts() {
		$.post( ajaxurl, {
			action: 'migrate_posts',
			_ajax_nonce: $button.attr( 'data-nonce' )
		}, function ( response ) {
			postsMigrationCallback( response, handleMigratePosts );
		} );
	}

	function handleMigrateTerms() {
		$.post( ajaxurl, {
			action: 'migrate_terms',
			restart: restart, // reset again after posts migration.
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

		// Submit form again
		if ( response.data.type == 'continue' ) {
			message = '<p>' + response.data.message + '</p>';
			$termStatus.html( message );
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
			message = '<p>' + response.data + '</p>';
			$postStatus.html( message );
			return;
		}

		// Submit form again
		if ( response.data.type == 'continue' ) {
			message = '<p>' + response.data.message + '</p>';
			$postStatus.html( message );
			func();
		} else {
			restart = 1; // reset again after posts migration.
			handleMigrateTerms();
		}
	}

} )( window, document, jQuery );
