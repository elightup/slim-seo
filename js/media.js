( function( ss, wp ) {
    function openMediaPopup() {
		let frame;

		function clickHandle( e ) {
			e.preventDefault();

			// Create a frame only if needed.
			if ( ! frame ) {
				frame = wp.media( {
					multiple: false,
					title: ss.mediaPopupTitle
				} );
			}

			frame.open();

			// Remove all attached 'select' event.
			frame.off( 'select' );

			// Handle selection.
			frame.on( 'select', () => {
				const url = frame.state().get( 'selection' ).first().toJSON().url;
				e.target.previousElementSibling.value = url;
			} );
		}

		const selectButtons = document.querySelectorAll( '.ss-select-image' );
		selectButtons.forEach( button => button.addEventListener( 'click', clickHandle ) );
    }

    openMediaPopup();
} )( ss, wp );
