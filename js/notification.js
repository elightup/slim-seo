document.addEventListener( 'DOMContentLoaded', function() {
	const closeButton = document.querySelector( '#slim-seo-notification .notice-dismiss' );
	if ( closeButton ) {
		closeButton.addEventListener( 'click', () => fetch( ajaxurl + '?action=slim_seo_dismiss_notification&nonce=' + SlimSEONotification.nonce ) );
	}
} );
