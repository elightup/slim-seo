document.addEventListener( 'DOMContentLoaded', function() {
	document.querySelector( '#slim-seo-notification .notice-dismiss' ).addEventListener( 'click', function() {
		fetch( ajaxurl + '?action=slim_seo_dismiss_notification&nonce=' + SlimSEONotification.nonce );
	} );
} );
