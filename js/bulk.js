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
		fetch( `${ ajaxurl }?action=ss_quick_edit&post_id=${ id }&nonce=${ document.querySelector( '#ss_nonce' ).value }` )
			.then( response => response.json() )
			.then( ( { success, data } ) => {
				if ( !success ) {
					return;
				}
				// Populate inputs with SEO data.
				let row = document.querySelector( `#edit-${ id }` );
				row.querySelector( 'input[name="slim_seo\\[title\\]"]' ).value = data.title;
				row.querySelector( 'textarea[name="slim_seo\\[description\\]"]' ).value = data.description;
				row.querySelector( 'input[name="slim_seo\\[noindex\\]"]' ).checked = !!data.noindex;
			} );
	};

	// Bulk edit.
	document.addEventListener( 'click', e => {
		if ( 'bulk_edit' !== e.target.id ) {
			return;
		}

		let row = document.querySelector( '#bulk-edit' );
		let list = document.querySelector( '#the-list' );
		let posts = list.querySelectorAll( 'input[name="post[]"]' );

		post_ids = new Array();
		posts.forEach( ( node , index ) => {
			if ( ! node.checked ) {
				return;
			}
			post_ids.push( node.value );
		} );
		let noindex = row.querySelector( 'input[name="slim_seo[noindex]"]' ).checked ? 1 : 0;

		fetch( `${ ajaxurl }?action=ss_save_bulk&post_ids=${ post_ids }&noindex=${ noindex }&nonce=${ document.querySelector( '#ss_nonce' ).value }` );
	} );
} )();
