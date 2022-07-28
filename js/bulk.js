( function( $ ) {
	var wp_inline_edit_function = inlineEditPost.edit;
	inlineEditPost.edit = function( post_id ) {
		wp_inline_edit_function.apply( this, arguments );

		// Get the post ID from the argument
		var id = 0;
		if ( typeof( post_id ) == 'object' ) {
			id = parseInt( this.getId( post_id ) );
		}
		if ( id == 0 ) {
			return;
		}

		// Collect inputs value
		let specific_post_edit_row = $( '#edit-' + id )[0];
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'ss_quick_edit',
				post_id: id,
				nonce: $('#ss_nonce').val()
			},
			success: function(response) {
				console.log( 'response ', response );
				if(response.success) {
					// Populate inputs with column data
					$( ':input[name="slim_seo\\[title\\]"]', specific_post_edit_row ).val( response.data.slim_seo.title );
					$( ':input[name="slim_seo\\[description\\]"]', specific_post_edit_row ).val( response.data.slim_seo.description );
					$( ':input[name="slim_seo\\[noindex\\]"]', specific_post_edit_row ).prop('checked', response.data.slim_seo.noindex );
				}
				else {
					console.log( 'An error occured' );
				}
			},
			error: function( jqXHR, textStatus, errorThrown ){
				console.log( 'The following error occured: ' + textStatus, errorThrown );
			}
		});
	}
	jQuery( 'body' ).on( 'click', 'input[name="bulk_edit"]', function()  {
		$( this ).after('<span class="spinner is-active"></span>');

		let bulk_edit_row = $( 'tr#bulk-edit' ), // Bulk edit form
		post_ids = new Array()

		bulk_edit_row.find( '#bulk-titles' ).children().each( function() {
			post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});
		let slim_seo = new Array();
		slim_seo.push({
			title: bulk_edit_row.find( 'input[name="slim_seo[title]"]' ).val(),
			description: bulk_edit_row.find( 'input[name="slim_seo[description]"]' ).val(),
			noindex: bulk_edit_row.find( 'input[name="slim_seo[noindex]"]' ).attr('checked') ? 1 : 0,
		});
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'ss_save_bulk',
				post_ids: post_ids, // Array of post IDs
				slim_seo: slim_seo,
				nonce: $('#ss_nonce').val()
			}
		});
	});
} )( jQuery );
