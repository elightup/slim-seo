( function ( window, document, $ ) {
	'use strict';

	var $postStatus = $( '#posts-status' ),
		$doneStatus = $( '#done-status' ),
		$termStatus = $( '#terms-status' ),
		$platformSelect = $( '#platform' ),
		$button = $( '#process' ),
		platform,
		restart;

	$button.on( 'click', async function () {
		platform = $platformSelect.val();
		try {
			preProcess();
			await prepareMigration();
			await handleMigratePosts();
			resetCounter();
			await handleMigrateTerms();
			doneMigration();
		} catch ( err ) {
			printMessage( $postStatus, err.responseJSON.data );
		}
	} );

	function printMessage( $status, text ) {
		var message = '<p>' + text + '</p>';
		$status.html( message );
	}

	function preProcess() {
		$button.closest( '.migration-handler' ).hide();
		printMessage( $postStatus, ssMigration.preProcessText );
	}

	function doneMigration() {
		printMessage( $doneStatus, ssMigration.doneText );
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

	function resetCounter() {
		restart = 1;
	}

	function startCounter() {
		restart = 0;
	}

	/**
	 * Import data.
	 * Keep sending ajax requests for the action until done.
	 */
	async function handleMigratePosts() {
		const response = await $.post( ajaxurl, {
			action: 'migrate_posts',
		} );

		if ( response.data.type == 'continue' ) {
			printMessage( $postStatus, response.data.message );
			await handleMigratePosts();
		}
	}

	async function handleMigrateTerms() {
		const response = await $.post( ajaxurl, {
			action: 'migrate_terms',
			restart, // reset again after posts migration.
		} );
		startCounter();
		// Submit form again
		if ( response.data.type == 'continue' ) {
			printMessage( $termStatus, response.data.message );
			await handleMigrateTerms();
		}
	}

} )( window, document, jQuery );
