( function() {
	let name = ssObjectType === 'post' ? 'inlineEditPost' : 'inlineEditTax';

	if ( typeof window[ name ] === 'undefined' ) {
		return;
	}

	// Quick edit.
	let edit = window[ name ].edit;
	window[ name ].edit = function( id ) {
		edit.apply( this, arguments );

		let objectId = 0;
		if ( typeof id === 'object' ) {
			objectId = parseInt( this.getId( id ), 10 );
		}
		if ( objectId === 0 ) {
			return;
		}

		// Populate inputs with SEO data with Ajax.
		fetch( `${ ajaxurl }?action=ss_quick_edit_${ ssObjectType }&object_id=${ objectId }&nonce=${ document.querySelector( '#ss_nonce' ).value }` )
			.then( response => response.json() )
			.then( ( { success, data } ) => {
				if ( !success ) {
					return;
				}
				let row = document.querySelector( `#edit-${ objectId }` );
				row.querySelector( 'input[name="slim_seo[title]"]' ).value = data.title;
				row.querySelector( 'textarea[name="slim_seo[description]"]' ).value = data.description;
				row.querySelector( 'input[name="slim_seo[noindex]"]' ).checked = !!data.noindex;
			} );
	};

	// Bulk edit.
	document.addEventListener( 'click', e => {
		if ( 'bulk_edit' !== e.target.id ) {
			return;
		}

		let noindex = document.querySelector( '#bulk-edit select[name="noindex"]' ).value;
		let ids = [ ...document.querySelectorAll( '#the-list input[name="post[]"]' ) ]
			.filter( node => node.checked ).map( node => node.value )
			.join( ',' );

		fetch( `${ ajaxurl }?action=ss_save_bulk_${ ssObjectType }&object_ids=${ ids }&noindex=${ noindex }&nonce=${ document.querySelector( '#ss_nonce' ).value }` );
	} );
} )();
