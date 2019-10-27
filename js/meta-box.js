( function ( document ) {
	function updateCounter( e ) {
		e.target.nextElementSibling.querySelector( '.ss-number' ).textContent = e.target.value.length;
	}

	var inputs = document.querySelectorAll( '.ss-limit' );

	// The 'input' event, used for trigger.
	var inputEvent = document.createEvent( 'HTMLEvents' );
	inputEvent.initEvent( 'input', false, false );

	inputs.forEach( function( input ) {
		input.addEventListener( 'input', updateCounter );
		input.dispatchEvent( inputEvent );
	} );
} )( document );
