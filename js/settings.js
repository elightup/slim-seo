( function( document, location ) {
	const tabs = document.querySelectorAll( '.ss-tab' ),
		panes = document.querySelectorAll( '.ss-tab-pane' );

	function clickHandle( e ) {
		if ( !e.target.classList.contains( 'ss-tab' ) ) {
			return;
		}

		e.preventDefault();

		history.pushState( null, null, e.target.getAttribute( 'href' ) );

		tabs.forEach( tab => tab.classList.remove( 'ss-is-active' ) );
		e.target.classList.add( 'ss-is-active' );

		panes.forEach( pane => pane.classList.remove( 'ss-is-active' ) );
		document.querySelector( e.target.getAttribute( 'href' ) ).classList.add( 'ss-is-active' );
	}

	function activateFirstTab() {
		const hash = location.hash || tabs[ 0 ].getAttribute( 'href' );

		document.querySelector( `a[href="${ hash }"]` ).classList.add( 'ss-is-active' );
		document.querySelector( hash ).classList.add( 'ss-is-active' );
	}

	document.querySelector( '.ss-tab-list' ).addEventListener( 'click', clickHandle );

	activateFirstTab();


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
				e.target.parentElement.previousElementSibling.value = url;
			} );
		};

		const selectButtons = document.querySelectorAll( '.ss-select-image' );
		selectButtons.forEach( button => button.addEventListener( 'click', clickHandle ) );
	};
	openMediaPopup();

} )( document, location );
