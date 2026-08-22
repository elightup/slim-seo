( function ( $ ) {
	'use strict';

	function init () {
		$( '#post' ).append( '<input type="hidden" name="ss_primary_term_nonce" value="' + ssPrimaryTerm.nonce + '">' );

		Object.entries( ssPrimaryTerm.taxonomies ).forEach( ( [ taxonomy, taxonomyData ] ) => {
			const panel = $( '#taxonomy-' + taxonomy );

			if ( !panel.length ) {
				return;
			}
			
			panel.append( '<input type="hidden" name="' + taxonomyData.metaKey + '" id="ss-primary-term-' + taxonomy + '" value="' + taxonomyData.primaryValue + '">' );

			updateLinks( panel, taxonomy );

			panel.on( 'change', 'input[type="checkbox"]', function () {
				const $input = $( '#ss-primary-term-' + taxonomy );
				const termId = $( this ).val();

				if ( !this.checked && termId === $input.val() ) {
					$input.val( '' );
				}

				updateLinks( panel, taxonomy );
			} );

			$( document ).on( 'ajaxComplete', function ( e, xhr, settings ) {
				if ( settings.data && typeof settings.data === 'string' && -1 !== settings.data.indexOf( 'action=add-' + taxonomy ) ) {
					setTimeout( function () {
						updateLinks( panel, taxonomy );
					}, 200 );
				}
			} );
		} );
	}

	function updateLinks ( panel, taxonomy ) {
		const allTab = panel.find( '#' + taxonomy + '-all' );
		const checkedBoxes = allTab.find( 'input[type="checkbox"]:checked' );
		const $input = $( '#ss-primary-term-' + taxonomy );
		const primaryId = $input.val() || checkedBoxes.first().val();

		if ( !$input.val() ) {
			$input.val( primaryId );
		}

		panel.find( '.ss-primary-term' ).remove();
		
		if ( checkedBoxes.length <= 1 ) {
			$input.val( 1 === checkedBoxes.length ? checkedBoxes.val() : '' );

			return;
		}

		checkedBoxes.each( function () {
			const $checkbox = $( this );
			const termId = $checkbox.val();
			const isPrimary = termId === primaryId;
			const $link = $( '<a>' )
				.addClass( 'ss-primary-term' )
				.toggleClass( 'ss-primary-term--active', isPrimary )
				.attr( 'href', '#' )
				.text( isPrimary ? ssPrimaryTerm.primaryText : ssPrimaryTerm.setText )
				.on( 'click', function ( e ) {
					e.preventDefault();
					
					$input.val( termId );
					
					updateLinks( panel, taxonomy );
				} );

			$checkbox.closest( 'li' ).append( $link );
		} );
	}

	$( document ).ready( init );
} )( jQuery );
