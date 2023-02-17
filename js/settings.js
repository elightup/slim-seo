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

	if ( typeof tippy !== 'undefined' ) {
		tippy( '.ss-tooltip', {
			placement: 'right',
			arrow: true,
			animation: 'fade'
		} );
	}

	function toggleCheckboxHandleClick( e ) {
		const input = e.target.parentElement.querySelector( 'input' );
		input.checked = !input.checked;
	}

	const toggleCheckboxes = document.querySelectorAll( '.ss-feature .ss-toggle' );
	toggleCheckboxes.forEach( toggleCheckbox => toggleCheckbox.addEventListener( 'click', toggleCheckboxHandleClick ) );
} )( document, location );
