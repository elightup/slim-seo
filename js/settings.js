( function ( document ) {
	const tabs = document.querySelectorAll( '.ss-tab' );
	const panes = document.querySelectorAll( '.ss-tab-pane' );

	function clickHandle( e ) {
		console.log( e );
		if ( ! e.target.classList.contains( 'ss-tab' ) ) {
			return;
		}

		e.preventDefault();

		tabs.forEach( tab => tab.classList.remove( 'ss-is-active' ) );
		e.target.classList.add( 'ss-is-active' );

		panes.forEach( pane => pane.classList.remove( 'ss-is-active' ) );
		document.querySelector( e.target.getAttribute( 'href' ) ).classList.add( 'ss-is-active' );
	}

	document.querySelector( '.ss-tab-list' ).addEventListener( 'click', clickHandle );

	tippy( '.ss-tooltip', {
		placement: 'right',
		arrow: true,
		animation: 'fade'
	} );
} )( document );