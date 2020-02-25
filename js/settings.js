( function () {
	const tabs = document.querySelectorAll( '.nav-tab' );
	const panes = document.querySelectorAll( '.ss-tab-pane' );

	function clickHandle( e ) {
		if ( ! e.target.classList.contains( 'nav-tab' ) ) {
			return;
		}

		e.preventDefault();

		tabs.forEach( tab => tab.classList.remove( 'nav-tab-active' ) );
		e.target.classList.add( 'nav-tab-active' );

		panes.forEach( pane => pane.classList.remove( 'ss-is-active' ) );
		document.querySelector( e.target.getAttribute( 'href' ) ).classList.add( 'ss-is-active' );
	}

	document.querySelector( '.nav-tab-wrapper' ).addEventListener( 'click', clickHandle );
} () );