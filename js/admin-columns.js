( function() {
	let name = ssObjectType === 'post' ? 'inlineEditPost' : 'inlineEditTax';

	if ( typeof window[ name ] === 'undefined' || ! document.querySelector( '#ss_nonce' ) ) {
		return;
	}

	const nonce = document.querySelector( '#ss_nonce' ).value;
	const toQueryString = params => ( new URLSearchParams( params ) ).toString();

	// Quick edit.
	let edit = window[ name ].edit;
	window[ name ].edit = function( objectId ) {
		edit.apply( this, arguments );

		let id = 0;
		if ( typeof objectId === 'object' ) {
			id = parseInt( this.getId( objectId ), 10 );
		}
		if ( id === 0 ) {
			return;
		}

		// Populate inputs with SEO data with Ajax.
		const params = {
			action: `ss_quick_edit_${ ssObjectType }`,
			id,
			nonce
		};
		fetch( `${ ajaxurl }?${ toQueryString( params ) }` )
			.then( response => response.json() )
			.then( ( { success, data } ) => {
				if ( !success ) {
					return;
				}
				let row = document.querySelector( `#edit-${ id }` );
				row.querySelector( 'input[name="slim_seo[title]"]' ).value = data.title;
				row.querySelector( 'textarea[name="slim_seo[description]"]' ).value = data.description;
				row.querySelector( 'input[name="slim_seo[noindex]"]' ).checked = !!data.noindex;
			} );
	};
} )();
