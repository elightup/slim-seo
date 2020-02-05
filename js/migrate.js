( function ( window, document, $ ) {
	'use strict';

	var $postStatus = $( '#posts-migration-status' ),
		$termStatus = $( '#terms-migration-status' ),
		$platformSelect = $( '#platform' ),
		$button = $( '#process' ),
		platform,
		restart;

	$button.on( 'click', function ( e ) {
		e.preventDefault();

		platform = $platformSelect.val();

		preProcess();
		prepareMigration();
	} );

	function preProcess() {
		$button.closest( '.migration-handler' ).hide();
		var message = '<p>' + ssMigration.preProcessText + '</p>';
		$postStatus.html( message );
	}

	/**
	 * Before migration.
	 * Setup replacer and restart the counter.
	 */
	function prepareMigration() {
		$.post( ajaxurl, {
			action: 'prepare_migration',
			platform,
			_ajax_nonce: ssMigration.nonce
		}, handleMigratePosts );
	}

	/**
	 * Import data.
	 * Keep sending ajax requests for the action until done.
	 */
	function handleMigratePosts() {
		$.post( ajaxurl, {
			action: 'migrate_posts',
		}, function ( response ) {
			postsMigrationCallback( response, handleMigratePosts );
		} );
	}

	function handleMigrateTerms() {
		$.post( ajaxurl, {
			action: 'migrate_terms',
			restart: restart, // reset again after posts migration.
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
			message = '<p>' + ssMigration.doneText + '</p>';
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
