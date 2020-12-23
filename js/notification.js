document.addEventListener( 'click', function( { target } ) {
	if ( target.classList.contains( 'notice-dismiss') && target.closest( '#slim-seo-notification' ) ) {
		fetch( ajaxurl + '?action=slim_seo_dismiss_notification&nonce=' + SlimSEONotification.nonce );
	}
} );
