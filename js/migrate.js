( function( document, i18n ) {
	const postStatus = document.querySelector( '#posts-status' ),
		doneStatus = document.querySelector( '#done-status' ),
		termStatus = document.querySelector( '#terms-status' ),
		redirectsStatus = document.querySelector( '#redirects-status' ),
		sourceSelect = document.querySelector( '#source' ),
		button = document.querySelector( '#process' );

	let sourceId;

	if ( !button ) {
		return;
	}

	button.addEventListener( 'click', async () => {
		sourceId = sourceSelect.value;

		const group = sourceSelect.options[ sourceSelect.selectedIndex ].parentElement.getAttribute( 'value' );

		try {
			preProcess();
			await prepareMigration();

			if ( 'meta' === group ) {
				await resetCounter();
				await handleMigratePosts();
				await resetCounter();
				await handleMigrateTerms();
			}

			await handleMigrateRedirects();

			doneMigration();
		} catch ( message ) {
			printMessage( postStatus, message );
		}
	} );

	const preProcess = () => {
		button.closest( '.migration-handler' ).classList.add( 'hidden' );
		printMessage( postStatus, i18n.preProcessText );
	};

	const handleMigratePosts = async () => {
		const response = await get( `${ ajaxurl }?action=ss_migrate_posts` );
		if ( response.data.type === 'continue' ) {
			printMessage( postStatus, response.data.message );
			await handleMigratePosts();
		}
	};

	const handleMigrateTerms = async () => {
		const response = await get( `${ ajaxurl }?action=ss_migrate_terms` );
		if ( response.data.type === 'continue' ) {
			printMessage( termStatus, response.data.message );
			await handleMigrateTerms();
		}
	};

	const handleMigrateRedirects = async () => {
		const response = await get( `${ ajaxurl }?action=ss_migrate_redirects` );

		printMessage( redirectsStatus, response.data.message );
	};

	const get = async ( url ) => {
		const response = await fetch( url );
		const json = await response.json();
		if ( !response.ok ) {
			throw Error( json.data );
		}
		return json;
	};

	const prepareMigration = () => get( `${ ajaxurl }?action=ss_prepare_migration&source_id=${ sourceId }&_ajax_nonce=${ i18n.nonce }` );
	const doneMigration = () => printMessage( doneStatus, i18n.doneText );
	const resetCounter = () => get( `${ ajaxurl }?action=ss_reset_counter&_ajax_nonce=${ i18n.nonce }` );
	const printMessage = ( container, text ) => container.innerHTML = `<p>${ text }</p>`;

} )( document, ssMigration );
