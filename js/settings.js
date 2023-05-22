( function( document, location ) {
	const initTabs = container => {
		const tabList = container.querySelector( ':scope > .ss-tab-list' ),
			tabs = tabList.querySelectorAll( '.ss-tab' ),
			panes = container.querySelectorAll( ':scope > .ss-tab-pane' ),
			withHash = !container.classList.contains( 'ss-tabs--no-hash' );

		const clickHandle = e => {
			if ( !e.target.classList.contains( 'ss-tab' ) ) {
				return;
			}

			e.preventDefault();

			if ( withHash ) {
				history.pushState( null, null, e.target.getAttribute( 'href' ) );
			}

			tabs.forEach( tab => tab.classList.remove( 'ss-is-active' ) );
			e.target.classList.add( 'ss-is-active' );

			panes.forEach( pane => pane.classList.remove( 'ss-is-active' ) );
			container.querySelector( e.target.getAttribute( 'href' ) ).classList.add( 'ss-is-active' );
		}

		const activateFirstTab = () => {
			const hash = withHash && location.hash ? location.hash : tabs[ 0 ].getAttribute( 'href' );

			container.querySelector( `a[href="${ hash }"]` ).classList.add( 'ss-is-active' );
			container.querySelector( hash ).classList.add( 'ss-is-active' );
		}

		tabList.addEventListener( 'click', clickHandle );
		activateFirstTab();
	}

	document.querySelectorAll( '.ss-tabs' ).forEach( initTabs );

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
