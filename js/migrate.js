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
			await resetCounter();
			await handleMigratePosts();
			await resetCounter();
			await handleMigrateTerms();
			doneMigration();
		} catch ( err ) {
			printMessage( $postStatus, err.responseJSON.data );
		}
	} );

	function preProcess() {
		$button.closest( '.migration-handler' ).hide();
		printMessage( $postStatus, ssMigration.preProcessText );
	}

	/**
	 * Setup replacer and restart the counter.
	 */
	function prepareMigration() {
		return $.post( ajaxurl, {
			action: 'ss_prepare_migration',
			platform,
			_ajax_nonce: ssMigration.nonce
		} );
	}

	function resetCounter() {
		return $.post( ajaxurl, {
			action: 'ss_reset_counter',
			_ajax_nonce: ssMigration.nonce
		} );
	}

	/**
	 * Keep sending ajax requests for the action until done.
	 */
	async function handleMigratePosts() {
		const response = await $.post( ajaxurl, {
			action: 'ss_migrate_posts',
		} );

		if ( response.data.type == 'continue' ) {
			printMessage( $postStatus, response.data.message );
			await handleMigratePosts();
		}
	}

	async function handleMigrateTerms() {
		const response = await $.post( ajaxurl, {
			action: 'ss_migrate_terms',
		} );
		// Submit form again
		if ( response.data.type == 'continue' ) {
			printMessage( $termStatus, response.data.message );
			await handleMigrateTerms();
		}
	}

	function doneMigration() {
		printMessage( $doneStatus, ssMigration.doneText );
	}

	function printMessage( $status, text ) {
		var message = '<p>' + text + '</p>';
		$status.html( message );
	}

} )( window, document, jQuery );
