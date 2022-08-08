( function( $ ) {
	if ( typeof inlineEditPost === 'undefined' ) {
		return;
	}
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
		let $row = $( `#edit-${ id }` );
		$.ajax( {
			url: ajaxurl,
			type: 'GET',
			data: {
				action: 'ss_quick_edit',
				post_id: id,
				nonce: $( '#ss_nonce' ).val()
			},
			success: function( { success, data } ) {
				if ( !success ) {
					return;
				}
				// Populate inputs with SEO data.
				$row
					.find( 'input[name="slim_seo\\[title\\]"]' ).val( data.title ).end()
					.find( 'textarea[name="slim_seo\\[description\\]"]' ).val( data.description ).end()
					.find( 'input[name="slim_seo\\[noindex\\]"]' ).prop( 'checked', data.noindex );
			},
		} );
	};
} )( jQuery );
