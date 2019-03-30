jQuery( function( $ ) {
	'use strict';

	$( '#slim-seo-notification .notice-dismiss' ).on( 'click', function( event ) {
		event.preventDefault();

		$.post( ajaxurl, {
			action: 'slim_seo_dismiss_notification',
			nonce: SlimSEONotification.nonce
		} );
	} );
} );
