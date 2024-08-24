const select = document.querySelector( '#ss-post-type-select' );
if ( select ) {
	select.addEventListener( 'change', e => {
		document.querySelectorAll( '.ss-post-type-settings' ).forEach( el => el.classList.remove( 'ss-is-active' ) );
		document.querySelector( `.ss-post-type-settings--${ e.target.value }` ).classList.add( 'ss-is-active' );
	} );

	// Selected post type when page is loaded.
	document.querySelector( `.ss-post-type-settings--${ select.value }` ).classList.add( 'ss-is-active' );
}

const openMediaPopup = () => {
	let frame;

	const clickHandle = e => {
		e.preventDefault();

		// Create a frame only if needed.
		if ( !frame ) {
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
	};

	const selectButtons = document.querySelectorAll( '.ss-select-image' );
	selectButtons.forEach( button => button.addEventListener( 'click', clickHandle ) );
};