( function() {
	if ( typeof inlineEditPost === 'undefined' ) {
		return;
	}

	// Quick edit.
	let edit = inlineEditPost.edit;
	inlineEditPost.edit = function( post_id ) {
		edit.apply( this, arguments );

		// Get the post ID from the argument.
		let id = 0;
		if ( typeof post_id === 'object' ) {
			id = parseInt( this.getId( post_id ), 10 );
		}
		if ( id === 0 ) {
			return;
		}

		// Populate inputs with SEO data with Ajax.
		fetch( `${ ajaxurl }?action=ss_quick_edit&object_type=post&object_id=${ id }&nonce=${ document.querySelector( '#ss_nonce' ).value }` )
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

	// Bulk edit.
	document.addEventListener( 'click', e => {
		if ( 'bulk_edit' !== e.target.id ) {
			return;
		}

		let noindex = document.querySelector( '#bulk-edit select[name="noindex"]' ).value;
		let ids = [ ...document.querySelectorAll( '#the-list input[name="post[]"]' ) ]
			.filter( node => node.checked ).map( node => node.value )
			.join( ',' );

		fetch( `${ ajaxurl }?action=ss_save_bulk&object_type=post&object_ids=${ ids }&noindex=${ noindex }&nonce=${ document.querySelector( '#ss_nonce' ).value }` );
	} );
} )();
