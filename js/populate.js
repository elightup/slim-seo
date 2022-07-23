( function( $ ) {
	var wp_inline_edit_function = inlineEditPost.edit;
	inlineEditPost.edit = function( post_id ) {
		wp_inline_edit_function.apply( this, arguments );

		// get the post ID from the argument
		var id = 0;
		if ( typeof( post_id ) == 'object' ) {
			id = parseInt( this.getId( post_id ) );
		}
		if ( id > 0 ) {
			// collect inputs value
			let specific_post_edit_row = $( '#edit-' + id )[0],
				specific_post_row = $( '#post-' + id )[0],
				title = $( '.column-slim_seo\\[title\\]', specific_post_row ).text().substring(0),
				description = $( '.column-slim_seo\\[description\\]', specific_post_row ).text().substring(0),
				noindex = false;
			if ( $( '.column-slim_seo\\[noindex\\]', specific_post_row ).text() == 'Yes' ) noindex = true;

			// populate inputs with column data
			$( ':input[name="slim_seo\\[title\\]"]', specific_post_edit_row ).val( title );
			$( ':input[name="slim_seo\\[description\\]"]', specific_post_edit_row ).val( description );
			$( ':input[name="slim_seo\\[noindex\\]"]', specific_post_edit_row ).prop('checked', noindex );
		}
	}
	// jQuery( 'body' ).on( 'click', 'input[name="bulk_edit"]', function()  {
	// 	jQuery( this ).after('<span class="spinner is-active"></span>');
	// });
} )( jQuery );
