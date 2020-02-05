( function ( window, document, $ ) {
	'use strict';

	var $postStatus = $( '#posts-status' ),
		$prepareStatus = $( '#prepare-status' ),
		$termStatus = $( '#terms-status' ),
		$platformSelect = $( '#platform' ),
		$button = $( '#process' ),
		platform,
		restart;

	$button.on( 'click', async function () {
		platform = $platformSelect.val();
		try {
			preProcess();
			const prepare = await prepareMigration();
			const migratePosts = await handleMigratePosts( prepare );
			postsMigrationCallback( migratePosts, handleMigratePosts );
			restart = 1;
			const handleMigrateTerms = await handleMigrateTerms( )
		} catch ( err ) {
			printMessage( $prepareStatus, err.responseJSON.data );
		}
	} );

	function printMessage( $status, text ) {
		var message = '<p>' + text + '</p>';
		$status.html( message );
	}

	function preProcess() {
		$button.closest( '.migration-handler' ).hide();
		printMessage( $prepareStatus, ssMigration.preProcessText );
	}

	/**
	 * Before migration.
	 * Setup replacer and restart the counter.
	 */
	function prepareMigration() {
		return $.post( ajaxurl, {
			action: 'prepare_migration',
			platform,
			_ajax_nonce: ssMigration.nonce
		} )
	}

	/**
	 * Import data.
	 * Keep sending ajax requests for the action until done.
	 */
	function handleMigratePosts() {
		return $.post( ajaxurl, {
			action: 'migrate_posts',
		} );
	}

	function handleMigrateTerms() {
		return $.post( ajaxurl, {
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
