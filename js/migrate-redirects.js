( function ( document, i18n ) {
	const status = document.querySelector( '#redirects-migration-status' ),
		platformSelect = document.querySelector( '#redirection-platform' ),
		button = document.querySelector( '#redirects-migration-process' );

	if ( !button ) {
		return;
	}

	button.addEventListener( 'click', async () => {
		const platform = platformSelect.value;

		try {
			button.closest( '.redirects-migration-handler' ).classList.add( 'hidden' );
			printMessage( status, i18n.preProcessText );

			const response = await get( `${ajaxurl}?action=ss_migrate_redirects&platform=${platform}` );
			printMessage( status, response.data.message );
		} catch ( message ) {
			printMessage( status, message );
		}
	} );

	const get = async ( url ) => {
		const response = await fetch( url );
	    const json = await response.json();

		if ( ! response.ok ) {
			throw Error( json.data );
	    }

		return json;
	}

	const printMessage = ( container, text ) => container.innerHTML = `<p>${ text }</p>`;

} )( document, ssRedirectsMigration );
