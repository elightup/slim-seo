( function ( document, i18n ) {
	'use strict';

	const postStatus = document.querySelector( '#posts-status' ),
		doneStatus = document.querySelector( '#done-status' ),
		termStatus = document.querySelector( '#terms-status' ),
		platformSelect = document.querySelector( '#platform' ),
		button = document.querySelector( '#process' );

	let platform;

	button.addEventListener( 'click', async function () {
		platform = platformSelect.value;
		try {
			preProcess();
			await prepareMigration();
			await resetCounter();
			await handleMigratePosts();
			await resetCounter();
			await handleMigrateTerms();
			doneMigration();
		} catch ( message ) {
			printMessage( postStatus, message );
		}
	} );

	function preProcess() {
		button.closest( '.migration-handler' ).classList.add( 'hidden' );
		printMessage( postStatus, i18n.preProcessText );
	}

	function prepareMigration() {
		return get( `${ajaxurl}?action=ss_prepare_migration&platform=${platform}&_ajax_nonce=${i18n.nonce}` );
	}

	function resetCounter() {
		return get( `${ajaxurl}?action=ss_reset_counter&_ajax_nonce=${i18n.nonce}` );
	}

	async function handleMigratePosts() {
		const response = await get( `${ajaxurl}?action=ss_migrate_posts` );
		if ( response.data.type == 'continue' ) {
			printMessage( postStatus, response.data.message );
			await handleMigratePosts();
		}
	}

	async function handleMigrateTerms() {
		const response = await get( `${ajaxurl}?action=ss_migrate_terms` );
		if ( response.data.type == 'continue' ) {
			printMessage( termStatus, response.data.message );
			await handleMigrateTerms();
		}
	}

	async function get( url ) {
		const response = await fetch( url );
	    const json = await response.json();
		if ( ! response.ok ) {
	       	throw Error( json.data );
	    }
		return json;
	}

	function doneMigration() {
		printMessage( doneStatus, i18n.doneText );
	}

	function printMessage( container, text ) {
		container.innerHTML = `<p>${text}</p>`;
	}
} )( document, ssMigration );
